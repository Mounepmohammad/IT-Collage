<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\employe;
use Validator;

class employe_auth_controller extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('employe', ['except' => ['employe_login']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function employe_login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->guard('employe-api')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }
    // /**
    //  * Register a User.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function secrtary_register(Request $request) {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|between:2,100',
    //         'email' => 'required|string|email|max:100|unique:doctors',
    //         'password' => 'required|string|min:6',
    //         'specification' => 'required|string',
    //     ]);
    //     if($validator->fails()){
    //         return response()->json($validator->errors()->toJson(), 400);
    //     }
    //     if($request->specification == "programming"){
    //         $type = 1;
    //     }else if($request->specification == "intelligence"){
    //         $type = 2;
    //     }else{
    //         $type = 3;
    //     }
    //     $secrtary = secrtary ::create(array_merge(
    //                 $validator->validated(),
    //                 ['password' => bcrypt($request->password)],
    //                 ['type' => $type]
    //             ));
    //     return response()->json([
    //         'message' => 'secrtary successfully registered',
    //         'secrtary' => $secrtary
    //     ], 201);
    // }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function employe_logout() {
        auth()->guard('employe-api')->logout();
        return response()->json(['message' => 'employe successfully signed out']);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function employe_profile() {
        return response()->json(['employe'=>auth()->guard('employe-api')->user()]);
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('employe-api')->factory()->getTTL() * 60,
            'employe' => auth()->guard('employe-api')->user()
        ]);
    }
}


