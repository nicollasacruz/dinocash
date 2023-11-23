<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Withdraw;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Withdraw>
 */
class WithdrawFactory extends Factory
{
    protected $model = Withdraw::class;

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
            'amount' => $this->faker->randomNumber(5),
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
