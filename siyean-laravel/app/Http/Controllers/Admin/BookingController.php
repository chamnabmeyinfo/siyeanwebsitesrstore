<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class BookingController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::with('product')
            ->latest()
            ->paginate(50);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', Booking::STATUSES)],
        ]);

        $booking->update(['status' => $data['status']]);

        return back()->with('success', "Booking #{$booking->id} marked {$data['status']}.");
    }
}
