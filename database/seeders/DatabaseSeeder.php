<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'test owner',
            'email' => 'testowner@example.com',
            'password' => Hash::make("password"),
            'role' => "owner",
        ]);

        \App\Models\User::factory()->create([
            'name' => 'test kitchen',
            'email' => 'testkitchen@example.com',
            'password' => Hash::make("password"),
            'role' => "kitchen",
        ]);

        \App\Models\User::factory()->create([
            'name' => 'test cashier',
            'email' => 'testcashier@example.com',
            'password' => Hash::make("password"),
            'role' => "cashier",
        ]);
    }
}
