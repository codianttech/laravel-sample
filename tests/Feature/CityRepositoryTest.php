<?php

namespace Tests\Feature;

use App\Models\City;
use App\Repositories\CityRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class CityRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Method testCityListWithCountyId
     *
     * @return void
     */
    public function testCityListWithCountyId(): void
    {
        City::factory()->count(6)->create();
        $data['country_id'] = City::first()->country_id;
        $cityRepository = new CityRepository(new City);
        $cities = $cityRepository->cityList($data);
        $this->assertEquals(count($cities), 1);
    }

    /**
     * Method testCityListWithWrongCountyId
     *
     * @return void
     */
    public function testCityListWithWrongCountyId(): void
    {
        City::factory()->count(6)->create();
        $data['country_id'] = rand(400, 500);
        $cityRepository = new CityRepository(new City);
        $cities = $cityRepository->cityList($data);
        $this->assertEquals(count($cities), 0);
    }

    /**
     * Method testCityListWithStateId
     *
     * @return void
     */
    public function testCityListWithStateId(): void
    {
        City::factory()->count(6)->create();
        $data['state_id'] = City::first()->state_id;
        $cityRepository = new CityRepository(new City);
        $cities = $cityRepository->cityList($data);
        $this->assertEquals(count($cities), 1);
    }

    /**
     * Method testCityListWithWrongStateId
     *
     * @return void
     */
    public function testCityListWithWrongStateId(): void
    {
        City::factory()->count(6)->create();
        $data['state_id'] = rand(40, 50);
        $cityRepository = new CityRepository(new City);
        $cities = $cityRepository->cityList($data);
        $this->assertEquals(count($cities), 0);
    }

    /**
     * Method testGetCities
     *
     * @return void
     */
    public function testGetCities(): void
    {
        City::factory()->count(6)->create();
        $data['state_id'] = City::first()->state_id;
        $cityRepository = new CityRepository(new City);
        $cities = $cityRepository->getCities($data);
        $this->assertEquals(count($cities), 1);
    }
}
