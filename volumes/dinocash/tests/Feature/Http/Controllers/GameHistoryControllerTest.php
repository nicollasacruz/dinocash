<?php

use App\Models\User;


test('test play game dino and create game history', function () {
    $user = User::factory()->create();
    $user->changeWallet(50);
    $user->save();

    Auth::login($user);

    $response = $this->get(route('user.play'));

    $response->assertStatus(200);
    $user->refresh();

    $response = $this->post(route('user.play.store'), ['user'=> $user->id, 'amount' => "50.01"]);
    $response->assertStatus(500);
    $user->refresh();
    
    $response = $this->post(route('user.play.store'), ['user'=> $user->id, 'amount' => "-50.01"]);
    $response->assertStatus(200);
    expect(($response->json())["message"])->toBe("The amount field must be at least 1.");
    $user->refresh();
    
    $response = $this->post(route('user.play.store'), ['user'=> $user->id, 'amount' => "10.50"]);
    $response->assertStatus(200);
    $user->refresh();
    
    expect($user->gameHistories->count())->toBe(1);
    expect($user->gameHistories->where('type', 'pending')->count())->toBe(1);
    expect($user->wallet)->toBe(39.5);
    
    $response = $this->patch(route('user.play.store'), [
        'user'=> $user->id,
        'amount' => '20.00',
        'distance' => '100'
    ]);
    $response->assertStatus(200);
    $user->refresh();

    expect(($response->json())["message"])->toBe("The type field is required.");

    $response = $this->patch(route('user.play.store'), [
        'user'=> $user->id,
        'amount' => '20.00',
        'distance' => '100',
        'type' => 'win',
    ]);

    $response->assertStatus(200);
    $user->refresh();
    $user->gameHistories->last()->refresh();

    
    expect($user->gameHistories->count())->toBe(1);
    expect($user->gameHistories->last()->type)->toBe('win');
    expect($user->wallet)->toBe(59.5);
    
    $response = $this->patch(route('user.play.store'), [
        'user'=> $user->id,
        'amount' => '20.00',
        'distance' => '100',
        'type' => 'loss',
    ]);
    $user->refresh();
    $user->gameHistories->last()->refresh();
    $response->assertStatus(200);

    expect(($response->json())["message"])->toBe("Partida não encontrada.");
    expect($user->gameHistories->count())->toBe(1);
    expect($user->gameHistories->last()->type)->toBe('win');
    expect(number_format($user->gameHistories->last()->finalAmount, 2))->toBe(number_format(20, 2));
    expect(number_format($user->gameHistories->last()->amount, 2))->toBe(number_format(10.50, 2));
    expect($user->wallet)->toBe(59.5);


    $response = $this->post(route('user.play.store'), ['user'=> $user->id, 'amount' => "20"]);
    $response->assertStatus(200);
    $user->refresh();

    expect($user->gameHistories->count())->toBe(2);
    expect($user->gameHistories->where('type', 'pending')->count())->toBe(1);
    expect(number_format($user->gameHistories->last()->finalAmount, 2))->toBe(number_format(0, 2));
    expect(number_format($user->gameHistories->last()->amount, 2))->toBe(number_format(20.00, 2));

    $response = $this->patch(route('user.play.store'), [
        'user'=> $user->id,
        'amount' => '20.00',
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
