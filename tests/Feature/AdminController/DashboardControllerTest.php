<?php

namespace Tests\Feature\AdminController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * DashboardControllerTest
 */
class DashboardControllerTest extends TestCase
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
     * Method testGetLatestUsers
     *
     * @return void
     */
    public function testGetLatestUsers(): void
    {
        User::factory()->count(10)->create();
        $latestUsers = User::where(
            'user_type',
            User::TYPE_USER
        )->orderBy('id', 'desc')->limit(10)->get();
        $url = route('admin.dashboard');
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get($url);
        $response->assertViewIs('admin.dashboard.index');
        $response->assertViewHas('latestUsers', $latestUsers);
    }
}
