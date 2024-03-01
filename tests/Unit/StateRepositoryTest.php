<?php

namespace Tests\Unit;

use Carbon\Carbon;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use App\Models\State;
use App\Models\Country;
use Illuminate\Support\Facades\Log;
use App\Repositories\StateRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StateRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A basic unit test example.
     * 
     * @return void
     */
    public function testExample(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Method testStatesDatabaseHasExpectedColumnsCount
     *
     * @return void
     */
    public function testStatesDatabaseHasExpectedColumnsCount()
    {
        $columnCount = $this->tableColumnCount('states');
        $this->assertEquals($columnCount, 6);
    }
    
    /**
     * Method testStatesDatabaseHasExpectedColumns
     *
     * @return void
     */
    public function testStatesDatabaseHasExpectedColumns()
    {
        $this->assertTrue( 
            Schema::hasColumns(
                'states', 
                [
                    'id', 'name', 'country_id', 'status', 'created_at', 'updated_at'
                ]
            ), 1
        );
    }
  
    /**
     * Method testGettingAllStates
     *
     * @return void
     */
    public function testGettingAllStates()
    {
        $country = Country::factory()->create();
        $state = [
            'name' => 'Test State',
            'country_id' => $country->id,
        ];
        $stateNew = State::factory()->create($state);
        
        $this->assertDatabaseHas(
            'states',
            [
                'id' => $stateNew->id,
            ]
        );
    }
}
