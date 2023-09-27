<?php

namespace App\Http\Controllers;

use App\Events\CommentDeleted;
use App\Events\CommentedEpisodeEvent;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Episode;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except('index');
    }

    public function index($episodeId)
    {
        $episode = Episode::whereHas('lesson', fn ($q)  => $q->where('public', true))->findOrFail($episodeId);
        $comments = $episode->comments()->with('user')->orderBy('created_at', 'desc')->get();

        return response()->json(CommentResource::collection($comments));
    }

    public function store(Request $request, $episodeId)
    {
        $episode = Episode::whereHas('lesson', fn ($q)  => $q->where('public', true))->findOrFail($episodeId);
        $data = $request->validate(['body' => 'required|max:1500']);
        $comment = new Comment(['user_id' => $request->user()->id, 'body' => $data['body']]);
        $episode->comments()->save($comment);
        CommentedEpisodeEvent::dispatch($comment);

        $comment->load('user');

        return response()->json(CommentResource::make($comment));
    }

    public function destroy(Request $request, $commentId)
    {
        $comment = $request->user()->comments()->findOrFail($commentId);
        $comment->delete();
        CommentDeleted::dispatch($comment->toArray());

        return response()->noContent();
    }
}
