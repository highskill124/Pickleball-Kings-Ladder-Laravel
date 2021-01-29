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
Route::get('email/verify/{id}/{uid}', 'App\Http\Controllers\Api\UsersController@emailVerified');
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

Route::get('request-by-rank-category/{id?}', 'App\Http\Controllers\Api\RequestsController@getByRankCategory');

Route::post('filter-matches-by-rank-category', 'App\Http\Controllers\Api\MatchesController@filtertByRankCategory');

Route::get('request-matches-by-rank-category/{id?}', 'App\Http\Controllers\Api\MatchesController@getByRankCategory');

Route::post('matches-ladder-user-ranking/{id?}', 'App\Http\Controllers\Api\MatchesController@getUserRankingByLadder');
Route::get('matches-ladder-user-ranking/{id?}', 'App\Http\Controllers\Api\MatchesController@getUserRankingByLadder');


Auth::routes(['verify' => true]);
