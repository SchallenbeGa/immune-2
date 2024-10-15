<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ComputerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {// Vérifier s'il y a des utilisateurs dans la base de données
       
        $employee = Employee::factory()->create();
        
        return [
            'reference' => $this->faker->unique()->uuid, // Référence aléatoire
            'employee_id' => $employee->id, // Attribuer un utilisateur aléatoire
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
