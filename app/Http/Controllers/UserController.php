<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::withCount('loans')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated() + ['email_verified_at' => now()]);
        $user->syncRoles($user->role); // sinkronkan ke Spatie Permission

        ActivityLogger::log('create_user', "Menambah akun {$user->name} ({$user->email}) dengan role {$user->role}.");

        return redirect()->route('admin.users.index')->with('success', "Akun {$user->roleLabel()} {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        // Admin tidak boleh menurunkan role dirinya sendiri agar tidak terkunci dari sistem.
        if ($user->is($request->user())) {
            $data['role'] = $user->role;
        }

        $user->update($data);
        $user->syncRoles($user->role);

        ActivityLogger::log('update_user', "Mengubah akun {$user->name} ({$user->email}).");

        return redirect()->route('admin.users.index')->with('success', "Akun {$user->roleLabel()} {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'Kamu tidak dapat menghapus akunmu sendiri.');
        }

        // Riwayat peminjaman (termasuk yang di-soft delete) harus tetap punya pemilik.
        if ($user->loans()->withTrashed()->exists()) {
            return back()->with('error', "Akun {$user->name} memiliki riwayat peminjaman sehingga tidak dapat dihapus.");
        }

        $user->delete();

        ActivityLogger::log('delete_user', "Menghapus akun {$user->name} ({$user->email}).");

        return redirect()->route('admin.users.index')->with('success', "Akun {$user->roleLabel()} {$user->name} berhasil dihapus.");
    }
}
