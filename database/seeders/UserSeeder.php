<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $gym = Gym::where('name', 'GMS Demo Gym')->first();

        if (!$gym) {
            $this->command->error(
                'GMS Demo Gym not found. Run OwnerSeeder first.'
            );

            return;
        }

        $users = [
            [
                'name' => 'yaser Ali',
                'email' => 'ahmed@gmail.com',
                'role' => 'trainer',
                'phone' => '01000003001',
            ],
            [
                'name' => 'sara Hassan',
                'email' => 'sara@gmail.com',
                'role' => 'receptionist',
                'phone' => '01000000401',
            ],
            [
                'name' => 'Omar Samir',
                'email' => 'omar@gmail.com',
                'role' => 'receptionist',
                'phone' => '01000006001',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                [
                    'email' => $user['email'],
                ],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password123'),
                    'role' => $user['role'],
                    'phone' => $user['phone'],
                    'gym_id' => $gym->id,
                ]
            );
        }
    }
}
