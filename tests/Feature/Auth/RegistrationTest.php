<?php

test('registration screen can be rendered', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    /** @var \Tests\TestCase $this */
    $token = csrf_token();

    $response = $this->withSession(['_token' => $token])->post('/register', [
        '_token' => $token,
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
});
