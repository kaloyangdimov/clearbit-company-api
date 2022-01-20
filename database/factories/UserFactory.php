<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email'          => $this->faker->unique()->safeEmail(),
            'password'       => 'password',
            'token'          => hash('sha256', Uuid::uuid4()->toString()),
            'token_valid_to' => now()->add('days', 1)->toDateTime()
        ];
    }
}
