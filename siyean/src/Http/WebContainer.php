<?php

declare(strict_types=1);

namespace App\Http;

use App\BookingRepository;
use App\InventoryRepository;
use App\NotificationService;
use App\ReportService;
use App\SaleService;
use App\UserRepository;

/**
 * Request-scoped services for the legacy PHP front controller.
 */
final class WebContainer
{
    public function __construct(
        public readonly InventoryRepository $inventory,
        public readonly SaleService $sales,
        public readonly ReportService $report,
        public readonly UserRepository $users,
        public readonly BookingRepository $bookings,
        public readonly NotificationService $notifications,
    ) {
    }
}
