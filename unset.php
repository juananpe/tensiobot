<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

require_once("config.php");

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Unset webhook
    $result = $telegram->unsetWebHook();

    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}
