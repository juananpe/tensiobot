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

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/help" command
 */
class HelpCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'help';

    /**
     * @var string
     */
    protected $description = 'Mostrar ayuda de los comandos disponibles';

    /**
     * @var string
     */
    protected $usage = '/help or /help <command>';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * Command execute method
     *
     * @return mixed
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $user = $message->getFrom();

        require("db.php");
        require("l10n.php");

        $message_id = $message->getMessageId();

        $commands = ['checkcommand', 'videocommand', 'historialcommand', 'citacommand', 'cancelcommand', 'cambiarcommand', 'langcommand'];

        $text = '';
        foreach($commands as $command){
            $text .= $mensajes[$command] . " - " . $mensajes['help' . str_replace('command','',$command)] . "\n";
        }

        $data = [
            'chat_id'             => $chat_id,
            'reply_to_message_id' => $message_id,
            'text'                => $text,
        ];

        return Request::sendMessage($data);
    }
}
