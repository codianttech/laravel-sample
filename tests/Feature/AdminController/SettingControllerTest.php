<?php

namespace Tests\Feature\AdminController;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class SettingControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;
        
    /**
     * Method testGetAllSetting
     *
     * @return void
     */
    public function testControllerGetAllSetting()
    {   
        Artisan::call('db:seed --class=AdminUserSeeder');
        Artisan::call('db:seed --class=SettingTableSeeder');

        $user = User::where('email', 'backend@mailinator.com')->first();
        $url = 'admin/setting';

        $this->withoutExceptionHandling();
        $response = $this->actingAs($user)->ajaxGet($url);
        $response->assertSuccessful();
        $response->assertViewIs('admin.setting.index');
        $response->assertViewHasAll(['settings', 'maintenanceMode']);
    }
    
    /**
     * Method testControllerUpdateSetting
     *
     * @return void
     */
    public function testControllerUpdateSetting()
    {   
        Artisan::call('db:seed --class=AdminUserSeeder');
        $user = User::where('email', 'backend@mailinator.com')->first();
       
        $setting = Setting::create(
            [
                'setting_key' => $this->faker->userName,
                'setting_label' => $this->faker->firstName,
                'setting_value' => $this->faker->randomNumber,
            ]
        );

        $payload = [
            'setting_key' => $this->faker->userName,
            'setting_label' => $this->faker->firstName,
            'setting_value' => $this->faker->randomNumber,
        ];

        $this->actingAs($user)->call('PATCH', 'admin/setting/' . $setting->id, $payload)
            ->assertStatus(200);

    }


    /**
     * Method testControllerEditSetting
     *
     * @return void
     */
    public function testControllerEditSetting()
    {   
        Artisan::call('db:seed --class=AdminUserSeeder');
        $user = User::where('email', 'backend@mailinator.com')->first();
       
        $setting = Setting::create(
            [
                'setting_key' => $this->faker->userName,
                'setting_label' => $this->faker->firstName,
                'setting_value' => $this->faker->randomNumber,
            ]
        );

        $this->actingAs($user)->call('GET', 'admin/setting/' . $setting->id)
            ->assertStatus(200);

    }

    /**
     * Method testControllerCreateSetting
     *
     * @return void
     */
    public function testControllerCreateSetting()
    {
        Artisan::call('db:seed --class=AdminUserSeeder');
        $user = User::where('email', 'backend@mailinator.com')->first();

        $payload = [
            'setting_key' => $this->faker->userName,
            'setting_label' => $this->faker->firstName,
            'setting_value' => $this->faker->randomNumber,
        ];

        $this->actingAs($user)->call('POST', 'admin/setting/', $payload)
            ->assertStatus(200);
    }

    /**
     * Method testControllerGeneralListSetting
     *
     * @return void
     */
    public function testControllerGeneralListSetting()
    {
        Artisan::call('db:seed --class=AdminUserSeeder');

        $user = User::where('email', 'backend@mailinator.com')->first();
        $url = 'admin/setting/general';

        $this->withoutExceptionHandling();
        $response = $this->actingAs($user)->ajaxGet($url);
        $response->assertSuccessful();
        $response->assertViewIs('admin.setting.general');
        $response->assertViewHas(['maintenanceMode']);
    }

    /**
     * Method testControllerRunCommand
     *
     * @return void
     */
    public function testControllerRunCommand()
    {
        Artisan::call('db:seed --class=AdminUserSeeder');

        $user = User::where('email', 'backend@mailinator.com')->first();
        $url = 'admin/runCommand/opcl';

        $this->withoutExceptionHandling();
        $response = $this->actingAs($user)->ajaxGet($url);
        $response->assertSuccessful();
    }
    

}
