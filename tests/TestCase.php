<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTableColumnCount;

    /**
     * Method ajaxPost
     *
     * @param string $uri
     * @param array  $data
     *
     * @return TestResponse
     */
    protected function ajaxPost(string $uri, array $data = [])
    {
        return $this->post($uri, $data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    }

    /**
     * Method ajaxGet
     *
     * @param string $uri
     * @param array  $data
     *
     * @return TestResponse
     */
    protected function ajaxGet(string $uri, array $data = [])
    {
        return $this->get($uri, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    }
}
