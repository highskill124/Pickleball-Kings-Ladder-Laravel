<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\adminChangeUserPassword;
use App\Mail\adminChangeUserSeason;
use App\Mail\adminDeleteUser;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\ForgetPasswordEmail;
use App\Mail\VerifyEmailAddress;
use App\Models\MatchLadders;
use App\Models\UserPaidRankings;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use PayPal\Api\Payment as Payments;
use Image;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified'])->except('store','emailVerified','passwordResetEmail','updateForgetPassword');
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
            $obj_data = new \stdClass();
            $obj_data->app_name = config('app.name');
            $obj_data->app_client = config('app.client');
            $obj_data->user = $user;
            Mail::to($user->email)->send(new VerifyEmailAddress($obj_data));
            if ($request->has('profile_picture') &&  $request->profile_picture != null) {
                $uploadFileName = $user->id . "_" . $request->profile_picture->getClientOriginalName();

                //checking and creating directory 
                if (!file_exists(public_path('images/users'))) {
                    mkdir(public_path('images/users'), 0755, true);
                }
                $image = $request->file('profile_picture');
                $input['imagename'] = $uploadFileName;
             
                $destinationPath = public_path('/images/users');
                // $img = Image::make($image->getRealPath());
                
                // $img->resize(500, 500)->save($destinationPath . '/' . $input['imagename']);
           
                // $destinationPath = public_path('/images');
                // $image->move($destinationPath, $input['imagename']);
                $request->profile_picture->move(public_path('images/users'), $uploadFileName);
                $user->profile_picture = $uploadFileName;
                $user->save();
            }
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
            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['singles'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_singles'])){           
            $request->validate([
                'additional_singles' => 'required|exists:match_rank_categories,id',
            ]);
            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['additional_singles'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['doubles'])){           
            $request->validate([
                'doubles' => 'required|exists:match_rank_categories,id',
            ]);

            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['doubles'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['double_partner'])){            
            $request->validate([
                'double_partner' => 'required|exists:match_rank_categories,id',
            ]);
            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['double_partner'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['double_second_partner'])){
            $request->validate([
                'double_second_partner' => 'required|exists:match_rank_categories,id',
            ]);
            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['double_second_partner'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_doubles'])){
            $request->validate([
                'additional_doubles' => 'required|exists:match_rank_categories,id',
            ]);

            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['additional_doubles'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_double_partner'])){
            $request->validate([
                'additional_double_partner' => 'required|exists:match_rank_categories,id',
            ]);

            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['additional_double_partner'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_double_second_partner'])){
            $request->validate([
                'additional_double_second_partner' => 'required|exists:match_rank_categories,id',
            ]);

            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['additional_double_second_partner'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();

            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['mixed_doubles'])){
            $request->validate([
                'mixed_doubles' => 'required|exists:match_rank_categories,id',
            ]);


            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['mixed_doubles'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();

            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['mixed_doubles_partner'])){
            $request->validate([
                'mixed_doubles_partner' => 'required|exists:match_rank_categories,id',
            ]);

            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['mixed_doubles_partner'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();

            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['mixed_doubles_second_partner'])){
            $request->validate([
                'mixed_doubles_second_partner' => 'required|exists:match_rank_categories,id',
            ]);

            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['mixed_doubles_second_partner'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();
            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_mixed_doubles'])){
            $request->validate([
                'additional_mixed_doubles' => 'required|exists:match_rank_categories,id',
            ]);
            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['additional_mixed_doubles'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();

            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_mixed_doubles_partner'])){
            $request->validate([
                'additional_mixed_doubles_partner' => 'required|exists:match_rank_categories,id',
            ]);

            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['additional_mixed_doubles_partner'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();

            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
            $paid_rankings->user_id = $user->id;
            $paid_rankings->save();
        }
        if(isset($input_data['additional_mixed_doubles_second_partner'])){
            $request->validate([
                'additional_mixed_doubles_second_partner' => 'required|exists:match_rank_categories,id',
            ]);

            $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$input_data['additional_mixed_doubles_second_partner'])->where('seasons_id', $request->season_id)->where('gender', $request->gender)->first();

            $paid_rankings = new UserPaidRankings();
            $paid_rankings->match_ladder_id = $ladder->id;
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
        $delete_entry = User::findOrFail($id);
        $user = $delete_entry;
        if ($delete_entry->delete()) {
            Mail::to($user->email)->send(new adminDeleteUser($user));
            return response(null, 200);
        } else {
            return response(null, 400);
        }
    }
                        /** CTA for verify email*/

    public function emailVerified(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->email_verified_at = date("Y-m-d h:i:s");
        $user->save();
        return response(null, 200);
    }
                    /** CTA for sending reset email */

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

            Mail::to($user->email)->send(new ForgetPasswordEmail($obj_data));
        } else {
            return response(['message' => ['errors' => ['email' => "We can't find a user with that email address."]]], 422);
        }
    }


                /** CTA for update user password by admin*/

    public function  adminUpdatePassword(Request $request, $id){
        $user = User::findOrFail($id);
        $this->validate($request, [
            'new_password' => 'required|max:50|min:3|different:current_password',
            'confirm_password' => 'required|string|max:50|min:3|same:new_password',
        ]);
        if($user->fill([
            'password' => Hash::make($request->new_password)
        ])->save()){
            Mail::to($user->email)->send(new adminChangeUserPassword($user));
            return response(null, 200);
        } else {
            return response(null, 400);
        }
    }


            /** CTA for update forget password given instructions in email token */

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

            /** CTA for updating forget password */
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

        /** CTA for getting paid user by id */

    public function withCategories($id){
        $categories = UserPaidRankings::where('user_id',$id)->get();
        return $categories;
    }

    /** CTA for getting paid users in a ladder */

    public function getPaidUserInLadder(Request $request, $id){
        
        $query = UserPaidRankings::query();
        if(isset($_GET['with_current'])){
            $query->with('user')->whereHas('user', function($user){
                $user->where('gender','=',$_GET['gender']); })->where('match_ladder_id', $id);

        } else{
            $query->with('user')->whereHas('user', function($user){
                $user->where('gender','=',$_GET['gender']); })->where('match_ladder_id', $id)->where('user_id','!=',auth()->user()->id);
        }
       $users = $query->get();
       
        return $users;
    }

    /** CTA for updating user season from admin */

    public function adminUpdateSeason(Request $request,$id){
        $this->validate($request, [
            'id' => 'required',
            'rank_category' => 'required',
            'season_id' => 'required',
        ]);
        $paid_rank = UserPaidRankings::findOrFail($id);
        $ladder = MatchLadders::select('id')->where('match_rank_categories_id',$request['rank_category'])->where('seasons_id', $request->season_id)->first();
        $paid_rank->match_ladder_id = $ladder->id;
        if ($paid_rank->save()) {
            $user = User::findOrFail($paid_rank->user_id);
            Mail::to($user->email)->send(new adminChangeUserSeason($user));
            return response(null, 200);
        } else {
            return response(null, 400);
        }

    }

/** CTA for getting users payment history from admin */

    public function getPaymenthistory(){
        try {
            $params = array('count' => 10, 'start_index' => 5);
        
            $payments = Payments::all($params, $apiContext);
        } catch (Exception $ex) {
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
           return ["List Payments", "Payment", null, $params, $ex];
            exit(1);
        }
    }
}
