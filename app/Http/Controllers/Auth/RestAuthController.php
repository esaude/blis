<?php namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AuthRequest;
use App\Http\Controllers\Controller;
use JWTAuth;
use Hash;
use App\Models\User;

class RestAuthController extends  Controller
{
    public function authenticate(AuthRequest $request)
    {
        $this->validate($request, [
            'username' => 'required|min:3',
            'password' => 'required'
        ]);
        $credentials = $request->only('username', 'password');

        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

//        $userToken = JWTAuth::fromUser($user);
            try {
                //attempt to verify the credentials and create a token for the user
                if(!$userToken = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 401);
                }
            } catch(JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

//
//        $objectToken = JWTAuth::setToken($userToken);
//        $expiration = JWTAuth::decode($objectToken->getToken()->get('exp'));

        return response()->json([
            'access_token' => $userToken,
            'token_type' => 'Bearer'
        ]);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_']);
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

       return response()->json(compact('user'));

    }

    public function logout(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatustCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalideException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        }catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        //The token  valide and we have found user via the sub claim
        JWTAUth::invalidate(JWTAuth::getToken());
        return response()->json(['user_logout' => 'Successful'], 200);
    }

}