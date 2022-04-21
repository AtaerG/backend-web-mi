<?php

namespace App\Http\Controllers;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Http\Requests\MessageRequest;

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
    public function sendMessage(MessageRequest $request)
    {
        //$user = User::where('id', $request->get('user_id'))->first();
        broadcast(new MessageSent($request->get('id'), $request->get('name'), $request->get('message')))->toOthers();
        return ['name'=> $request->get('name'),'message'=> $request->get('message')];
    }
}
