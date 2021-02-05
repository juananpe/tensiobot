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

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;

/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'Genericmessage';
    protected $description = 'Handle generic message';
    protected $version = '1.0.2';
    protected $need_mysql = true;
    /**#@-*/

    /**
     * Execution if MySQL is required but not available
     *
     * @return boolean
     */
    public function executeNoDb()
    {
        //Do nothing
        return Request::emptyResponse();
    }

    /**
     * Execute command
     *
     * @return boolean
     */
    public function execute()
    {

        //If a conversation is busy, execute the conversation command after handling the message
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );
        //Fetch conversation command if it exists and execute it
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command, $this->update);
        }

	$command = $this->getMessage()->getText();
        $command = explode(' ', $command)[0];
	if ( (strtolower($command) == "tomar" || strtolower($command) == "ver" || strtolower($command) == "consultar" ) && $this->getTelegram()->getCommandObject($command)) {
            return $this->getTelegram()->executeCommand($command);
	}else {
		require("config.php");
		require("db.php");
		$query = "select * from tensiones where user_id = " . $this->getMessage()->getFrom()->getId() . " and clave='username'";
		// echo "DEBUG: " . $query . PHP_EOL;
		$stmt =  $conn->query($query);
		if ($stmt->rowCount() == 0){ // nombre sin asignar
           		$command = "asignar";
	   		return $this->getTelegram()->executeCommand($command);
		}
	}

        return Request::emptyResponse();
    }
}
