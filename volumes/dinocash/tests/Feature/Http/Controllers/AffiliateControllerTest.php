<?php
use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\GameHistory;
use App\Models\Withdraw;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

ini_set('memory_limit', '-1');
use App\Models\User;

test('affiliate information can be updated for admin', function () {
    $user = User::factory()->create();
    $user->role = 'admin';
    $user->save();

    $response = $this
        ->actingAs($user)
        ->patch(route('admin.afiliados.update'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'wallet' => 100,
            'CPA' => 20,
            'revShare' => 20,
            'role' => 'admin',
            'invitation_link' => 'claudinhoy',
            'isAffiliate' => true,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.afiliados'))
    ;

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertSame('claudinhoy', $user->invitation_link);
    $this->assertSame((float) 100, $user->wallet);
    $this->assertSame(20, $user->CPA);
    $this->assertSame(20, $user->revShare);
    $this->assertSame('admin', $user->role);
});

test('admin can view affiliate admin page', function () {
    $user = User::factory()->withInvitationLink('claudinhoy')->create();
    $user->role = 'admin';
    $user->save();
    $admin = User::factory()->admin()->create();
    auth()->login($admin);
    $affiliateWithdraw = AffiliateWithdraw::factory()->paid()->create();

    $response = $this->get(route('admin.afiliados'));
    $props = ($response->viewData('page'))['props'];
    $affiliates = $props['affiliates'];
    $affiliatesWithdraws = $props['affiliatesWithdraws'];
    $affiliatesWithdrawsList = $props['affiliatesWithdrawsList'];

    $response->assertSessionHasNoErrors()
        ->assertStatus(200)
    ;
    expect(count($affiliates))->toBe(2);
    expect(count($affiliatesWithdrawsList))->toBe(1);
    expect($affiliatesWithdraws)->toBe($affiliateWithdraw->amount);
});

test('admin can view affiliate admin page with filter', function () {
    $user = User::factory()->withInvitationLink('claudinhoy')->create();
    $user->role = 'admin';
    $user->save();
    $admin = User::factory()->admin()->create();
    auth()->login($admin);
    $affiliateWithdraw = AffiliateWithdraw::factory()->paid()->create();

    $response = $this->get(route('admin.afiliados') . '?email=yyyyy');
    $props = ($response->viewData('page'))['props'];
    $affiliates = $props['affiliates'];
    $affiliatesWithdraws = $props['affiliatesWithdraws'];
    $affiliatesWithdrawsList = $props['affiliatesWithdrawsList'];

    $response->assertSessionHasNoErrors()
        ->assertStatus(200)
    ;
    expect(count($affiliates))->toBe(0);
    expect(count($affiliatesWithdrawsList))->toBe(0);
    expect($affiliatesWithdraws)->toBe(0);
});

test('admin can see historys of the affiliate with button in affiliate page', function () {
    $user = User::factory()->withInvitationLink('claudinhoy')->create();
    $user2 = User::factory()->create();

    $user->role = 'admin';
    $user->save();
    $user2->affiliateId = $user->id;
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

    $withdraw = AffiliateWithdraw::factory()->paid()->create();
    $withdraw->userId = $user->id;
    $withdraw->save();
    auth()->login($user);

    $responseComissao = post(route('admin.afiliados.comissao', ['user' => $user]));
    $responseJogadas = post(route('admin.afiliados.jogadas', ['user' => $user]));
    $responseSaques = post(route('admin.afiliados.saques', ['user' => $user]));

    $responseComissao->assertSessionHasNoErrors()
        ->assertStatus(200);
    $responseJogadas->assertSessionHasNoErrors()
        ->assertStatus(200);
    $responseSaques->assertSessionHasNoErrors()
        ->assertStatus(200);

    expect(($responseComissao->json())['transactions'][0]['affiliateId'])->toBe($user->id);
    expect(count(($responseComissao->json())['transactions']))->toBe(1);
    expect(($responseJogadas->json())['transactions'][0]['userId'])->toBe($user->id);
    expect(count(($responseJogadas->json())['transactions']))->toBe(1);
    expect(($responseSaques->json())['transactions'][0]['userId'])->toBe($user->id);
    expect(count(($responseSaques->json())['transactions']))->toBe(1);
});


test('admin can delete affiliate with button in affiliate page', function () {
    $user2 = User::factory()->withInvitationLink('claudinho')->create();

    $admin = User::factory()->admin()->create();
    auth()->login($admin);

    actingAs($admin)->delete(route('admin.afiliados.destroy', ['user' => $user2]));

    $response = $this->get(route('admin.afiliados'));
    $props = ($response->viewData('page'))['props'];
    $affiliates = $props['affiliates'];

    $response->assertSessionHasNoErrors()
        ->assertStatus(200)
    ;
    expect(count($affiliates))->toBe(1);
});
