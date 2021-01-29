<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\purposeAllEmail;
use App\Mail\purposeMail;
use App\Models\Matches;
use App\Models\Requests;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RequestsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requests = Requests::with('to')->with('by')->get();
        return $requests;
       
        //
    }
    public function getByType()
    {
        // dd($_GET);
        $requests = Requests::where('type',$_GET['type'])->with('to')->with('by')->with('match')->get();
        if($requests){
            foreach ($requests as $key => $value) {
                # code...
                if($value['time']){
                    $value['date']=  Carbon::parse($value['time'])->format('M d, Y');
                    $value['date_time']= date("h.i A", strtotime($value['time']));
                }
                
                $value['responded_at']=  Carbon::parse($value['updated_at'])->format('M d, Y');
                // $value['dd']
            }
            return $requests;
        }
       
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if($request->type!='purpose'){
            $this->validate($request, [
                'challenge_to' => 'required|exists:users,id|different:request_by',
                'request_by_gender'=>'required',
                'requested_to_gender' =>'required|same:request_by_gender'
            ]);
        }
        $this->validate($request, [
            'category'=>'required',
            'type' => 'required|',
            'request_by'=>'required|exists:users,id',
            'location'=>'required',
            'time'=>'required|date',
        ]);
        $requests = new Requests;
        $requests->category = $request->category;
        $requests->type = $request->type;
        if(isset($request->challenge_to)){
            $requests->request_to = isset($request->challenge_to) ? $request->challenge_to:'';
        }
       
        $requests->request_by = $request->request_by;
        $requests->location = $request->location;
        $requests->time = $request->time;
        if ($requests->save()) {
            $user = User::findOrFail($request->request_by);
            Mail::to('muzaffar.munir@nextscrum.dev')->send(new purposeMail($user));
            return response(null, 200);
        } else {
            return response(null, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete_entry = Requests::findOrFail($id);
        if ($delete_entry->delete()) {
            return response(null, 200);
        } else {
            return response(null, 400);
        }
    }
    public function purposeAll(Request $request){
        $this->validate($request, [
            'category'=>'required',
            'type' => 'required|',
            'request_by'=>'required|exists:users,id',
        ]);
        $user = User::findOrFail($request->request_by);
        $users = User::where('id','!=', $user->id)->get();
        if($user && $users){
            foreach ($users as $key => $value) {
                Mail::to('muzaffar.munir@nextscrum.dev')->send(new purposeAllEmail($user));
                # code...
            }
        }
        // Mail::to('muzaffar.munir@nextscrum.dev')->send(new purposeAllEmail($user));
        $requests = new Requests;
        $requests->category = $request->category;
        $requests->type = $request->type;
        $requests->status = 'emailed';
        $requests->request_by = $request->request_by;
        if ($requests->save()) {
            return response(null, 200);
        } else {
            return response(null, 400);
        }
    }
    public function acceptPurposal(Request $request){
        $this->validate($request, [
            'accepted_by'=>'required|exists:users,id',
            'purposal_id'=>'required|exists:requests,id',
        ]);
        $purposal= Requests::findOrFail($request->purposal_id);
        $purposal->request_to = $request->accepted_by;
        $purposal->status = 'accepted';
        $match = new Matches();
        $match->requests_id= $purposal->id;        
        if ($purposal->save() && $match->save()) {
            $purposal->matches_id = $match->id;
            $purposal->save();

            return response(null, 200);
        } else {
            return response(null, 400);
        }
    }
    public function unacceptPurposal(Request $request){
        $this->validate($request, [
            'purposal_id'=>'required|exists:requests,id',
        ]);
        $purposal= Requests::findOrFail($request->purposal_id);
        $purposal->request_to = null;
        $purposal->status = 'pending';
        if ($purposal->save()) {
            return response(null, 200);
        } else {
            return response(null, 400);
        }
    }
    public function getByRankCategory($id){
        $requests = Requests::where('type',$_GET['type'])->where('category',$id)->with('to')->with('by')->with('match')->get();
        if($requests){
            foreach ($requests as $key => $value) {
                # code...
                if($value['time']){
                    $value['date']=  Carbon::parse($value['time'])->format('M d, Y');
                    $value['date_time']= date("h.i A", strtotime($value['time']));
                }
                
                $value['responded_at']=  Carbon::parse($value['updated_at'])->format('M d, Y');
                // $value['dd']
            }
            return $requests;
        }
    }
}
