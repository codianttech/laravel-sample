<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Repositories\SettingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SettingRepositoryTest extends TestCase
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
     * Method testDeleteSetting
     *
     * @return void
     */
    public function testDeleteSetting() :void
    {
        $setting = Setting::create(
            [
                'setting_key' => $this->faker->userName,
                'setting_label' => $this->faker->firstName,
                'setting_value' => $this->faker->randomNumber,
            ]
        );

        $this->assertModelExists($setting);

        $settingRepository = new SettingRepository(new Setting());
        $settingRepository->delete($setting->id);

        $this->assertModelMissing($setting);
    }


    /**
     * Method testDeleteSetting
     *
     * @return void
     */
    public function testGetSettingDetail() :void
    {
        $setting = Setting::create(
            [
                'setting_key' => $this->faker->userName,
                'setting_label' => $this->faker->firstName,
                'setting_value' => $this->faker->randomNumber,
            ]
        );

        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->getDetail($setting->id);

        $this->assertEquals($setting->setting_key, $response->setting_key);
        $this->assertEquals($setting->setting_label, $response->setting_label);
        $this->assertEquals($setting->setting_value, $response->setting_value);
    }

    /**
     * Method testUpdateSettingDetail
     *
     * @return void
     */
    public function testUpdateSettingDetail() :void
    {
        $key = strtolower($this->faker->firstName);
        $key1 = strtolower($this->faker->firstName);
        $key2 = strtolower($this->faker->firstName);
        $data = [
            [
                'setting_key' => 'app.'.$key,
                'setting_label' => $this->faker->firstName,
                'setting_value' => $this->faker->randomNumber,
            ],
            [
                'setting_key' => 'app.'.$key1,
                'setting_label' => $this->faker->firstName,
                'setting_value' => $this->faker->randomNumber,
            ],
            [
                'setting_key' => 'app.'.$key2,
                'setting_label' => $this->faker->firstName,
                'setting_value' => $this->faker->randomNumber,
            ]
        ];

        Setting::insert($data);

        $updateData = [
            $key => $this->faker->randomNumber,
            $key1 => $this->faker->randomNumber,
            $key2 => $this->faker->randomNumber,
        ];

        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->updateDetail($updateData);
        $this->assertTrue($response);

        $this->assertDatabaseHas(
            'settings', 
            [
                'setting_key' => $data[0]['setting_key'],
                'setting_label' => $data[0]['setting_label'],
                'setting_value' => $updateData[$key],
            ]
        );

        $this->assertDatabaseHas(
            'settings', 
            [
                'setting_key' => $data[1]['setting_key'],
                'setting_label' => $data[1]['setting_label'],
                'setting_value' => $updateData[$key1],
            ]
        );

        $this->assertDatabaseHas(
            'settings', 
            [
                'setting_key' => $data[2]['setting_key'],
                'setting_label' => $data[2]['setting_label'],
                'setting_value' => $updateData[$key2],
            ]
        );
    }

    /**
     * Method testUpdateLogoSettingDetail
     *
     * @return void
     */
    public function testUpdateLogoSettingDetail() :void
    {
        $setting = Setting::updateOrCreate(
            [
                'setting_key' => 'app.logo',
                'setting_label' => 'logo',
            ]
        );
        
        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');

        $updateData = [
            'logo' => $file
        ];

        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->updateDetail($updateData);
        $this->assertTrue($response);

        $updateSetting = $settingRepository->getDetail($setting->id);

        $this->assertDatabaseHas(
            'settings', 
            [
                'setting_key' => 'app.logo',
                'setting_label' => 'logo',
                'setting_value' => $updateSetting->setting_value
            ]
        );
        
        $this->assertTrue(Storage::exists($updateSetting->setting_value));
        if (!empty($setting->setting_value)) {
            $this->assertEquals(false, Storage::exists($setting->setting_value));
        }

    }

    /**
     * Method testGetAllSetting
     *
     * @return void
     */
    public function testGetAllSetting() :void
    {
        $data = [
            [
                'setting_key' => $this->faker->userName,
                'setting_label' => $this->faker->firstName,
                'setting_value' => $this->faker->randomNumber,
            ],
            [
                'setting_key' => $this->faker->userName,
                'setting_label' => $this->faker->firstName,
                'setting_value' => $this->faker->randomNumber,
            ],
            [
                'setting_key' => $this->faker->userName,
                'setting_label' => $this->faker->firstName,
                'setting_value' => $this->faker->randomNumber,
            ]
        ];

        Setting::insert($data);

        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->getAll();

        $requestData = [];
        foreach ($data as $row) {
            $requestData[$row['setting_key']] = $row['setting_value'];
        }
        
        $this->assertEquals($requestData, $response);
        
    }

    /**
     * Method testRunMaintenanceModeDownCommand
     *
     * @return void
     */
    public function testRunMaintenanceModeDownCommand() :void
    {
        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->runCommand('down');
        $this->assertTrue($response);
        
    }

    /**
     * Method testRunMaintenanceModeUpCommand
     *
     * @return void
     */
    public function testRunMaintenanceModeUpCommand() :void
    {
        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->runCommand('up');
        $this->assertTrue($response);
        
    }

    /**
     * Method testConfigCacheCommand
     *
     * @return void
     */
    public function testConfigCacheCommand() :void
    {
        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->runCommand('coca');
        $this->assertTrue($response);
        
    }

    /**
     * Method testClearConfigCacheViewCommand
     *
     * @return void
     */
    public function testClearConfigCacheViewCommand() :void
    {
        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->runCommand('clear');
        $this->assertTrue($response);
        
    }

    /**
     * Method testOptimizeClearCommand
     *
     * @return void
     */
    public function testOptimizeClearCommand() :void
    {
        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->runCommand('opcl');
        $this->assertTrue($response);
        
    }

    /**
     * Method testClearConfigCacheViewCommand
     *
     * @return void
     */
    public function testRunMigrateCommand() :void
    {
        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->runCommand('rm');
        $this->assertTrue($response);
        
    }


    /**
     * Method testRunMigrateFreshSeedCommand
     *
     * @return void
     */
    public function testRunMigrateFreshSeedCommand() :void
    {
        $settingRepository = new SettingRepository(new Setting());
        $response = $settingRepository->runCommand('rmf');
        $this->assertTrue($response);
        
    }

}
