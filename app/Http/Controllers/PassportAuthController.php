<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Facades\Mail;

class PassportAuthController extends Controller{
    /**
     * Registration
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:5',
            'surname' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'surname' => $request->surname,
            'password' => bcrypt($request->password),
            'role' => 'normal_user'
        ]);

        $token = $user->createToken('miauth')->accessToken;
        Mail::to("ataerg.web-designer@outlook.com")->send(new UserCreatedMail($user));
        return response()->json(['token' => $token], 200);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        if( Auth::attempt(['email'=>$request->email, 'password'=>$request->password]) ) {

            $user = Auth::user();
            $userRole = $user->role;

            if ($userRole) {
                $this->scope = $userRole;
            }

            $token = $user->createToken('MIWEB', [$this->scope]);

            return response()->json(
            [
                $token
            ]);
        }
    }
    public function logout()
    {
        Auth::user()->token()->revoke();
        return response(['message' => 'You have been successfully logged out.'], 200);
    }
}
