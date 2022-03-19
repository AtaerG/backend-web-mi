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
            'name' => 'required|max:20',
            'surname' => 'required|max:50',
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
                'token' => $token,
                'user_role' => $token->token->scopes[0],
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } else {
            $user = Auth::user();
            if($user->email === $request->email){
                return response()->json(['error' => 'Error en contraseña!'], 403);
            }
        }
    }
    public function logout()
    {
        Auth::user()->token()->revoke();
        return response(['message' => 'You have been successfully logged out.'], 200);
    }
}
