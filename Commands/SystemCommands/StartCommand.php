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

use \DateTime;

/**
 * Start command
 */
class StartCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'start';
    protected $description = 'Start command';
    protected $usage = '/start';
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

    private function filter($message, $username=""){
	$search = array('{username}');
	$replace = array($username);
	return str_replace($search, $replace, $message);
    }


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

	require("db.php");
	require("l10n.php");

	//State machine
        //Entrypoint of the machine state if given by the track
        //Every time a step is achieved the track is updated
        switch ($state) {
            case 0:
                if ($text === '') {
                    $notes['state'] = 1; 
                    $this->conversation->update();


			$query = "replace into alertas set user_id = " . $this->getMessage()->getFrom()->getId() . 
", hora1= concat(date(now()), ' ', '10:00:00') , hora2=  concat(date(now()), ' ', '21:00:00') ";
				// ", iniciar= date(now()) , finalizar = date( date_add( NOW(), INTERVAL 7 DAY) )";

			$stmt =  $conn->query($query);

		    $text = 'Hi! I\'m @' . $BOT_NAME . '. Your personal assistant to help you check your blood pressure. Before starting, could you please tell me what the password is?';

		        $data['text'] = $text;
		        $data['chat_id'] = $chat_id;

			$result = Request::sendMessage($data);
			break;
		}
	    case 1:
		    if (strtolower($text) != strtolower($CLAVE)){
			 $data['text'] = "Incorrect password. Please, try again.";
		         $data['chat_id'] = $chat_id;
			 $result = Request::sendMessage($data);
			 break;
		    }	    
		       $text = '';
		    // no break
	    case 2:
		    if ($text === ''){
			  $notes['state'] = 2; 
			  $this->conversation->update();
			  $data['text'] = "Right! From now on I will help you check your blood pressure, but first I need to know your name, so please, could you tell me your name?";
			  $result = Request::sendMessage($data);
			  break;
		    }


		$query = "select id from tensiones where user_id = " . $this->getMessage()->getFrom()->getId(). " and clave = 'username'";
		$rs = $conn->query($query);
		$extra = "";
		if ($rs->rowCount() > 0){
			list($id) = $rs->fetch();
			$extra = ", id = " . $id;
		}

		$query = "replace into tensiones set valor='". $text .
				"', user_id = " . $this->getMessage()->getFrom()->getId() . 
				", clave='username'" . $extra;
		$stmt =  $conn->query($query);


		$notes['state'] = 3; 
		$notes['name'] = $text;
		  $this->conversation->update();
		  $text = '';

	    case 3:
		    	    if ($text === '' || !in_array(substr(strtolower($text),0,1), ['y','n'])) {
				$data['text'] = $mensajes['citatehandado'];
				$data['reply_markup'] = (new Keyboard(['Yes', 'No']))
				->setResizeKeyboard(true)
				->setOneTimeKeyboard(true)
				->setSelective(true);

				$result = Request::sendMessage($data);


			    break;
		    } 

		    $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
		    if (strtolower($text) == 'no'){
			    $mensajes['citacorrecta'] = $mensajes['citanohaycita'];
			    goto nohaycita;
		    }

		    $data['text'] = $this->filter($mensajes['citamedico'], $notes['name']);
		    $data['chat_id'] = $chat_id;
		    $result = Request::sendMessage($data);

		    $notes['state'] = 4; 
		  $this->conversation->update();
		    $text = '';

			break;


case 4: 
      if ($text!='' && preg_match( "/(\d+)\/(\d+)\/(\d+)/", $text, $matches) ){

	      // simple check for correct appointment date
	      $date_now = new DateTime();
	      if ($matches[2] >= 1 && $matches[2] <=12 && $matches[1]>=1 && $matches[1]<=31 && $matches[3]>=2017)
		      $cita    = new DateTime($matches[2]. "/" . $matches[1]. "/" . $matches[3]);
	      else
		      $cita = new DateTime("yesterday");

	      if ($date_now  > $cita){
		$data['chat_id'] = $chat_id;
		$data['text'] = $mensajes['citafechaincorrecta']. " " . $mensajes['citaintroducefecha'];
		$result = Request::sendMessage($data);
		break;
	      }
	      
		$query = "select id from tensiones where user_id = " . $this->getMessage()->getFrom()->getId(). 
			" and clave = 'cita'";
		$rs = $conn->query($query);
		$extra = "";
		if ($rs->rowCount() > 0){
			list($id) = $rs->fetch();
			$extra = ", id = " . $id;
		}


		$query = "replace into tensiones set valor='". $text .
				"', user_id = " . $this->getMessage()->getFrom()->getId() . 
				", clave='cita'" . $extra;
		$stmt =  $conn->query($query);

		$query = "update alertas set finalizar =  str_to_date('$text', '%d/%m/%Y'), 
			iniciar =   date_sub( str_to_date('$text', '%d/%m/%Y') , INTERVAL 7 DAY)  where 
			user_id = " . $this->getMessage()->getFrom()->getId();
		$stmt = $conn->query($query);


		nohaycita:
		$data = [
		    'chat_id' => $chat_id,
		    'text'    => $mensajes['citacorrecta'],
		];

		$data['reply_markup'] = (new Keyboard(['/Tension', '/Video', '/Historial', '/Cita']))
				->setResizeKeyboard(true)
				->setOneTimeKeyboard(false)
				->setSelective(true);

		$result = Request::sendMessage($data);

                unset($notes['state']);
                $this->conversation->stop();
		break;

	      } else {

			$data['text'] = $mensajes['citafechaincorrecta'];
			$data['chat_id'] = $chat_id;

			$result = Request::sendMessage($data);
			break;	
	      }
	} // end switch
    } // end method
} // end class
