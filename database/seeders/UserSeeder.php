<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Kredensial untuk testing (juga dicantumkan di README): password semua akun = "password".
        foreach (InventoryData::users() as [$name, $email, $role, $phone]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'role' => $role, 'phone' => $phone, 'password' => InventoryData::PASSWORD, 'email_verified_at' => now()]
            );
            $user->syncRoles($role);
        }
    }
}
