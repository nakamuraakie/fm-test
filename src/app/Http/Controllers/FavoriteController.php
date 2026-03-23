<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle($itemId)
{
    $userId = Auth::id();
    if (!$userId) return redirect('/login');

    $query = Favorite::where('user_id', $userId)
        ->where('item_id', $itemId);

    if ($query->exists()) {
        $query->delete();
    } else {
        Favorite::create([
            'user_id' => $userId,
            'item_id' => $itemId,
        ]);
    }

    return back();
}

}
