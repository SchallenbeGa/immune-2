<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Computer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EmployeeComputerHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {// Vérifier s'il y a des utilisateurs dans la base de données
       
        $employee = Employee::factory()->create();
        $computer = Computer::factory()->create([
            'employee_id' => $employee->id
        ]);
        return [
            'employee_id' => $employee->id,
            'computer_id' => $computer->id,
            'assigned_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
