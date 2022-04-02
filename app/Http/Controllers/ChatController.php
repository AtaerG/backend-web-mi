<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{

    /**
     * Fetch all messages
     *
     * @return Message
     */
    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        $user = User::where('id', $request->get('user_id'))->first();
        //var_dump($user);
        /*
        $message = $user->messages()->create([
            'message' => $request->get('message')
        ]);
        */
        broadcast(new MessageSent($user, $request->get('message')))->toOthers();
        return [];
    }
}
