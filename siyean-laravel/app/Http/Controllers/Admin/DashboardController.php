<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $sales = Sale::query();

        $totalRevenue = (float) $sales->clone()->get()->sum(fn (Sale $s) => $s->total);
        $totalSold = (int) $sales->clone()->sum('quantity');
        $salesCount = (int) $sales->clone()->count();

        $stockOnHand = (int) Product::sum('quantity_on_hand');
        $skuCount = (int) Product::count();

        $pendingBookings = Booking::where('status', 'pending')->count();

        $lowStock = Product::orderBy('quantity_on_hand')
            ->take(5)
            ->get();

        $recentSales = Sale::with(['product', 'customer'])
            ->latest('sold_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalSold', 'salesCount',
            'stockOnHand', 'skuCount', 'pendingBookings',
            'lowStock', 'recentSales'
        ));
    }
}
