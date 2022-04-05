<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PasswordRequest;
use App\Models\User;
use App\Http\Requests\PasswordResetedRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PassportResetMail;

class PasswordController extends Controller
{
    public function forgot(PasswordRequest $request){

        $email = $request->get('email');

        $token = Str::random(10);

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token
        ]);

        return response()->json($token, 200);
        //Mail::to($email)->send(new PassportResetMail());
    }

    public function reset(PasswordResetedRequest $request){

        $token =  $request->token;
        /*
        if(!$passwordResets = DB::table('password_resets')->where('token', $token)->first()){
            return response()->json(['error'=>'Invalid token'], 400);
        }

        if(!$user = User::where('email', $passwordResets->email)->first()){
            return response()->json(['error'=>'User dont exist'], 404);
        }
        */
        $passwordResets = DB::table('password_resets')->where('token', $token)->first();
        $user = User::where('email', $passwordResets->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['message'=>'success'], 200);
    }
}