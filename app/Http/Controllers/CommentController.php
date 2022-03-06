<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comment = Comment::get();
        return response()->json($comment, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
    {
        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->stars = $request->get('stars');
        $comment->user()->associate($request->get('user_id'));
        $comment->product()->associate($request->get('product_id'));
        $comment->save();
        return response()->json($comment, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        return response()->json($comment, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        if (Gate::denies('update', $comment)) {
            abort(403);
        }
        $comment->stars = $request->get('stars');
        $comment->content = $request->get('content');
        $comment->save();
        return response()->json($comment, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $response = Gate::inspect('destroy', $comment);
        if ($response->allowed()) {
            $comment->delete();
            return response()->json(null, 204);
        } else {
            echo $response->message();
        }
    }
}
