<?php

namespace Tests\Feature\AdminController;

use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;


/**
 * UserControllerTest
 */
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Method createAdmin
     *
     * @return object
     */
    public function createAdmin()
    {
        $data = [
            'name' => 'Admin',
            'email' => 'backend@mailinator.com',
            'user_type' => User::TYPE_ADMIN,
            'password' => 'Test@123',
            'status' => User::STATUS_ACTIVE,
        ];
        return User::create($data);
    }
    /**
     * Method createUser
     *
     * @return void
     */
    public function createUser()
    {

        // Create user in database
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
                'status' => 'Inactive',
                'password' => 123456
            ]
        ];

        User::insert($data);
    }
    /**
     * Method testUserListWithAjaxRequest
     *
     * @return void
     */
    public function testUserListWithAjaxRequest(): void
    {
        // Create user in database
        User::factory()->count(6)->create();
        // Create admin in database
        $admin = $this->createAdmin();
        $url = 'admin/user';
        $response = $this->actingAs($admin)->ajaxGet($url)->assertSuccessful();
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('meta')
                    ->has('data', 6)
                    ->has('links')
            );
    }

    /**
     * Method testLoadUserListViewPage
     *
     * @return void
     */
    public function testLoadUserListViewPage(): void
    {
        // Create admin in database
        $admin = $this->createAdmin();
        $url = 'admin/user';
        $response = $this->actingAs($admin)->get($url);
        $response->assertViewIs('admin.user.index');
    }
      
    /**
     * Method testSearchHttpUserList
     *
     * @return void
     */
    public function testSearchHttpUserList(): void
    {
        $this->createUser();
        $admin = $this->createAdmin();
        $url = 'admin/user?search=sam';
        $response = $this->actingAs($admin)->ajaxGet($url)->assertSuccessful();
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('meta')
                    ->has('data', 3)
                    ->has(
                        'data.0',
                        fn ($json) =>
                        $json
                            ->where('name', 'sam')
                            ->etc()
                    )
                    ->has('links')
            );
    }

    /**
     * Method testUserFilterList
     *
     * @return void
     */
    public function testUserFilterList(): void
    {
        $this->createUser();
        $admin = $this->createAdmin();
        $url = 'admin/user?email=samuals@mailinator.com';
        $response = $this->actingAs($admin)->ajaxGet($url)->assertSuccessful();
        // asserting email
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('meta')
                    ->has('data', 1)
                    ->has(
                        'data.0',
                        fn ($json) =>
                        $json
                            ->where('email', 'samuals@mailinator.com')
                            ->etc()
                    )
                    ->has('links')
            );

        $url = 'admin/user?name=robin';
        $response = $this->actingAs($admin)->ajaxGet($url)->assertSuccessful();
        // asserting name
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('meta')
                    ->has('data', 1)
                    ->has(
                        'data.0',
                        fn ($json) =>
                        $json
                            ->where('name', 'robin')
                            ->etc()
                    )
                    ->has('links')
            );
        $url = 'admin/user?status=active';
        $response = $this->actingAs($admin)->ajaxGet($url)->assertSuccessful();
        // asserting active status
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('meta')
                    ->has('data', 3)
                    ->has(
                        'data.0',
                        fn ($json) =>
                        $json
                            ->where('status', 'active')
                            ->etc()
                    )
                    ->has('links')
            );
    }
       
    /**
     * Method testShowFunction
     *
     * @return void
     */
    public function testShowFunction()
    {
        // Create user in database
        $user = User::factory()->create();
        // Create admin in database
        $admin = $this->createAdmin();
        $url = 'admin/user/' . $user->id;
        $response = $this->actingAs($admin)->get($url);
        $response->assertViewIs('admin.user.user-details');
        $response->assertViewHas('user', $user);
        $this->mock(
            UserRepository::class,
            function (MockInterface $mock) use ($user) {
                $mock->shouldReceive('getUserDetail')->with($user->id)->once();
            }
        );
        app(UserController::class)->show($user->id);
    }

     
    /**
     * Method testChangeInactiveStatusForUser
     *
     * @return void
     */
    public function testChangeInactiveStatusForUser()
    {
        // Create user in database
        $user = User::factory()->create();
        // Create admin in database
        $admin = $this->createAdmin();
        $url = 'admin/user/changeStatus';
        $data = [
            'status' => 'inactive',
            'id' => $user->id
        ];
        $response = $this->actingAs($admin)->ajaxPost($url, $data)->assertSuccessful();
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('success')
                    ->has('data', null)
                    ->has('message')
            );
        $this->assertDatabaseHas(
            'users',
            [
                'email' => $user->email,
                'status' => 'inactive'
            ]
        );
        $this->mock(
            UserRepository::class,
            function (MockInterface $mock) use ($data) {
                $mock->shouldReceive('changeStatus')->with($data, $data['id'])->once();
            }
        );
        $request = Request::create('/user/changeStatus', 'POST', $data);
        app(UserController::class)->changeStatus($request);
    }

    /**
     * Method testChangeAactiveStatusForUser
     *
     * @return void
     */
    public function testChangeAactiveStatusForUser()
    {
        // Create user in database
        $data = [
            'name' => 'robin',
            'user_type' => 'user',
            'email' => "robin@mailinator.com",
            'status' => 'inactive',
            'password' => 123456
        ];
        $user = User::create($data);
        // Create admin in database
        $admin = $this->createAdmin();
        $url = 'admin/user/changeStatus';
        $data = [
            'status' => 'active',
            'id' => $user->id
        ];
        $response = $this->actingAs($admin)->ajaxPost($url, $data)->assertSuccessful();
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('success')
                    ->has('data', null)
                    ->has('message')
            );
        $this->assertDatabaseHas(
            'users',
            [
                'email' => $user->email,
                'status' => 'active'
            ]
        );
    }
}
