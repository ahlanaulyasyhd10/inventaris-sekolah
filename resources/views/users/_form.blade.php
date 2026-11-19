@php($editing = isset($user))
<div class="card-body">
    <div class="mb-3">
        <label class="form-label required" for="name">Nama lengkap</label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap" value="{{ old('name', $user->name ?? '') }}" required autofocus>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="row">
        <div class="col-md-8 mb-3">
            <label class="form-label required" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="contoh: nama@sekolah.test" value="{{ old('email', $user->email ?? '') }}" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label required" for="role">Role</label>
            <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required @disabled($editing && $user->is(auth()->user()))>
                <option value="user" @selected(old('role', $user->role ?? 'user') === 'user')>User</option>
                <option value="admin" @selected(old('role', $user->role ?? 'user') === 'admin')>Admin</option>
            </select>
            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if ($editing && $user->is(auth()->user()))
                <div class="form-hint">Role akunmu sendiri tidak dapat diubah.</div>
            @endif
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="phone">No. WhatsApp</label>
        <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Contoh: 081234567890" value="{{ old('phone', $user->phone ?? '') }}">
        <div class="form-hint">Opsional. Dipakai Admin untuk menghubungi peminjam.</div>
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label {{ $editing ? '' : 'required' }}" for="password">Kata sandi</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ $editing ? 'Kosongkan jika tidak diganti' : 'Minimal 8 karakter' }}" autocomplete="new-password" {{ $editing ? '' : 'required' }}>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label {{ $editing ? '' : 'required' }}" for="password_confirmation">Konfirmasi kata sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" autocomplete="new-password" {{ $editing ? '' : 'required' }}>
        </div>
    </div>
</div>
<div class="card-footer text-end">
    <a href="{{ route('admin.users.index') }}" class="btn btn-link">Batal</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>
