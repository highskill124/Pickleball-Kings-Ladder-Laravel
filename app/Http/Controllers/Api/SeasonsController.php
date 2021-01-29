<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchLadders;
use App\Models\MatchRankCategory;
use App\Models\MatchSingleDoubles;
use App\Models\Seasons;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SeasonsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified'])->except('index');
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
                $value['start_date'] = Carbon::parse($value['start_date'])->format('M d, Y');
                $value['end_date'] = Carbon::parse($value['end_date'])->format('M d, Y');
                $value['registration_deadline'] = Carbon::parse($value['registration_deadline'])->format('M d, Y');
                $value['playoff_date'] = Carbon::parse($value['playoff_date'])->format('M d, Y');
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
            'start_date' => 'required|date|unique:social_links,type',
            'end_date' => 'required|date',
            'registration_deadline' => 'required|date',
            'playoff_date' => 'required|date',
            'number_of_weeks'=>'required'
        ]);
        $season = new Seasons;
        $season->title = $request->title;
        $season->match_single_doubles_id = $request->match_single_doubles_id;
        $season->start_date = $request->start_date;
        $season->end_date = $request->end_date;
        $season->registration_deadline = $request->registration_deadline;
        $season->playoff_date = $request->playoff_date;
        $season->number_of_weeks = $request->number_of_weeks;
        if ($season->save()) {
            $match_categories = MatchRankCategory::where('match_single_doubles_id',$request->match_single_doubles_id)->get();
            $match_single_doubles = MatchSingleDoubles::findOrFail($request->match_single_doubles_id);

            /*    for mix loop one time */
            if ($match_single_doubles->id == 'ef0084e6-90cb-4dd0-8c49-5a622d4c5e33') {
                foreach ($match_categories as  $value) {
                    $match_ladder = new MatchLadders();
                    $match_ladder->title =  $value->title . ' ' . $match_single_doubles->title;
                    $match_ladder->seasons_id = $season->id;
                    $match_ladder->gender = "MX";
                    $match_ladder->match_rank_categories_id = $value->id;
                    $match_ladder->save();
                }
            } else {
                foreach ($match_categories as  $value) {
                    $match_ladder = new MatchLadders();
                    $match_ladder->title = "Men's " . ' ' . $value->title . ' ' . $match_single_doubles->title;
                    $match_ladder->seasons_id = $season->id;
                    $match_ladder->gender = "M";
                    $match_ladder->match_rank_categories_id = $value->id;
                    $match_ladder->save();
                }
                foreach ($match_categories as $key => $value) {

                    $match_ladder = new MatchLadders();
                    $match_ladder->title = "Women's " . ' ' . $value->title . ' ' . $match_single_doubles->title;
                    $match_ladder->seasons_id = $season->id;
                    $match_ladder->gender = "F";
                    $match_ladder->match_rank_categories_id = $value->id;
                    $match_ladder->save();
                }
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
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'registration_deadline' => 'required|date',
            'playoff_date' => 'required|date',
            'number_of_weeks' => 'required'
        ]);
        $season->title = $request->title;
        $season->start_date = $request->start_date;
        $season->end_date = $request->end_date;
        $season->registration_deadline = $request->registration_deadline;
        $season->playoff_date = $request->playoff_date;
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
}
