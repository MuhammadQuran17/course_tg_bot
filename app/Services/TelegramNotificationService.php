<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\UserRegistrationDTO;

/**
 * TelegramNotificationService: Handles sending messages to Telegram channel.
 */
class TelegramNotificationService
{
    private string $botToken;
    private string $channelId;
    private string $apiUrl = 'https://api.telegram.org';

    public function __construct(string $botToken, string $channelId)
    {
        $this->botToken = $botToken;
        $this->channelId = $channelId;
    }

    /**
     * Send a registration notification to the Telegram channel.
     *
     * @param UserRegistrationDTO $userData User registration data
     * @return bool True if message was sent successfully
     */
    public function sendRegistrationNotification(UserRegistrationDTO $userData): bool
    {
        $message = $this->formatRegistrationMessage($userData);
        return $this->sendMessage($message);
    }

    /**
     * Send a custom message to the Telegram channel.
     *
     * @param string $message The message text (supports HTML formatting)
     * @param bool $parseHtml If true, message will be parsed as HTML
     * @return bool True if message was sent successfully
     */
    public function sendMessage(string $message, bool $parseHtml = true): bool
    {
        $endpoint = "{$this->apiUrl}/bot{$this->botToken}/sendMessage";

        $postData = [
            'chat_id' => $this->channelId,
            'text' => $message,
        ];

        if ($parseHtml) {
            $postData['parse_mode'] = 'HTML';
        }

        $response = $this->makeRequest($endpoint, $postData);

        if (isset($response['ok']) && $response['ok'] === true) {
            return true;
        }

        // Log error if needed
        if (isset($response['description'])) {
            error_log('Telegram API Error: ' . $response['description']);
        }

        return false;
    }

    /**
     * Format a registration message for Telegram.
     *
     * @param UserRegistrationDTO $userData User registration data
     * @return string Formatted message
     */
    private function formatRegistrationMessage(UserRegistrationDTO $userData): string
    {
        $message = "✅ <b>New Registration</b>\n\n";
        $message .= "<b>Name:</b> " . htmlspecialchars($userData->name) . "\n";
        $message .= "<b>Programming Language:</b> " . htmlspecialchars($userData->programmingLanguage) . "\n";
        $message .= "<b>Telegram ID:</b> <code>{$userData->telegramId}</code>\n";
        $message .= "<b>Time:</b> " . date('Y-m-d H:i:s') . "\n";

        return $message;
    }

    /**
     * Make a cURL request to Telegram API.
     *
     * @param string $url API endpoint
     * @param array $postData POST data
     * @return array API response decoded as array
     */
    private function makeRequest(string $url, array $postData): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            error_log('Curl Error: ' . $curlError);
            return ['ok' => false, 'description' => 'Curl error: ' . $curlError];
        }

        if ($httpCode !== 200) {
            error_log("HTTP Error: {$httpCode} - {$response}");
            return ['ok' => false, 'description' => "HTTP {$httpCode}"];
        }

        return json_decode($response, true) ?? ['ok' => false];
    }
}
