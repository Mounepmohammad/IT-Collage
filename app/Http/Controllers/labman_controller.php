<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\employe;
use App\Models\lab;
use App\Models\hall;
use App\Models\formal_program;
use App\Models\reserve;
use App\Models\static_doctor;
use App\Models\static_lecture;
use App\Models\complaint;

use Validator;

class labman_controller extends Controller
{

      /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('employe:employe-api');
    }


    public function static_doctors()
    {
        $doctors = static_doctor::all();
        return response()->json([
           'doctors'=> $doctors
        ], 200);
    }
    public function static_lectures()
    {
        $lectures = static_lecture::all();
        return response()->json([
           'lectures'=> $lectures
        ], 200);
    }

    public function labs()
    {
        $labs = lab::all();
        return response()->json([
           'labs'=> $labs
        ], 200);
    }
    public function halls()
    {
        $halls = hall::all();
        return response()->json([
           'halls'=> $halls
        ], 200);
    }


    // إنشاء معمل جديد
    public function add_lab(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'pc_number' => 'nullable|integer',
            'projector' => 'nullable|string|max:255',
            'descreption' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $lab = lab::create($validator->validated());

        return response()->json([
            'message'=> 'lab added sucssesfuly',
           'lab'=> $lab
        ], 200);
    }

    // إرجاع معمل محدد
    public function show_lab(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lab_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $lab = Lab::find($request->lab_id);

        if (!$lab) {
            return response()->json(['error' => 'Lab not found'], 404);
        }

        return response()->json([
           'lab'=> $lab
        ], 200);
    }

    // تحديث معمل محدد
    public function update_lab(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lab_id'=>'required',
            'name' => 'sometimes|required|string|max:255',
            'pc_number' => 'sometimes|nullable|integer',
            'projector' => 'sometimes|nullable|string|max:255',
            'descreption' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $lab = lab::find($request->lab_id);

        if (!$lab) {
            return response()->json(['error' => 'Lab not found'], 404);
        }

        $lab->update($validator->validated());

        return response()->json([
            'message'=> 'lab updated sucssesfuly',
           'lab'=> $lab
        ], 200);
    }

    // حذف معمل محدد
    public function delete_lab(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lab_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $lab = lab::find($request->lab_id);

        if (!$lab) {
            return response()->json(['error' => 'Lab not found'], 404);
        }

        $lab->delete();

        return response()->json(['message' => 'Lab deleted successfully'], 200);
    }



    public function add_hall(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'projector' => 'nullable|string|max:255',
            'descreption' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $hall = hall::create($validator->validated());

        return response()->json([
            'message'=> 'hall added sucssesfuly',
           'hall'=> $hall
        ], 200);
    }

    // إرجاع معمل محدد
    public function show_hall(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $hall = hall::find($request->hall_id);

        if (!$hall) {
            return response()->json(['error' => 'hall not found'], 404);
        }

        return response()->json([
           'hall'=> $hall
        ], 200);
    }

    // تحديث معمل محدد
    public function update_hall(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id'=>'required',
            'name' => 'sometimes|required|string|max:255',
            'projector' => 'sometimes|nullable|string|max:255',
            'descreption' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $hall = hall::find($request->hall_id);

        if (!$hall) {
            return response()->json(['error' => 'hall not found'], 404);
        }

        $hall->update($validator->validated());

        return response()->json([
            'message'=> 'hall updated sucssesfuly',
           'hall'=> $hall
        ], 200);
    }

    // حذف معمل محدد
    public function delete_hall(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $hall = hall::find($request->hall_id);

        if (!$hall) {
            return response()->json(['error' => 'hall not found'], 404);
        }

        $hall->delete();

        return response()->json(['message' => 'hall deleted successfully'], 200);
    }



    public function add_formal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'place' => 'required|string',
            'year' => 'required|string',
            'day' => 'required|string',
            'from'=>'required|date_format:H:i',
            'to'=>'required|date_format:H:i',
            'doctor' => 'required|string',
            'lecture' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
         $doctor = static_doctor::firstOrCreate(
            ['name' => $request->doctor],
        );
        $lecture = static_lecture::firstOrCreate(
            ['name' => $request->lecture],
        );

        $formal = formal_program::create($validator->validated());

        return response()->json([
            'message'=> 'program filed added sucssesfuly',
           'formal'=> $formal
        ], 200);
    }

    public function update_formal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formal_id'=>'required',
            'place' => 'sometimes|required|string',
            'year' => 'sometimes|required|string',
            'day' => 'sometimes|required|string',
            'from'=>'sometimes|required|date_format:H:i',
            'to'=>  'sometimes|required|date_format:H:i',
            'doctor' => 'sometimes|required|string',
            'lecture' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $formal = formal_program::find($request->formal_id);

        if (!$formal) {
            return response()->json(['error' => 'formal not found'], 404);
        }

        $formal->update($validator->validated());

        return response()->json([
            'message'=> 'program filed updated sucssesfuly',
           'formal'=> $formal
        ], 200);
    }

    public function delete_formal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formal_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $formal = formal_program::find($request->formal_id);

        if (!$formal) {
            return response()->json(['error' => 'formal not found'], 404);
        }

        $formal->delete();

        return response()->json(['message' => 'formal deleted successfully'], 200);
    }



    public function formal_day(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'day' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $formal_day = formal_program::where('day',$request->day)->get();

        return response()->json([
           'formal_day'=> $formal_day
        ], 200);
    }

    public function formal_place(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'place' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $formal_place = formal_program::where('place',$request->place)->get();

        return response()->json([
           'formal_place'=> $formal_place
        ], 200);
    }

    public function my_reserves(Request $request){
        return response()->json([
           'reserves'=> reserve::all()
       ]
           , 200);
    }

    public function control_reserve(Request $request){
        $validator = Validator:: make($request->all(),[
            'reserve_id'=>'required',
            'state'=>'required|in:reject,accept'

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $reserve = reserve::find($request->reserve_id)->update([
            'state'=>$request->state,

        ]);
        return response()->json([
            'reserve'=> reserve::find($request->reserve_id),
            'message' => 'reserve has update success']
            , 200);
    }




    public function my_complaints()
    {
        $complaints = Complaint::where('state','processing')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'complaints' => $complaints
        ], 200);
    }

    // تابع لتغيير حالة الشكوى إلى "تمت" (للإدارة)
    public function control_complaints(Request $request)
    {

         $validator = Validator:: make($request->all(),[
            'id'=>'required',
            'state'=>'required'

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $complaint = complaint::find($request->id)->update([
            'state'=>$request->state,

        ]);

        return response()->json([
            'message' => 'Complaint marked as completed',
            'complaint' => complaint::find($request->id)
        ], 200);
    }


}


