#!/usr/bin/env php
<?php
//README
//This configuration file is intented to run the bot with the webhook method
//Uncommented parameters must be filled

//bash script
//while true; do ./getUpdatesCLI.php; done

// Load composer
require __DIR__ . '/vendor/autoload.php';

require_once("config.php");

$commands_path = __DIR__ . '/Commands/';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Enable MySQL
    $telegram->enableMySQL($mysql_credentials);

    //// Enable MySQL with table prefix
    //$telegram->enableMySQL($mysql_credentials, $BOT_NAME . '_');

    //// Add an additional commands path
    $telegram->addCommandsPath($commands_path);

    //// Here you can enable admin interface for the channel you want to manage
    $telegram->enableAdmins([4694560]);
    //$telegram->setCommandConfig('sendtochannel', ['your_channel' => '@type_here_your_channel']);

    //// Here you can set some command specific parameters,
    //// for example, google geocode/timezone api key for date command:
    //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

    //// Logging
    //\Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);
    //\Longman\TelegramBot\TelegramLog::initErrorLog($path . '/' . $BOT_NAME . '_error.log');
    //\Longman\TelegramBot\TelegramLog::initDebugLog($path . '/' . $BOT_NAME . '_debug.log');
    //\Longman\TelegramBot\TelegramLog::initUpdateLog($path . '/' . $BOT_NAME . '_update.log');

    //// Set custom Upload and Download path
    $telegram->setDownloadPath('Files');
    $telegram->setUploadPath('Files');

    //// Botan.io integration
    //$telegram->enableBotan('your_token');

    // Handle telegram getUpdate request
    $ServerResponse = $telegram->handleGetUpdates();

    if ($ServerResponse->isOk()) {
        $n_update = count($ServerResponse->getResult());
        print(date('Y-m-d H:i:s', time()) . ' - Processed ' . $n_update . ' updates' . "\n");
    } else {
        print(date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . "\n");
        echo $ServerResponse->printError() . "\n";
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
    // log telegram errors
    \Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    //catch log initilization errors
    echo $e;
}
