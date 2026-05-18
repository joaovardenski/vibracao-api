<?php

namespace Database\Factories;

use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Participant>
 */
class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),

            'cpf' => fake()->unique()->numerify('###########'),

            'email' => fake()->unique()->safeEmail(),

            'phone' => fake()->numerify('(##) #####-####'),

            'city' => fake()->city(),

            'parish' => fake()->randomElement([
                'Paróquia São Cristóvão',
                'Paróquia Nossa Senhora Aparecida',
                'Paróquia Sagrado Coração de Jesus',
                'Paróquia São João Batista',
                'Paróquia Sant’Ana',
            ]),

            'emergency_contact' => fake()->optional()->phoneNumber(),
        ];
    }
}