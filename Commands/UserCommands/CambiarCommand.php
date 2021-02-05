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

/**
 * User "/cambiar" command
 */
class CambiarCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'cambiar';

    /**
     * @var string
     */
    protected $description = 'Cambiar alertas';

    /**
     * @var string
     */
    protected $usage = '/cambiar';

    /**
     * @var string
     */
    protected $version = '0.0.1';

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

    private function isAlertaDesactivada($indice, $user_id){
	require("db.php");
	$alerta = "select year(hora$indice) > " . date("Y") . " from alertas where user_id = $user_id";
	$rs = $conn->query($alerta);
	return $rs->fetch()[0];
    }

    private function obtenerAlertas($user_id){
	require("db.php");
	$alertas = "select concat(hour(hora1),':', LPAD(minute(hora1), 2, '0')), concat(hour(hora2),':', LPAD(minute(hora2), 2, '0')) from alertas where user_id = $user_id";
	$rs = $conn->query($alertas);
	return $rs->fetch();
    }

    private function cambiarAlerta($hora, $indice, $user_id, $tope=2017){
	    require("db.php");
		$alertas = "update alertas
			  set hora$indice ='$tope-05-01 $hora' 
				where user_id = $user_id";
		// error_log(__file__ . PHP_EOL . $alertas . PHP_EOL, 3, "/tmp/error.log");
		$conn->query($alertas);

    }


    private function inrange($valor, $horamin){
	if ($horamin == "h")
		return $valor >= 0 && $valor <=23;
	else
		return $valor >= 0 && $valor <= 59;
		
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
	list($alerta1, $alerta2) = $this->obtenerAlertas($user_id);

	// error_log(__file__ . PHP_EOL . $alerta1 . " State:" . $state . " Text: " . $text, 3, "/tmp/error.log");

        //State machine
        //Entrypoint of the machine state if given by the track
        //Every time a step is achieved the track is updated
        switch ($state) {
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

		    if ($this->isAlertaDesactivada(1, $user_id)){
			    $data['text'] = 'La primera alerta está desactivada. ¿Quieres activarla?';
			    $opciones = ['Sí', 'No'];
		    }else{
	                    $data['text']         = 'La primera alerta es a las: ' . $alerta1 . '. ¿Quieres cambiarla?';
			    $opciones = ['Sí', 'No', 'Desactivar esta alerta'];
		    }
		    $data['reply_markup'] = Keyboard::remove(['selective' => true]);

		    $data['reply_markup'] = (new Keyboard($opciones))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                    break;
		}

		if (strpos(strtolower($text), "desactivar")!==false){

			$this->cambiarAlerta($alerta1, 1, $user_id, 2222);
			$data['text'] = "De acuerdo, alerta desactivada.";
			$result = Request::sendMessage($data);

			Request::sendChatAction([
					'chat_id' => $chat_id,
					'action' => 'typing'
				]);
			sleep(3);

			$notes['state'] = 2;
			$this->conversation->update();
			$text = '';
			goto state2;
		}else if (strpos(strtolower($text), "s")!==false){
			// $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
			$text          = '';
		} 
            // no break
            case 1:  // cambiar primera hora
                if ($text === '' || !preg_match("/(\d\d*):(\d\d*)/", $text, $trozos)){
                    $notes['state'] = 1;
                    $this->conversation->update();

		    if ($text === '')
                         $data['text'] = 'De acuerdo, indica la hora, en formato HH:MM. Por ejemplo, 13:15';
		    elseif(strpos($text, "No")===false)
		         $data['text'] = 'Vaya, parece que la hora introducida no es correcta. Recuerda que la hora debe estar comprendida entre 0 y 23. Los minutos entre 0 y 59. Por favor, indica la hora en formato HH:MM.';

		    if (strpos($text, "No")===false){
		    	$data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
                    	$result = Request::sendMessage($data);
		    	break;
		    }
		} elseif (isset($trozos) && (!$this->inrange($trozos[1], "h") || !$this->inrange($trozos[2], "m")) ){

			// error_log(__file__ . PHP_EOL . print_r($trozos, 1) . " inrange: " . $this->inrange($trozos[1], "h"), 3, "/tmp/error.log");
			$data['text'] = 'La hora introducida no es correcta. Recuerda que la hora debe estar comprendida entre 0 y 23. Los minutos entre 0 y 59. Por favor, indica la hora en formato HH:MM.';
			$result = Request::sendMessage($data);
			break;	
		}	
		
		if (strpos($text, "No")===false){
			$this->cambiarAlerta($trozos[1] . ":" . $trozos[2], 1, $user_id);
			$notes['hora1'] = $text;
			$data['text'] = "OK, ¡cambiada!";
			$result = Request::sendMessage($data);

		}
			$text             = '';
            // no break
            case 2:
		    state2:
    		    if ($text === '') {
                    $notes['state'] = 2;
                    $this->conversation->update();

		    if ($this->isAlertaDesactivada(2, $user_id)){
			    $data['text'] = 'La segunda alerta está desactivada. ¿Quieres activarla?';
			    $opciones = ['Sí', 'No'];
		    } else { 
			    $data['text']         = 'La segunda alerta es a las: ' . $alerta2 . '. ¿Quieres cambiarla?';
			    $opciones = ['Sí', 'No', 'Desactivar esta alerta'];
		    }

                    // $data['reply_markup'] = Keyboard::remove(['selective' => true]);

		    $data['reply_markup'] = (new Keyboard($opciones))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                    break;

		   }


		if (strpos(strtolower($text), "desactivar")!==false){

			$this->cambiarAlerta($alerta2, 2, $user_id, 2222);
			$data['text'] = "De acuerdo, alerta desactivada.";
			$result = Request::sendMessage($data);

			Request::sendChatAction([
					'chat_id' => $chat_id,
					'action' => 'typing'
				]);
			sleep(3);

			$notes['state'] = 6;
			goto state6;
			$text = '';
			break;
		}


		if (strpos(strtolower($text), "s")!==false){
			// $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
			$text          = '';
		} 

            // no break
            case 3:
              // cambiar segunda hora
                if ($text === '' || !preg_match("/(\d\d*):(\d\d*)/", $text, $trozos)){
                    $notes['state'] = 3;
                    $this->conversation->update();

		    if ($text === '')
                    $data['text'] = 'De acuerdo, indica la hora, en formato HH:MM. Por ejemplo, 20:30.';
		    elseif(strpos($text, "No")===false)
		    $data['text'] = 'Vaya, parece que la hora introducida no es correcta. Recuerda que la hora debe estar comprendida entre 0 y 23. Los minutos entre 0 y 59. Por favor, indica la hora en formato HH:MM.';

		    if (strpos($text, "No")===false){
		       $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
                       $result = Request::sendMessage($data);
		       break;
		    }
		} elseif (isset($trozos) && (!$this->inrange($trozos[1], "h") || !$this->inrange($trozos[2], "m")) ){
			// error_log(__file__ . PHP_EOL . print_r($trozos, 1) . " inrange: " . $this->inrange($trozos[2], "m"), 3, "/tmp/error.log");
			$data['text'] = 'La hora introducida no es correcta. Recuerda que la hora debe estar comprendida entre 0 y 23. Los minutos entre 0 y 59. Por favor, indica la hora en formato HH:MM.';
			$result = Request::sendMessage($data);
			break;	
		}	
		
		if (strpos($text, "No")===false){
			$this->cambiarAlerta($trozos[1] . ":" . $trozos[2], 2, $user_id);
			$notes['hora2'] = $text;
		}
			$text             = '';
            // no break
            case 6:
		state6:
                $this->conversation->update();
                $out_text = 'Muy bien. Los datos han sido actualizados correctamente.' . PHP_EOL;
                unset($notes['state']);


		list($alerta1, $alerta2) = $this->obtenerAlertas($user_id);
		//  $out_text .= PHP_EOL . "Media de sistólicas:" . $mediasistolica;
		if ( $this->isAlertaDesactivada(1, $user_id) ) {
			$out_text .= PHP_EOL . "La primera alerta está desactivada. "; 
		} else{
			$out_text .= PHP_EOL . "La primera alerta será a las: " . $alerta1;
		}

		if ( $this->isAlertaDesactivada(2, $user_id) ) {
			$out_text .= PHP_EOL . "La segunda alerta está desactivada. ";

		} else {

			$out_text .= PHP_EOL . "La segunda alerta será a las: " . $alerta2;
		}

       //         $data['reply_markup'] = Keyboard::remove(['selective' => true]);
		
		//		$data['text'] = "Cuando quieras, puedes volver a pedirme ayuda para guardar los datos de la siguiente toma de tensión. ".
//		       		"También puedes ver tu historial o ver un vídeo explicativo.";

	    	$data['reply_markup'] = (new Keyboard(['/Check ❤️', '/Video 📺', '/Chart 📈', '/Appt 📅']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(false)
                        ->setSelective(true);

	        $data['text']      = $out_text;
                $this->conversation->stop();

                $result = Request::sendMessage($data);

		break;
        }

        return $result;
    }
}
