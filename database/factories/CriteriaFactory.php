<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class CriteriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'QUESTION-'.$this->faker->unique()->jobTitle(),
        ];
    }
}
