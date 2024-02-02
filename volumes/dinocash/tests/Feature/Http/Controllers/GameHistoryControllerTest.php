<?php

use App\Models\GameHistory;
use App\Models\User;


test('test play game dino and create game history', function () {
    $user = User::factory()->create();
    $user->changeWallet(50, 'test');
    $user->save();

    Auth::login($user);

    $response = $this->get(route('user.play'));

    $response->assertStatus(200);
    $user->refresh();

    $response = $this->post(route('user.play.store'), ['amount' => "50.01"]);
    $response->assertStatus(500);
    $user->refresh();
    
    $response = $this->post(route('user.play.store'), ['amount' => "-50.01"]);
    $response->assertStatus(200);
    expect(($response->json())["message"])->toBe("The amount field must be at least 1.");
    $user->refresh();
    
    $response2 = $this->post(route('user.play.store'), ['amount' => "10.50"]);
    $response2->assertStatus(200);
    $user->refresh();

    $gameId = ($response2->json())['gameHistory']['id'];
    
    expect($user->gameHistories->count())->toBe(1);
    expect($user->gameHistories->where('type', 'pending')->count())->toBe(1);
    expect($user->wallet)->toBe(39.5);
    
    $hashString = hash('sha256', $gameId.Auth::user()->id.'dinocash');
    $response = $this->patch(route('user.play.store'), [
        'gameId'=> $gameId,
        'distance' => '100',
        'token' => $hashString
    ]);
    $response->assertStatus(200);
    $user->refresh();

    expect(($response->json())["message"])->toBe("The type field is required.");

    $response = $this->patch(route('user.play.store'), [
        'gameId'=> $gameId,
        'type' => 'win',
        'distance' => '100',
        'token' => $hashString
    ]);

    $response->assertStatus(200);
    $user->refresh();
    $user->gameHistories->last()->refresh();

    expect($user->gameHistories->count())->toBe(1);
    expect($user->gameHistories->last()->type)->toBe('win');
    expect($user->wallet)->toBe(41.6);
    
    $response = $this->patch(route('user.play.store'), [
        'gameId'=> $gameId,
        'token' => $hashString,
        'distance' => '100',
        'type' => 'loss',
    ]);
    
    $user->refresh();
    $user->gameHistories->last()->refresh();
    $response->assertStatus(200);

    expect(($response->json())["message"])->toBe("Partida não encontrada.");
    expect($user->gameHistories->count())->toBe(1);
    expect($user->gameHistories->last()->type)->toBe('win');
    expect(number_format($user->gameHistories->last()->finalAmount, 2))->toBe(number_format(-8.40, 2));
    expect(number_format($user->gameHistories->last()->amount, 2))->toBe(number_format(10.50, 2));
    expect($user->wallet)->toBe(41.6);


    $response3 = $this->post(route('user.play.store'), ['amount' => "20"]);
    $response3->assertStatus(200);
    $user->refresh();
    $gameId = ($response3->json())['gameHistory']['id'];
    $hashString = hash('sha256', $gameId.Auth::user()->id.'dinocash');

    expect($user->gameHistories->count())->toBe(2);
    expect($user->gameHistories->where('type', 'pending')->count())->toBe(1);
    expect(number_format($user->gameHistories->last()->finalAmount, 2))->toBe(number_format(0, 2));
    expect(number_format($user->gameHistories->last()->amount, 2))->toBe(number_format(20.00, 2));

    $response = $this->patch(route('user.play.update'), [
        'gameId'=> $gameId,
        'token' => $hashString,
        'distance' => '100',
        'type' => 'loss',
    ]);

    $user->gameHistories->last()->refresh();
    $response->assertStatus(200);
    $user->refresh();

    expect(number_format($user->gameHistories->last()->finalAmount, 2))->toBe(number_format(-20.00, 2));
    expect($user->gameHistories->where('type', 'pending')->count())->toBe(0);
    expect($user->gameHistories->count())->toBe(2);
});
