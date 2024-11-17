<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\group;
use App\Models\doctor;
use App\Models\interview;
use App\Models\formal_program;
use App\Models\lab;
use App\Models\hall;
use App\Models\reserve;
use App\Models\Complaint;
use App\Models\file;

use Carbon;
use Validator;
use Datetime;

class doctor2_controller extends Controller
{
     /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('doctor:doctor-api');
    }

    public function myinterview(Request $request){
        return response()->json([
           'doctor_interview'=> auth()->guard('doctor-api')->user()->interviews()->where('state','reserve')
           ->orwhere('state','accept')->with('group')->get()
       ]
           , 200);
    }


public function control_interview(Request $request){
    $validator = Validator:: make($request->all(),[
        'interview_id'=>'required',
        'state'=>'required|in:reject,accept'

    ]);
    if($validator->fails()){
        return response()->json($validator->errors(), 422);
    }

    $interview2 =  auth()->guard('doctor-api')->user()->interviews()->get()->find($request->interview_id)->update([
        'state'=>$request->state,

    ]);
    return response()->json([
        'interview'=> auth()->guard('doctor-api')->user()->interviews()->get()->find($request->interview_id),
        'message' => 'interview has update success']
        , 200);
}


public function control_all_interviews(Request $request)
{
    $validator = Validator::make($request->all(), [
         'date'=>'required|date|date_format:Y-m-d',
         'state' => 'required|in:reject,accept'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $doctor = auth()->guard('doctor-api')->user();

    $interviews = $doctor->interviews()->where('date', $request->date)
    ->where('state', 'reserve')->get();

    if ($interviews->isEmpty()) {
        return response()->json(['error' => 'No interviews found for the given date'], 404);
    }

    foreach ($interviews as $interview) {
        $interview->update(['state' => $request->state]);
    }

    return response()->json([
        'message' => 'All interviews for the given date have been updated successfully',
        'interviews' => $interviews
    ], 200);
}


    public function add_note(Request $request){
    $validator = Validator:: make($request->all(),[
        'interview_id'=>'required',
        'note'=>'required|string'

    ]);
    if($validator->fails()){
        return response()->json($validator->errors(), 422);
    }

    $interview2 =  auth()->guard('doctor-api')->user()->interviews()->get()->find($request->interview_id)->update([
        'note'=>$request->note,

    ]);
    return response()->json([
        'interview'=> auth()->guard('doctor-api')->user()->interviews()->get()->find($request->interview_id),
        'message' => 'interview has update success']
        , 200);
}




public function change_interview(Request $request){
    $validator = Validator:: make($request->all(),[
        'interview_id'=>'required',
        'date'=>'required|date|date_format:Y-m-d',
        'from'=>'required|date_format:H:i',
        'to'=>'required|date_format:H:i',
        'state'=>'required|in:rejected,accept'

    ]);
    if($validator->fails()){
        return response()->json($validator->errors(), 422);
    }
    $list_interview = interview::where('doctor_id','=',Auth::guard('doctor-api')->user()->id)->where('date','=',$request->date)->get();

    foreach ( $list_interview as $interv) {
        $time = strtotime($interv->to);
        $time2= $time - (1*60);
        $date = date("H:i:s", $time2);

        if  ($interv->id != $request->interview_id && (
             ( $request->from >= $interv->from && $request->from <$date)||
             ($request->to > $interv->from && $request->to <= $interv->to) ||
             ($request->from <= $interv->from && $request->to >= $interv->to)
        ))

         {
            return response()->json([
                'message' => 'The time interview overlaps with an existing interview.',


            ], 422);
        }
    }
    $interview2 =  auth()->guard('doctor-api')->user()->interviews()->get()->find($request->interview_id)->update([
        'date' => $request->date,
        'from' => $request->from,
        'to' => $request->to,
        'state'=>$request->state,

    ]);
    return response()->json([
        'interview'=> auth()->guard('doctor-api')->user()->interviews()->get()->find($request->interview_id),
        'message' => 'interview has update success']
        , 200);
}

public function notes(Request $request){
    $validator = Validator::make($request->all(),[
        'group_id'=>'required|integer'
]);
if($validator->fails()){
    return response()->json($validator->errors(), 422);
}

$notes = interview::select('note')->where('doctor_id',Auth::guard('doctor-api')->user()->id)
->where('group_id',$request->group_id)->where('note','!=','null')->get();

return response()->json([
    'notes'=>$notes
], 200);

}

// public function my_formal(){
//    $name = auth('doctor-api')->user()->name;
//    $formal = formal_program::where('doctor', 'like', '%' . $name . '%')
//                       ->get();

//   return response()->json(['formal'=>$formal],200);



// }

public function my_formal()
{
    $loggedInName = auth('doctor-api')->user()->name;
    $allFormals = formal_program::all();
    $similarFormals = [];

    foreach ($allFormals as $formal) {
        similar_text($loggedInName, $formal->doctor, $percent);
        if ($percent > 90) { // نسبة التشابه المطلوبة
            $similarFormals[] = $formal;
        }
    }

    return response()->json(['formal' => $similarFormals], 200);
}





////////////////////////////////////////////reserve hall and labs/////////////////////////////
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




// public function available_place(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'date' => 'required|date|date_format:Y-m-d',
//         'from' => 'required|date_format:H:i',
//         'to' => 'required|date_format:H:i',
//     ]);

//     if ($validator->fails()) {
//         return response()->json($validator->errors(), 422);
//     }

//     $currentDate = date('Y-m-d');
//     if ($request->date < $currentDate) {
//         return response()->json([
//             'message' => 'The date cannot be before today.',
//         ], 422);
//     }

//     // Get all rooms and lecture halls
//     $labs = Lab::all();
//     $halls = Hall::all();

//     // Get reserved rooms and lecture halls for the specified date and time range
//     $date = $request->date;
//     $from = $request->from;
//     $to = $request->to;

//     // // Convert date to day of the week (1 = Sunday, 2 = Monday, ..., 7 = Saturday)
//     // $dayOfWeek = date('N', strtotime($date));

//     $dayOfWeek = date('w', strtotime($date)) + 1;


//     $reservedlabs = Reserve::where('date', $date)
//     ->where(function ($query) use ($from, $to) {
//         $query->where(function ($q) use ($from, $to) {
//             $q->where('from', '>=', $from)
//               ->where('from', '<', $to);
//         })
//         ->orWhere(function ($q) use ($from, $to) {
//             $q->where('to', '>', $from)
//               ->where('to', '<=', $to);
//         });
//     })
//     ->pluck('place');

// $reservedhalls = formal_program::where('day', $dayOfWeek)
//     ->where(function ($query) use ($from, $to) {
//         $query->where(function ($q) use ($from, $to) {
//             $q->where('from', '>=', $from)
//               ->where('from', '<', $to);
//         })
//         ->orWhere(function ($q) use ($from, $to) {
//             $q->where('to', '>', $from)
//               ->where('to', '<=', $to);
//         });
//     })
//     ->pluck('place');

//     // Get available rooms and lecture halls by excluding reserved ones
//     $availablelab1 = $labs->filter(function ($lab) use ($reservedlabs) {
//         return ! $reservedlabs->contains($lab->name);
//     });
//     $availablelab2 = $availablelab1->filter(function ($lab) use ($reservedhalls) {
//         return ! $reservedhalls->contains($lab->name);
//     });

//     $availablehall1 = $halls->filter(function ($hall) use ($reservedlabs) {
//         return ! $reservedlabs->contains($hall->name);
//     });
//     $availablehall2 =  $availablehall1->filter(function ($hall) use ($reservedhalls) {
//         return ! $reservedhalls->contains($hall->name);
//     });
//     // $availablelab = $availablelab1 + $availablelab2;
//     // $availablehall = $availablehall1 + $availablehall2;
//     return response()->json([
//         'available_labs' => $availablelab2,
//         'available_halls' => $availablehall2,
//        ], 200);
// }


// public function available_place1(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'date' => 'required|date|date_format:Y-m-d',
//         'from' => 'required|date_format:H:i',
//         'to' => 'required|date_format:H:i',
//     ]);

//     if ($validator->fails()) {
//         return response()->json($validator->errors(), 422);
//     }

//     $currentDate = date('Y-m-d');
//     if ($request->date < $currentDate) {
//         return response()->json([
//             'message' => 'The date cannot be before today.',
//         ], 422);
//     }


//     $labs = Lab::all();
//     $halls = Hall::all();


//     $date = $request->date;
//     $from = $request->from;
//     $to = $request->to;
//     $dayOfWeek = date('w', strtotime($date)) + 1;


//     $reservedlabs = Reserve::where('date', $date)
//         ->whereRaw("(? >= `from` AND ? < `to`) OR (? > `from` AND ? <= `to`)", [$from, $to, $from, $to])
//         ->pluck('place');

//     $reservedhalls = formal_program::where('day', $dayOfWeek)
//         ->whereRaw("(? >= `from` AND ? < `to`) OR (? > `from` AND ? <= `to`)", [$from, $to, $from, $to])
//         ->pluck('place');

//     // Get available rooms and lecture halls by excluding reserved ones
//     $availablelab = $labs->whereNotIn('name', $reservedlabs)->whereNotIn('name', $reservedhalls)->values();
//     $availablehall = $halls->whereNotIn('name', $reservedlabs)->whereNotIn('name', $reservedhalls)->values();

//     return response()->json([
//         $reservedlabs,
//         $reservedhalls,
//         'available_labs' => $availablelab,
//         'available_halls' => $availablehall,
//     ], 200);
// }


// public function reserve_place1(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'place'=>'required|string',
//         'date' => 'required|date|date_format:Y-m-d',
//         'from' => 'required|date_format:H:i',
//         'to' => 'required|date_format:H:i',
//         'reason'=>'nullable|string',
//     ]);

//     if ($validator->fails()) {
//         return response()->json($validator->errors(), 422);
//     }

//     $currentDate = date('Y-m-d');
//     if ($request->date < $currentDate) {
//         return response()->json([
//             'message' => 'The date cannot be before today.',
//         ], 422);
//     }

//     // Get all rooms and lecture halls
//     $labs = Lab::all();
//     $halls = Hall::all();

//     // Get reserved rooms and lecture halls for the specified date and time range
//     $date = $request->date;
//     $from = $request->from;
//     $to = $request->to;

//     // Convert date to day of the week (1 = Sunday, 2 = Monday, ..., 7 = Saturday)
//     $dayOfWeek = date('w', strtotime($date)) + 1;

//     $reservedlabs = Reserve::where('date', $date)
//         ->whereRaw("(? >= `from` AND ? < `to`) OR (? > `from` AND ? <= `to`)", [$from, $to, $from, $to])
//         ->pluck('place');

//     $reservedhalls = formal_program::where('day', $dayOfWeek)
//         ->whereRaw("(? >= `from` AND ? < `to`) OR (? > `from` AND ? <= `to`)", [$from, $to, $from, $to])
//         ->pluck('place');

//     // Get available rooms and lecture halls by excluding reserved ones
//     $availablelab = $labs->whereNotIn('name', $reservedlabs)->whereNotIn('name', $reservedhalls)->values();
//     $availablehall = $halls->whereNotIn('name', $reservedlabs)->whereNotIn('name', $reservedhalls)->values();

//         if ($availablelab->contains($request->place) || $availablehall->contains($request->place)) {
//             $reserve = reserve::create(array_merge(
//                 $validator->validated(),
//                 [
//                 'doctor_id'=> auth('doctor-api')->user()->id ,
//                 'doctor_name'=> auth('doctor-api')->user()->name,
//                 ]
//             ));
//             return response()->json([
//                 'message' => 'reserve in processing',
//                 'reserve' => $reserve
//             ], 200);

//         }else{
//         return response()->json([
//             'message' => 'place is reservel alredy',
//             'available_lab'=>$availablelab,
//             'available_hall'=>$availablehall,
//         ], 400);

//     }


// }




public function available_place(Request $request)
{
    // التحقق من صحة الإدخال
    $validator = Validator::make($request->all(), [
        'date' => 'required|date|date_format:Y-m-d',
        'from' => 'required|date_format:H:i',
        'to' => 'required|date_format:H:i',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // الحصول على التاريخ الحالي باستخدام Carbon
    $currentDate = \Carbon\Carbon::today()->format('Y-m-d');
    if ($request->date < $currentDate) {
        return response()->json([
            'message' => 'The date cannot be before today.',
        ], 422);
    }

    // الحصول على جميع الغرف والقاعات
    $labs = Lab::all();
    $halls = Hall::all();

    // الحصول على الغرف والقاعات المحجوزة في التاريخ والفترة الزمنية المحددة
    $date = $request->date;
    $from = $request->from;
    $to = $request->to;

    // تحويل التاريخ إلى اليوم من الأسبوع باستخدام Carbon
    $dayOfWeek = date('w', strtotime($date)) + 1;

    // استعلام للحصول على الغرف والقاعات المحجوزة
    $reservedlabs = Reserve::where('date', $date)
        ->where(function($query) use ($from, $to) {
            $query->where(function($query) use ($from, $to) {
                $query->where('from', '<', $to)
                      ->where('to', '>', $from);
            });
        })
        ->pluck('place');

    $reservedhalls = formal_program::where('day', $dayOfWeek)
        ->where(function($query) use ($from, $to) {
            $query->where(function($query) use ($from, $to) {
                $query->where('from', '<', $to)
                      ->where('to', '>', $from);
            });
        })
        ->pluck('place');

    // الحصول على الغرف والقاعات المتاحة عن طريق استبعاد المحجوزة
    $availablelabs = $labs->filter(function($lab) use ($reservedlabs, $reservedhalls) {
        return !$reservedlabs->contains($lab->name) && !$reservedhalls->contains($lab->name);
    })->values();

    $availablehalls = $halls->filter(function($hall) use ($reservedlabs, $reservedhalls) {
        return !$reservedlabs->contains($hall->name) && !$reservedhalls->contains($hall->name);
    })->values();

    return response()->json([
        'available_labs' => $availablelabs,
        'available_halls' => $availablehalls,
    ], 200);
}



public function reserve_place(Request $request)
{
    // التحقق من صحة الإدخال
    $validator = Validator::make($request->all(), [
        'place' => 'required|string',
        'date' => 'required|date|date_format:Y-m-d',
        'from' => 'required|date_format:H:i',
        'to' => 'required|date_format:H:i',
        'reason' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // الحصول على التاريخ الحالي باستخدام Carbon
    $currentDate = \Carbon\Carbon::today()->format('Y-m-d');
    if ($request->date < $currentDate) {
        return response()->json([
            'message' => 'The date cannot be before today.',
        ], 422);
    }

    // الحصول على الغرف والقاعات المحجوزة في التاريخ والفترة الزمنية المحددة
    $date = $request->date;
    $from = $request->from;
    $to = $request->to;

    // تحويل التاريخ إلى اليوم من الأسبوع باستخدام Carbon

    $dayOfWeek = date('w', strtotime($date)) + 1;
    // استعلام للحصول على الغرف والقاعات المحجوزة
    $reservedlabs = Reserve::where('date', $date)
        ->where(function($query) use ($from, $to) {
            $query->where(function($query) use ($from, $to) {
                $query->where('from', '<', $to)
                      ->where('to', '>', $from);
            });
        })
        ->pluck('place');

    $reservedhalls = formal_program::where('day', $dayOfWeek)
        ->where(function($query) use ($from, $to) {
            $query->where(function($query) use ($from, $to) {
                $query->where('from', '<', $to)
                      ->where('to', '>', $from);
            });
        })
        ->pluck('place');

    // التحقق من أن المكان غير محجوز
    if ($reservedlabs->contains($request->place) || $reservedhalls->contains($request->place)) {
        return response()->json([
            'message' => 'The place is already reserved.',
        ], 422);
    }

    // إنشاء الحجز
    $reserve = Reserve::create(array_merge(
        $validator->validated(),
        [
            'doctor_id' => auth('doctor-api')->user()->id,
            'doctor_name' => auth('doctor-api')->user()->name,
        ]
    ));

    return response()->json([
        'message' => 'Reserve in processing',
        'reserve' => $reserve
    ], 200);
}



public function my_reserves(Request $request){
    return response()->json([
       'doctor_reserve'=> auth()->guard('doctor-api')->user()->reserves()->get()
   ]
       , 200);
}

public function add_complaint(Request $request)
{
    $validator = Validator::make($request->all(), [
        'place' => 'required|string',
        'descreption' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $complaint = Complaint::create([
        'doctor_id' => auth('doctor-api')->user()->id,
        'doctor_name' => auth('doctor-api')->user()->name,
        'place' => $request->place,
        'descreption' => $request->descreption,
        'state' => 'processing'
    ]);

    return response()->json([
        'message' => 'Complaint added successfully',
        'complaint' => $complaint
    ], 200);
}

public function cancel_complaint(Request $request)
{

    $validator = Validator::make($request->all(), [
        'id' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }
    $complaint = Complaint::find($request->id);


    if (!$complaint) {
        return response()->json([
            'message' => 'Complaint not found or you are not authorized to cancel this complaint',
        ], 404);
    }

    $complaint->delete();

    return response()->json([
        'message' => 'Complaint cancelled successfully',
    ], 200);
}
public function my_complaints()
{
    return response()->json([
        'doctor_complaints'=> auth()->guard('doctor-api')->user()->complaints()->get()
    ]
        , 200);
}


public function upload_file(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'file' => 'required|file|mimes:pdf,doc,docx,zip,jpg,png',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }


    $filepath = '/' . $request->file('file')->store('files', 'public');

    $uploadedFile = File::create([
        'doctor_id' => auth('doctor-api')->user()->id,
        'name' => $request->name,
        'file' => $filepath,
    ]);
    return response()->json([
        'message' => 'File uploaded successfully',
        'file'=> $uploadedFile,

    ], 200);
}


public function delete_file(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $file = File::where('id', $request->id)
            ->where('doctor_id', auth('doctor-api')->user()->id)
            ->first();

        if (!$file) {
            return response()->json([
                'message' => 'File not found or you are not authorized to delete this file',
            ], 404);
        }

        Storage::disk('public')->delete($file->file);
        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully'
        ], 200);
    }

    // عرض الملفات الخاصة بالدكتور
    public function my_files()
    {
        $files = File::where('doctor_id', auth('doctor-api')->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'files' => $files
        ], 200);
    }





}
