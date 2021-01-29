<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\ForgetPasswordEmail;
use App\Models\Categories;
use App\Models\UserPaidRankings;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified'])->except('store','emailVerified');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(isset($_GET['gender'])){
            $users = User::where('gender', $_GET['gender'])->where('id','!=',auth()->user()->id)->get();
            return $users;
        }
        else{
            $users = User::all();
            return $users;
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
        $input_data = $request->all();
        $this->validate($request, [
            'first_name' => 'required|string|max:50|min:3',
            'last_name' => 'required|string|max:50|min:3',
            'email' => 'required|string|max:50|unique:users',
            'password' => 'required|string|max:50|min:3',
            'confirm_password' => 'required|string|max:50|min:3|same:password',            
        ]);

        $user = new User;
        $user->first_name = $input_data['first_name'];
        $user->last_name = $input_data['last_name'];
        $user->full_name = $user->first_name . ' ' . $user->last_name;
        $user->email = $input_data['email'];
        $user->password = Hash::make($input_data['password']);
        $user->phone = isset($input_data['phone']) ? $input_data['phone'] : '';
        $user->gender = isset($input_data['gender']) ? $input_data['gender'] : '';
        $user->address = isset($input_data['address']) ? $input_data['address'] : '';
        $user->city = isset($input_data['city']) ? $input_data['city'] : '';
        $user->state = isset($input_data['state']) ? $input_data['state'] : '';
        $user->zip_code = isset($input_data['zip_code']) ? $input_data['zip_code'] : '';
        $user->source = isset($input_data['source']) ? $input_data['source'] : '';
        $user->skill_level = isset($input_data['skill_level']) ? $input_data['skill_level'] : '';
        $user->get_proposal_emails = isset($input_data['get_proposal_emails']) ? $input_data['get_proposal_emails'] : '';     
        

        if ($user->save()) {
            $user->sendEmailVerificationNotification();     
            $this->paidCategories($request, $user);         
            return response(null, 200);
        } else {
            return response(null, 400);
        }

        //
    }
    public static function paidCategories($request, $user){
        $input_data = $request->all();
          /*
        generate paid rankings on user create
        */
        
        if(isset($input_data['singles'])){
            
            $request->validate([
                'singles' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['singles'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_singles'])){           
            $request->validate([
                'additional_singles' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['additional_singles'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['doubles'])){           
            $request->validate([
                'doubles' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['doubles'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['double_partner'])){            
            $request->validate([
                'double_partner' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['double_partner'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['double_second_partner'])){
            $request->validate([
                'double_second_partner' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['double_second_partner'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_doubles'])){
            $request->validate([
                'additional_doubles' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['additional_doubles'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_double_partner'])){
            $request->validate([
                'additional_double_partner' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['additional_double_partner'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_double_second_partner'])){
            $request->validate([
                'additional_double_second_partner' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['additional_double_second_partner'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['mixed_doubles'])){
            $request->validate([
                'mixed_doubles' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['mixed_doubles'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['mixed_doubles_partner'])){
            $request->validate([
                'mixed_doubles_partner' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['mixed_doubles_partner'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['mixed_doubles_second_partner'])){
            $request->validate([
                'mixed_doubles_second_partner' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['mixed_doubles_second_partner'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_mixed_doubles'])){
            $request->validate([
                'additional_mixed_doubles' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['additional_mixed_doubles'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_mixed_doubles_partner'])){
            $request->validate([
                'additional_mixed_doubles_partner' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['additional_mixed_doubles_partner'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_mixed_doubles_second_partner'])){
            $request->validate([
                'additional_mixed_doubles_second_partner' => 'required|exists:match_rank_categories,id',
            ]);
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_rank_categories_id = $input_data['additional_mixed_doubles_second_partner'];
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
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
        $input_data = $request->all();
        $this->validate($request, [
            'first_name' => 'required|string|max:50|min:3',
            'last_name' => 'required|string|max:50|min:3',
        ]);
        $user = User::findOrFail($id);
        $user->first_name = $input_data['first_name'];
        $user->last_name = $input_data['last_name'];
        $user->full_name = $user->first_name . ' ' . $user->last_name;
        if ($user->email != $input_data['email']) {
            $this->validate($request, [
                'email' => 'required|string|max:50|unique:users',
            ]);
        }
        $user->email = $input_data['email'];
        $user->phone = isset($input_data['phone']) ? $input_data['phone'] : '';
        $user->skill_level = isset($input_data['skill_level']) ? $input_data['skill_level'] : '';
        $user->get_proposal_emails = isset($input_data['get_proposal_emails']) ? $input_data['get_proposal_emails'] : '';
        $user->gender = isset($input_data['gender']) ? $input_data['gender'] : '';
        $user->address = isset($input_data['address']) ? $input_data['address'] : '';
        $user->city = isset($input_data['city']) ? $input_data['city'] : '';
        $user->state = isset($input_data['state']) ? $input_data['state'] : '';
        $user->zip_code = isset($input_data['zip_code']) ? $input_data['zip_code'] : '';
        if ($user->save()) {
            return response($user, 200);
        } else {
            return response(null, 400);
        }
        
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
    public function emailVerified(Request $request, $uid, $id)
    {
        $user = User::findOrFail($uid);
        $user->email_verified_at = date("Y-m-d h:i:s");
        $user->save();
        return  redirect()->away('/');
    }
    public function passwordResetEmail(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();
        if ($user) {
            $obj_data = new \stdClass();
            $obj_data->app_name = config('app.name');
            $obj_data->app_url = config('app.client');
            $obj_data->token = sha1(time());
            $user->remember_token = $obj_data->token;
            $user->save();
            $obj_data->user = $user;

            Mail::to($request->email)->send(new ForgetPasswordEmail($obj_data));
        } else {
            return response(['message' => ['errors' => ['email' => "We can't find a user with that email address."]]], 422);
        }
    }
    public function UpdatePassword(Request $request, $id){
        $user = User::findOrFail($id);
        $this->validate($request, [
            'new_password' => 'required|max:50|min:3|different:current_password',
            'confirm_password' => 'required|string|max:50|min:3|same:new_password',
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }],
        ]);
        if (Hash::check($request->current_password, $user->password)) {
            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            return response(null, 200);
        } else {
            return response(null, 400);
        }
    }
    public function updateForgetPassword(Request $request)
    {

        $user = User::where('email', '=', $request->email)->first();

        if ($request->token == $user->remember_token) {
            $this->validate($request, [
                'new_password' => 'required|max:50|min:3',
                'confirm_password' => 'required|string|max:50|min:3|same:new_password',
                'email' => 'required',
                'token' => 'required'
            ]);
            $user->password = Hash::make($request->new_password);
            if ($user->save()) {
                $user->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
                return response(null, 200);
            } else {
                return response(null, 400);
            }
        } else {
            return response(null, 406);
        }
    }
    public function withCategories($id){
        $categories = UserPaidRankings::where('user_id',$id)->with('matchrankcategories')->get();
        return $categories;
    }
    public function getPaidUserInLadder(Request $request, $id){
        if(isset($_GET['with_current'])){
            $users = UserPaidRankings::with('user')->whereHas('user', function($user){
                $user->where('gender','=',$_GET['gender']); })->where('match_rank_categories_id', $id)->get();

        } else{
            $users = UserPaidRankings::with('user')->whereHas('user', function($user){
                $user->where('gender','=',$_GET['gender']); })->where('user_id','!=',auth()->user()->id)->where('match_rank_categories_id', $id)->get();
        }
       
        return $users;
    }
}
