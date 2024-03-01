<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * AuthControllerApiTest
 */
class AuthControllerApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Method testValidatePasswordForRegistrationApi
     *
     * @return void
     */
    public function testValidatePasswordForRegistrationApi()
    {
        /* validate confirm password required */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The confirm password field is required."
                    ]
                ]
            );

        /* validate password and confirm password not match */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@123",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);
        
        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The confirm password and password must match."
                    ]
                ]
            );

        /* validate password required */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "",
            "confirm_password" => "",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The confirm password field is required."
                    ]
                ]
            );
    }

    /**
     * Method testValidatePasswordRulesForRegistrationApi
     *
     * @return void
     */
    public function testValidatePasswordRulesForRegistrationApi()
    {
        /* validate password rule */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "test@test",
            "confirm_password" => "test@test",
            "device_id" => "AND123",
            "device_type" => "android",
        ];

        $this->json('POST', 'api/v1/register', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Password should be 8-15 characters & include 1 uppercase, a lowercase, a special character & a number."
                    ]
                ]
            );

        /* validate password rule */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@test",
            "confirm_password" => "Test@test",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        
        $this->json('POST', 'api/v1/register', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Password should be 8-15 characters & include 1 uppercase, a lowercase, a special character & a number."
                    ]
                ]
            );

        /* validate password rule */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test123",
            "confirm_password" => "Test123",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        
        $this->json('POST', 'api/v1/register', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Password should be 8-15 characters & include 1 uppercase, a lowercase, a special character & a number."
                    ]
                ]
            );

        /* validate password space */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@ 123",
            "confirm_password" => "Test@ 123",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        
        $this->json('POST', 'api/v1/register', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Space is not allowed in password."
                    ]
                ]
            );

        /* validate password length */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12",
            "confirm_password" => "Test@12",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        
        $this->json('POST', 'api/v1/register', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Password should be 8-15 characters & include 1 uppercase, a lowercase, a special character & a number."
                    ]
                ]
            );

        /* validate password length */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Testing@123456789",
            "confirm_password" => "Testing@123456789",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        
        $this->json('POST', 'api/v1/register', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Password should be 8-15 characters & include 1 uppercase, a lowercase, a special character & a number."
                    ]
                ]
            );
    }

    /**
     * Method testValidatePhoneNumberForRegistrationApi
     *
     * @return void
     */
    public function testValidatePhoneNumberForRegistrationApi()
    {
        /* validate phone number required */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];

        $response = $this->json('POST', 'api/v1/register', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The phone number field is required."
                    ]
                ]
            );

        /* validate phone number for digit */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "test",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);
        
        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The phone number must be between 6 and 14 digits."
                    ]
                ]
            );

        /* validate phone number for digit length */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "45678",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);
        
        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The phone number must be between 6 and 14 digits."
                    ]
                ]
            );
    }

    /**
     * Method testValidatePhoneCodeForRegistrationApi
     *
     * @return void
     */
    public function testValidatePhoneCodeForRegistrationApi()
    {
        /* validate phone code required */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];

        $response = $this->json('POST', 'api/v1/register', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The phone code field is required."
                    ]
                ]
            );

        /* validate phone code for digit */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "te",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);
        
        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The phone code must be between 1 and 3 digits."
                    ]
                ]
            );

        /* validate phone code for digit length */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "9164",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);
        
        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The phone code must be between 1 and 3 digits."
                    ]
                ]
            );
    }

    /**
     * Method testValidateEmailForRegistrationApi
     *
     * @return void
     */
    public function testValidateEmailForRegistrationApi()
    {
        /* validate email required */
        $userData = [
            "name" => "John Doe",
            "email" => "",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];

        $response = $this->json('POST', 'api/v1/register', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email field is required."
                    ]
                ]
            );

        /* validate email for formate */
        $userData = [
            "name" => "John Doe",
            "email" => "doe@com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);
        
        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email must be a valid email address."
                    ]
                ]
            );

        /* validate email for unique */
        User::create(
            [
                "name" => "John Doe",
                "email" => "doe@example.com",
                "phone_code" => "91",
                "phone_number" => "7896542",
                "password" => "Test@12345",
                "user_type" => User::TYPE_USER,
            ]
        );

        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);
        
        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email has already been taken."
                    ]
                ]
            );
    }

    /**
     * Method testValidateNameForRegistrationApi
     *
     * @return void
     */
    public function testValidateNameForRegistrationApi()
    {
        /* validate name required */
        $userData = [
            "name" => "",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];

        $response = $this->json('POST', 'api/v1/register', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The name field is required."
                    ]
                ]
            );

        /* validate name for min length */
        $userData = [
            "name" => "J",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);
        
        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The name must be at least 2 characters."
                    ]
                ]
            );

        /* validate name for max length */
        $userData = [
            "name" => "John doe John doe John doe John doe John doe John doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];
        $response = $this->json('POST', 'api/v1/register', $userData);
        
        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The name must not be greater than 50 characters."
                    ]
                ]
            );
    }

    /**
     * Method testValidateDeviceIdForRegistrationApi
     *
     * @return void
     */
    public function testValidateDeviceIdForRegistrationApi()
    {
        /* validate device id is required */
        $userData = [
            "name" => "John doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "",
            "device_type" => "android",
        ];

        $response = $this->json('POST', 'api/v1/register', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device id field is required."
                    ]
                ]
            );

        /* validate device id for min length */
        $userData1 = [
            "name" => "John doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "A",
            "device_type" => "android",
        ];
        $response1 = $this->json('POST', 'api/v1/register', $userData1);
        
        $response1->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device id must be at least 2 characters."
                    ]
                ]
            );
        
        $deviceId = "crxVPkD4Z3ACgnVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFGcrxVPkD4Z3ACgnVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFG$2y$10crxVPkD4Z3ACg.nVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFG$2y$10crxVPkD4Z3ACgnVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFG$2y$10crxVPkD4Z3ACsd34v4vc43445gvdgf4545v";

        /* validate device id for max length */
        $userData2 = [
            "name" => "John doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => $deviceId,
            "device_type" => "android",
        ];
        $response2 = $this->json('POST', 'api/v1/register', $userData2);
        
        $response2->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device id must not be greater than 255 characters."
                    ]
                ]
            );
    }

    /**
     * Method testValidateDeviceTypeForRegistrationApi
     *
     * @return void
     */
    public function testValidateDeviceTypeForRegistrationApi()
    {
        /* validate device type is required */
        $userData = [
            "name" => "John doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND4585",
            "device_type" => "",
        ];

        $response = $this->json('POST', 'api/v1/register', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device type field is required."
                    ]
                ]
            );

        /* validate device type for specific type */
        $userData1 = [
            "name" => "John doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND4585",
            "device_type" => "web",
        ];
        $response1 = $this->json('POST', 'api/v1/register', $userData1);
        
        $response1->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The selected device type is invalid."
                    ]
                ]
            );
    }

    /**
     * Method testSuccessfulRegistrationApi
     *
     * @return void
     */
    public function testSuccessfulRegistrationApi()
    {
        $doVerify = config('constants.verification_required');

        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "phone_code" => "91",
            "phone_number" => "7896542",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345",
            "device_id" => "AND123",
            "device_type" => "android",
        ];

        $response = $this->json('POST', 'api/v1/register', $userData)
            ->assertSuccessful();
        
        if ($doVerify) {
            $response->assertJsonStructure(
                [
                    "data" => 
                        [
                            "id",
                            "name",
                            "email",
                            "user_type",
                            "phone_code",
                            "phone_number",
                            "profile_image",
                            "is_profile_completed",
                            "status"
                        ]
                ]
            );
        } else {
            $response->assertJsonStructure(
                [
                    "data" => 
                        [
                            "id",
                            "name",
                            "email",
                            "user_type",
                            "phone_code",
                            "phone_number",
                            "profile_image",
                            "is_profile_completed",
                            "status",
                            "is_verified",
                            "token"
                        ]
                ]
            );
        }

    }

    /**
     * Method testValidateOtpForVerifyOtpApi
     *
     * @return void
     */
    public function testValidateOtpForVerifyOtpApi()
    {
        /* validate otp is required */
        $userData = [
            "otp" => "",
            "email" => "doe@example.com",
            "device_id" => "AND123",
            "device_type" => "android",
            "type" => "registration"
        ];

        $response = $this->json('POST', 'api/v1/verify-otp', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The otp field is required."
                    ]
                ]
            );


        /* validate otp for integer */
        $userData = [
            "otp" => "as54",
            "email" => "doe@example.com",
            "device_id" => "AND123",
            "device_type" => "android",
            "type" => "registration"
        ];

        $response = $this->json('POST', 'api/v1/verify-otp', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The otp must be an integer.,The otp must be ".config('constants.otp.otp_length')." digits."
                    ]
                ]
            );

        /* validate otp for length */
        $userData = [
            "otp" => "16545",
            "email" => "doe@example.com",
            "device_id" => "AND123",
            "device_type" => "android",
            "type" => "registration"
        ];

        $response = $this->json('POST', 'api/v1/verify-otp', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The otp must be ".config('constants.otp.otp_length')." digits."
                    ]
                ]
            );
    }

    /**
     * Method testValidateEmailForVerifyOtpApi
     *
     * @return void
     */
    public function testValidateEmailForVerifyOtpApi()
    {
        /* validate email required */
        $userData = [
            "otp" => "1685",
            "email" => "",
            "device_id" => "AND123",
            "device_type" => "android",
            "type" => "registration"
        ];

        $response = $this->json('POST', 'api/v1/verify-otp', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email field is required."
                    ]
                ]
            );

        /* validate email for formate */
        $userData1 = [
            "otp" => "1685",
            "email" => "doe@com",
            "device_id" => "AND123",
            "device_type" => "android",
            "type" => "registration"
        ];
        $response1 = $this->json('POST', 'api/v1/verify-otp', $userData1);
        
        $response1->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email must be a valid email address."
                    ]
                ]
            );
    }

    /**
     * Method testValidateDeviceIdForVerifyOtpApi
     *
     * @return void
     */
    public function testValidateDeviceIdForVerifyOtpApi()
    {
        /* validate device id is required */
        $userData = [
            "otp" => "1685",
            "email" => "doe@example.com",
            "device_id" => "",
            "device_type" => "android",
            "type" => "registration"
        ];

        $response = $this->json('POST', 'api/v1/verify-otp', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device id field is required."
                    ]
                ]
            );

        /* validate device id for min length */
        $userData1 = [
            "otp" => "1685",
            "email" => "doe@example.com",
            "device_id" => "A",
            "device_type" => "android",
            "type" => "registration"
        ];
        $response1 = $this->json('POST', 'api/v1/verify-otp', $userData1);
        
        $response1->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device id must be at least 2 characters."
                    ]
                ]
            );
        
        $deviceId = "crxVPkD4Z3ACgnVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFGcrxVPkD4Z3ACgnVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFG$2y$10crxVPkD4Z3ACg.nVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFG$2y$10crxVPkD4Z3ACgnVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFG$2y$10crxVPkD4Z3ACsd34v4vc43445gvdgf4545v";

        /* validate device id for max length */
        $userData2 = [
            "otp" => "1685",
            "email" => "doe@example.com",
            "device_id" => $deviceId,
            "device_type" => "android",
            "type" => "registration"
        ];
        $response2 = $this->json('POST', 'api/v1/verify-otp', $userData2);
        
        $response2->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device id must not be greater than 255 characters."
                    ]
                ]
            );
    }

    /**
     * Method testValidateDeviceTypeForVerifyOtpApi
     *
     * @return void
     */
    public function testValidateDeviceTypeForVerifyOtpApi()
    {
        /* validate device type is required */
        $userData = [
            "otp" => "1685",
            "email" => "doe@example.com",
            "device_id" => "Asdf45623",
            "device_type" => "",
            "type" => "registration"
        ];

        $response = $this->json('POST', 'api/v1/verify-otp', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device type field is required."
                    ]
                ]
            );

        /* validate device type for specific type */
        $userData1 = [
            "otp" => "1685",
            "email" => "doe@example.com",
            "device_id" => "Asdf45623",
            "device_type" => "web",
            "type" => "registration"
        ];
        $response1 = $this->json('POST', 'api/v1/verify-otp', $userData1);
        
        $response1->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The selected device type is invalid."
                    ]
                ]
            );
    }

    /**
     * Method testVerifyOtpRegistrationApi
     *
     * @return void
     */
    public function testVerifyOtpRegistrationApi()
    {
        $doVerify = config('constants.verification_required');
        
        if ($doVerify) {
            /* create user with OTP */
            User::create(
                [
                    "name" => "John Doe",
                    "email" => "doe@example.com",
                    "phone_code" => "91",
                    "phone_number" => "7896542",
                    "password" => "Test@12345",
                    "device_id" => "AND123",
                    "device_type" => "android",
                    "user_type" => User::TYPE_USER,
                    "status" => User::STATUS_INACTIVE,
                    "otp" => "1685",
                    "otp_expires_at" => Carbon::now()->addMinutes(config('constants.otp.max_time'))
                ]
            );

            /* call verify otp api */
            $userData = [
                "otp" => "1685",
                "email" => "doe@example.com",
                "device_id" => "AND123",
                "device_type" => "android",
                "type" => "registration"
            ];
    
            $response = $this->json('POST', 'api/v1/verify-otp', $userData)
                ->assertSuccessful();
            
            $response->assertJsonStructure(
                [
                    "data" => 
                        [
                            "id",
                            "name",
                            "email",
                            "user_type",
                            "phone_code",
                            "phone_number",
                            "profile_image",
                            "is_profile_completed",
                            "status"
                        ]
                ]
            );
        } else {
            $this->assertFalse($doVerify);
        }

    }

    /**
     * Method testValidateEmailPasswordForLoginApi
     *
     * @return void
     */
    public function testValidateEmailPasswordForLoginApi()
    {
        /* validate email required */
        $userData = [
            "email" => "",
            "password" => "Test@12345",
            "device_id" => "ASD456",
            "device_type" => "ios"
        ];

        $response = $this->json('POST', 'api/v1/login', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email field is required."
                    ]
                ]
            );

        /* validate email for formate */
        $userData1 = [
            "email" => "Test@vcom",
            "password" => "Test@12345",
            "device_id" => "ASD456",
            "device_type" => "ios"
        ];
        $response1 = $this->json('POST', 'api/v1/login', $userData1);
        
        $response1->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email must be a valid email address."
                    ]
                ]
            );

        /* validate password required */
        $userData = [
            "email" => "johndoe@gmail.com",
            "password" => "",
            "device_id" => "ASD456",
            "device_type" => "ios"
        ];

        $response = $this->json('POST', 'api/v1/login', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The password field is required."
                    ]
                ]
            );
    }

    /**
     * Method testValidateDeviceIdForLoginApi
     *
     * @return void
     */
    public function testValidateDeviceIdForLoginApi()
    {
        /* validate device id is required */
        $userData = [
            "email" => "johndoe@example.com",
            "password" => "Test@123",
            "device_id" => "",
            "device_type" => "android"
        ];

        $response = $this->json('POST', 'api/v1/login', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device id field is required."
                    ]
                ]
            );

        /* validate device id for min length */
        $userData1 = [
            "email" => "johndoe@example.com",
            "password" => "Test@123",
            "device_id" => "A",
            "device_type" => "android"
        ];
        $response1 = $this->json('POST', 'api/v1/login', $userData1);
        
        $response1->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device id must be at least 2 characters."
                    ]
                ]
            );
        
        $deviceId = "crxVPkD4Z3ACgnVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFGcrxVPkD4Z3ACgnVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFG$2y$10crxVPkD4Z3ACg.nVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFG$2y$10crxVPkD4Z3ACgnVXdPAevOv1mBJWTPVQk6ua3u6QWfnrG4CcZFG$2y$10crxVPkD4Z3ACsd34v4vc43445gvdgf4545v";

        /* validate device id for max length */
        $userData2 = [
            "email" => "johndoe@example.com",
            "password" => "Test@123",
            "device_id" => $deviceId,
            "device_type" => "android"
        ];
        $response2 = $this->json('POST', 'api/v1/login', $userData2);
        
        $response2->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device id must not be greater than 255 characters."
                    ]
                ]
            );
    }

    /**
     * Method testValidateDeviceTypeForLoginApi
     *
     * @return void
     */
    public function testValidateDeviceTypeForLoginApi()
    {
        /* validate device type is required */
        $userData = [
            "email" => "johndoe@example.com",
            "password" => "Test@123",
            "device_id" => "Asdf45623",
            "device_type" => ""
        ];

        $response = $this->json('POST', 'api/v1/login', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The device type field is required."
                    ]
                ]
            );

        /* validate device type for specific type */
        $userData1 = [
            "email" => "johndoe@example.com",
            "password" => "Test@123",
            "device_id" => "Asdf45623",
            "device_type" => "web"
        ];
        $response1 = $this->json('POST', 'api/v1/login', $userData1);
        
        $response1->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The selected device type is invalid."
                    ]
                ]
            );
    }

    /**
     * Method testLoginRequestApi
     *
     * @return void
     */
    public function testLoginRequestApi()
    {
        /* create user for login */
        User::create(
            [
                "name" => "John Doe",
                "email" => "johndoe@example.com",
                "phone_code" => "91",
                "phone_number" => "7896542",
                "password" => "Test@12345",
                "device_id" => "AND123",
                "device_type" => "android",
                "user_type" => User::TYPE_USER,
                "status" => User::STATUS_ACTIVE,
                "email_verified_at" => Carbon::now()
            ]
        );

        /* login details */
        $userData = [
            "email" => "johndoe@example.com",
            "password" => "Test@12345",
            "device_id" => "ASD456",
            "device_type" => "ios"
        ];

        $response = $this->json('POST', 'api/v1/login', $userData)
            ->assertSuccessful();

        $response->assertJsonStructure(
            [
                "data" => 
                    [
                        "id",
                        "name",
                        "email",
                        "user_type",
                        "phone_code",
                        "phone_number",
                        "profile_image",
                        "is_profile_completed",
                        "status",
                        "is_verified",
                        "token"
                    ]
            ]
        );
    }

    /**
     * Method testLoginRequestApiWithInvalidDetail
     *
     * @return void
     */
    public function testLoginRequestApiWithInvalidDetail()
    {
        /* create user for login */
        User::create(
            [
                "name" => "John Doe",
                "email" => "johndoe@example.com",
                "phone_code" => "91",
                "phone_number" => "7896542",
                "password" => "Test@12345",
                "device_id" => "AND123",
                "device_type" => "android",
                "user_type" => User::TYPE_USER,
                "status" => User::STATUS_ACTIVE
            ]
        );

        /* with wrong email */
        $userData = [
            "email" => "john@example.com",
            "password" => "Test@12345",
            "device_id" => "ASD456",
            "device_type" => "ios"
        ];

        $this->json('POST', 'api/v1/login', $userData)
            ->assertStatus(400)
            ->assertJson(
                [
                    "success" => false,
                    "message" => "The email or password entered is incorrect."
                ]
            );


        /* with wrong password */
        $userData1 = [
            "email" => "johndoe@example.com",
            "password" => "Test@123",
            "device_id" => "ASD456",
            "device_type" => "ios"
        ];

        $this->json('POST', 'api/v1/login', $userData1)
            ->assertStatus(400)
            ->assertJson(
                [
                    "success" => false,
                    "message" => "The email or password entered is incorrect."
                ]
            );

        /* with wrong email and password */
        $userData2 = [
            "email" => "john@example.com",
            "password" => "Test@123",
            "device_id" => "ASD456",
            "device_type" => "ios"
        ];

        $this->json('POST', 'api/v1/login', $userData2)
            ->assertStatus(400)
            ->assertJson(
                [
                    "success" => false,
                    "message" => "The email or password entered is incorrect."
                ]
            );
    }

    /**
     * Method testValidateEmailForSendOtpApi
     *
     * @return void
     */
    public function testValidateEmailForSendOtpApi()
    {
        /* validate email required */
        $userData = [
            "email" => "",
            "type" => "registration",
        ];

        $this->json('POST', 'api/v1/send-otp', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email field is required."
                    ]
                ]
            );

        /* validate email for formate */
        $userData1 = [
            "email" => "john@gmail",
            "type" => "registration",
        ];

        $this->json('POST', 'api/v1/send-otp', $userData1)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email must be a valid email address."
                    ]
                ]
            );

        /* validate user email is exist */
        User::create(
            [
                "name" => "John Doe",
                "email" => "doe@example.com",
                "phone_code" => "91",
                "phone_number" => "7896542",
                "password" => "Test@12345",
                "user_type" => User::TYPE_USER,
            ]
        );

        $userData2 = [
            "email" => "johndoe@example.com",
            "type" => "registration",
        ];

        $this->json('POST', 'api/v1/send-otp', $userData2)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The selected email is invalid."
                    ]
                ]
            );
    }

    /**
     * Method testValidateTypeForSendOtpApi
     *
     * @return void
     */
    public function testValidateTypeForSendOtpApi()
    {
        /* validate type required */
        $userData = [
            "email" => "doe@example.com",
            "type" => "",
        ];

        $this->json('POST', 'api/v1/send-otp', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The type field is required."
                    ]
                ]
            );

        /* validate type specific values */
        $userData1 = [
            "email" => "doe@example.com",
            "type" => "test",
        ];

        $this->json('POST', 'api/v1/send-otp', $userData1)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The selected type is invalid."
                    ]
                ]
            );
    }
        
    /**
     * Method testSendOtpRequestApi
     *
     * @return void
     */
    public function testSendOtpRequestApi()
    {
        /* create user */
        User::create(
            [
                "name" => "John Doe",
                "email" => "johndoe@example.com",
                "phone_code" => "91",
                "phone_number" => "7896542",
                "password" => "Test@12345",
                "device_id" => "AND123",
                "device_type" => "android",
                "user_type" => User::TYPE_USER,
                "status" => User::STATUS_INACTIVE
            ]
        );

        /* send otp details */
        $userData = [
            "email" => "johndoe@example.com",
            "type" => "registration"
        ];

        $this->json('POST', 'api/v1/send-otp', $userData)
            ->assertStatus(200)
            ->assertJson(
                [
                    "success" => true,
                    "message" => "OTP send successfully"
                ]
            );
    }

    /**
     * Method testValidateEmailForResetPasswordApi
     *
     * @return void
     */
    public function testValidateEmailForResetPasswordApi()
    {
        /* validate email required */
        $userData = [
            "email" => "",
            'otp' => '2354',
            'password' => 'Test@12345',
            'confirm_password' => 'Test@12345',
        ];

        $this->json('POST', 'api/v1/reset-password', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email field is required."
                    ]
                ]
            );

        /* validate email for formate */
        $userData1 = [
            "email" => "john@com",
            'otp' => '2354',
            'password' => 'Test@12345',
            'confirm_password' => 'Test@12345',
        ];

        $this->json('POST', 'api/v1/reset-password', $userData1)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The email must be a valid email address."
                    ]
                ]
            );

        /* validate user email is exist */
        User::create(
            [
                "name" => "John Doe",
                "email" => "doe@example.com",
                "phone_code" => "91",
                "phone_number" => "7896542",
                "password" => "Test@12345",
                "user_type" => User::TYPE_USER,
            ]
        );

        $userData2 = [
            "email" => "johndoe@example.com",
            'otp' => '2354',
            'password' => 'Test@12345',
            'confirm_password' => 'Test@12345',
        ];

        $this->json('POST', 'api/v1/reset-password', $userData2)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The selected email is invalid."
                    ]
                ]
            );
    }

    /**
     * Method testValidatePasswordForResetPasswordApi
     *
     * @return void
     */
    public function testValidatePasswordForResetPasswordApi()
    {
        /* validate confirm password required */
        $userData = [
            "email" => "doe@example.com",
            "otp" => "9156",
            "password" => "Test@12345"
        ];
        $response = $this->json('POST', 'api/v1/reset-password', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The confirm password field is required."
                    ]
                ]
            );

        /* validate password and confirm password not match */
        $userData = [
            "email" => "doe@example.com",
            "otp" => "9156",
            "password" => "Test@12345",
            "confirm_password" => "Test@123"
        ];
        $response = $this->json('POST', 'api/v1/reset-password', $userData);
        
        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The confirm password and password must match."
                    ]
                ]
            );

        /* validate password required */
        $userData = [
            "email" => "doe@example.com",
            "otp" => "9156",
            "password" => "",
            "confirm_password" => ""
        ];
        $response = $this->json('POST', 'api/v1/reset-password', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The confirm password field is required."
                    ]
                ]
            );
    }

    /**
     * Method testValidatePasswordRulesForResetPasswordApi
     *
     * @return void
     */
    public function testValidatePasswordRulesForResetPasswordApi()
    {
        /* validate password rule */
        $userData = [
            "email" => "doe@example.com",
            "otp" => "9156",
            "password" => "test@test",
            "confirm_password" => "test@test"
        ];

        $this->json('POST', 'api/v1/reset-password', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Password should be 8-15 characters & include 1 uppercase, a lowercase, a special character & a number."
                    ]
                ]
            );

        /* validate password rule */
        $userData = [
            "email" => "doe@example.com",
            "otp" => "9156",
            "password" => "Test@test",
            "confirm_password" => "Test@test"
        ];
        
        $this->json('POST', 'api/v1/reset-password', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Password should be 8-15 characters & include 1 uppercase, a lowercase, a special character & a number."
                    ]
                ]
            );

        /* validate password rule */
        $userData = [
            "email" => "doe@example.com",
            "otp" => "9156",
            "password" => "Test123",
            "confirm_password" => "Test123"
        ];
        
        $this->json('POST', 'api/v1/reset-password', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Password should be 8-15 characters & include 1 uppercase, a lowercase, a special character & a number."
                    ]
                ]
            );

        /* validate password space */
        $userData = [
            "email" => "doe@example.com",
            "otp" => "9156",
            "password" => "Test@ 123",
            "confirm_password" => "Test@ 123"
        ];
        
        $this->json('POST', 'api/v1/reset-password', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Space is not allowed in password."
                    ]
                ]
            );

        /* validate password length */
        $userData = [
            "email" => "doe@example.com",
            "otp" => "9156",
            "password" => "Test@12",
            "confirm_password" => "Test@12",
        ];
        
        $this->json('POST', 'api/v1/reset-password', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Password should be 8-15 characters & include 1 uppercase, a lowercase, a special character & a number."
                    ]
                ]
            );

        /* validate password length */
        $userData = [
            "email" => "doe@example.com",
            "otp" => "9156",
            "password" => "Testing@123456789",
            "confirm_password" => "Testing@123456789"
        ];
        
        $this->json('POST', 'api/v1/reset-password', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "Password should be 8-15 characters & include 1 uppercase, a lowercase, a special character & a number."
                    ]
                ]
            );
    }

    /**
     * Method testValidateOtpForResetPasswordApi
     *
     * @return void
     */
    public function testValidateOtpForResetPasswordApi()
    {
        /* validate otp is required */
        $userData = [
            "otp" => "",
            "email" => "doe@example.com",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345"
        ];

        $response = $this->json('POST', 'api/v1/reset-password', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The otp field is required."
                    ]
                ]
            );


        /* validate otp for integer */
        $userData = [
            "otp" => "as54",
            "email" => "doe@example.com",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345"
        ];

        $response = $this->json('POST', 'api/v1/reset-password', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The otp must be an integer.,The otp must be ".config('constants.otp.otp_length')." digits."
                    ]
                ]
            );

        /* validate otp for length */
        $userData = [
            "otp" => "16545",
            "email" => "doe@example.com",
            "password" => "Test@12345",
            "confirm_password" => "Test@12345"
        ];

        $response = $this->json('POST', 'api/v1/reset-password', $userData);

        $response->assertStatus(422)
            ->assertJson(
                [
                    "success" => false,
                    "error" => [
                        "message" => "The otp must be ".config('constants.otp.otp_length')." digits."
                    ]
                ]
            );
    }

    /**
     * Method testResetPasswordRequestApi
     *
     * @return void
     */
    public function testResetPasswordRequestApi()
    {
        /* create user with OTP */
        User::create(
            [
                "name" => "John Doe",
                "email" => "doe@example.com",
                "phone_code" => "91",
                "phone_number" => "7896542",
                "password" => "Test@12345",
                "device_id" => "AND123",
                "device_type" => "android",
                "user_type" => User::TYPE_USER,
                "status" => User::STATUS_ACTIVE,
                "email_verified_at" => Carbon::now(),
                "otp" => "1685",
                "otp_expires_at" => Carbon::now()->addMinutes(config('constants.otp.max_time'))
            ]
        );

        /* call reset password api */
        $userData = [
            "otp" => "1685",
            "email" => "doe@example.com",
            "password" => "Test@6789",
            "confirm_password" => "Test@6789",
        ];

        $this->json('POST', 'api/v1/reset-password', $userData)
            ->assertStatus(200)
            ->assertJson(
                [
                    "success" => true,
                    "message" => "Password has been successfully updated."
                ]
            );
        
        /* check login with updated password */
        $userData = [
            "email" => "doe@example.com",
            "password" => "Test@6789",
            "device_id" => "ASD456",
            "device_type" => "ios"
        ];
        $this->json('POST', 'api/v1/login', $userData)
            ->assertSuccessful();

        /* check login with old password */
        $userData = [
            "email" => "doe@example.com",
            "password" => "Test@12345",
            "device_id" => "ASD456",
            "device_type" => "ios"
        ];
        $this->json('POST', 'api/v1/login', $userData)
            ->assertStatus(400);
    }

    /**
     * Method testResetPasswordRequestApiWithWrongOtp
     *
     * @return void
     */
    public function testResetPasswordRequestApiWithWrongOtp()
    {
        /* create user with OTP */
        User::create(
            [
                "name" => "John Doe",
                "email" => "doe@example.com",
                "phone_code" => "91",
                "phone_number" => "7896542",
                "password" => "Test@12345",
                "device_id" => "AND123",
                "device_type" => "android",
                "user_type" => User::TYPE_USER,
                "status" => User::STATUS_ACTIVE,
                "otp" => "1685",
                "otp_expires_at" => Carbon::now()->addMinutes(config('constants.otp.max_time'))
            ]
        );

        /* call reset password api */
        $userData = [
            "otp" => "4568",
            "email" => "doe@example.com",
            "password" => "Test@6789",
            "confirm_password" => "Test@6789",
        ];

        $this->json('POST', 'api/v1/reset-password', $userData)
            ->assertStatus(400)
            ->assertJson(
                [
                    "success" => false,
                    "message" => "Invalid verification code"
                ]
            );
    }

    /**
     * Method testResetPasswordRequestApiWithExpiredOtp
     *
     * @return void
     */
    public function testResetPasswordRequestApiWithExpiredOtp()
    {
        /* create user with OTP */
        User::create(
            [
                "name" => "John Doe",
                "email" => "doe@example.com",
                "phone_code" => "91",
                "phone_number" => "7896542",
                "password" => "Test@12345",
                "device_id" => "AND123",
                "device_type" => "android",
                "user_type" => User::TYPE_USER,
                "status" => User::STATUS_ACTIVE,
                "otp" => "1685",
                "otp_expires_at" => Carbon::now()
            ]
        );

        /* call reset password api */
        $userData = [
            "otp" => "1685",
            "email" => "doe@example.com",
            "password" => "Test@6789",
            "confirm_password" => "Test@6789",
        ];

        $this->json('POST', 'api/v1/reset-password', $userData)
            ->assertStatus(422)
            ->assertJson(
                [
                    "message" => "OTP has been expired"
                ]
            );
    }

    /**
     * Method testLogoutRequestApi
     *
     * @return void
     */
    public function testLogoutRequestApi()
    {
        $testUser = User::create(
            [
                "name" => "John Doe",
                "email" => "doe@example.com",
                "phone_code" => "91",
                "phone_number" => "7896542",
                "password" => "Test@12345",
                "device_id" => "AND123",
                "device_type" => "android",
                "user_type" => User::TYPE_USER,
                "status" => User::STATUS_ACTIVE,
            ]
        );

        $token = $testUser->createToken('accessToken')->plainTextToken;

        $testResponse = $this->withHeader('Authorization', "Bearer $token")
            ->json('GET', 'api/v1/logout');

        $testResponse->assertOk()
            ->assertJson(
                [
                    'message' => 'You have been logged out successfully.'
                ]
            );

        self::assertCount(0, $testUser->tokens);

    }

}
