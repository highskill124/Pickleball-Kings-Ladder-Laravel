<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchLadders;
use App\Models\MatchRankCategory;
use App\Models\MatchSingleDoubles;
use App\Models\Seasons;
use App\Models\UserMatchesLadderRank;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SeasonsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified'])->except('index','getNextAvailableSeason','getRecentlyCompleted','getRecentlyCompletedSeason');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $seasons = Seasons::orderBy('start_date', 'ASC')->get();
        if ($seasons) {
            foreach ($seasons as $key => $value) {
                if(isset($value['start_date']) && $value['start_date']){
                    $value['start_date'] = Carbon::parse($value['start_date'])->format('M d, Y');
                }
                if(isset($value['end_date']) && $value['end_date']){
                    $value['end_date'] = Carbon::parse($value['end_date'])->format('M d, Y');
                }
                if(isset($value['registration_deadline']) && $value['registration_deadline']){
                    $value['registration_deadline'] = Carbon::parse($value['registration_deadline'])->format('M d, Y');
                }
                if(isset($value['playoff_date']) && $value['playoff_date']){
                    $value['playoff_date'] = Carbon::parse($value['playoff_date'])->format('M d, Y');
                }
            }
        }
        return $seasons;
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
        
        $this->validate($request, [
            'title' => 'required',
            'match_single_doubles_id' => 'required|exists:match_single_doubles,id',
            'dates_not_decided'=> 'required',
            'number_of_weeks'=>'required',
            'late_fee' =>'required'
        ]);
        $season = new Seasons;

        if($request->dates_not_decided != 1 || $request->dates_not_decided != true || $request->dates_not_decided != 'true'){
            $this->validate($request, [
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'registration_deadline' => 'required|date|before:start_date',
            'playoff_date' => 'required|date',
            'playoff_date2' => 'required|date',
            ]);
            $season->start_date = $request->start_date;
            $season->end_date = $request->end_date;
            $season->registration_deadline = $request->registration_deadline;
            $season->playoff_date = $request->playoff_date;
            $season->playoff_date2 = $request->playoff_date2;
        }       
        $season->title = $request->title;
        $season->match_single_doubles_id = $request->match_single_doubles_id;     
        $season->dates_not_decided = $request->dates_not_decided;
        $season->number_of_weeks = $request->number_of_weeks;
        $season->late_fee = $request->late_fee;
        if ($season->save()) {
            
            $match_categories = MatchRankCategory::with('matchsingledoubles')->get();

            foreach ($match_categories as $key => $data) {

                $match_ladder = new MatchLadders();
                $match_ladder->title = $data->title . ' ' . $data->matchsingledoubles->title;
                $match_ladder->seasons_id = $season->id;
                $match_ladder->gender = "M/F";
                $match_ladder->match_rank_categories_id = $data->id;
                $match_ladder->save();

                // /*    for mix loop one time */   // :: CLIENT NO MORE NEED MIX DOUBLES and NO MORE SEPARATE MEN AND WOMEN IN PICKLE BALL

                // if($data->match_single_doubles_id=='ef0084e6-90cb-4dd0-8c49-5a622d4c5e33'){
                //     $match_ladder = new MatchLadders();
                //     $match_ladder->title =  $data->title . ' ' . $data->matchsingledoubles->title;
                //     $match_ladder->seasons_id = $season->id;
                //     $match_ladder->gender = "MX";
                //     $match_ladder->match_rank_categories_id = $data->id;
                //     $match_ladder->save();
                // }
                // else{
                //     $match_ladder = new MatchLadders();
                //     $match_ladder->title =  "Men's " . ' ' . $data->title . ' ' . $data->matchsingledoubles->title;
                //     $match_ladder->seasons_id = $season->id;
                //     $match_ladder->gender = "M";
                //     $match_ladder->match_rank_categories_id = $data->id;
                //     $match_ladder->save();


                //     $match_ladder = new MatchLadders();
                //     $match_ladder->title =  "Women's " . ' ' . $data->title . ' ' . $data->matchsingledoubles->title;
                //     $match_ladder->seasons_id = $season->id;
                //     $match_ladder->gender = "F";
                //     $match_ladder->match_rank_categories_id = $data->id;
                //     $match_ladder->save();
                // }
                
            }
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
        $season = Seasons::findOrFail($id);
        return $season;
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
        $season = Seasons::findOrFail($id);

        $this->validate($request, [
            'title' => 'required',
            'dates_not_decided'=>'required',
            'number_of_weeks' => 'required',
            'late_fee' => 'required',
        ]);
        if($request->dates_not_decided != 1 || $request->dates_not_decided != true || $request->dates_not_decided != 'true'){
            $this->validate($request, [
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'registration_deadline' => 'required|date',
            'playoff_date' => 'required|date',
            'playoff_date2' => 'required|date',
            ]);
            $season->start_date = $request->start_date;
            $season->end_date = $request->end_date;
            $season->registration_deadline = $request->registration_deadline;
            $season->playoff_date = $request->playoff_date;
            $season->playoff_date2 = $request->playoff_date2;
        }
        $season->title = $request->title;
        $season->dates_not_decided = $request->dates_not_decided;
        $season->late_fee = $request->late_fee;
        $season->number_of_weeks = $request->number_of_weeks;
        if ($season->save()) {
            return response($season, 200);
        } else {
            return response(null, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete_entry = Seasons::findOrFail($id);
        if ($delete_entry->delete()) {
            return response(null, 200);
        } else {
            return response(null, 400);
        }
    }
    public function getNextAvailableSeason(){
        $season = Seasons::orderBy('start_date', 'ASC')->first();
        if($season){
            $weekMap = [
                0 => 'Sunday',
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wedesday',
                4 => 'Thrusday',
                5 => 'Friday',
                6 => 'Saturday',
            ];
            if(isset($season['start_date'])){
                $season['start_date_formated']=  $weekMap[Carbon::parse($season['start_date'])->dayOfWeek]. ' '.Carbon::parse($season['start_date'])->format('M d');
            }
            if(isset($season['end_date'])){
                $season['end_date_formated']=  $weekMap[Carbon::parse($season['end_date'])->dayOfWeek]. ' '.Carbon::parse($season['end_date'])->format('M d');
            }
            if(isset($season['registration_deadline'])){
                $season['registration_deadline_formated'] = Carbon::parse($season['registration_deadline'])->format('M d');
            }
            if(isset($season['playoff_date'])){
                $season['playoff1_formated'] = Carbon::parse($season['playoff_date'])->format('M d');
            }
            if(isset($season['playoff_date2'])){
                $season['playoff2_formated'] = Carbon::parse($season['playoff_date2'])->format('M d');
            }      
            
        }
        return $season;
    }

    public function getRecentlyCompleted(){
        $now = \Carbon\Carbon::today();
        $season = Seasons::where('end_date','<=',$now)->orderBy('end_date', 'ASC')->first(); 
        if($season){
            $results = MatchLadders::orderBy('title','ASC')->select('id','title','gender')->where('seasons_id', $season->id)->get()->groupBy('gender')->toArray();
            
            // to show them in order
            $mens = isset($results['M']) ? $results['M'] : [];
            $womens = isset($results['F']) ? $results['F'] : [];
            $mixed =  isset($results['MX']) ? $results['MX'] : [];
            $results =array_merge( $mens,  $womens);
            $ladders = array_merge( $results,  $mixed);
            $users = [];
            $i = 0;
            foreach ($ladders as $key => $value) {
               $winner = UserMatchesLadderRank::where('match_ladder_id',$value['id'])->orderBy('rank_points', 'desc')->with('match_ladder')->with('user')->first();
               
               if($winner){
                   $i++;
                   $val=new \stdClass();
                $loser = UserMatchesLadderRank::where('match_ladder_id',$value['id'])->where('rank_points', '<', $winner->rank_points)->with('match_ladder')->with('user')->first();
                $val->winner=$winner;
                $val->loser=$loser;
                $val->match_ladder = $value;
                array_push($users,$val);
               } else{
                $val=new \stdClass();
                $val->match_ladder = $value;
                array_push($users,$val);
               }
            }
    
            return $users;
        }
       
    }
    public function getRecentlyCompletedSeason()
    {
        $now = \Carbon\Carbon::today();
        $season = Seasons::where('end_date','<=',$now)->orderBy('end_date', 'ASC')->first(); 
        return $season;
    }
}
