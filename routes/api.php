<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware(['auth:sanctum','verified'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->get('/user-with-categories/{id?}',  'App\Http\Controllers\Api\UsersController@withCategories');
Route::apiResource('users', 'App\Http\Controllers\Api\UsersController');
Route::get('email/verify/{id}', 'App\Http\Controllers\Api\UsersController@emailVerified');
Route::post('user/password-reset-email', 'App\Http\Controllers\Api\UsersController@passwordResetEmail')->name('forget-password-email');
Route::post('user/reset-password', 'App\Http\Controllers\Api\UsersController@updateForgetPassword')->name('forget-password');
Route::post('user/update-password/{id?}', 'App\Http\Controllers\Api\UsersController@UpdatePassword')->name('update-password');
Route::apiResource('social_links', 'App\Http\Controllers\Api\SocialLinksController');
Route::apiResource('seasons', 'App\Http\Controllers\Api\SeasonsController');
Route::apiResource('requests', 'App\Http\Controllers\Api\RequestsController');
Route::get('request-by-type', 'App\Http\Controllers\Api\RequestsController@getByType');
Route::post('requests/accept-purposal', 'App\Http\Controllers\Api\RequestsController@acceptPurposal');
Route::post('requests/unaccept-purposal', 'App\Http\Controllers\Api\RequestsController@unacceptPurposal');
Route::post('requests/purposeAll', 'App\Http\Controllers\Api\RequestsController@purposeAll');
Route::apiResource('matches', 'App\Http\Controllers\Api\MatchesController');
Route::apiResource('matches-single-doubles', 'App\Http\Controllers\Api\MatchSingleDoublesController');
Route::apiResource('matches-rankings', 'App\Http\Controllers\Api\MatchRankCategoriesController');
Route::apiResource('matches-ladder', 'App\Http\Controllers\Api\MatchLaddersController');
Route::get('matches-ladder-by-season/{id?}', 'App\Http\Controllers\Api\MatchLaddersController@getBySeason');
Route::get('paid-user-in-ladder/{id?}', 'App\Http\Controllers\Api\UsersController@getPaidUserInLadder');

Route::post('request-by-ladder/{id?}', 'App\Http\Controllers\Api\RequestsController@getByLadder');
Route::get('request-by-ladder/{id?}', 'App\Http\Controllers\Api\RequestsController@getByLadder');



Route::post('request-matches-by-ladder/{id?}', 'App\Http\Controllers\Api\MatchesController@getByLadder');
Route::get('request-matches-by-ladder/{id?}', 'App\Http\Controllers\Api\MatchesController@getByLadder');

Route::post('matches-ladder-user-ranking/{id?}', 'App\Http\Controllers\Api\MatchesController@getUserRankingByLadder');
Route::get('matches-ladder-user-ranking/{id?}', 'App\Http\Controllers\Api\MatchesController@getUserRankingByLadder');

Route::get('get-next-available-season', 'App\Http\Controllers\Api\SeasonsController@getNextAvailableSeason');

Route::get('get-paypal-history', 'App\Http\Controllers\Api\UsersController@getPaymenthistory');

Route::post('user/admin-update-password/{id?}', 'App\Http\Controllers\Api\UsersController@adminUpdatePassword')->name('update-password');

Route::post('user/admin-update-season/{id?}', 'App\Http\Controllers\Api\UsersController@adminUpdateSeason')->name('update-season');
Route::get('user-paid-in-ladders/{id?}', 'App\Http\Controllers\Api\UserPaidRankingsController@getPaidByUser');

Route::post('social_links/get-by-type', 'App\Http\Controllers\Api\SocialLinksController@getByType');

Route::apiResource('user-paid-rankings', 'App\Http\Controllers\Api\UserPaidRankingsController');
Auth::routes(['verify' => true]);
