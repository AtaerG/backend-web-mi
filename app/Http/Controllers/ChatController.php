<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Events\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function message(Request $request){
        event(new Message(Auth::user()->name, $request->input('message')));
        return ['user'=> Auth::user()->name, 'message'=> $request->input('message')];
    }
}
