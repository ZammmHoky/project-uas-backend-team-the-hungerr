<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Menambahkan pengguna baru
        $user = new User();
        $user->name = 'hunger';
        $user->email = 'admin@hunger.com';
        $user->password = bcrypt('admin123'); // Pastikan password di-hash
        $user->save();
    }
}
