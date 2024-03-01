<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Exceptions\CustomException;
use Carbon\Carbon;

/**
 * UserRepositoryTest
 */
class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Method testGetUser
     * test create user
     * 
     * @return void
     */
    public function testCreateUser(): void
    {
        $data = [
            'name' => 'sam',
            'user_type' => 'user',
            'email' => "sam@mailinator.com",
            'status' => 'active',
            'password' => 123456
        ];
        $userRepository = new UserRepository(new User);
        $userRepository->createUser($data);
        $this->assertDatabaseHas(
            'users',
            [
                'email' => 'sam@mailinator.com',
            ]
        );
    }

    /**
     * Method testGetUser
     * get user
     * 
     * @return void
     */
    public function testGetUser(): void
    {
        $user = User::factory()->create();
        $userRepository = new UserRepository(new User);
        $userInfo = $userRepository->getUser($user->id);
        $this->assertEquals($user['email'], $userInfo->email);
        $this->assertEquals($user['name'], $userInfo->name);
    }

    /**
     * Method testUpdateUser
     * update user
     * 
     * @return void
     */
    public function testUpdateUser(): void
    {
        $user = User::factory()->create(); // Create User
        $userRepository = new UserRepository(new User);
        $userInfo = $userRepository->getUser($user->id); // get User
        $this->assertEquals($user['email'], $userInfo->email); // asert user
        $data = [
            'name' => 'Tom',
            'email' => "Tom@mailinator.com",
        ];
        $updatedUser = $userRepository->updateUser($data, $user->id); // Update user
        // Check assertion
        $this->assertEquals($data['email'], $updatedUser->email);
        $this->assertDatabaseMissing(
            'users',
            [
                'email' => $userInfo->email,
            ]
        );
    }

    /**
     * Method testGetUserList
     * get user list
     * 
     * @return void
     */
    public function testGetUserList(): void
    {
        // Create 6 user in database
        User::factory()->count(6)->create();
        $userRepository = new UserRepository(new User);
        $userList = $userRepository->getUserList([]);
        //assert return count is 6
        $this->assertEquals(count($userList), 6);
    }

    /**
     * Method testGetUserList
     * search user list
     * 
     * @return void
     */
    public function testSearchUserList(): void
    {
        // Create 6 user in database
        $data = [
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'active',
                'password' => 123456
            ],
            [
                'name' => 'samst',
                'user_type' => 'user',
                'email' => "samst@mailinator.com",
                'status' => 'active',
                'password' => 123456
            ],
            [
                'name' => 'samuals',
                'user_type' => 'user',
                'email' => "samuals@mailinator.com",
                'status' => 'active',
                'password' => 123456
            ],
            [
                'name' => 'robin',
                'user_type' => 'user',
                'email' => "robin@mailinator.com",
                'status' => 'active',
                'password' => 123456
            ]
        ];
        User::insert($data);
        $userRepository = new UserRepository(new User);

        $userList = $userRepository->getUserList(['search' => 'sam']);
        //assert return count is 6
        $this->assertEquals(count($userList), 3);
    }

    /**
     * Method insertUserData
     * insert data with user table
     * 
     * @return void
     */
    public function insertUserData()
    {
        // Create 4 user in database
        $data = [
            [
                'name' => 'sam',
                'user_type' => 'user',
                'phone_number' => '1234567893',
                'email' => "sam@mailinator.com",
                'status' => 'inactive',
                'password' => 123456
            ],
            [
                'name' => 'samst',
                'user_type' => 'user',
                'phone_number' => '1234567892',
                'email' => "samst@mailinator.com",
                'status' => 'active',
                'password' => 123456
            ],
            [
                'name' => 'samuals',
                'user_type' => 'user',
                'phone_number' => '1234567891',
                'email' => "samuals@mailinator.com",
                'status' => 'active',
                'password' => 123456
            ],
            [
                'name' => 'robin',
                'user_type' => 'user',
                'phone_number' => '1234567890',
                'email' => "robin@mailinator.com",
                'status' => 'active',
                'password' => 123456
            ]
        ];
        User::insert($data);
    }


    /**
     * Method testUserEmailFilters
     * test cases for filters with email
     * 
     * @return void
     */
    public function testUserEmailFindFilters(): void
    {
        $this->insertUserData();
        $userRepository = new UserRepository(new User);
        // filters by email
        $userList = $userRepository->getUserList(['email' => 'samuals@mailinator.com']);
        $this->assertEquals(count($userList), 1);
    }

    /**
     * Method testUserEmailNotFindFilters
     * test cases for filters with wrong email
     * 
     * @return void
     */
    public function testUserEmailNotFindFilters(): void
    {
        User::factory()->count(6)->create();
        $userRepository = new UserRepository(new User);
        // filters by email
        $userList = $userRepository->getUserList(['email' => 'samuals@mailinator.com']);
        $this->assertEquals(count($userList), 0);
    }


    /**
     * Method testUserNameFilters
     * test cases for filters with name
     * 
     * @return void
     */
    public function testUserNameFindFilters(): void
    {
        $this->insertUserData();
        $userRepository = new UserRepository(new User);
        // filters by name
        $userList = $userRepository->getUserList(['name' => 'robin']);
        //assert return count is 1
        $this->assertEquals(count($userList), 1);
    }

    /**
     * Method testUserNameNotFindFilters
     * test cases for filters with wrong name
     * 
     * @return void
     */
    public function testUserNameNotFindFilters(): void
    {
        User::factory()->count(6)->create();
        $userRepository = new UserRepository(new User);
        // filters by name
        $userList = $userRepository->getUserList(['name' => 'samuals']);
        $this->assertEquals(count($userList), 0);
    }
    /**
     * Method testUserActiveFilters
     * test cases for filters with active status
     * 
     * @return void
     */
    public function testUserActiveFilters(): void
    {
        $this->insertUserData();
        $userRepository = new UserRepository(new User);
        // filters by active status
        $userList = $userRepository->getUserList(['status' => 'active']);
        $this->assertEquals(count($userList), 3);
    }
    /**
     * Method testUserInActiveFilters
     * test cases for filters with inactive status
     * 
     * @return void
     */
    public function testUserInActiveFilters(): void
    {
        $this->insertUserData();
        $userRepository = new UserRepository(new User);
        // filters by inactive status
        $userList = $userRepository->getUserList(['status' => 'inactive']);
        $this->assertEquals(count($userList), 1);
    }



    /**
     * Method testChangeInactiveStatus
     * test cases for change status with inactive status
     * 
     * @return void
     */
    public function testChangeInactiveStatus()
    {
        $user = User::factory()->create(); // Create User
        $data = ['status' => 'inactive'];
        $userRepository = new UserRepository(new User);
        // Change user status
        $userRepository->changeStatus($data, $user->id);

        $this->assertDatabaseHas(
            'users',
            [
                'email' => $user->email,
                'status' => 'inactive'
            ]
        );
    }

    /**
     * Method testChangeAactiveStatus
     * test cases for change status with active status
     * 
     * @return void
     */
    public function testChangeAactiveStatus(): void
    {
        $user = User::factory()->create(); // Create User
        $data = ['status' => 'active'];
        $userRepository = new UserRepository(new User);
        // Change user status
        $userRepository->changeStatus($data, $user->id);

        $this->assertDatabaseHas(
            'users',
            [
                'email' => $user->email,
                'status' => 'active'
            ]
        );
    }

    /**
     * Method testUserExistOnChangeStatus
     * test caeses for user exist or not when call change status function
     * 
     * @return void
     */
    public function testUserExistOnChangeStatus(): void
    {
        $user = User::factory()->create(); // Create User
        $data = ['status' => 'active'];
        $userRepository = new UserRepository(new User);
        $id = $user->id + rand(2, 50);
        // Change user status
        $this->expectException(CustomException::class);
        $this->expectExceptionMessage('User not found.');
        $userRepository->changeStatus($data, $id);
    }

    /**
     * Method testGetUserDetail
     * test cases for user find with valid id
     * 
     * @return void
     */
    public function testGetUserDetail(): void
    {
        $user = User::factory()->create(); // Create User
        $userRepository = new UserRepository(new User);
        $userList = $userRepository->getUserDetail($user->id);
        $this->assertNotNull($userList);
    }
    /**
     * Method testUserNotFind
     * test cases for user not found with invalid id
     * 
     * @return void
     */
    public function testUserNotFind(): void
    {
        $user = User::factory()->create(); // Create User
        $userRepository = new UserRepository(new User);
        $id = $user->id + rand(2, 50);
        $userList = $userRepository->getUserDetail($id);
        $this->assertNull($userList);
    }

    /**
     * Method testDeleteUser
     * test cases for user delete
     * 
     * @return void
     */
    public function testDeleteUser(): void
    {
        $user = User::factory()->create(); // Create User
        $userRepository = new UserRepository(new User);
        $response = $userRepository->deleteUser($user);
        $this->assertTrue($response);
    }

    /**
     * Method getLatestUsers
     * test cases for show latest user list
     * 
     * @return void
     */
    public function testGetLatestUsers(): void
    {
        $this->insertUserData(); // Create User
        $userRepository = new UserRepository(new User);
        $userList = $userRepository->getLatestUsers(6);
        $users = $userRepository->getUserList([]);
        $this->assertNotEquals($userList->pluck('id'), $users->pluck('id'));
    }
    /**
     * Method testUpdatePassword
     * test cases for update password

     * @return void
     */
    public function testUpdatePassword(): void
    {
        $user = User::create(
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'active',
                'password' => 12345678
            ]
        );
        $this->actingAs($user);
        $userRepository = new UserRepository(new User);
        $response = $userRepository->updatePassword(['password' => 123456]);
        $this->assertTrue($response);
        $userData = $userRepository->getUser($user->id);
        $this->assertNotEquals(
            $user->password,
            $userData->password
        ); // Update password

    }

    /**
     * Method verifyOtp
     * test cases for verify otp
     * 
     * @return void
     */
    public function testVerifyOtp(): void
    {
        User::create(
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'active',
                'otp' => '1234',
                'otp_expires_at' => Carbon::now()->addMinutes(config("constants.otp.max_time")),
                'password' => 12345678
            ]
        );
        $otpData = [
            'otp' => '1234',
            'type' => 'otp-verification',
            'email' => 'sam@mailinator.com',
        ];
        $userRepository = new UserRepository(new User);
        $user = $userRepository->verifyOtp($otpData);
        $this->assertEquals('sam@mailinator.com', $user->email);
    }
    /**
     * Method testVarify
     * test cases for registration verify otp
     * 
     * @return void
     */
    public function testVarifyForRegistration(): void
    {
        User::create(
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'active',
                'otp' => '1234',
                'otp_expires_at' => Carbon::now()->addMinutes(config("constants.otp.max_time")),
                'password' => 12345678
            ]
        );
        $otpData = [
            'otp' => '1234',
            'type' => 'registration',
            'email' => 'sam@mailinator.com',
        ];
        $userRepository = new UserRepository(new User);
        $user = $userRepository->verifyOtp($otpData);
        $this->assertEquals('sam@mailinator.com', $user->email);
    }
    /**
     * Method testVerifyOtpForResetPassword
     * test cases for reset password verify otp
     * 
     * @return void
     */
    public function testVerifyOtpForResetPassword()
    {
        User::create(
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'active',
                'otp' => '1234',
                'otp_expires_at' => Carbon::now()->addMinutes(config("constants.otp.max_time")),
                'password' => Hash::make(12345678)
            ]
        );
        $otpData = [
            'otp' => '1234',
            'type' => 'reset-password',
            'email' => 'sam@mailinator.com',
            'password' => Hash::make(123456)
        ];
        $userRepository = new UserRepository(new User);
        $user = $userRepository->verifyOtp($otpData);
        $this->assertEquals('sam@mailinator.com', $user->email);
    }
    /**
     * Method testSendOtp
     * send otp
     * 
     * @return void
     */
    public function testSendOtp(): void
    {
        $user = User::create(
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'active',
                'otp' => '1234',
                'otp_expires_at' => Carbon::now()->addMinutes(config("constants.otp.max_time")),
                'password' => 12345678
            ]
        );
        $userRepository = new UserRepository(new User);
        $status = $userRepository->sendOtp($user);
        $this->assertTrue($status);
    }
    /**
     * Method testCheckLogin
     * test check login with email
     * 
     * @return void
     */
    public function testCheckLogin(): void
    {
        User::create(
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'active',
                'otp' => '1234',
                'otp_expires_at' => Carbon::now()->addMinutes(config("constants.otp.max_time")),
                'password' => 12345678,
                'email_verified_at' => Carbon::now()
            ]
        );
        $data = [
            'email' => "sam@mailinator.com",
        ];
        $userRepository = new UserRepository(new User);
        $status = $userRepository->checkLogin($data);
        $this->assertNotNull($status);
    }
    /**
     * Method testInvalidLogin
     * test login with invalid email
     * 
     * @return void
     */
    public function testInvalidLogin(): void
    {
        User::create(
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'active',
                'otp' => '1234',
                'otp_expires_at' => Carbon::now()->addMinutes(config("constants.otp.max_time")),
                'password' => 12345678
            ]
        );
        $data = [
            'email' => "invalid@mailinator.com",
        ];
        $userRepository = new UserRepository(new User);
        $this->expectException(CustomException::class);
        $this->expectExceptionMessage('The email or password entered is incorrect.');
        $userRepository->checkLogin($data);
    }
    /**
     * Method testUserAccountVarified
     * test cases for user account not varified with check login functions
     * 
     * @return void
     */
    public function testUserAccountNotVarified(): void
    {
        User::create(
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'active',
                'otp' => '1234',
                'otp_expires_at' => Carbon::now()->addMinutes(
                    config("constants.otp.max_time")
                ),
                'password' => 12345678,
                'email_verified_at' => null
            ]
        );

        $data = [
            'email' => "sam@mailinator.com",
        ];
        $userRepository = new UserRepository(new User);
        if (config('constants.verification_required')) {
            $this->expectException(CustomException::class);
            $this->expectExceptionMessage('Your account is not verified');
            $userRepository->checkLogin($data);
        } else {
            $status = $userRepository->checkLogin($data);
            $this->assertNotNull($status);
        }
    }
    /**
     * Method testInactiveUserLogin
     * login with inactive user
     * 
     * @return void
     */
    public function testInactiveUserLogin(): void
    {
        User::create(
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'inactive',
                'otp' => '1234',
                'otp_expires_at' => Carbon::now()->addMinutes(config("constants.otp.max_time")),
                'password' => 12345678,
                'email_verified_at' => Carbon::now()
            ]
        );
        
        $data = [
            'email' => "sam@mailinator.com",
        ];
        $userRepository = new UserRepository(new User);
        $this->expectException(CustomException::class);
        $this->expectExceptionMessage('Your account is inactive');
        $userRepository->checkLogin($data);
        
    }
}
