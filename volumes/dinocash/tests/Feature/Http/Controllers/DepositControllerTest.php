<?php
use App\Providers\RouteServiceProvider;

test('create user and make a deposit', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'contact' => '(22)22222-2222',
        'document' => '156.201.067-05',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);

    
    $responseDeposit = $this->post('/user/deposit', [
        'document' => '156.201.067-05',
        'amount' => 15.00,
    ]);
    $deposit = Auth::user()->deposits();
    dd($deposit);
});