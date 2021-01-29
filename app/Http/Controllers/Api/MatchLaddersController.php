<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchLadders;
use Illuminate\Http\Request;

class MatchLaddersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ladders =  MatchLadders::findOrFail($id);
        return $ladders;
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
        //
    }
    public function getBySeason($sid){
        $results =  MatchLadders::orderBy('title','ASC')->where('seasons_id', $sid)->get()->groupBy('gender')->toArray();
        $mens = isset($results['M']) ? $results['M'] : [];
        $womens = isset($results['F']) ? $results['F'] : [];
        $mixed =  isset($results['MX']) ? $results['MX'] : [];
        $results =array_merge( $mens,  $womens);
        $all = array_merge( $results,  $mixed);
        return $all;
    }
}
