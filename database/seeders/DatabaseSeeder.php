<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo người dùng admin mặc định
        User::create([
            'username' => 'admin',
            'password_hash' => Hash::make('tu211102'), // Mật khẩu: 'password', mã hóa bằng Hash
            'role' => 'admin',
            'created_at' => now(),
        ]);
    }
}