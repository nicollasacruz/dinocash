<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AffiliateWithdraw;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Withdraw>
 */
class AffiliateWithdrawFactory extends Factory
{
    protected $model = AffiliateWithdraw::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'pendent',
            'userId' => function () {
                return User::factory()->withInvitationLink($this->faker->userName)->create()->id;
            },
            'transactionId' => $this->faker->uuid,
            'amount' => $this->faker->randomNumber(5),
        ];
    }

    /**
     * Indicate that the model'swith status paid.
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'paid',
            'approvedAt' => now(),
            'userId' => function () {
                return User::factory()->withInvitationLink($this->faker->userName)->create()->id;
            },
            'transactionId' => $this->faker->uuid,
            'amount' => $this->faker->randomNumber(5),
        ]);
    }

    /**
     * Indicate that the model'swith status rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'rejected',
            'reprovedAt' => now(),
            'userId' => function () {
                return User::factory()->withInvitationLink($this->faker->userName)->create()->id;
            },
            'transactionId' => $this->faker->uuid,
            'amount' => $this->faker->randomNumber(5),
        ]);
    }
}
