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
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Request;
use \DateTime;

/**
 * User "/appt" command
 */
class ApptCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'appt';

    /**
     * @var string
     */
    protected $description = 'Set medical appointment date';

    /**
     * @var string
     */
    protected $usage = '/appt';

    /**
     * @var string
     */
    protected $version = '0.0.1';

    /**
     * @var bool
     */
    protected $need_mysql = true;


    protected $conn = null;
    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

    private function obtenerCita($user_id){
	$alertas = "select valor from tensiones where user_id = $user_id and clave='cita'";
	$rs = $this->conn->query($alertas);
	return $rs->fetch();
    }

    private function anularCita($user_id){
	$anular = "delete from tensiones where user_id= $user_id and clave = 'cita'";
	$this->conn->query($anular);

	$query = "update alertas set finalizar =  NOW(), 
			iniciar = NOW()  where 
			user_id = " . $this->getMessage()->getFrom()->getId();
	$this->conn->query($query);

    }

    private function filter($mensaje, $cita){
	$search = array('{cita}');
	$replace = array($cita);
	return str_replace($search, $replace, $mensaje);
    }

    /**
     * Command execute method
     *
     * @return mixed
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
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
	$this->conn = $conn;

	list($cita) = $this->obtenerCita($user_id);

        //State machine
        //Entrypoint of the machine state if given by the track
        //Every time a step is achieved the track is updated
        switch ($state) {
	case 0:
                if ($text === '' or $text == '📅') {
                    $notes['state'] = 0;
                    $this->conversation->update();

		    if ($cita != '') {
			    $data['text'] = $this->filter($mensajes['citacambiar'], $cita);
			    $opciones = [$mensajes['yes'], $mensajes['no'], $mensajes['canceldate']];
		    } else {
			    $data['text'] = $mensajes['citaningunadefinida'];
			    $opciones = ['Yes', 'No'];
		    }

		    $data['reply_markup'] = Keyboard::remove(['selective' => true]);

		    $data['reply_markup'] = (new Keyboard($opciones))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                    break;
		}


		if (strpos(
			strtolower(explode(' ', $text)[0]), 
			strtolower(explode(' ', $mensajes['canceldate'])[0])) !== false){
			$this->anularCita($user_id);
			$data['text'] = $mensajes['citaanulada'];
			$result = Request::sendMessage($data);
	                $this->conversation->stop();
			break;
		}else if (strpos(strtolower($text), strtolower($mensajes['yes']))!==false){
			// $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
			$text          = '';
	                $notes['state'] = 1;
         	        $this->conversation->update();
			// no break;
		}else {
			if ($cita != '')
				$data['text'] = $mensajes['citaok'];
			else
				$data['text'] = $mensajes['citadeacuerdo'];

			$data['reply_markup'] = Keyboard::remove(['selective' => true, 'oneTime' => true, 'resize' => true]);
			
			$result = Request::sendMessage($data);
	                $this->conversation->stop();
			break;
		}

            // no break
	case 1:  // cambiar cita

	    if ($text != '' && preg_match( "/(\d+)\/(\d+)\/(\d+)/", $text, $matches) ){

	      // simple check if the appointment is correct
	      $date_now = new DateTime();
	      if ($matches[2] >= 1 && $matches[2] <=12 && $matches[1]>=1 && $matches[1]<=31 && $matches[3]>=2017)
		      $cita    = new DateTime($matches[2]. "/" . $matches[1]. "/" . $matches[3]);
	      else
		      $cita = new DateTime("yesterday");

	      if ($date_now  > $cita){
		$data['chat_id'] = $chat_id;
		$data['text'] = $mensajes['citafechaincorrecta'] . " " . $mensajes['citaintroducefecha'];
		$result = Request::sendMessage($data);
		break;
	      }
	      
		$query = "select id from tensiones where user_id = " . $this->getMessage()->getFrom()->getId(). 
			" and clave = 'cita'";
		$rs = $this->conn->query($query);
		$extra = "";
		if ($rs->rowCount() > 0){
			list($id) = $rs->fetch();
			$extra = ", id = " . $id;
		}


		$query = "replace into tensiones set valor='". $text .
				"', user_id = " . $this->getMessage()->getFrom()->getId() . 
				", clave='cita'" . $extra;
		$stmt =  $this->conn->query($query);

		$query = "update alertas set finalizar =  str_to_date('$text', '%d/%m/%Y'), 
			iniciar =   date_sub( str_to_date('$text', '%d/%m/%Y') , INTERVAL 7 DAY)  where 
			user_id = " . $this->getMessage()->getFrom()->getId();
		$stmt = $conn->query($query);


		$data = [
		    'chat_id' => $chat_id,
		    'text'    => $mensajes['citacorrecta'],
		];

			$data['reply_markup'] = (new Keyboard([$mensajes['tension'] . ' ❤️', $mensajes['video'].' 📺', $mensajes['historial'] . ' 📈', $mensajes['cita'] . ' 📅']))
				->setResizeKeyboard(true)
				->setOneTimeKeyboard(false)
				->setSelective(true);

		$result = Request::sendMessage($data);

                unset($notes['state']);
                $this->conversation->stop();
		break;

	    } else { 
		$data['text'] = $mensajes['citaintroducefecha'];
		$data['chat_id'] = $chat_id;

		$result = Request::sendMessage($data);
		break;	
	    } 
	}	
        return $result;
    }
}
