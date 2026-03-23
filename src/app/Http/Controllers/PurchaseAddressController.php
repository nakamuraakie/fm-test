<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\AddressRequest;

class PurchaseAddressController extends Controller
{
    public function edit(Item $item)
    {
        $key = "purchase_address.{$item->id}";
        $address = session($key, [
            'postcode' => auth()->user()->profile->postcode ?? '',
            'address'  => auth()->user()->profile->address ?? '',
            'building' => auth()->user()->profile->building ?? '',
        ]);

        return view('purchase_address', compact('item', 'address'));
    }

    public function update(AddressRequest $request, Item $item)
    {
        $data = $request->validated();

        session()->put("purchase_address.{$item->id}", [
            'postcode' => $data['postcode'],
            'address'  => $data['address'],
            'building' => $request->building,
        ]);

        return redirect("/purchase/{$item->id}");
    }
}