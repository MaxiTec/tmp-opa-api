<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'PROPERTY-'.$this->faker->unique()->company(),
            'manager' => $this->faker->unique()->name('male'|'female'),
            'code' =>$this->faker->unique()->postcode(),
            'brand_img' =>$this->faker->imageUrl(640, 480),
            'address' =>$this->faker->streetAddress(),
            'phone' =>$this->faker->phoneNumber(),
            'phone_code' => '+52',
            'lat' => $this->faker->latitude(-90,90) ,
            'lon' => $this->faker->longitude(-180, 180) ,
            'rooms' => $this->faker->numberBetween(100, 1000),
        ];
    }
}
