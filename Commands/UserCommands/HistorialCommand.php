<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/consultar" command
 */
class ChartCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'chart';
    protected $description = 'Show a chart of your past blood pressure values';
    protected $usage = '/chart 📈';
    protected $version = '1.0.0';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = trim($message->getText(true));

        if ($text === '') {
            $text = 'Uso: ' . $this->getUsage();
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

	require("config.php");
	$realm = $config['realm'];
	exec($config[$realm]['rscript'] . " " .  __DIR__ . "/obtenerPic.R " . $chat_id . "  2>&1" , $output, $error);
	error_log(__file__ . ":". PHP_EOL . print_r($output, 1) . PHP_EOL . $error . PHP_EOL . __DIR__, 3, "/tmp/error.log");

        return Request::sendPhoto($data, $this->telegram->getUploadPath() . '/imgs/' . $chat_id . '.png' );
    }
}
