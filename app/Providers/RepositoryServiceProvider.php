<?php

namespace App\Providers;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * RepositoryServiceProvider
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Method register
     *
     * @return void
     */
    public function register(): void
    {
        
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
       
    }

    /**
     * Method boot
     *
     * @return void
     */
    public function boot(): void
    {

    }
}
