<?php

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

Route::get('/', 'HomeController@index')->name('home');
Route::get('/user-list', 'UserController@getUsers');
Route::get('/user-profile', 'UserController@getUsers');
Route::get('/oauth-setting', 'UserController@getUsers');

Route::prefix('auth')->group(function () {
    Route::get('/facebook', 'Auth\LoginController@redirectToProvider');
    Route::get('/facebook/callback', 'Auth\LoginController@handleProviderCallback');
    
    Route::get('/8ppy', function (){
        
        $query = http_build_query([
            'client_id'         => 1,
            'redirect_uri'      => 'http://client.8ppy.dev:81/auth/8ppy/callback',
            'response_type'     => 'code',
            'scope'             => ''
        ]);
        
        return redirect('http://server.8ppy.dev/oauth/authorize?', $query);
    });
//    Route::get('/8ppy/callback', 'Auth\OauthSocialiteController@handleProviderCallback');
    Route::get('/8ppy/callback', function(Request $request){
        $http = new \GuzzleHttp\Client();
        
        $response = $http->post('http://server.8ppy.dev/oauth/token', [
            'form_params' => [
                'grant_type'        => 'authorization_code',
                'client_id'         => 1,
                'client_secret'     => 'oH7qgAdYK72HNUM1gyZXKr2FuxstVOSe7Rqj9Oqk',
                'redirect_uri'      => 'http://client.8ppy.dev:81/auth/8ppy/callback',
                'code'              => $request->code,
            ]
        ]);
        
        return json_decode((string) $response->getBody(), true);
    });
});

Auth::routes();

Route::prefix('api')->group(function () {
    Route::get('/app', function(){
        return view('api.apps.register');
    });
});
