<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $users = [
            [
                'name' => 'Budi Staff',
                'email' => 'staff@system.com',
                'password' => $password,
                'role' => 'Staff',
            ],
            [
                'name' => 'Siti Supervisor',
                'email' => 'spv@system.com',
                'password' => $password,
                'role' => 'Supervisor',
            ],
            [
                'name' => 'Agus Manager',
                'email' => 'manager@system.com',
                'password' => $password,
                'role' => 'Manager',
            ],
            [
                'name' => 'Dewi Director',
                'email' => 'director@system.com',
                'password' => $password,
                'role' => 'Director',
            ],
            [
                'name' => 'Fahmi Finance',
                'email' => 'finance@system.com',
                'password' => $password,
                'role' => 'Finance',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );

            // Assign Spatie Role
            $user->syncRoles([$userData['role']]);
        }
    }
}
