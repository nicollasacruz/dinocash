<?php

namespace Database\Seeders;

use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\Withdraw;
use Illuminate\Database\Seeder;
use App\Models\GameHistory;
use Faker\Factory as Faker;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            User::factory()->withInvitationLink((string) $faker->randomNumber(4))->create();
            Deposit::factory()->create();
            Deposit::factory()->paid()->create();
            Withdraw::factory()->create();
            Withdraw::factory()->paid()->create();
            Withdraw::factory()->rejected()->create();
            AffiliateWithdraw::factory()->create();
            AffiliateWithdraw::factory()->paid()->create();
            AffiliateWithdraw::factory()->rejected()->create();
        }
        foreach (range(1, 50) as $index) {
            $user = User::factory()->create();
            $affiliateId = $faker->randomNumber(1);
            $user->addReferral($faker->randomElement(User::where('isAffiliate', true)->get()));
            $user->save();
            $type = $faker->randomElement(['win', 'loss']);
            $amount = $faker->randomNumber($type === 'win' ? 1 : 3) * ($type === 'win' ? 1 : -1);
            GameHistory::create([
                'Amount' => $amount,
                'finalAmount' => $amount < 0 ? $amount : $amount * 2 ,
                'userId' => $user->id,
                'type' => $type,
            ]);
        }
    }
}
