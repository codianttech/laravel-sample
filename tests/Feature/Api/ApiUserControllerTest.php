<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

class ApiUserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Method createUser
     *
     * @return object
     */
    public function createUser()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'user@mailinator.com',
            'user_type' => User::TYPE_USER,
            'password' => 'Test@123',
            'status' => User::STATUS_ACTIVE,
        ];
        return User::create($data);
    }


    /**
     * Method testUserShow
     *
     * @return void
     */
    public function testUserShow(): void
    {
        // Create user in database
        $loginUser = $this->createUser();
        $url = '/api/v1/users/' . $loginUser->id;
        $response = $this->actingAs($loginUser)->get($url);
        $response->assertSee('Test User');
    }


    /**
     * Method testUpdateUserProfile
     *
     * @return void
     */
    public function testUpdateUserProfile(): void
    {
        // Create user in database
        $loginUser = $this->createUser();
        Storage::fake();
        $profile_image = UploadedFile::fake()->image('test_png.png');
        $data = [
            'name' => 'Update Test User',
            'email' => 'user@mailinator.com',
            'user_type' => User::TYPE_USER,
            'password' => 'Test@123',
            'status' => User::STATUS_ACTIVE,
            'phone_code' => '91',
            'phone_number' => '7896541235',
            'profile_image' => $profile_image,
        ];
        $url = '/api/v1/users/' . $loginUser->id;
        $response = $this->actingAs($loginUser)->put($url, $data);
        $response->assertSessionDoesntHaveErrors(
            [
                'name',
                'email',
                'phone_code',
                'phone_number',
                'profile_image'
            ]
        );
        $response->assertSessionHasNoErrors();
        $response->assertSee('Update Test User');
        $this->assertDatabaseHas(
            'users',
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'status' => 'active'
            ]
        );
    }


    /**
     * Method testUserUpdateValidation
     *
     * @return void
     */
    public function testUserUpdateValidation(): void
    {
        // Create user in database
        $loginUser = $this->createUser();
        $url = '/api/v1/users/' . $loginUser->id;
        Storage::fake();
        $profile_image = UploadedFile::fake()->image('test_png.png');
        $data = [
            'name' => '',
            'email' => 'user@mailinator.com',
            'user_type' => User::TYPE_USER,
            'password' => 'Test@123',
            'status' => User::STATUS_ACTIVE,
            'phone_code' => '91',
            'phone_number' => '7896541235',
            'profile_image' => $profile_image,
        ];
        $response = $this->actingAs($loginUser)->put($url, $data);
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('success')
                    ->has('error')
            );
        $response->assertSee('The name field is required.');
        $response->assertStatus(422);
    }

    /**
     * Method testDestroyUser
     *
     * @return void
     */
    public function testDestroyUser(): void
    {
        // Create user in database
        $loginUser = $this->createUser();
        $url = '/api/v1/users/' . $loginUser->id;
        $this->assertModelExists($loginUser);
        $this->actingAs($loginUser)->delete($url);
        $this->assertModelMissing($loginUser);
    }

    /**
     * Method changePassword
     *
     * @return void
     */
    public function testChangePassword(): void
    {
        // Create user in database
        $loginUser = $this->createUser();
        $url = '/api/v1/change-password';
        $userDetails = User::find($loginUser->id);
        $data = [
            'current_password' => 'Test@123',
            'password_confirmation' => 'Test@1234',
            'password' => 'Test@1234'
        ];
        $response = $this->actingAs($loginUser)->post($url, $data);
        $response->assertSee('Password updated successfully.');
        $this->assertDatabaseMissing(
            'users',
            [
                'password' => $userDetails->password
            ]
        );
    }

    /**
     * Method testChangePasswordValidation
     *
     * @return void
     */
    public function testChangePasswordValidation(): void
    {
        // Create user in database
        $loginUser = $this->createUser();
        $url = '/api/v1/change-password';
        User::find($loginUser->id);
        $data = [
            'current_password' => 'Test@123',
            'password_confirmation' => 'Test@1234',
            'password' => 'Test@123'
        ];
        $response = $this->actingAs($loginUser)->post($url, $data);
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('success')
                    ->has('error')
            );
        $response->assertSee('New Password and Confirm Password do not match.,Please use different password.');
        $response->assertStatus(422);
    }


    /**
     * Method testUserNotAllowedToDeleteAnotherUser
     *
     * @return void
     */
    public function testUserNotAllowedToDeleteAnotherUser(): void
    {
        // Create user in database
        $loginUser = $this->createUser();
        $user = User::factory()->create(); // Create User

        $url = '/api/v1/users/' . $user->id;
        $this->assertModelExists($user);
        $response = $this->actingAs($loginUser)->delete($url);
        $this->assertModelExists($user);
        $response->assertSee('You are not allowed to perform this action.');
        $this->assertDatabaseHas(
            'users',
            [
                'password' => $user->password
            ]
        );
    }
}
