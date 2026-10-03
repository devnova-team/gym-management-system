<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $gym = Gym::firstOrCreate(
            [
                'name' => 'GMS Demo Gym',
            ],
            [
                'phone' => '01000000000',
                'subscription_tier' => 'basic',
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'owner@gmail.com',
            ],
            [
                'name' => 'GMS Owner',
                'phone' => '01000000001',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'gym_id' => $gym->id,
            ]
        );
    }

}

