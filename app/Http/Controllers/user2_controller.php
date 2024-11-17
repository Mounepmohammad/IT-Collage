<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\secrtarynotifications;
use App\Models\User;
use App\Models\group;
use App\Models\doctor;
use App\Models\interview;
use App\Models\file;
use App\Models\employe;
use App\Models\user_req;

use Validator;

class user2_controller extends Controller
{
      /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api');
    }
     /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     public function create_group(Request $request){

        $validator = Validator::make($request->all() , [
            'student1'=> 'required|max:100|string',
            'student2'=> 'required|max:100|string',
            'student3'=>'nullable|max:100|string',
            'student4'=>'nullable|max:100|string',
            'student5'=> 'nullable|max:100|string',
            'student6'=> 'nullable|max:100|string',
            'type'=> 'nullable|max:100|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $group = group::create(array_merge(
            $validator->validated(),
            ['student_id'=> Auth::user()->id]

        ));
        return response()->json([
            'message' => 'group created sucsess',
            'group' => $group
        ], 200);


     }
     public function mygroups(Request $request){
         return response()->json([
            'groups'=> Auth::user()->groups()->get()
        ]
            , 200);
     }


     public function doctors(Request $request){
        $doctors = doctor::where('specification','programming')->get();
        return response()->json([
           'doctors'=> $doctors
       ]
           , 200);
    }
    public function all_doctors(Request $request){
        $doctors = doctor::all();
        return response()->json([
           'all_doctors'=> $doctors
       ]
           , 200);
    }



     public function create_interview(Request $request){

        $validator = Validator::make($request->all() , [
        'group_id'=> 'required|integer',
        'doctor_id'=>'required|integer',
        'goal'=>'required|string|max:100',
        'title'=>'nullable|string|max:100',
        'reason'=>'nullable|string|max:100',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $group = group::find($request->group_id);

        if (!$group) {
            return response()->json([
                'message' => 'group not found',
            ], 400);
        }

        $interview = interview::create(array_merge(
            $validator->validated(),
            ['student_id'=> Auth::user()->id ,]
        ));

        $employe = employe::find(1);
        $employe->notify(new secrtarynotifications($interview));

        return response()->json([
            'message' => 'interview in processing',
            'interview' => $interview
        ], 200, [], JSON_UNESCAPED_UNICODE);


     }
     public function myinterview(Request $request){
        return response()->json([
           'user_interview'=> Auth::user()->interviews()->get()
       ]
           , 200);
    }

    public function doctor_files(Request $request)

    {

        $validator = Validator::make($request->all() , [
            'doctor_id'=>'required'

        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $files = File::where('doctor_id', $request->doctor_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'doctor_files' => $files
        ], 200);


          }

// public function download_file(Request $request)
// {
//     $validator = Validator::make($request->all() , [
//         'file_id'=>'required'

//     ]);
//     if($validator->fails()){
//         return response()->json($validator->errors()->toJson(), 400);
//     }

//     $file = File::find($request->file_id);

//     if (!$file) {
//         return response()->json([
//             'message' => 'File not found',
//         ], 404);
//     }

//     $filePath = str_replace('public/', '', $file->file);
//     $filePath = storage_path('app/public/' . $filePath);

//     if (!file_exists($filePath)) {
//         return response()->json(['message' => 'File not found.'], 404);
//     }

//     return response()->download($filePath, $file->name);
// }

public function download_file(Request $request)
{
    $validator = Validator::make($request->all(), [
        'file_id' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    $file = File::find($request->file_id);

    if (!$file) {
        return response()->json([
            'message' => 'File not found',
        ], 404);
    }

    // الحصول على مسار الملف الصحيح من قاعدة البيانات
    $filePath = '/storage' . $file->file;

    // التأكد من أن الملف موجود في المسار الصحيح
    if (!file_exists(public_path($filePath))) {
        return response()->json([
            'message' => 'The file does not exist',
        ], 404);
    }

    // عرض الملف مباشرة في المتصفح
    return response()->file(public_path($filePath));
}

public function create_request(Request $request)
{
    // التحقق من صحة البيانات
    $validator = Validator::make($request->all(), [
        'student_name' => 'required|string',
        'collage_number' => 'nullable|string',
        'photo1' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'photo2' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'photo3' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'photo4' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'photo5' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'type' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

     // التحقق مما إذا كان هناك طلب سابق من نفس الطالب بنفس النوع
     $existingRequest = User_req::where('student_id', Auth::user()->id )
     ->where('type', $request->type)
     ->whereIn('state', ['processing']) // يمكنك تحديد الحالات التي تعتبر الطلب ما زال نشطًا
     ->first();

 if ($existingRequest) {
     return response()->json([
         'message' => 'You already have a request of the same type in process.',
     ], 400);
 }

    // تخزين الصور
    $data = $request->all();
    if ($request->hasFile('photo1')) {
        $data['photo1'] = '/'. $request->file('photo1')->store('photos', 'public');
    }
    if ($request->hasFile('photo2')) {
        $data['photo2'] = '/'. $request->file('photo2')->store('photos', 'public');
    }
    if ($request->hasFile('photo3')) {
        $data['photo3'] = '/'. $request->file('photo3')->store('photos', 'public');
    }
    if ($request->hasFile('photo4')) {
        $data['photo4'] = '/'. $request->file('photo4')->store('photos', 'public');
    }
    if ($request->hasFile('photo5')) {
        $data['photo5'] = '/'. $request->file('photo5')->store('photos', 'public');
    }

    // إنشاء الطلب
    $requestCreated = user_req::create(array_merge($data, [
        'student_id' => Auth::user()->id ,
        'student_name' => $request->student_name,
        'collage_number' => $request->collage_number,
        'state' => 'processing',
        'type' => $request->type,
    ]));

    return response()->json([
        'message' => 'Request created successfully!',
        'request' => $requestCreated
    ], 201);
}


public function update_request(Request $request)
{
    // التحقق من صحة البيانات
    $validator = Validator::make($request->all(), [
        'id'=>'required|integer',
        'student_name' => 'sometimes|required|string',
        'collage_number' => 'sometimes|nullable|string',
        'photo1' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'photo2' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'photo3' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'photo4' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'photo5' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'type' => 'sometimes|required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

     // العثور على الطلب
     $requestToUpdate = user_req::find($request->id);

     if (!$requestToUpdate) {
         return response()->json(['message' => 'Request not found'], 404);
     }
    // التأكد من حالة الطلب: يمكن التعديل فقط إذا كانت الحالة 'processing' أو 'modify'
    if (!in_array($requestToUpdate->state, ['processing', 'modify'])) {
        return response()->json(['message' => 'Request cannot be modified in this state'], 403);
    }




    if ($request->hasFile('photo1')) {
        // حذف الصورة القديمة
        if ($requestToUpdate->photo1 && Storage::disk('public')->exists($requestToUpdate->photo1)) {
            Storage::disk('public')->delete($requestToUpdate->photo1);
        }
        // تخزين الصورة الجديدة
        $data['photo1'] = '/'. $request->file('photo1')->store('photos', 'public');
    }

    if ($request->hasFile('photo2')) {
        if ($requestToUpdate->photo2 && Storage::disk('public')->exists($requestToUpdate->photo2)) {
            Storage::disk('public')->delete($requestToUpdate->photo2);
        }
        $data['photo2'] = '/'. $request->file('photo2')->store('photos', 'public');
    }

    if ($request->hasFile('photo3')) {
        if ($requestToUpdate->photo3 && Storage::disk('public')->exists($requestToUpdate->photo3)) {
            Storage::disk('public')->delete($requestToUpdate->photo3);
        }
        $data['photo3'] = '/'. $request->file('photo3')->store('photos', 'public');
    }

    if ($request->hasFile('photo4')) {
        if ($requestToUpdate->photo4 && Storage::disk('public')->exists($requestToUpdate->photo4)) {
            Storage::disk('public')->delete($requestToUpdate->photo4);
        }
        $data['photo4'] = '/'. $request->file('photo4')->store('photos', 'public');
    }

    if ($request->hasFile('photo5')) {
        if ($requestToUpdate->photo5 && Storage::disk('public')->exists($requestToUpdate->photo5)) {
            Storage::disk('public')->delete($requestToUpdate->photo5);
        }
        $data['photo5'] = '/'. $request->file('photo5')->store('photos', 'public');
    }
    // تحديث الطلب
    $requestToUpdate->update(array_merge($data, [
        'student_name' => $request->student_name,
        'collage_number' => $request->collage_number,
        'state' => 'processing',
        'type' => $request->type,
    ]));

    return response()->json([
        'message' => 'Request updated successfully!',
        'request' => $requestToUpdate
    ], 200);
}

public function my_request()
{
    // استرجاع الطلبات التي حالتها processing مرتبة حسب تاريخ التعديل الأحدث أولاً
    $requests = user_req::where('student_id', Auth::user()->id)->get();

        return response()->json([
            'requests' => $requests
        ], 200);
}




}

