<?php

namespace Tests\Feature\AdminController;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * NotificationControllerTest
 */
class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Method createUser
     *
     * @return void
     */
    public function createUser()
    {
        return User::create(
            [
                'name' => 'sam',
                'user_type' => 'user',
                'email' => "sam@mailinator.com",
                'status' => 'active',
                'password' => Hash::make(123456)
            ]
        );
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
     * Method testNotificationListWithAjaxRequest
     *
     * @return void
     */
    public function testNotificationListWithAjaxRequest(): void
    {
        // Create admin in database
        Artisan::call('db:seed --class=AdminUserSeeder');
        $admin = User::where('email', 'backend@mailinator.com')->first();
        // Create 3 notification in database
        $this->insertNotificationData($admin);
        $url = 'admin/notification';
        $response = $this->actingAs(
            $admin
        )->ajaxGet($url)->assertSuccessful();
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('meta')
                    ->has('data', 3)
                    ->has('links')
            );
    }


    /**
     * Method testLoadNotificationListViewPage
     *
     * @return void
     */
    public function testLoadNotificationListViewPage(): void
    {
        // Create admin in database
        Artisan::call('db:seed --class=AdminUserSeeder');
        $admin = User::where('email', 'backend@mailinator.com')->first();
        // Create notification in database
        $data = [
            'user_id' => $admin->id,
            'title' => 'nofification',
            'message' => "third nofification send",
            'is_readed' => 0,
            'status' => 0
        ];
        Notification::create($data);
        $url = 'admin/notification';
        $response = $this->actingAs(
            $admin
        )->get($url);
        $response->assertViewIs('admin.notification.index');
    }

    /**
     * Method testMarkAllRead
     *
     * @return void
     */
    public function testMarkAllRead(): void
    {
        // Create admin in database
        Artisan::call('db:seed --class=AdminUserSeeder');
        $admin = User::where('email', 'backend@mailinator.com')->first();
        // Create 3 notification in database
        $this->insertNotificationData($admin);
        $url = route('admin.notification.read');
        $this->actingAs(
            $admin
        )->get($url);
        $this->assertDatabaseHas(
            'notifications',
            [
                'title' => 'nofification',
                'message' => "third nofification send",
                'is_readed' => 1,
                'status' => 0
            ]
        );
    }
    /**
     * Method testMarkAsReadById
     *
     * @return void
     */
    public function testMarkAsReadById(): void
    {
        // Create admin in database
        Artisan::call('db:seed --class=AdminUserSeeder');
        $admin = User::where('email', 'backend@mailinator.com')->first();
        // Create 3 notification in database
        $this->insertNotificationData($admin);
        $data = Notification::create(
            [
                'user_id' => $admin->id,
                'title' => 'nofification',
                'message' => "fourth nofification send",
                'is_readed' => 0,
                'status' => 0
            ]
        );
        $url = route('admin.notification.readbyid', $data->id);
        $this->actingAs(
            $admin
        )->get($url);
        $this->assertDatabaseHas(
            'notifications',
            [
                'user_id' => $admin->id,
                'title' => 'nofification',
                'message' => "fourth nofification send",
                'is_readed' => 1,
                'status' => 0
            ]
        );
        $this->assertDatabaseHas(
            'notifications',
            [
                'title' => 'nofification',
                'message' => "third nofification send",
                'is_readed' => 0,
                'status' => 0
            ]
        );
    }
}
