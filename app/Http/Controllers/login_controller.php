<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class login_controller extends Controller
{
   /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // محاولة تسجيل الدخول كـ Doctor
        if ($token = $this->attemptLogin($validator->validated(), 'doctor-api')) {
            return $this->createNewToken($token, 'doctor','doctor-api');
        }

        // محاولة تسجيل الدخول كـ Secretary
        if ($token = $this->attemptLogin($validator->validated(), 'employe-api')) {
            return $this->createNewToken($token, 'employe','employe-api');
        }

        // محاولة تسجيل الدخول كـ Admin
        if ($token = $this->attemptLogin($validator->validated(), 'api')) {
            return $this->createNewToken($token, 'user','api');
        }


        return response()->json(['error' => 'Unauthorized'], 401);
    }

    private function attemptLogin($credentials, $guard)
    {
        // config(['auth.defaults.guard' => $guard]);

        if ($token = auth()->guard($guard)->attempt($credentials)) {
            return $token;
        }

        return false;
    }

    protected function createNewToken($token, $userType,$guard)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard($guard)->factory()->getTTL() * 60 * 24* 7* 50,
            'user' => auth()->guard($guard)->user(),
            'user_type' => $userType
        ]);
    }





}

