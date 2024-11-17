<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\employe;
use App\Models\user_req;
use Validator;

class manager_controller extends Controller
{
       /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('employe:employe-api');
    }


    public function get_requests()
{
    // استرجاع الطلبات التي حالتها processing مرتبة حسب تاريخ التعديل الأحدث أولاً
    $requests = user_req::where('state', 'processing')
        ->orderBy('updated_at', 'desc')
        ->get();

        return response()->json([
            'requests' => $requests
        ], 200);
}



public function get_req_state(Request $request)
{
    // التحقق من صحة البيانات
    $validator = Validator::make($request->all(), [
        'state' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // استرجاع الطلبات بناءً على الحالة
    $requests = user_req::where('state', $request->state)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'requests' => $requests
        ], 200);
}

public function get_req_type(Request $request)
{
    // التحقق من صحة البيانات
    $validator = Validator::make($request->all(), [
        'type' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // استرجاع الطلبات بناءً على الحالة
    $requests = user_req::where('type', $request->type)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'requests' => $requests
        ], 200);
}




public function search(Request $request)
{
    // التحقق من صحة البيانات
    $validator = Validator::make($request->all(), [
        'word' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // البحث ضمن الطلبات بناءً على الاسم أو الرقم الجامعي أو المعرف
    $word = $request->word;

    $requests = user_req::where('student_name', 'LIKE', "%{$word}%")
        ->orWhere('collage_number', 'LIKE', "%{$word}%")
        ->orWhere('id', $word)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'requests' => $requests
        ], 200);
}

public function update_state(Request $request)
{
    // التحقق من صحة البيانات
    $validator = Validator::make($request->all(), [
        'id'=>'required|integer',
        'state' => 'required|string',
        'descreption' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // العثور على الطلب
    $requestToUpdate = user_req::find($request->id);

    if (!$requestToUpdate) {
        return response()->json(['message' => 'Request not found'], 404);
    }

    // تحديث حالة الطلب والتعليق (الوصف)
    $requestToUpdate->update([
        'state' => $request->state,
        'descreption' => $request->descreption,
    ]);

    return response()->json([
        'message' => 'Request state updated successfully!',
        'request' => $requestToUpdate
    ], 200);
}

}
