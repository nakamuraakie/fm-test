<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use App\Models\Favorite;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
public function index(Request $request)
{
    $tab = $request->query('tab', 'recommend');
    $keyword = $request->query('keyword');

    if ($tab === 'mylist' && !Auth::check()) {
        return view('index', ['items' => collect(), 'tab' => $tab, 'keyword' => $keyword]);
    }

    $query = Item::query();

    if ($tab === 'mylist') {
        $query->whereHas('favorites', function ($q) {
            $q->where('user_id', Auth::id());
        });
    } else {
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }
    }

    if (!empty($keyword)) {
        $query->where('name', 'like', "%{$keyword}%");
    }

    $items = $query->latest()->get();
    return view('index', compact('items', 'tab', 'keyword'));
}



    public function show(Item $item)
    {

    $likeCount = $item->favorites()->count();
    $isLiked = auth()->check()
    ? Favorite::where('user_id', auth()->id())->where('item_id', $item->id)->exists()
    : false;
    $likeCount = Favorite::where('item_id', $item->id)->count();


    $comments = $item->comments()->with('user')->latest()->get();
    $commentCount = $comments->count();

    $allCategories = Category::all();
    $item->load('categories');



    return view('detail', compact(
        'item',
        'likeCount',
        'commentCount',
        'comments',
        'isLiked',
        'allCategories'
    ));

    }


    public function purchase(Item $item)
{
    return view('purchase', compact('item'));
}


    public function create()
    {


    if (!auth()->check()) {
        return redirect()->route('login');
    }


        $categories = Category::all();
        $conditions = Condition::all();

        return view('create', compact('categories', 'conditions'));
    }


    public function store(ExhibitionRequest $request)
    {

        $imagePath = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id' => Auth::id(),
            'condition_id' => $request->condition_id,
            'name' => $request->name,
            'brand' => $request->brand,
            'description' => $request->description,
            'price' => $request->price,
            'image_url' => $imagePath,
            'status' => 'available',
        ]);


        $item->categories()->attach($request->categories);

        return redirect()->route('index')->with('success', '商品を出品しました');
    }


}