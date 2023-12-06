<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/user/perfil');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/user/perfil', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'wallet' => 0,
            'role' => 'user',
            'invitation_link' => 'claudinhoy',
            'isAffiliate' => true,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/user/perfil');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertSame('claudinhoy', $user->invitation_link);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/user/perfil', [
            'name' => 'Test User',
            'email' => $user->email,
            'wallet' => 0,
            'role' => 'user',
            'invitation_link' => 'claudinhoy',
            'isAffiliate' => false,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/user/perfil');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/user/perfil', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/user/perfil')
        ->delete('/user/perfil', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect('/user/perfil');

    $this->assertNotNull($user->fresh());
});
