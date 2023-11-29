<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GameHistory;
use Faker\Factory as Faker;
use App\Models\User;

class GameHistorySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            User::factory()->withInvitationLink((string) $faker->randomNumber(4))->create();
        }
        foreach (range(1, 100) as $index) {
            $user = User::factory()->create();
            $affiliateId = $faker->randomNumber(1);
            $user->addReferral($faker->randomElement(User::where('isAffiliate', true)->get()));
            $user->save();
            $type = $faker->randomElement(['win', 'loss']);
            $amount = $faker->randomNumber(3) * ($type === 'win' ? 1 : -1);
            GameHistory::create([
                'Amount' => $amount,
                'finalAmount' => $amount < 0 ? $amount : $amount * 2 ,
                'userId' => $user->id,
                'type' => $type,
            ]);
        }
    }
}
