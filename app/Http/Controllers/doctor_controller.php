<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\doctor;
use App\Models\code;
use App\Rules\ArabicOnly;
use File;
use Validator;

class doctor_controller extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('doctor', ['except' => ['doctor_login', 'doctor_register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function doctor_login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth('doctor-api')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function doctor_register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'between:2,100', new ArabicOnly()],
            'email' => 'required|string|email|max:100|unique:doctors',
            'password' => 'required|string|min:6',
            'specification' => 'nullable|string',
            'code'=>  'required|string',
            'phone' => 'nullable|numeric|digits:10',
            'photo' => 'nullable|mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $type = 'doctor';
        $existingCode = code::where('type',$type)->first();
        if($request->code != $existingCode->code){
            return response()->json([
                'message' => 'wrong code you are not authorized as a doctor',
            ], 403);


        }

        $path = null;
        if ($request->hasFile('photo')) {
            $filename = time() . '.' . $request->photo->extension();
            $path = '/images/' . $filename;
            $request->photo->move(public_path('/images/'), $filename);
        }

        $doctor = doctor::create(array_merge(
            $validator->validated(),
            [
                'specification'=> 'programming',
                'password' => bcrypt($request->password),
                'photo' => $path,
            ]
        ));

        return response()->json([
            'message' => 'doctor successfully registered',
            'doctor' => $doctor
        ], 201);
    }



       /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function doctor_update(Request $request)
    {
        $doctor = doctor::find(auth('doctor-api')->user()->id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|between:2,100',
            'email' => 'sometimes|required|string|email|max:100|unique:doctors,email,' . auth('doctor-api')->user()->id,
            'password' => 'sometimes|required|string|min:6',
            'specification' => 'sometimes|nullable|string',
            'phone' => 'sometimes|nullable|numeric|digits:10',
            'photo' => 'sometimes|nullable|mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $path = $doctor->photo;
        if ($request->hasFile('photo')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($doctor->photo && File::exists(public_path($doctor->photo))) {
                File::delete(public_path($doctor->photo));
            }

            // حفظ الصورة الجديدة
            $filename = time().'.'.$request->photo->extension();
            $path = '/images/' . $filename;
            $request->photo->move(public_path('/images/'), $filename);
        }

        $doctor->update(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'photo' => $path,
            ]
        ));

        return response()->json([
            'message' => 'User successfully updated',
            'doctor' => $doctor,
        ], 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function doctor_logout() {
        auth()->guard('doctor-api')->logout();
        return response()->json(['message' => 'doctor successfully signed out']);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function doctor_Profile() {
        return response()->json(['doctor'=>auth()->guard('doctor-api')->user()]);
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
            'expires_in' => auth('doctor-api')->factory()->getTTL() * 60,
            'doctor' => auth('doctor-api')->user()
        ]);
    }
}

