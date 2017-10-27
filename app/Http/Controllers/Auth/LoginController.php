<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Socialite;
use App\User;
use App\UserFbtoken;
use App\FbResponse;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }
    
    /**
     * Obtain the user information from Facebook.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $fbUser = Socialite::driver('facebook')->user();

        $user   = $this->findOrCreateFacebookUser($fbUser);

        auth()->login($user);
        
        return redirect('/');
        // $user->token;
    }
    
    public function findOrCreateFacebookUser($fbUser)
    {
        
        $loginFacebook = UserFbtoken::firstOrNew(['facebook_id' => (string)$fbUser->id]);

        if ($loginFacebook->exists) return $loginFacebook->user;

        if ($fbUser->email) {
            $user = User::firstOrNew(['email' => $fbUser->email]);
            
            if ($user->exists) {
                $loginFacebook->fill([
                    'user_id'          => (integer)$user->id,
                    'email'            => $fbUser->email,
                    'facebook_id'      => $fbUser->id,
                    'token'     => (string)$fbUser->token,
                    'refreshtoken'     => (string)$fbUser->refreshToken,
                ])->save();
                
                return $user;
            }
        }
        
        $user = new User;
        
        $user->fill([
            'name'                      => $fbUser->name,
            'email'                     => $fbUser->email,
            'avatar'                    => $fbUser->avatar,
            'current_login_by'          => 'facebook',
            'provider_id'               => $fbUser->id,
        ])->save();

        $loginFacebook->fill([
            'user_id'          => (integer)$user->id,
            'email'            => $fbUser->email,
            'facebook_id'      => $fbUser->id,
        ])->save();
        
        $fbResponse = FbResponse::create([
            'facebook_id'       =>  $loginFacebook->id,
            'scope'             => 'public',
            'response'        => json_encode($fbUser),
        ]);
        
        return $user;
    }   
}
