<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
}
