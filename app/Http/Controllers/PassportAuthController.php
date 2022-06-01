<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\LoginRequest;

class PassportAuthController extends Controller
{
    /**
     * Registration
     */
    public function register(AuthRequest $request)
    {
        try {

            $token_recapV3 = filter_var($request->token_recapV3, FILTER_SANITIZE_STRING);
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = array('secret' => env('RECAPTCHAV3_SECRET'), 'response' => $token_recapV3);
            $options = array(
                'http' => array(
                    'method'  => 'POST',
                    'header'  => array('Content-type: application/x-www-form-urlencoded'),
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $result = json_decode(file_get_contents($url, false, $context));

            if ($result->success === FALSE) {
                return response()->json(['error' => 'Error! Eres robot!'], 504);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'surname' => $request->surname,
                'password' => bcrypt($request->password),
                'role' => 'normal_user'
            ]);
            $token = $user->createToken('miauth')->accessToken;
            try {
                Mail::to($user->email)->send(new UserCreatedMail($user));
            } catch (\Exception $e) {

            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el usuario'], 401);
        }
    }

    /**
     * Login
     */
    public function login(LoginRequest $request)
    {
        try {

            $token_recapV3 = filter_var($request->token_recapV3, FILTER_SANITIZE_STRING);
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = array('secret' => env('RECAPTCHAV3_SECRET'), 'response' => $token_recapV3);
            $options = array(
                'http' => array(
                    'method'  => 'POST',
                    'header'  => array('Content-type: application/x-www-form-urlencoded'),
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $result = json_decode(file_get_contents($url, false, $context));

            if ($result->success === FALSE) {
                return response()->json(['error' => 'Error! Eres robot!'], 504);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

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
                    ]
                );
            } else {
                $user = Auth::user();
                if ($user->email === $request->email) {
                    return response()->json(['error' => 'Error en contraseña!'], 403);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error!'], 500);
        }
    }
    public function logout()
    {
        try {
            Auth::user()->token()->revoke();
            return response(['message' => 'Exito'], 200);
        } catch (\Exception $e) {
            return response(['error' => 'Error!'], 500);
        }
    }
}
