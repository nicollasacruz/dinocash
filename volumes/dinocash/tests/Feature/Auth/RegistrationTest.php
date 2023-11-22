<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'contact' => '(22)22222-2222',
        'document' => '123.456.789-00',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('new users can register with affiliated session', function () {
    // Set the session value before making the request
    $this->withSession(['invitation_link' => 'claudinhoy']);

    User::factory()->withInvitationLink('claudinhoy')->create();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'contact' => '(22)22222-2222',
        'document' => '123.456.789-00',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    
    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);

    $user = User::where('email', 'test@example.com')->first();
    $referral = User::where('invitation_link', 'claudinhoy')->first();

    $this->assertTrue($user->affiliateId !== null);
    $this->assertTrue($user->affiliateId === $referral->id);
});

test('new users can register with affiliated session but referral not isAffiliate', function () {
    // Set the session value before making the request
    $this->withSession(['invitation_link' => 'claudinhoy']);

    User::factory()->create();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'contact' => '(22)22222-2222',
        'document' => '123.456.789-00',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    
    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);

    $user = User::where('email', 'test@example.com')->first();
    $referral = User::where('invitation_link', 'claudinhoy')->first();

    $this->assertTrue($user->affiliateId === null);
});

test('can create a user, add a referral, and set cpaCollected', function () {
    // Criar um usuário
    $user = User::factory()->create();

    // Adicionar um usuário de referência
    $referral =  User::factory()->create();

    $user->addReferral($referral);

    // Verificar se o usuário tem a referência correta
    expect($referral->referredUsers->contains('id', $user->id))->toBeTrue();

    // Definir cpaCollected como verdadeiro
    $user->update(['cpaCollected' => true]);

    // Verificar se cpaCollected foi definido corretamente
    expect($user->cpaCollected)->toBeTrue();

    // Verificar se cpaCollectedAt foi preenchido
    expect($user->cpaCollectedAt)->not()->toBeNull();

});
