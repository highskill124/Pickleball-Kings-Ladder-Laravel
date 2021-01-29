<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Matches;
use App\Models\Seasons;
use App\Models\UserMatchesLadderRank;
use App\Models\UserMatchesRankFactor;
use App\Models\UserPaidRankings;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Validator;

class MatchesController extends Controller
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
        $requests = Matches::with('request')->with('request.to')->with('request.by')->get();
        if ($requests) {
            foreach ($requests as $key => $value) {
                # code...
                if ($value['request'] && $value['request']['time']) {
                    $value['request']['date'] =  Carbon::parse($value['request']['time'])->format('M d, Y');
                    $value['request']['date_time'] = date("h.i A", strtotime($value['request']['time']));
                }
                if ($value['played']) {
                    $value['played_date'] =  Carbon::parse($value['played'])->format('M d, Y');
                    $value['played_time'] = date("h.i A", strtotime($value['played']));
                }
                $value['responded_at'] =  Carbon::parse($value['request']['updated_at'])->format('M d, Y');
                // $value['dd']
            }
            return $requests;
        }
        return $requests;
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
        $match = Matches::with('request')->with('request.to')->with('request.by')->findOrFail($id);
        return $match;
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
        $this->validate($request, [
            'played' => 'required|date',
            'by' => 'required|exists:users,id',
            'to' => 'required|exists:users,id',
            'match_ladder' => 'required',
            'match_rank' => 'required',
            'season_id'=>'required'
        ]);
        /* Logic for implementing user points **/

            $season = Seasons::findOrFail($request->season_id);
            $res = Carbon::create($request->played)->between($season->start_date, $season->end_date);
            if(!$res){
                $season_start_date =  Carbon::parse($season->start_date)->format('M d, Y');
                // $season_start_time = date("h.i A", strtotime($season->start_date));
                $season_end_date =  Carbon::parse($season->end_date)->format('M d, Y');
                // $season_end_time = date("h.i A", strtotime($season->end_date));

                return response(['errors' => ['played_error' => "Played date is not in season start and end dates. Please select date from ".$season_start_date." to ". $season_end_date ]], 422);
            }
            $period = CarbonPeriod::between($season->start_date, $season->end_date);
            $days = [];
            $played=$request->played;
           $start_date= Carbon::create($season->start_date);
           $end_date = Carbon::create($played);
           $week = $start_date->diffInDays($end_date)/7;
            $week_number =  is_float($week) ? ((int)($week)) +1 : (int)($week);
            // foreach ($period as $key=>$date) {
            //     // return $key;
            //     $day = $date->format('m-d');
            //     $days[] = $day;
            //     if ($day ===Carbon::parse($request->played)->format('m-d')) {
            //         // $period->skip(3);
            //       return  Carbon::parse($request->played)->format('W');
            //     }
            // }
            // return $res;
            // $dt = Carbon::parse($request->played)->format('W');
            
            // // return $dt->weekOfMonth;
            // $period = CarbonPeriod::create($season->start_date, $season->end_date);
            // return $period;
        /** check if both have already ranking */
            $user_to_rank = UserMatchesLadderRank::where('user_id', $request->to)->where('match_ladder_id',$request->match_ladder)->first();
            $user_by_rank = UserMatchesLadderRank::where('user_id', $request->by)->where('match_ladder_id',$request->match_ladder)->first();
    
            $match = Matches::findOrFail($id);
            $match->played = $request->played;
            $match->point1_user1 = isset($request->point1_user1) ? $request->point1_user1 : '';
            $match->point2_user1 = isset($request->point2_user1) ? $request->point2_user1 : '';
            $match->point3_user1 = isset($request->point3_user1) ? $request->point3_user1 : '';
            $match->point1_user2 = isset($request->point1_user2) ? $request->point1_user2 : '';
            $match->point2_user2 = isset($request->point2_user2) ? $request->point2_user2 : '';
            $match->point3_user2 = isset($request->point3_user2) ? $request->point3_user2 : '';

            $to_object = UserMatchesRankFactor::where('user_id', $request->to)->where('matches_id',$id)->first();
            $by_object = UserMatchesRankFactor::where('user_id', $request->by)->where('matches_id',$id)->first();
            
        if ($user_to_rank && $user_by_rank) {         
            
            if($to_object){
                $user_to_rank->rank_points = $user_to_rank->rank_points- $to_object->earned_points;
                $to_object->delete();
            }
            if($by_object){
                $user_by_rank->rank_points = $user_by_rank->rank_points- $by_object->earned_points;
                $by_object->delete();
            }
            /* method for calculating ladder points to both users */
           
            $calculatios = $this->calculatePoints($request, $user_by_rank->rank_points, $user_to_rank->rank_points);

            $rankings = false;
            
            if($calculatios){
                $rankings = $this->SaveRankings($calculatios['user1'], $calculatios['user2'],$request->match_ladder,$week_number, false);
            }          


            if ($rankings && $match->save()) {
                return response(null, 200);
            } else {
                return response($calculatios, 400);
            }
        } else {
            /* method for getting initial points of both users */
            $user_to_rank = $this->calculateRank($request->match_rank);
            $user_by_rank = $this->calculateRank($request->match_rank);

            $calculatios =  $this->calculatePoints($request, $user_by_rank, $user_to_rank);

            $rankings = false;
            if($calculatios){
                $rankings = $this->SaveRankings($calculatios['user1'], $calculatios['user2'],$request->match_ladder,$week_number, true);
            }          

            if ($rankings && $match->save()) {
                return response(null, 200);
            } else {
                return response($calculatios, 400);
            }
            // return $user_to_rank;
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
        //
    }

    /** this method will calculates initial rank of both users */
    public static function calculateRank($rank_id)
    {
        $count = UserPaidRankings::where('match_rank_categories_id', $rank_id)->count();
        return $count;
    }

    public static function calculatePoints($data, $user1_rank, $user2_rank)
    {
        
        $user1_counts = 0;
        $user2_counts = 0;
        $user1_win_points = 0;
        $user2_win_points = 0;
        /** user1 have win 1 and 3rd set  */

        if ($data->point1_user1 > $data->point1_user2) {
            $user1_counts = $user1_counts + 1;
            $user1_win_points = $user1_win_points + ($data->point1_user1 - $data->point1_user2);
        } else {
            $user2_counts = $user2_counts + 1;
            $user2_win_points = $user2_win_points + ($data->point1_user2 - $data->point1_user1);
        }

        if ($data->point2_user1 > $data->point2_user2) {
            $user1_win_points = $user1_win_points + ($data->point2_user1 - $data->point2_user2);
            $user1_counts = $user1_counts + 1;
        } else {
            $user2_counts = $user2_counts + 1;
            $user2_win_points = $user2_win_points + ($data->point2_user2 - $data->point2_user1);
        }

        if ($data->point3_user1 > $data->point3_user2) {
            $user1_win_points = $user1_win_points + ($data->point3_user1 - $data->point3_user2);
            $user1_counts = $user1_counts + 1;
        } else {
            $user2_counts = $user2_counts + 1;
            $user2_win_points = $user2_win_points + ($data->point3_user2 - $data->point3_user1);
        }
        

        if ($user1_counts > $user2_counts) { // User 1 is winner

            // Calculatig winner points according to Algorithem

            
            // Case 1: User 1 (Winner) curent rank < User 2(Loser) Current Rank

            
            // ::FORMULA::   If higher-ranked: 10 plus Difference in games in sets won  :: 
            if ($user1_rank <  $user2_rank) {
                $user1_rank = 10 + $user1_win_points;
            }

            // Case 2: User 1 (Winner) curent rank > User 2(Loser) Current Rank
            // ::FORMULA::   If lower-ranked: 15 plus ((Difference in points in games won times the Difference in rankings up to 7) divided by 4)  :: 

            elseif ($user1_rank > $user2_rank) {
                /* for lower ranks */
                $diff = $user1_rank - $user2_rank;
                if($diff>7){
                    $diff =7;
                }
                $user1_rank = 15 + (($user1_win_points * $diff) / 4);
            }
            // Case 3: User 1 (Winner) curent rank == User 2(Loser) Current Rank
            // ::FORMULA::   If tied: 15 plus Difference in games in sets won :: 

            elseif ($user1_rank == $user2_rank) {
                $user1_rank = 15 + $user1_win_points;
            }

            // calculate points for loser (user2)
            $user2_rank = $user2_win_points;
            if($user2_rank>12){
                $user2_rank = 12;
            }
            // Save into DB = two entries => one for User1 (winner) and one for User2 (loser)

            // user1 is who created or purpose a challenge


            $user1_score = new UserMatchesRankFactor;
            $user1_score->user_id = $data->by;
            $user1_score->matches_id = $data->id;
            $user1_score->earned_points = $user1_rank;
            $user1_score->win_loss_status = '1';
            $user1_score->ladder_id = $data->match_ladder;
            $user1_score->save();

            $user2_score = new UserMatchesRankFactor;
            $user2_score->user_id = $data->to;
            $user2_score->matches_id = $data->id;
            $user2_score->earned_points = $user2_rank;
            $user2_score->win_loss_status = '0';
            $user2_score->ladder_id = $data->match_ladder;
            $user2_score->save();

            return ['user1'=>$user1_score, 'user2'=>$user2_score];
        } elseif ($user2_counts > $user1_counts) { // User 1 is LOSS
            
             // Calculatig winner points according to Algorithem for user2
    
            // Case 1: User 2 (Winner) curent rank < User 1(Loser) Current Rank

            // ::FORMULA::   If higher-ranked: 10 plus Difference in games in sets won  :: 
            if ($user2_rank <  $user1_rank) {
               
                $user2_rank = 10 + $user2_win_points;
                // return '<  '.$user2_rank;
                
            }

            // Case 2: User 2 (Winner) curent rank > User 1(Loser) Current Rank
            // ::FORMULA::   If lower-ranked: 15 plus ((Difference in games in sets won times the Difference in rankings up to 7) divided by 4)  :: 

            elseif ($user2_rank > $user1_rank) {
               
                /* for lower ranks */
                $diff = $user2_rank - $user1_rank;
                if($diff>7){
                    $diff =7;
                }
                $user2_rank = 15 + (($user2_win_points * $diff) / 4);
                // return '>  '.$user2_win_points;
            }
            // Case 3: User 1 (Winner) curent rank == User 2(Loser) Current Rank
            // ::FORMULA::   If tied: 15 plus Difference in games in sets won :: 

            elseif ($user1_rank == $user2_rank) {
                $user2_rank = 15 + $user2_win_points;
            }

            // calculate points for loser (user1)
            // $user1_rank = 10;
            // $user1_points = $data->point1_user1 + $data->point2_user1 + $data->point3_user1;
            
            // if($user1_points>10){
            //     $user1_rank = 10;
            // }
            $user1_rank = $user1_win_points;
            if($user1_rank>12){
                $user1_rank = 12;
            }

            // Save into DB = two entries => one for User1 (winner) and one for User2 (loser)

            // user1 is who created or purpose a challenge
           
            $user1_score = new UserMatchesRankFactor; 
            $user1_score->user_id = $data->by;
            $user1_score->matches_id = $data->id;
            $user1_score->earned_points = $user1_rank;
            $user1_score->win_loss_status = '0';
            $user1_score->ladder_id = $data->match_ladder;
            $user1_score->save();


            $user2_score = new UserMatchesRankFactor;
            $user2_score->user_id = $data->to;
            $user2_score->matches_id = $data->id;
            $user2_score->earned_points = $user2_rank;
            $user2_score->win_loss_status = '1';
            $user2_score->ladder_id = $data->match_ladder;
            $user2_score->save();
            return ['user1'=>$user1_score, 'user2'=>$user2_score];
        }
    }


    /* method for saving ranking for both users
    */
    public static function SaveRankings($user1, $user2, $laader_id,$week=1, $is_new=true){
       
        /* is_new suggests is data is already saved or new record*/

        if($is_new){
            /* saving user1 ladder ranks in user match ladder rank*/
        $user1_ladder_ranks = new UserMatchesLadderRank;
        $user1_ladder_ranks->user_id = $user1['user_id'];
        $user1_ladder_ranks->rank_points = $user1['earned_points'];
        $user1_ladder_ranks->week = $week;
        $user1_ladder_ranks->match_ladder_id = $laader_id;
        $user1_ladder_ranks->save();


        /* saving user2 ladder ranks in user match ladder rank*/
        $user2_ladder_ranks = new UserMatchesLadderRank;
        $user2_ladder_ranks->user_id = $user2['user_id'];
        $user2_ladder_ranks->rank_points = $user2['earned_points'];
        $user2_ladder_ranks->week = $week;
        $user2_ladder_ranks->match_ladder_id = $laader_id;
        $user2_ladder_ranks->save();

        return true;
        }
        else{
            /**updating already existing rankings   */
            $user1_ladder_ranks = UserMatchesLadderRank::where('user_id', $user1['user_id'])->where('match_ladder_id',$laader_id)->firstOrFail();
            $user1_ladder_ranks->user_id = $user1['user_id'];
            $user1_ladder_ranks->rank_points = $user1['earned_points'];
            $user1_ladder_ranks->week = 8;
            $user1_ladder_ranks->match_ladder_id = $laader_id;
            $user1_ladder_ranks->save();

            $user2_ladder_ranks = UserMatchesLadderRank::where('user_id', $user2['user_id'])->where('match_ladder_id',$laader_id)->firstOrFail();
            $user2_ladder_ranks->user_id = $user2['user_id'];
            $user2_ladder_ranks->rank_points =  $user2['earned_points'];
            $user2_ladder_ranks->week = 8;
            $user2_ladder_ranks->match_ladder_id = $laader_id;
            $user2_ladder_ranks->save();

            return true;

        } 
    }
    public function getByRankCategory(Request $request, $id){
        $requests = Matches::with('request')->whereHas('request', function($data) use ($id){
            $data->where('category', $id);
        })->with('request.to')->with('request.by')->get();
        if ($requests) {
            foreach ($requests as $key => $value) {

                $queryFactors = UserMatchesRankFactor::where('matches_id', $value->id)->get();
                if(isset($queryFactors[0]) && isset($queryFactors[1]) && $queryFactors[0]->earned_points > $queryFactors[1]->earned_points){
                    $value['LP'] =$queryFactors[1]->earned_points;
                    $value['WP'] =$queryFactors[0]->earned_points;
                }
                
                if(isset($queryFactors[0]) && isset($queryFactors[1]) && $queryFactors[1]->earned_points > $queryFactors[0]->earned_points){
                    $value['LP'] =$queryFactors[0]->earned_points;
                    $value['WP'] =$queryFactors[1]->earned_points;
                }


                # code...
                if ($value['request'] && $value['request']['time']) {
                    $value['request']['date'] =  Carbon::parse($value['request']['time'])->format('M d, Y');
                    $value['request']['date_time'] = date("h.i A", strtotime($value['request']['time']));
                }
                if ($value['played']) {
                    $value['played_date'] =  Carbon::parse($value['played'])->format('M d, Y');
                    $value['played_time'] = date("h.i A", strtotime($value['played']));
                }
                $value['responded_at'] =  Carbon::parse($value['request']['updated_at'])->format('M d, Y');

            }
            return $requests;
        }
        return $requests;
        // $requests = UserMatchesRankFactor::with(['match','user'])->with('match.request')->with('match.request.to')->with('match.request.by')->get();
        // return $requests;
    }
   public function getUserRankingByLadder(Request $request,$id)
    {
        if($request->filter_week){
            $requests = UserMatchesLadderRank::orderBy('rank_points', 'DESC')->where('match_ladder_id',$id)->where('week', $request->filter_week)->with('user')->get();
            return $requests;
        } else{
            $requests = UserMatchesLadderRank::orderBy('rank_points', 'DESC')->where('match_ladder_id',$id)->with('user')->get();
            
            $queryFactors = UserMatchesRankFactor::where('ladder_id', $id)->get();
            // return $queryFactors;
            $i = 0 ; 
            foreach ($requests as $key => $value) {
                
                $user_id = $value->user_id;

               $filteredWinner =  $queryFactors->filter(function ($item) use ($user_id) {
                    return $item->user_id == $user_id && $item->win_loss_status==1; 
                })->values();
                $filteredLosers =  $queryFactors->filter(function ($item) use ($user_id) {
                    return $item->user_id == $user_id && $item->win_loss_status==0; 
                })->values();

                $winerPoints = $filteredWinner->sum('earned_points');
                $loserPoints = $filteredLosers->sum('earned_points');
               
                if(isset($requests[$key]->earned_points) && isset($requests[$key+1]->earned_points) && $requests[$key]->earned_points == $requests[$key+1]->earned_points){
                    $i;
                } else{
                    $i++;
                }


               /** as we have calculated points above now we have to patch them with obj */
               $value['WP'] = $winerPoints;
               $value['LP'] = $loserPoints;
               $value['rank'] = $i;

            }
            return $requests;
        }      
    }
    public function filtertByRankCategory(Request $request)
    {
        // $query = User::query();
        // $authors = $query->get();
    }
}
