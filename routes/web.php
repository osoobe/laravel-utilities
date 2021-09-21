
<?php

use App\Http\Controllers\UserController;
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


Route::group([ 'prefix' => 'api', 'name' => 'api.'], function() {
    Route::get('/resources/{slug}/{format}', '\Osoobe\Utilities\Http\Controllers\AjaxController@getResource')->name('api.resource.get');
});
