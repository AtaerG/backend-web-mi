<?php

namespace App\Http\Controllers;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Http\Requests\MessageRequest;

class ChatController extends Controller
{
    public function sendMessage(MessageRequest $request)
    {
        broadcast(new MessageSent($request->get('id'), $request->get('name'), $request->get('message')))->toOthers();
        return ['name'=> $request->get('name'),'message'=> $request->get('message')];
    }
}
