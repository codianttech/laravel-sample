<?php

namespace Tests\Feature\AdminController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * ProfileControllerTest
 */
class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Method testGetloginUserProfile
     *
     * @return void
     */
    public function testGetloginUserProfile(): void
    {
        Artisan::call('db:seed --class=AdminUserSeeder');
        $userData = User::where('email', 'backend@mailinator.com')->first();
        $url = 'admin/profile';
        $response = $this->actingAs($userData)->ajaxGet($url)->assertSuccessful();
        $response->assertViewIs('admin.profile.index');
        $response->assertViewHas('userData', $userData);
    }

    /**
     * Method testUpdateProfileNameValidation
     *
     * @return void
     */
    public function testUpdateProfileNameValidation(): void
    {
        Artisan::call('db:seed --class=AdminUserSeeder');
        $userData = User::where('email', 'backend@mailinator.com')->first();
        $url = route('admin.profile.update', $userData->id);
        $data = [
            'email' => 'backend@mailinator.com',
            'user_type' => User::TYPE_ADMIN,
            'password' => 'Test@123',
            'status' => User::STATUS_ACTIVE,
            'phone_number' => '9265606124',
        ];
        $data['name'] = '';
        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'name' => 'The Name field is required.'
            ]
        )->assertStatus(302);
        // min validations
        $data['name'] = 'A';
        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'name' => 'The name must be at least 3 characters.'
            ]
        )->assertStatus(302);
        // max validation
        $data['name'] = 'In another fascinating psychology test,
             we discover that the first letter of your name
             holds interesting secrets of your personality
             traits. Just like body parts can reveal your
             personality, your name also can reveal your
             true personality traits. In this name personality';

        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'name' => 'The name must not be greater than 50 characters.'
            ]
        )->assertStatus(302);
    }

    /**
     * Method testUpdateProfileEmailValidation
     *
     * @return void
     */
    public function testUpdateProfileEmailValidation(): void
    {
        Artisan::call('db:seed --class=AdminUserSeeder');
        $userData = User::where('email', 'backend@mailinator.com')->first();
        $url = route('admin.profile.update', $userData->id);
        $data = [
            'name' => 'ABCD',
            'user_type' => User::TYPE_ADMIN,
            'password' => 'Test@123',
            'status' => User::STATUS_ACTIVE,
            'phone_number' => '9265606124',
        ];
        $data['email'] = '';
        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'email' => 'The Email field is required.'
            ]
        )->assertStatus(302);
        // min validations
        $data['email'] = 'b';

        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'email' => 'The email must be a valid email address.'
            ]
        )->assertStatus(302);
        // max validation
        $data['email'] = 'backendbackendbackendbackendbackendbackendbackendbackendbackendbackend@mailinator.com';

        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'email' => 'The email must not be greater than 50 characters.'
            ]
        )->assertStatus(302);
        // valid email address validation
        $data['email'] = 'backend';
        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'email' => 'The email must be a valid email address.'
            ]
        )->assertStatus(302);
    }

    /**
     * Method testUpdateProfilePhoneNumberValidation
     *
     * @return void
     */
    public function testUpdateProfilePhoneNumberValidation(): void
    {
        Artisan::call('db:seed --class=AdminUserSeeder');
        $userData = User::where('email', 'backend@mailinator.com')->first();
        $url = route('admin.profile.update', $userData->id);
        $data = [
            'name' => 'ABCD',
            'email' => 'backend@mailinator.com',
            'user_type' => User::TYPE_ADMIN,
            'password' => 'Test@123',
            'status' => User::STATUS_ACTIVE,
        ];
        $data['phone_number'] = '';
        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'phone_number' => 'The Phone number field is required.'
            ]
        )->assertStatus(302);
        // min validations
        $data['phone_number'] = 'bbc';
        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'phone_number' => 'The phone number must be a number.'
            ]
        )->assertStatus(302);
        // max validation
        $data['phone_number'] = 6564546546464646;
        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'phone_number' => 'The phone number must be between 10 and 12 digits.'
            ]
        )->assertStatus(302);
        // validation
        $data['phone_number'] = 123654123;
        $this->actingAs($userData)->put($url, $data)->assertSessionHasErrors(
            [
                'phone_number' => 'The phone number must be between 10 and 12 digits.'
            ]
        )->assertStatus(302);
    }

    /**
     * Method testUpdateLoginUserProfile
     *
     * @return void
     */
    public function testUpdateLoginUserProfile(): void
    {
        Artisan::call('db:seed --class=AdminUserSeeder');
        $userData = User::where('email', 'backend@mailinator.com')->first();
        $url = route('admin.profile.update', $userData->id);
        $data = [
            'name' => 'ABCD',
            'email' => 'backend@mailinator.com',
            'user_type' => User::TYPE_ADMIN,
            'password' => 'Test@123',
            'status' => User::STATUS_ACTIVE,
            'phone_number' => 1236547890,
        ];
        $response = $this->actingAs($userData)->put($url, $data);
        $response->assertSee('Profile updated successfully.')->assertStatus(200);
        $this->assertDatabaseHas(
            'users',
            [
                'id' => $userData->id,
                'name' => 'ABCD',
                'email' => 'backend@mailinator.com',
                'user_type' => User::TYPE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'phone_number' => 1236547890,
            ]
        );
    }

     
        
    /**
     * Method testHttpChangePassword
     *
     * @return void
     */
    public function testHttpChangePassword(): void
    {
        Artisan::call('db:seed --class=AdminUserSeeder');
        $userData = User::where('email', 'backend@mailinator.com')->first();
        $url = route('admin.change-password');
        $data = [
            'current_password' => 'Test@123',
            'password' => 'Test@1234',
            'password_confirmation' => 'Test@1234',
        ];
        $response = $this->actingAs($userData)->post($url, $data);
        $user = User::where('email', 'backend@mailinator.com')->first();
        $response->assertSee('Password updated successfully.')->assertStatus(200);
        $this->assertFalse(Hash::check($user->password, $userData->password));
        $this->assertTrue(Hash::check($data['password_confirmation'], $user->password));
        $this->assertDatabaseMissing(
            'users',
            [
                'password' => $userData->password
            ]
        );
    }

       
    /**
     * Method testNewPasswordHttpValidation
     *
     * @return void
     */
    public function testNewPasswordHttpValidation(): void
    {
        Artisan::call('db:seed --class=AdminUserSeeder');
        $userData = User::where('email', 'backend@mailinator.com')->first();
        $url = route('admin.change-password');
        $data = [
            'current_password' => 'Test@123',
            'password' => '',
            'password_confirmation' => 'Test@1234',
        ];
        $this->actingAs($userData)->post($url, $data)->assertSessionHasErrors(
            [
                'password' => 'The New password field is required.'
            ]
        )->assertStatus(302);
        $data = [
            'current_password' => 'Test@123',
            'password' => 'Test@123',
            'password_confirmation' => 'Test@1234',
        ];
        $this->actingAs($userData)->post($url, $data)->assertSessionHasErrors(
            [
                'password' => 'New Password and Confirm Password do not match.'
            ]
        )->assertStatus(302);
    }

       
    /**
     * Method testCurrentPasswordHttpValidation
     *
     * @return void
     */
    public function testCurrentPasswordHttpValidation(): void
    {
        Artisan::call('db:seed --class=AdminUserSeeder');
        $userData = User::where('email', 'backend@mailinator.com')->first();
        $url = route('admin.change-password');
        $data = [
            'current_password' => '',
            'password' => 'Test@1234',
            'password_confirmation' => 'Test@1234',
        ];
        $this->actingAs($userData)->post($url, $data)->assertSessionHasErrors(
            [
                'current_password' => 'The Current password field is required.'
            ]
        )->assertStatus(302);
        $data = [
            'current_password' => 'Test@1235',
            'password' => 'Test@1234',
            'password_confirmation' => 'Test@1234',
        ];
        $this->actingAs($userData)->post($url, $data)->assertSessionHasErrors(
            [
                'current_password' => 'Your current password does not match.'
            ]
        )->assertStatus(302);
    }
}
