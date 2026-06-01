<?php

use App\Models\User;

test('confirm password screen can be rendered', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/confirm-password');

    $response->assertStatus(200);
});

test('password can be confirmed', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $token = csrf_token();

    $response = $this->actingAs($user)->withSession(['_token' => $token])->post('/confirm-password', [
        '_token' => $token,
        'password' => 'password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $token = csrf_token();

    $response = $this->actingAs($user)->withSession(['_token' => $token])->post('/confirm-password', [
        '_token' => $token,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
});
