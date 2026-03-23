<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;


class ProfileController extends Controller
{


    public function show(Request $request)
{
    $user = Auth::user();

    if (!$user) {
    return redirect('/login');
}

    $tab  = $request->query('tab', 'sell');



    $sellItems = Item::where('user_id', $user->id)
        ->latest()
        ->get();


    $buyItems = Order::with('item')
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    return view('profile.mypage', compact('user', 'tab', 'sellItems', 'buyItems'));
}


    public function edit()
    {

    if (!Auth::check()) {
        return redirect('/login');
    }

        $user = Auth::user();
        $profile = $user->profile ?? new Profile();
    return view('profile.edit', compact('profile', 'user'));
}



    public function store(ProfileRequest $request)
    {
        $request->validate([
            'name'     => ['required'],
        'postcode' => ['required'],
        'address'  => ['required'],
        'building' => ['nullable'],
        'image'    => ['nullable', 'image'],
    ]);

    dd($request->all(), $request->file('image'));

        $user = Auth::user();

        $user->profile()->create([
            'postcode'  => $request->postcode,
            'address'   => $request->address,
            'building'  => $request->building,
        ]);

        $user->profile_completed = 1;
        $user->save();

        return redirect('/mypage');
    }


    public function update(ProfileRequest $request)
{
    $user = Auth::user();


    $user->name = $request->name;
    $user->save();


    $data = [
        'postcode' => $request->postcode,
        'address'  => $request->address,
        'building' => $request->building,
    ];


    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('profiles', 'public');
        $data['image_url'] = $path;
    }

    $user->profile()->updateOrCreate(
        ['user_id' => $user->id],
        $data
    );

    $user->profile_completed = 1;
    $user->save();

    return redirect()->route('index');
}

}