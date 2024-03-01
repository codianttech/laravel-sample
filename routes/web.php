<?php


use App\Http\Controllers\Frontend\{
    Auth\LoginController,
    Auth\RegisterController,
    HomeController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(
    ['middleware' => ['check.login:web']],
    function () {
        Route::controller(LoginController::class)
        ->group(
            function () {
                    Route::get('/', 'index')->name('frontend.home');
                    Route::get('/login', 'index')->name('login');
                    Route::post('/login', 'login')->name('login.submit');



                }
            );

        Route::controller(RegisterController::class)
        ->group(
            function () {
                Route::get('/register', 'index')
                    ->name('user.signup-form');
                Route::post('/user-signup', 'register')->name('user.signup');
            }
        );
    }
);
