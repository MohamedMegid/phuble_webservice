<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateUser;
use App\User;
use Illuminate\Support\Facades\Validator;
use Laravolt\Avatar\Avatar;
use Image;
use App\Token;
use Auth;
use DB;

class UserController extends Controller {
    
    protected $uploadDestination = 'uploads/users/';
    
   

    public function register(Request $request) {
        $validation = Validator::make($request->all(), [
                    'first_name' => 'required|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'username' => 'required|string|alpha_dash|max:60|unique:users',
                    'email' => 'required|string|email|max:190|unique:users',
                    'password' => 'required|string|min:6|max:30',
        ]);
        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json(['status'=>401,'data'=>$errors], 401);
        }
        $input = $request->all();
        $host =config('app.HOST_NAME');
//        dd($host.$this->uploadDestination);
        $destination = storage_path($host.$this->uploadDestination);

        $avatar = new Avatar();
        $file_name = str_random(5) . '.' .'jpg';
        $image=$avatar->create($input['first_name'].' '. $input['last_name'])
        ->setBackground('#47b5c0')
        ->getImageObject();

        $file_name = time() . '.jpg';

        $path2 = public_path($this->uploadDestination . $file_name);
        $image->save($path2);
        $input['profile_img']=$file_name;
        $input['profile_img']=$file_name;
        $input['wall_img']='4.jpg';
        $input['password']= bcrypt($request->password);
        //create user
        $user = User::create($input);
//        create token
        $token=Token::create(['user_id'=>$user->id,'user_token'=>str_random(60),'device_token'=>$request->header('device_token')]);
        $input['wall_img']= $this->uploadDestination.$input['wall_img'];
        $input['profile_img']= $this->uploadDestination.$input['profile_img'];
        $input['user_token']=$token->user_token ;
       unset($input['password']);
//       dd($input);
       $user->notify(new \App\Notifications\ActivateAccountNotification($user));
        return response()->json(['status'=>201,'data'=>$input], 201);
    }
    
    
    public function login(Request $request) {
        $validation = Validator::make($request->all(), [
                    'email' => 'required|string|email|max:190',
                    'password' => 'required|string|min:6|max:30',
        ]);
        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json(['status' => 401, 'data' => $errors], 401);
        }
        $credentials = ['email' => $request->email, 'password' => $request->password];
        //check login
        if (Auth::attempt($credentials)) {
            //check verification
            $user= User::where('email',$request->email)->first();
//            dd($user->verified);
            if(!$user->verified)
            {
//                dd('here');
               return response()->json(['status' => 200, 'message' => 'User Not Verified Please Check You Email'], 200); 
            }
                
            //change user token 
            $token = Token::where('device_token',$request->header('device_token'))->first();
            $token->user_token=str_random(60);
            $token->save();
            return response()->json(['status' => 200, 'user_token' => $token->user_token], 200);
        }
        return response()->json(['status' => 403, 'message' => 'These credentials do not match our records.'], 401);
    }
    
    
    public function forget_password(Request $request)
    {
        
        $validation = Validator::make($request->all(), [
                    'email' => 'required|string|email|max:190',
                   
        ]);
        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json(['status' => 401, 'data' => $errors], 401);
        }
        $user= User::where('email',$request->email)->first();
        if(!$user)
            return response()->json(['status' => 401, 'message' => 'Invalid E-mail'], 401);
            
        DB::table('password_resets')->insert(
                ['email' => $user->email, 'token' => str_random(60)]
        );
        $password_token=DB::table('password_resets')->where('email',$request->email)->first();
        
        $host =config('app.HOST_NAME');
        $user->notify(new \App\Notifications\ForgetPasswordNotification($host, $password_token));
        
        return response()->json(['status' => 200, 'message' => 'Check You Email'], 200); 
    }

}
