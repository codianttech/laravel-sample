<?php
namespace Tests;

use Illuminate\Support\Facades\Schema;

trait DatabaseTableColumnCount
{
    /**
     * Method tableColumnCount
     *
     * @param string $table [explicite description]
     *
     * @return int
     */
    public function tableColumnCount(string $table)
    {
        return count(Schema::getColumnListing($table));
    }
}