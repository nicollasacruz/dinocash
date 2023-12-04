<?php

use App\Models\GameHistory;
use App\Models\User;

test('user confirm invitation link', function () {
    $invitation = 'claudinhoy';
    $user = User::factory()->withInvitationLink($invitation)->create();
    $this->assertTrue($user->getInvitationLink() === env('APP_URL') . '/ref/' . $invitation);
});

test('return affiliate of the user', function () {
    $invitation = 'claudinhoy';
    $affiliate = User::factory()->withInvitationLink($invitation)->create();

    $user = User::factory()->create();

    $user->setAffiliate($affiliate);
    $this->assertSame($user->affiliate()->first()->id, $affiliate->id);
});

test('return users of the affiliate', function () {
    $invitation = 'claudinhoy';
    $affiliate = User::factory()->withInvitationLink($invitation)->create();

    $user = User::factory()->create();
    $user2 = User::factory()->create();

    $user->setAffiliate($affiliate);
    $user2->setAffiliate($affiliate);

    $referredUsersCount = $affiliate->referredUsers->count();

    $this->assertSame($referredUsersCount, 2);

    $this->assertSame($user->affiliateId, $affiliate->id);
    $this->assertSame($user2->affiliateId, $affiliate->id);
});

test('change wallet of the user ', function () {
    $user = User::factory()->create();
    $user->wallet = 0.1;
    $user->changeWallet(0.2);
    $wallet = $user->wallet;
    $this->assertSame($wallet, 0.3);
});

test('change walletAffiliate of the affiliate ', function () {
    $invitation = 'claudinhoy';
    $affiliate = User::factory()->withInvitationLink($invitation)->create();
    $user = User::factory()->create();
    $user2 = User::factory()->create();
    $user->setAffiliate($affiliate);
    $user2->setAffiliate($affiliate);
    $affiliate->updateOrFail([
        'revShare'=> 10,
    ]);
    $affiliate->save();
    $affiliate->refresh();

    $user->save();
    $user2->save();

    $game1 = GameHistory::create([
        'amount' => 100,
        'finalAmount' => -100,
        'userId' => $user2->id,
        'type' => 'pendent',
    ]);

    $game2 = GameHistory::create([
        'amount' => 100,
        'finalAmount' => -100,
        'userId' => $user->id,
        'type' => 'pendent',
    ]);

    
    $game1->type = 'loss';
    $game2->type = 'loss';
    $game1->save();
    $game2->save();

    $affiliate->refresh();
    
    $this->assertSame($affiliate->walletAffiliate, 20.0);
});

