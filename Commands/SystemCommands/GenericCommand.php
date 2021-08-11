<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Generic command
 */
class GenericCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'Generic';
    protected $description = 'Handles generic commands or is executed by default when a command is not found';
    protected $version = '1.0.1';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
	    $message = $this->getMessage();

	    //You can use $command as param
	    $chat_id = $message->getChat()->getId();
	    $user_id = $message->getFrom()->getId();
	    $command = $message->getCommand();
	    $user = $message->getFrom();

	    require("db.php");
	    require("l10n.php");



	    if (in_array($user_id, $this->telegram->getAdminList()) && strtolower(substr($command, 0, 5)) == 'whois') {
		    return $this->telegram->executeCommand('help', $this->update);
	    }


	    if  (strtolower($command) == strtolower(substr($mensajes['tension'],1))) {
		    return $this->telegram->executeCommand('check', $this->update);
	    }
	    if  (strtolower($command) == strtolower(substr($mensajes['video'],1))) {
		    return $this->telegram->executeCommand('video', $this->update);
	    }
	    if  (strtolower($command) == strtolower(substr($mensajes['historial'],1))) {
		    return $this->telegram->executeCommand('chart', $this->update);
	    }
	    if  (strtolower($command) == strtolower(substr($mensajes['cita'],1))) {
		    return $this->telegram->executeCommand('appt', $this->update);
	    }
	    if  (strtolower($command) == strtolower(substr($mensajes['cambiar'],1))) {
		    return $this->telegram->executeCommand('cambiar', $this->update);
	    }






	    $data = [
		    'chat_id' => $chat_id,
		    'text'    => 'Comando /' . $command . ' no encontrado... :(',
	    ];

	    return Request::sendMessage($data);
    }
}
