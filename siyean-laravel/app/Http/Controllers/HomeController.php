<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Product::where('visible_online', true)
            ->orderByDesc('updated_at')
            ->take(6)
            ->get();

        return view('storefront.home', compact('featured'));
    }
}
