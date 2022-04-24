<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\LoginRequest;

class PassportAuthController extends Controller{
    /**
     * Registration
     */
    public function register(AuthRequest $request)
    {
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
    public function login(LoginRequest $request)
    {
        try{
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
                    'name' => $user->name,
                ]);
            } else {
                $user = Auth::user();
                if($user->email === $request->email){
                    return response()->json(['error' => 'Error en contraseña!'], 403);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error!'], 500);
        }
    }
    public function logout()
    {
        Auth::user()->token()->revoke();
        return response(['message' => 'You have been successfully logged out.'], 200);
    }
}
