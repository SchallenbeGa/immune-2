<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Computer;
use App\Models\User;
use App\Models\Employee;
use App\Models\EmployeeComputerHistory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory()->create([
            'email' => 'test@email.com',
            'password' => 'Colombier$2022',
            'role' => 1
        ]);

        EmployeeComputerHistory::factory(800)->create();
    }
}
