<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use App\Models\User;

class CommentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
    {
        if (Gate::denies('isAdmin')) {
            $comment = new Comment();
            $comment->content = $request->get('content');
            $comment->valoration = $request->get('valoration');
            $comment->user()->associate($request->get('user_id'));
            $comment->order()->associate($request->get('order_id'));
            $comment->product()->associate($request->get('product_id'));
            $comment->save();
            return response()->json($comment, 201);
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        if (!Gate::allows('isUsers', $comment)) {
            $comment->valoration = $request->get('valoration');
            $comment->content = $request->get('content');
            $comment->save();
            return response()->json($comment, 201);
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        if (Gate::allows('isUsers', $comment) || Gate::allows('isAdmin')) {
            $comment->delete();
            return response()->json(null, 204);
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }
}
