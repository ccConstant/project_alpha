<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'eq_internReference' => $this->faker->word,
            'eq_externReference' =>$this->faker->word,
            'eq_name' =>$this->faker->word,
            'eq_type' => $this->faker->randomElement(['PRODUCTION', 'CONTROL']),
            'eq_serialNumber' =>$this->faker->isbn13,
            'eq_constructor' => $this->faker->name, 
            'eq_mass' => $this->faker->randomFloat,
            'eq_massUnit'=> $this->faker->randomElement(['G','KG']),
            'eq_remarks' => $this->faker->sentence, 
            'eq_set' => $this->faker->word 

        ];
    }
}
