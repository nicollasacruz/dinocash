<?php
use App\Models\Deposit;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use function Pest\Laravel\actingAs;

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


    $responseDeposit = $this->post(route('user.deposit.store'), [
        'document' => '156.201.067-05',
        'amount' => 15.00,
    ]);

    $responseDeposit->assertSessionHasNoErrors()->assertStatus(200);
    $deposits = Auth::user()->deposits();


    expect($deposits->sum('amount'))->toBe(15.0);
    expect(Deposit::count())->toBe(1);
    expect($deposits->first()->user->document)->toBe('156.201.067-05');
    expect($deposits->first()->type)->toBe('pending');
    expect((float)$deposits->first()->amount)->toBe(15.00);
});

test('create user and make a deposit and aprove deposit', function () {
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


    $this->post(route('user.deposit.store'), [
        'document' => '156.201.067-05',
        'amount' => 15.00,
    ]);
    $deposit = Auth::user()->deposits()->first();
    $responseDeposit = $this->post(env('SUITPAY_URL_WEBHOOK'), [
        'idTransaction' => $deposit->transactionId,
        'typeTransaction' => 'PIX',
        'statusTransaction' => 'PAYMENT_ACCEPT',
    ]);

    $responseDeposit->assertSessionHasNoErrors()->assertStatus(200);
    $deposit->refresh();
    
    expect(Deposit::count())->toBe(1);
    expect($deposit->user->document)->toBe('156.201.067-05');
    expect($deposit->type)->toBe('paid');
    expect((float)$deposit->amount)->toBe(15.00);
    expect((float)$deposit->amount)->toBe($deposit->user->wallet);

});

test('create user and make a deposit and aprove deposit and check affiliate history and wallet', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'contact' => '(22)22222-2222',
        'document' => '156.201.067-05',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::find(Auth::user()->id);
    $affiliate = User::factory()->withInvitationLink('claudinhoy')->create();

    $user->addReferral($affiliate);
    expect($user->affiliateId)->toBe($affiliate->id);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);


    $this->post(route('user.deposit.store'), [
        'document' => '156.201.067-05',
        'amount' => 100.00,
    ]);
    $deposit = Auth::user()->deposits()->first();
    expect((float)$deposit->amount)->toBe(100.0);

    $responseDeposit = $this->post(env('SUITPAY_URL_WEBHOOK'), [
        'idTransaction' => $deposit->transactionId,
        'typeTransaction' => 'PIX',
        'statusTransaction' => 'PAYMENT_ACCEPT',
    ]);

    $responseDeposit->assertSessionHasNoErrors()->assertStatus(200);
    $user->refresh();
    $affiliate->refresh();
    $deposit->refresh();

    
    expect(Deposit::count())->toBe(1);
    expect($deposit->user->document)->toBe('156.201.067-05');
    expect($deposit->type)->toBe('paid');
    expect((float)$deposit->amount)->toBe(100.00);
    expect((float)$deposit->amount)->toBe($deposit->user->wallet);
    expect((float)$affiliate->walletAffiliate)->toBe(15.0);
});