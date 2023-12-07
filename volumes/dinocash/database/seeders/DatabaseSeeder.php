<?php

namespace Database\Seeders;

use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\Withdraw;
use Hash;
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
        if (User::count() == 0 || User::where("email","admin@dinocash.io")->count() == 0) {
            $user = User::create([
                'name' => 'admin',
                'email' => 'admin@dinocash.io',
                'contact' => '(22)22222-2221',
                'document' => '156.201.067-05',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'wallet' => 500
            ]);
            GameHistory::create([
                'amount' => 50,
                'finalAmount' => 100,
                'distance' => 2,
                'userId' => $user->id,
                'type' => 'win',
            ]);
        }

        $faker = Faker::create();
        foreach (range(1, 20) as $index) {
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
            $game = GameHistory::create([
                'amount' => $amount,
                'finalAmount' => $amount < 0 ? $amount : $amount * 2,
                'distance' => random_int(10, 500),
                'userId' => $user->id,
            ]);
            $game->update([
                'type' => $type,
            ]);
        }
    }
}
