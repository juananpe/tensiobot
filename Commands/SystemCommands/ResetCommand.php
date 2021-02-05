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
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;

/**
 * Reset command
 */
class ResetCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'reset';
    protected $description = 'Reset command';
    protected $usage = '/reset';
    protected $version = '1.0.1';
    /**#@-*/

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
	global $BOT_NAME;
     
	$message = $this->getMessage();

        $chat = $message->getChat();
        $user = $message->getFrom();
        $text = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        //Preparing Response
        $data = [
            'chat_id' => $chat_id,
        ];

        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            //reply to message id is applied by default
            //Force reply is applied by default so it can work with privacy on
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        //cache data from the tracking session if any
        $state = 0;
        if (isset($notes['state'])) {
            $state = $notes['state'];
        }

        $result = Request::emptyResponse();

	//State machine
        //Entrypoint of the machine state if given by the track
        //Every time a step is achieved the track is updated
        switch ($state) {
            case 0:
		    if ($text === '' || !in_array(substr(strtolower($text),0,1), ['s','n'])) {
				$data['text'] = 'Esto hará que @'. $BOT_NAME . ' se olvide de ti totalmente. ¿Estás seguro/a?';
				$data['reply_markup'] = (new Keyboard(['Sí', 'No']))
				->setResizeKeyboard(true)
				->setOneTimeKeyboard(true)
				->setSelective(true);

				$result = Request::sendMessage($data);


			    break;
		    } 

		    if (strtolower($text) == 'no'){
			    $data['text'] = "OK, falsa alarma ;-)";
			       $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
				$result = Request::sendMessage($data);
				break;
		    }


		    $notes['state'] = 1; 
		    $text = '';

		    // no break
		case 1:
                    $this->conversation->update();
		    require("db.php");

		    $query = "delete from alertas where user_id = " . $user_id ;
		    $stmt =  $conn->query($query);

		    $query = "delete from tensiones where user_id = " . $user_id ;
		    $stmt =  $conn->query($query);

		   $data['text'] = 'Datos borrados correctamente. Pulsa /start para comenzar de nuevo.';
		   $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
		        $data['chat_id'] = $chat_id;

			$result = Request::sendMessage($data);
			
			unset($notes['state']);
                	$this->conversation->stop();
			break;
	}


    }
}
