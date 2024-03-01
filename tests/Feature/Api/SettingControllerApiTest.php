<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

/**
 * SettingControllerApiTest
 */
class SettingControllerApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Method testCountriesListApi
     *
     * @return void
     */
    public function testCountriesListApi()
    {
        Artisan::call('db:seed --class=CountriesTableSeeder');

        $this->getJson('api/v1/countries')
            ->assertSuccessful();
    }

    /**
     * Method testStatesListApi
     *
     * @return void
     */
    public function testStatesListApi()
    {
        
        Artisan::call('db:seed --class=CountriesTableSeeder');
        Artisan::call('db:seed --class=StatesTableSeeder');

        $this->getJson('api/v1/states?country_id=1')
            ->assertSuccessful();
    }

    /**
     * Method testCitiesListApi
     *
     * @return void
     */
    public function testCitiesListApi()
    {
        
        Artisan::call('db:seed --class=CountriesTableSeeder');
        Artisan::call('db:seed --class=StatesTableSeeder');
        Artisan::call('db:seed --class=CitiesTableSeeder');

        $this->getJson('api/v1/cities?country_id=1&state_id=1')
            ->assertSuccessful();
    }


}
