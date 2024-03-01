<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserDevice;
use App\Repositories\UserDeviceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserDeviceRepositoryTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    
    /**
     * Method testCreateDevice
     *
     * @return void
     */
    public function testCreateDevice() :void
    {
        $user = User::create(
            [
                'name' => $this->faker->name,
                'user_type' => 'user',
                'email' => $this->faker->email,
                'status' => 'active',
                'password' => Hash::make(123456)
            ]
        );

        $data = [
            'user_id' => $user->id,
            'device_id' => $this->faker->randomNumber(5),
            'device_type' => 'web',
        ];

      
        $userRepository = new UserDeviceRepository(new UserDevice());
        $response = $userRepository->createDevice($data);

        $this->assertDatabaseHas(
            'user_devices', 
            [
                'user_id' => $response->user_id
            ]
        );
        
    }


    /**
     * Method testGetDeviceByUser
     *
     * @return void
     */
    public function testGetDeviceByUser() :void
    {
        $user = User::create(
            [
                'name' => $this->faker->name,
                'user_type' => 'user',
                'email' => $this->faker->email,
                'status' => 'active',
                'password' => Hash::make(123456)
            ]
        );

        UserDevice::create(
            [
                'user_id' => $user->id,
                'device_id' => $this->faker->randomNumber(5),
                'device_type' => 'web'
            ]
        );

        $userRepository = new UserDeviceRepository(new UserDevice());

        /* test without user */
        $response = $userRepository->getDeviceByUser($user->id);
        $this->assertModelExists($response);

        /* test with user */
        $responseUser = $userRepository->getDeviceByUser($user->id, true);
        $this->assertModelExists($responseUser->user);
            
    }


    /**
     * Method TestupdateDeviceByUser
     *
     * @return void
     */
    public function testUpdateDeviceByUser() :void
    {
        $user = User::create(
            [
                'name' => $this->faker->name,
                'user_type' => 'user',
                'email' => $this->faker->email,
                'status' => 'active',
                'password' => Hash::make(123456)
            ]
        );
        
        $userDevices = [
            [
                'user_id' => $user->id,
                'device_id' => $this->faker->randomNumber(5),
                'device_type' => 'web'
            ],
            [
                'user_id' => $user->id,
                'device_id' => $this->faker->randomNumber(5),
                'device_type' => 'web'
            ],
            [
                'user_id' => $user->id,
                'device_id' => $this->faker->randomNumber(5),
                'device_type' => 'web'
            ]
        ];
        
        UserDevice::insert($userDevices);
        
        $data = [
            'device_id' => $this->faker->randomNumber(5),
            'device_type' => 'ios'
        ];

        $userRepository = new UserDeviceRepository(new UserDevice());
        $response = $userRepository->updateDeviceByUser($data, $user->id);
        
        $this->assertEquals($response, true);

        $this->assertDatabaseHas(
            'user_devices', 
            [
                'user_id' => $user->id,
                'device_id' => $data['device_id'],
                'device_type' => $data['device_type'],
            ]
        );

    }

    /**
     * Method testDeleteDevice
     *
     * @return void
     */
    public function testDeleteDevice() :void
    {
        $user = User::create(
            [
                'name' => $this->faker->name,
                'user_type' => 'user',
                'email' => $this->faker->email,
                'status' => 'active',
                'password' => Hash::make(123456)
            ]
        );

        $userDevice = UserDevice::create(
            [
                'user_id' => $user->id,
                'device_id' => $this->faker->randomNumber(5),
                'device_type' => 'web'
            ]
        );

        $userRepository = new UserDeviceRepository(new UserDevice());
        $response = $userRepository->deleteDevice($user->id);
        $this->assertModelMissing($userDevice);

    }


}
