<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if ($item->status === 'sold') {
            abort(404);
        }

        if ($item->user_id === Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
        $profile = $user->profile;

        $address = session("purchase_address.{$item->id}");

        if (!$address) {
            $address = [
                'postcode' => $profile->postcode ?? '---',
                'address'  => $profile->address ?? '---',
                'building' => $profile->building ?? '',
            ];
        }

        return view('purchase', compact('item', 'user', 'profile', 'address'));
    }

    public function store(PurchaseRequest $request, Item $item)
{

    if ($item->status === 'sold') {
        return redirect()->route('purchase.show', $item)
            ->with('error', '売り切れです');
    }

    if ($item->user_id === Auth::id()) {
        abort(403);
    }

    $validated = $request->validated();
    $user = Auth::user();


    $sessionAddress = session("purchase_address.{$item->id}");

    if ($sessionAddress) {

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postcode' => $sessionAddress['postcode'],
                'address'  => $sessionAddress['address'],
                'building' => $sessionAddress['building'] ?? null,
            ]
        );
        $address = $sessionAddress;
    } else {

        $profile = $user->profile;
        $address = [
            'postcode' => $profile->postcode ?? '',
            'address'  => $profile->address ?? '',
            'building' => $profile->building ?? '',
        ];
    }


    if (empty($address['postcode']) || empty($address['address']) || $address['postcode'] === '---') {
        return redirect()->route('purchase.show', $item)
            ->with('error', '配送先住所が未設定です。住所を入力してください。');
    }


    DB::transaction(function () use ($validated, $item, $address) {
        $lockedItem = Item::where('id', $item->id)->lockForUpdate()->first();

        if (!$lockedItem || $lockedItem->status === 'sold') {
            throw new \Exception('sold');
        }

        Order::create([
            'user_id' => Auth::id(),
            'item_id' => $lockedItem->id,
            'payment_method' => $validated['payment_method'],
            'sending_postcode' => $address['postcode'],
            'sending_address'  => $address['address'],
            'sending_building' => $address['building'] ?? null,
        ]);

        $lockedItem->update(['status' => 'sold']);
    });


    session()->forget("purchase_address.{$item->id}");



    return $this->pay($request, $item);
}


    public function pay(PurchaseRequest $request, Item $item)
    {
    Stripe::setApiKey(config('services.stripe.secret'));


    $paymentMethodTypes = $request->validated()['payment_method'] === 'convenience'
        ? ['konbini']
        : ['card'];


    $session = CheckoutSession::create([
        'mode' => 'payment',
        'payment_method_types' => $paymentMethodTypes,
        'line_items' => [[
            'quantity' => 1,
            'price_data' => [
                'currency' => 'jpy',
                'unit_amount' => (int) $item->price,
                'product_data' => [
                    'name' => $item->name,
                ],
            ],
        ]],
        'success_url' => url('/purchase/success') . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => url('/purchase/cancel') . '?session_id={CHECKOUT_SESSION_ID}',
        'customer_email' => auth()->user()->email,
    ]);


    return redirect()->away($session->url);
}

    public function success()
    {
        return redirect()->route('index')
            ->with('success', '決済が完了しました！');
    }

    public function cancel()
    {
        return redirect()->route('index')
            ->with('cancel', '決済がキャンセルされました');
    }
}