<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class WebsiteAccountController extends Controller
{
    public function show(Request $request): View
    {
        return view('auth.account', [
            'user' => $request->user(),
        ]);
    }

    public function orders(Request $request): View
    {
        $user = $request->user();
        $customerIds = $user?->email
            ? Customer::where('email', $user->email)->pluck('id')
            : collect();

        $sales = $customerIds->isEmpty()
            ? collect()
            : Sale::with('product')
                ->whereIn('customer_id', $customerIds)
                ->latest('sold_at')
                ->take(50)
                ->get();

        return view('storefront.account.orders', compact('sales'));
    }
}
