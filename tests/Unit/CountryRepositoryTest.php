<?php

namespace Tests\Unit;

use App\Models\Country;
use Tests\TestCase;
use App\Repositories\CountryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertEquals;

class CountryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Method testCreateCountry
     *
     * @return void
     */
    public function testCreateCountry(): void
    {
        $data = [
            'code' => 'IN',
            'name' => 'India',
            'phone_code' => "+91",
            'status' => 'active',
        ];
        
        $countryRepository = new CountryRepository(new Country());
        $countryRepository->createCountry($data);
        $this->assertDatabaseHas(
            'countries',
            [
                'code' => 'IN',
                'name' => 'India',
                'phone_code' => "+91",
                'status' => 'active',
            ]
        );
    }
    /**
     * Method testGetCountries
     * get country with correct id
     * 
     * @return void
     */
    public function testGetCountries(): void
    {
        $country = Country::factory()->create();
        $countryRepository = new CountryRepository(new Country);
        $countryInfo = $countryRepository->getCountry($country->id);
        $this->assertEquals($country['name'], $countryInfo->name);
    }

    /**
     * Method testGetCountriesWithWrongId
     * get country with wrong id
     * 
     * @return void
     */
    public function testGetCountriesWithWrongId(): void
    {
        $country = Country::factory()->create();
        $countryRepository = new CountryRepository(new Country);
        $id = $country->id + rand(2, 50);
        $countryInfo = $countryRepository->getCountry($id);
        $this->assertNull($countryInfo);
    }


    /**
     * Method testChangeActiveStatus
     *
     * @return void
     */
    public function testChangeActiveStatus()
    {
        $country = Country::factory()->create();
        $data = ['status' => 'active'];
        $countryRepository = new CountryRepository(new Country);
        $countryRepository->changeStatus($data, $country->id);
        $this->assertDatabaseHas(
            'countries',
            [
                'name' => $country->name,
                'status' => 'active'
            ]
        );
    }


    /**
     * Method testChangeInactiveStatus
     *
     * @return void
     */
    public function testChangeInactiveStatus()
    {
        $country = Country::factory()->create();
        $data = ['status' => 'inactive'];
        $countryRepository = new CountryRepository(new Country);
        $countryRepository->changeStatus($data, $country->id);
        $this->assertDatabaseHas(
            'countries',
            [
                'name' => $country->name,
                'status' => 'inactive'
            ]
        );
    }

        
    /**
     * Method testGetAll
     *
     * @return void
     */
    public function testGetAll()
    {
        Country::factory()->count(11)->create();
        $countryRepository = new CountryRepository(new Country);
        $countries = $countryRepository->getAll();
        $this->assertEquals($countries->count(), 11);
    }
}
