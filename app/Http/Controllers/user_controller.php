<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use File;
use Validator;

class user_controller extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['user_login', 'user_register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user_login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user_register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'specification' => 'nullable|string',
            'phone' => 'nullable|numeric|digits:10',
            'collage_number' => 'nullable|numeric|unique:users,collage_number',
            'photo' => 'nullable|mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $path = null;
        if ($request->hasFile('photo')) {
            $filename = time() . '.' . $request->photo->extension();
            $path = '/images/' . $filename;
            $request->photo->move(public_path('/images/'), $filename);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            [
                'specification'=> 'programming',
                'password' => bcrypt($request->password),
                'photo' => $path,
            ]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }



       /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user_update(Request $request)
    {
        $user = User::find(auth()->user()->id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|between:2,100',
            'email' => 'sometimes|required|string|email|max:100|unique:users,email,' . auth()->user()->id,
            'password' => 'sometimes|required|string|min:6',
            'specification' => 'sometimes|nullable|string',
            'phone' => 'sometimes|nullable|numeric|digits:10',
            'photo' => 'sometimes|nullable|mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $path = $user->photo;
        if ($request->hasFile('photo')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->photo && File::exists(public_path($user->photo))) {
                File::delete(public_path($user->photo));
            }

            // حفظ الصورة الجديدة
            $filename = time().'.'.$request->photo->extension();
            $path = '/images/' . $filename;
            $request->photo->move(public_path('/images/'), $filename);
        }

        $user->update(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'photo' => $path,
            ]
        ));

        return response()->json([
            'message' => 'User successfully updated',
            'user' => $user,
        ], 201);
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user_logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user_Profile() {
        return response()->json(['user' => auth()->user()]);
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
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
