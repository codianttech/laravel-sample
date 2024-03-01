<?php

namespace Tests\Feature;

use App\Exceptions\CustomException;
use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\ResetPasswordRepository;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Str;


/**
 * ResetPasswordRepositoryTest
 */
class ResetPasswordRepositoryTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * Method testCreateRecord
     *
     * @return void
     */
    public function testCreateRecord(): void
    {
        $user = User::factory()->create(); // Create User
        $data = [
            'email' => $user->email,
            'name' => $user->name,
            'token' => Str::random(64),
            'created_at' => date("Y-m-d H:i:s"),
            'user_type' => User::TYPE_ADMIN
        ];
        $resetPasswordRepository = new ResetPasswordRepository(new UserRepository(new User), new PasswordReset);
        $resetPassword = $resetPasswordRepository->createRecord($data);
        $this->assertTrue($resetPassword);
        $this->assertDatabaseHas(
            'password_resets',
            [
                'email' => $data['email'],
            ]
        );
    }

    /**
     * Method testDeleteRecord
     *
     * @return void
     */
    public function testDeleteRecord(): void
    {
        $user = User::factory()->create(); // Create User
        $data = [
            'email' => $user->email,
            'name' => $user->name,
            'token' => Str::random(64),
            'created_at' => date("Y-m-d H:i:s"),
            'user_type' => User::TYPE_ADMIN
        ];
        $resetPasswordRepository = new ResetPasswordRepository(new UserRepository(new User), new PasswordReset);
        $resetPasswordRepository->createRecord($data);
        $data['email'] = $user->email;
        $deleteRecord = $resetPasswordRepository->deleteRecord(['email' => $data['email']]);
        $this->assertNotNull($deleteRecord);
        $this->assertDatabaseMissing(
            'password_resets',
            [
                'email' => $data['email']
            ]
        );
    }

    /**
     * Method testResetPassword
     *
     * @return void
     */
    public function testResetPassword(): void
    {
        $user = User::factory()->create(); // Create User
        $data = [
            'email' => $user->email,
            'name' => $user->name,
            'token' => Str::random(64),
            'created_at' => date("Y-m-d H:i:s"),
            'user_type' => User::TYPE_ADMIN,
            'password' => 123456
        ];
        $resetPasswordRepository = new ResetPasswordRepository(new UserRepository(new User), new PasswordReset);
        $resetPasswordRepository->createRecord($data);
        $deleteRecord = $resetPasswordRepository->resetPassword($data);
        $this->assertNotNull($deleteRecord);
        $this->assertDatabaseMissing(
            'password_resets',
            [
                'email' => $data['email']
            ]
        );
        $this->assertDatabaseMissing(
            'users',
            [
                'password' => $user->password
            ]
        );
    }

    /**
     * Method testUserNotFindResetPassword
     * user not find when reset password function call
     * 
     * @return void
     */
    public function testUserNotFindResetPassword(): void
    {
        $user = User::factory()->create(); // Create User
        $data = [
            'email' => $user->email,
            'name' => $user->name,
            'token' => Str::random(64),
            'created_at' => date("Y-m-d H:i:s"),
            'user_type' => User::TYPE_ADMIN,
            'password' => 123456
        ];
        $resetPasswordRepository = new ResetPasswordRepository(new UserRepository(new User), new PasswordReset);
        $resetPasswordRepository->createRecord($data);
        $infoData['email'] = $this->faker->email;
        $this->expectException(CustomException::class);
        $this->expectExceptionMessage('User not found.');
        $resetPasswordRepository->resetPassword($infoData);
    }

    /**
     * Method tokenExpireWithResetPassword
     *
     * @return void
     */
    public function testTokenExpireWithResetPassword(): void
    {
        $user = User::factory()->create(); // Create User
        $data = [
            'email' => $user->email,
            'name' => $user->name,
            'token' => Str::random(64),
            'created_at' => date("Y-m-d H:i:s"),
            'user_type' => User::TYPE_ADMIN,
            'password' => 123456
        ];
        $resetPasswordRepository = new ResetPasswordRepository(new UserRepository(new User), new PasswordReset);
        $resetPasswordRepository->createRecord($data);
        $data['token'] = Str::random(64);
        $this->expectException(CustomException::class);
        $this->expectExceptionMessage('Reset password Link Expired.');
        $resetPasswordRepository->resetPassword($data);
    }
}
