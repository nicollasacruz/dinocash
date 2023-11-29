<?php

namespace Database\Factories;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deposit>
 */
class DepositFactory extends Factory
{

    // protected $model = Deposit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'userId' => function () {
                return User::factory()->create()->id;
            },
            'transactionId' => $this->faker->uuid,
            'paymentCode' => $this->faker->uuid,
            'amount' => $this->faker->randomNumber(3),
            'type' => 'pendent',
        ];
    }

    /**
     * Indicate that the model'swith status paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'paid',
            'approvedAt' => now(),
        ]);
    }

}
