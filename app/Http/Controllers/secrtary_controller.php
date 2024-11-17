<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\doctornotifications;
use App\Models\User;
use App\Models\group;
use App\Models\doctor;
use App\Models\interview;
use App\Models\employe;
use App\Models\code;
use Illuminate\Support\Str;
 use App\Http\Controllers\start_app;
use Validator;
use Datetime;

class secrtary_controller extends Controller
{

    private $start_app;
      /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(start_app $start_app ) {
        $this->middleware('employe:employe-api');
        $this->start_app = $start_app;
    }

    public function codes(Request $request){

       // return response()->json($this->start_app->generateCode(), 200);

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


    public function myinterview(Request $request){
        $this->delete_old_interviews();
        return response()->json([
           'secrtary_interview'=> interview::where('state','processing')->orderBy('created_at', 'desc')
           ->with('doctor')->get()
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
    public function doctor_interview(Request $request){


        $validator = Validator::make($request->all(), [
            'doctor_id'=> 'required',
            'date' => 'required|date|date_format:Y-m-d',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $interview = interview::where('doctor_id', $request->doctor_id)->where('date', $request->date)
                                   ->where(function($query) {
                                       $query->where('state', 'reserve')
                                             ->orWhere('state', 'accept');
                                   })
                                   ->get();
        return response()->json([
            'doctor_interviews'=>$interview
        ],200);
    }

    public function complete_interview(Request $request){
        $validator = Validator:: make($request->all(),[
            'interview_id'=>'required',
            'date'=>'required|date|date_format:Y-m-d',
            'from'=>'required|date_format:H:i',
            'to'=>'required|date_format:H:i',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
            $currentDate = date('Y-m-d');
            if ($request->date < $currentDate) {
                return response()->json([
                    'message' => 'The date cannot be before today.',
                ], 422);
            }


        $interview =  interview::find($request->interview_id);
        $id =  $interview->doctor()->first()->id;

        $list_interview = interview::where('doctor_id','=',$id)->where('date','=',$request->date)
        ->where('state','!=','reject')->get();


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
        $doctor =  $interview->doctor()->first();
        $doctor->notify(new doctornotifications(1,$interview));

        $interview2 = interview::where('id',$request->interview_id)->update([
            'date' => $request->date,
            'from' => $request->from,
            'to' => $request->to,
            'state' => 'reserve',

        ]);
        return response()->json([
            'message' => ' select time fo  interview is done',
           'interview'=>interview::where('id',$request->interview_id)->get() ,




       ]
           , 200);
    }

    public function delete_old_interviews()
{
    $currentDate = date('Y-m-d');

    $deletedCount = interview::whereIn('state', ['processing'])
        ->where('date', '<', $currentDate)
        ->delete();

    return response()->json([
        'message' => 'Old interviews deleted successfully',
        'deleted_count' => $deletedCount,
    ], 200);
}

public function getNotifications(Request $request)
{
    $user = auth('employe-api')->user();
    $notifications = $user->unreadNotifications->filter(function ($notification) {
        return $notification->type === 'App\Notifications\secrtarynotifications';
    });

    return response()->json(['notifications' => $notifications], 200, [], JSON_UNESCAPED_UNICODE);
}
}
