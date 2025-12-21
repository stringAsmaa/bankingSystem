<?php

use App\Services\UserRegistrationService;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->service = new UserRegistrationService();
});

afterEach(function () {
    Mockery::close();
});

it('registers a user successfully', function () {
    // Arrange
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ];
    $role = 'client';

    // Mock Role existence check
    $roleMock = Mockery::mock('alias:Spatie\Permission\Models\Role');
    $roleMock->shouldReceive('where')
        ->with('name', $role)
        ->andReturnSelf();
    $roleMock->shouldReceive('exists')
        ->andReturn(true);

    // Mock Hash
    Hash::shouldReceive('make')
        ->with($data['password'])
        ->andReturn('hashed_password');

    // Mock User creation
    $user = Mockery::mock(User::class);
    $user->shouldReceive('assignRole')
        ->with($role)
        ->once();

    $userMock = Mockery::mock('alias:App\Models\User');
    $userMock->shouldReceive('create')
        ->with([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => 'hashed_password',
        ])
        ->andReturn($user);

    // Act
    $result = $this->service->register($data, $role);

    // Assert
    expect($result)->toBe($user);
})->skip('Cannot mock static User::create as class is already loaded');

it('throws exception if role does not exist', function () {
    // Arrange
    $data = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
    ];
    $role = 'invalid_role';

    // Mock Role existence check
    $roleMock = Mockery::mock('alias:Spatie\Permission\Models\Role');
    $roleMock->shouldReceive('where')
        ->with('name', $role)
        ->andReturnSelf();
    $roleMock->shouldReceive('exists')
        ->andReturn(false);

    // Act & Assert
    expect(fn () => $this->service->register($data, $role))
        ->toThrow(Exception::class, "Role '{$role}' does not exist.");
});
