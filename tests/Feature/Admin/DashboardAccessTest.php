<?php

use App\Models\User;

test('admin dashboard is not accessible by normal users', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $response = $this->actingAs($user)->get('/admin/dashboard');

    $response->assertForbidden();
});

test('admin dashboard is accessible by admin users', function () {
    /** @var \Tests\TestCase $this */
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertOk();
});