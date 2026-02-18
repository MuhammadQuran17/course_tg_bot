<?php

require __DIR__ . '/vendor/autoload.php';

use App\Conversations\RegistrationConversation;
use App\Database\UserRepository;
use App\Services\TelegramNotificationService;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Cache\FileCache;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

// [START] Load .env
    if (file_exists(__DIR__ . '/.env')) {
        $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                [$name, $value] = explode('=', $line, 2);
                $_ENV[trim($name)] = trim(trim($value), "\"'");
            }
        }
    }

    $token = $_ENV['TG_BOT_API_TOKEN'] ?? getenv('TG_BOT_API_TOKEN');
    if (!$token) {
        throw new RuntimeException('TG_BOT_API_TOKEN is not set. Add it to .env or set the environment variable.');
    }

    $channelId = $_ENV['TELEGRAM_CHANNEL_ID'] ?? getenv('TELEGRAM_CHANNEL_ID');
    if (!$channelId) {
        throw new RuntimeException('TELEGRAM_CHANNEL_ID is not set. Add it to .env or set the environment variable.');
    }
// [END] Load .env

// [START] BotMan Configuration
    $config = [
        'telegram' => [
            'token' => $token,
        ],
        'config' => [
            'conversation_cache_time' => 120,
        ],
    ];

    DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);

    // Persistent cache required for conversations (ArrayCache is in-memory and lost between webhook requests)
    $cacheDir = __DIR__ . '/storage/cache';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    $botman = BotManFactory::create($config, new FileCache($cacheDir));
// [END] BotMan Configuration

// [START] Initialize Services
    // $userRepository = new UserRepository();
    $notificationService = new TelegramNotificationService($token, $channelId);
// [END] Initialize Services

$botman->hears('/start', function (BotMan $bot) use ($userRepository, $notificationService) {
    $bot->startConversation(new RegistrationConversation($userRepository, $notificationService));
});

$botman->listen();