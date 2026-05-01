<?php

declare(strict_types=1);

namespace App\Http;

use DateTimeImmutable;

/**
 * Builds dashboard presentation state (live vs demo sample) on the server.
 *
 * @phpstan-type SummaryShape array{count?:int|float, units?:int|float, revenue?:float, avg_ticket?:float}
 */
final class DashboardViewModel
{
    /**
     * @param SummaryShape $summary
     * @param list<array<string, mixed>> $inventorySlice
     * @return array<string, mixed>
     */
    public static function build(array $summary, array $inventorySlice): array
    {
        $hasLiveData =
            $inventorySlice !== []
            || (int) ($summary['count'] ?? 0) > 0
            || (float) ($summary['revenue'] ?? 0.0) > 0.0;

        $today = new DateTimeImmutable('today');
        $demoPeriodEnd = $today;
        $demoPeriodStart = $today->modify('-29 days');

        $demoSummary = [
            'count' => 12,
            'units' => 18,
            'revenue' => 28490.0,
            'avg_ticket' => 2374.17,
        ];
        $demoInventory = [
            [
                'sku' => 'MBP-14-M4-512',
                'model' => 'MacBook Pro 14" M4',
                'storage_capacity' => 512,
                'quantity_on_hand' => 4,
                'demo_updated' => $today->modify('-1 day')->format('M j, Y'),
            ],
            [
                'sku' => 'MBA-13-M3-256',
                'model' => 'MacBook Air 13" M3',
                'storage_capacity' => 256,
                'quantity_on_hand' => 6,
                'demo_updated' => $today->modify('-3 days')->format('M j, Y'),
            ],
            [
                'sku' => 'iM-24-M4-512',
                'model' => 'iMac 24" M4',
                'storage_capacity' => 512,
                'quantity_on_hand' => 2,
                'demo_updated' => $today->modify('-5 days')->format('M j, Y'),
            ],
            [
                'sku' => 'ACC-USBC-2M',
                'model' => 'USB-C cable 2m',
                'storage_capacity' => 0,
                'quantity_on_hand' => 24,
                'demo_updated' => $today->modify('-2 days')->format('M j, Y'),
            ],
            [
                'sku' => 'PC-ULTRA-1TB',
                'model' => 'PC Workstation Ultra',
                'storage_capacity' => 1024,
                'quantity_on_hand' => 1,
                'demo_updated' => $today->modify('-7 days')->format('M j, Y'),
            ],
        ];

        $showDemoRibbon = !$hasLiveData;

        return [
            'showDemoRibbon' => $showDemoRibbon,
            'displaySummary' => $showDemoRibbon ? $demoSummary : $summary,
            'displayInventory' => $showDemoRibbon ? $demoInventory : $inventorySlice,
            'demoPeriodStart' => $demoPeriodStart,
            'demoPeriodEnd' => $demoPeriodEnd,
            'demoPeriodStartIso' => $demoPeriodStart->format('Y-m-d'),
            'demoPeriodEndIso' => $demoPeriodEnd->format('Y-m-d'),
        ];
    }
}
