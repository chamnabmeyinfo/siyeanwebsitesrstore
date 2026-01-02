<?php

declare(strict_types=1);

namespace App;

final class NotificationService
{
    public function __construct(private array $config)
    {
    }

    public function notifyBookingCreated(array $booking, array $product): void
    {
        $subject = "[Booking] {$booking['customer_name']} requested {$product['model']}";
        $body = sprintf(
            "New booking for %s (%s)\nQuantity: %d\nCustomer: %s (%s / %s)\nPreferred: %s %s\nDeposit: $%0.2f\nNotes: %s",
            $product['model'],
            $product['sku'],
            $booking['quantity'],
            $booking['customer_name'],
            $booking['customer_email'],
            $booking['customer_phone'],
            $booking['preferred_date'] ?: '—',
            $booking['preferred_time'] ?: '',
            $booking['deposit_amount'],
            $booking['notes'] ?: '—'
        );
        $this->broadcast($subject, $body);
    }

    public function notifyBookingStatus(array $booking): void
    {
        $subject = "[Booking] #{$booking['id']} marked {$booking['status']}";
        $body = sprintf(
            "Booking #%d for %s (%s)\nCustomer: %s (%s)\nStatus: %s\nNotes: %s",
            $booking['id'],
            $booking['model'] ?? '',
            $booking['sku'] ?? '',
            $booking['customer_name'],
            $booking['customer_email'],
            strtoupper($booking['status']),
            $booking['notes'] ?? '—'
        );
        $this->broadcast($subject, $body);
    }

    public function maybeNotifyLowStock(array $product): void
    {
        $threshold = (int) ($this->config['low_stock_threshold'] ?? 0);
        if ($threshold <= 0) {
            return;
        }
        if ((int) $product['quantity_on_hand'] > $threshold) {
            return;
        }

        $subject = "[Inventory] Low stock alert for {$product['model']}";
        $body = sprintf(
            "%s (%s) is down to %d units on hand. Consider restocking soon.",
            $product['model'],
            $product['sku'],
            $product['quantity_on_hand']
        );
        $this->broadcast($subject, $body);
    }

    private function broadcast(string $subject, string $body): void
    {
        $this->sendEmail($subject, $body);
        $this->sendTelegram($subject . "\n\n" . $body);
    }

    private function sendEmail(string $subject, string $body): void
    {
        $emailConfig = $this->config['email'] ?? [];
        if (empty($emailConfig['enabled']) || empty($emailConfig['to'])) {
            return;
        }
        $toRecipients = array_filter(array_map('trim', explode(',', $emailConfig['to'])));
        if (!$toRecipients) {
            return;
        }
        $headers = [
            'From: ' . ($emailConfig['from'] ?? 'no-reply@example.com'),
            'Content-Type: text/plain; charset=UTF-8',
        ];
        foreach ($toRecipients as $recipient) {
            @mail($recipient, $subject, $body, implode("\r\n", $headers));
        }
    }

    private function sendTelegram(string $message): void
    {
        $telegram = $this->config['telegram'] ?? [];
        if (empty($telegram['enabled']) || empty($telegram['bot_token']) || empty($telegram['chat_id'])) {
            return;
        }
        $url = sprintf(
            'https://api.telegram.org/bot%s/sendMessage',
            $telegram['bot_token']
        );
        $payload = [
            'chat_id' => $telegram['chat_id'],
            'text' => $message,
            'parse_mode' => 'HTML',
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        curl_close($ch);
    }
}

