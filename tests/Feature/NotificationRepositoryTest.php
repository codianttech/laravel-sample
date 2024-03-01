<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * NotificationRepositoryTest
 */
class NotificationRepositoryTest extends TestCase
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
            'name' => 'sam',
            'user_type' => 'user',
            'email' => "sam@mailinator.com",
            'status' => 'active',
            'password' => Hash::make(123456)
        ];
        $userRepository = new UserRepository(new User);
        return $userRepository->createUser($data);        // Create 1 user in database
    }

    /**
     * Method insertNotificationData
     *
     * @return void
     */
    public function insertNotificationData($user)
    {
        $data = [
            [
                'user_id' => $user->id,
                'title' => 'nofification',
                'message' => "first nofification send",
                'is_readed' => 0,
                'status' => 0
            ],
            [
                'user_id' => $user->id,
                'title' => 'nofification',
                'message' => "second nofification send two",
                'is_readed' => 1,
                'status' => 0
            ],
            [
                'user_id' => $user->id,
                'title' => 'nofification',
                'message' => "third nofification send",
                'is_readed' => 0,
                'status' => 0
            ],

        ];
        return Notification::insert($data);
    }
    /**
     * Method getNotificationList
     * user notification list for login user
     * 
     * @return void
     */
    public function testGetNotificationList(): void
    {
        // Create 4 notification in database
        $user = $this->createUser();
        $this->insertNotificationData($user);
        $userRepository = new UserRepository(new User);
        $userInfo = $userRepository->getUser($user->id); //get user as a login
        $this->actingAs($userInfo);
        $userRepository = new NotificationRepository(new Notification);
        $userList = $userRepository->getNotification([]);
        //assert return count is 3
        $this->assertEquals(count($userList), 3);
    }

    /**
     * Method testSearchNotification
     * search notification
     * 
     * @return void
     */
    public function testSearchNotification(): void
    {
        // Create 4 notification in database
        $user = $this->createUser();
        $this->insertNotificationData($user);
        $this->actingAs($user);
        $userRepository = new NotificationRepository(new Notification);
        $userList = $userRepository->getNotification(['search' => 'first']);
        //assert return count is 1
        $this->assertEquals(count($userList), 1);
    }

    /**
     * Method testAdminMarkAsReadNotification
     *
     * @return void
     */
    public function testAdminMarkAsReadNotification(): void
    {
        $user = $this->createUser();
        $data = [
            'name' => 'samy',
            'user_type' => 'user',
            'email' => "samy@mailinator.com",
            'status' => 'active',
            'password' => Hash::make(123456),
            'is_readed' => 0
        ];
        $userRepository = new UserRepository(new User);
        $userData = $userRepository->createUser($data);        // Create 1 user in database
        $this->insertNotificationData($user);                 // Create 4 notification in database
        $this->actingAs($user);
        $userRepository = new NotificationRepository(new Notification);
        $readCount = $userRepository->adminMarkAsRead();
        $this->assertEquals($readCount, 3);
        $this->assertDatabaseMissing(
            'notifications',
            [
                'id' => $userData->id,
                'is_readed' => 0
            ]
        );
    }

    /**
     * Method testUserNotFindNotification
     * user do not have any notification in notification table
     * 
     * @return void
     */
    public function testUserNotFindNotification()
    {
        // Create 4 notification in database
        $user = $this->createUser();
        $data = [
            'name' => 'samy',
            'user_type' => 'user',
            'email' => "samy@mailinator.com",
            'status' => 'active',
            'password' => Hash::make(123456),
            'is_readed' => 0
        ];
        $userRepository = new UserRepository(new User);
        $userData = $userRepository->createUser($data);        // Create 1 user in database
        $this->insertNotificationData($userData);              // Create 4 notification in database
        $this->actingAs($user);
        $userRepository = new NotificationRepository(new Notification);
        $readCount = $userRepository->adminMarkAsRead();
        $this->assertEquals($readCount, 0);
    }

    /**
     * Method testMarkAsReadNotificationById
     * read notification by id
     * 
     * @return void
     */
    public function testMarkAsReadNotificationById(): void
    {
        // Create 4 notification in database
        $user = $this->createUser();
        $this->insertNotificationData($user);
        $this->actingAs($user);
        $userRepository = new NotificationRepository(new Notification);
        $notification = Notification::first();
        $this->assertDatabaseHas(
            'notifications',
            [
                'id' => $notification->id,
                'is_readed' => 0
            ]
        );
        $readCount = $userRepository->markAsReadById($notification->id);
        $this->assertEquals($readCount, 1);
        $this->assertDatabaseHas(
            'notifications',
            [
                'id' => $notification->id,
                'is_readed' => 1
            ]
        );
    }
}
