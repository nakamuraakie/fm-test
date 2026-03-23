<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;


class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {

    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'ログインが必要です');
    }

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'comment' => $request->comment,
        ]);

        return back();
    }
}
