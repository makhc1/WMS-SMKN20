<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'              => 'Admin Gudang',
                'email'             => 'admin@smkn20wm.sch.id',
                'password'          => Hash::make('Admin@SMKN20#2026'),
                'role'              => 'Admin',
                'status'            => 'Active',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Warehouse Manager',
                'email'             => 'wm@smkn20wm.sch.id',
                'password'          => Hash::make('W@reh0use#2026'),
                'role'              => 'Warehouse Manager',
                'status'            => 'Active',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
