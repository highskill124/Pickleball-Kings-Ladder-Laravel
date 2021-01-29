<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialLinks;
use Illuminate\Http\Request;

class SocialLinksController extends Controller
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
        $socials = SocialLinks::all();
        return $socials;
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
            'type' => 'required|string|unique:social_links,type',
            'url' => 'required|string',
        ]);
        $socialLinks = new SocialLinks;
        $socialLinks->type = $request->type;
        $socialLinks->url = $request->url;
        if ($socialLinks->save()) {
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
        $link = SocialLinks::findOrFail($id);
        return $link;
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
        $socialLinks = SocialLinks::findOrFail($id);
        $this->validate($request, [
            'url' => 'required|string',
        ]);
        if ($socialLinks->type != $request->type) {
            $this->validate($request, [
                'type' => 'required|string|unique:social_links,type',
            ]);
        }
        $socialLinks->type = $request->type;
        $socialLinks->url = $request->url;
        if ($socialLinks->save()) {
            return response(null, 200);
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
        $delete_entry = SocialLinks::findOrFail($id);
        if ($delete_entry->delete()) {
            return response(null, 200);
        } else {
            return response(null, 400);
        }
    }
}
