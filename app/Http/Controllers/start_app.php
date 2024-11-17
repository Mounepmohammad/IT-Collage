<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\employe;
use App\Models\code;
use Illuminate\Support\Str;
use Validator;

class start_app extends Controller
{
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_employe(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:employes',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $employe = employe ::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)],
                ));
        return response()->json([
            'message' => 'employe successfully registered',
            'employe' => $employe
        ], 201);
    }

    public function generateCode()
{
    $type = 'doctor';

    // Check if a code with the same type already exists
    $existingCode = code::where('type',$type)->first();

    if ($existingCode) {
        // If a code exists, generate a new one and update the existing row
        $newCode = Str::random(6);
        $existingCode->update(['code' => $newCode]);
        return response()->json([
            'message' => 'code updated sucssesfuly',
            'code' => $existingCode
        ], 201);
    } else {
        // If no code exists, generate a new one and insert a new row
        $code1 = Str::random(6);
         $code = code::create([
            'type' => $type,
            'code' => $code1,
        ]);
        return response()->json([
            'message' => 'code updated sucssesfuly',
            'code' => $code
        ], 201);
    }
}
}
