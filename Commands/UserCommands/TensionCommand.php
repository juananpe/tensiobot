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
 * User "/check" command
 */
class CheckCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'check';

    /**
     * @var string
     */
    protected $description = 'Check your blood pressure';

    /**
     * @var string
     */
    protected $usage = '/Check';

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


    private function filter($message, $alerta1="", $alerta2=""){
	    $search = array('{alerta1}', '{alerta2}', '{date}');

	    $tz = 'Europe/Madrid';
	    $timestamp = time();
	    $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
	    $dt->setTimestamp($timestamp); //adjust the object to correct timestamp
	    $date = $dt->format('H:i');


	$replace = array($alerta1, $alerta2,  $date );
	return str_replace($search, $replace, $message);
    }

    private function obtenerAlertas($user_id){
	require("db.php");
	$alertas = "select concat(hour(hora1),':', LPAD(minute(hora1), 2, '0')), concat(hour(hora2),':', LPAD(minute(hora2), 2, '0')) from alertas where user_id = $user_id";
	$rs = $conn->query($alertas);
	return $rs->fetch();
    }

    private function deshabilitarAlertas($user_id){
	require("db.php");
	$markassent = "update tensiones set notes = 'sent' where user_id=". $user_id . " AND clave='alerta' AND notes IS NULL"; 
	$sent = $conn->query($markassent);
    }

    private function guardarDatos($clave, $valor, $user_id){
	    require("db.php");
		$tomas = "insert into tensiones 
			  set clave='$clave', valor='$valor', datecreated=NOW(), user_id = $user_id";
//		error_log(__file__ . PHP_EOL . $tomas . PHP_EOL, 3, "/tmp/error.log");
		$rs = $conn->query($tomas);
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

	        //State machine
        //Entrypoint of the machine state if given by the track
        //Every time a step is achieved the track is updated
        switch ($state) {
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

		    $data['text']         = $this->filter($mensajes['recuerdaquelatoma']);
			    // 'OK, recuerda que la toma de tensión debe hacerse antes de la comida (o 1h después) y antes de la cena (o 1h después). Son las '. date('H:i') . '. ¿Seguro que quieres tomarte ahora la tensión?';

		    $data['reply_markup'] = (new Keyboard(['Yes, now', 'No, I prefer to wait']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $result = Request::sendMessage($data);
                    break;
                }

		if (strpos($text, 'wait') !== false) {

			// $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
			$data['reply_markup'] = (new Keyboard(['/Check ❤️', '/Video 📺', '/Chart 📈', '/Appt 📅']))
				->setResizeKeyboard(true)
				->setOneTimeKeyboard(true)
				->setSelective(true);
			
			$data['text']      = "OK! We'll wait. Remember that you only have to press /check to reactivate me :)";
			$this->conversation->stop();
			$result = Request::sendMessage($data);
			break;

		}
                $text          = '';

            // no break
	    case 1:
		    $notes['toma'] = 1;

		    preg_match('/\d+/', $text, $match);

                if ($text === '' || empty($match)) {
                    $notes['state'] = 1;
                    $this->conversation->update();

		    if ($text === '')
                    $data['text'] = 'OK, check your pressure. When you finish, tell me what the maximum has been:';
		    else
				$data['text'] =  $mensajes['latensiondebesernumero'];
			    //'Disculpa. No entiendo. Tu tensión arterial debe ser un número. Tecléalo de nuevo, por favor.';

		    $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);


                    $result = Request::sendMessage($data);
                    break;
                }

		// hay match, pero en un rango no admitido
		if (!empty($match) && ($match[0] < 20 ||  $match[0] > 250)){
			
			$data['text'] =  $mensajes['latensiondebesernumero'];
			// 'Disculpa. No entiendo. Creo que no has metido bien tu valor de tensión arterial. Tecléalo de nuevo, por favor.';
		    $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
                    $result = Request::sendMessage($data);
                    break;
 
		}

                $notes['sistolica'. $notes['toma']] = $match[0];
                $text             = '';

            // no break
	    case 2:

		   preg_match('/\d+/', $text, $match);

                if ($text === '' || empty($match)) {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['text'] = 'Very well. Tell me what the minimum has been:';
                    if ($text !== '') {
                        $data['text'] =  $mensajes['latensiondebesernumero'];
			//  'Disculpa. No entiendo. La tensión arterial tebe ser un número. Tecléalo de nuevo, por favor:';
                    }

		    $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
                    $result = Request::sendMessage($data);
                    break;
                }

		// hay match, pero en un rango no admitido
		if (!empty($match) && ($match[0] < 20 || $match[0] > 250)){
			
                        $data['text'] =  $mensajes['latensiondebesernumero'];
			// 'Disculpa. No entiendo. Creo que no has metido bien tu valor de tensión arterial. Tecléalo de nuevo, por favor.';
		    $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
                    $result = Request::sendMessage($data);
                    break;
 
		}

                $notes['diastolica' . $notes['toma']] = $match[0];
                $text         = '';

		// comprobar si ha metido los datos al revés
		if ($notes['diastolica' . $notes['toma']] > $notes['sistolica' . $notes['toma']]){
			$data['text'] = 'I think you have entered the data backwards. Don\'t worry, I saved them correctly :)';
			$result = Request::sendMessage($data);
			$auxiliar = $notes['diastolica'. $notes['toma']];
			$notes['diastolica' . $notes['toma']] = $notes['sistolica' . $notes['toma']];
			$notes['sistolica' . $notes['toma']] = $auxiliar;
		}

		$data['text'] =  "I saved the following data. High: " . $notes['sistolica'.$notes['toma']]. " Low: " . $notes['diastolica'.$notes['toma']];
		$result = Request::sendMessage($data);



            // no break
            case 3:
                if ($text === '' || !in_array($text, [$mensajes['okyahevuelto']], true)) {
                    $notes['state'] = 3;
                    $this->conversation->update();

		    // 'OK, ya he vuelto a tomarme la tensión'
                    $data['reply_markup'] = (new Keyboard([$mensajes['okyahevuelto']]))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

		    // $prefix = ($notes['toma'] <= 2)?'OK. ': '';

		    $data['text'] = 'To confirm that the blood pressure is stable, you must wait 2 minutes and take it again. I\'ve already started timing ⏰' . 
			    '
Don\'t worry. I\'ll let you know. Be patient ... 😌';

		    $this->guardarDatos("alerta", date("Y-m-d H:i:s"), $user_id);

                    if ($text !== '' && !is_numeric($text)) {
			    $data['text'] = $mensajes['tehastomadoya'];
				    // '¿Te has tomado ya la tensión?';
                    }

		    // $data['reply_markup'] = Keyboard::remove(['selective' => true]);

                    $result = Request::sendMessage($data);
                    break;
                }

		$this->deshabilitarAlertas($user_id);
		$text = '';

            // no break
	    case 4:
		  preg_match('/\d+/', $text, $match);
		 
		  if ($text === '' || empty($match)) {
                    $notes['state'] = 4;
                    $this->conversation->update();

		    if ($text === '')
                    $data['text'] = 'Well, what has been the highest blood pressure now?:';
		    else
		    $data['text'] = $mensajes['latensiondebesernumero'];

                    $result = Request::sendMessage($data);
                    break;
		}


		// hay match, pero en un rango no admitido
		if (!empty($match) && ($match[0] < 20 || $match[0] > 250)){

			$data['text'] =  $mensajes['latensiondebesernumero'];
			// 'Disculpa. No entiendo. Creo que no has metido bien tu valor de tensión arterial. Tecléalo de nuevo, por favor.';
		    $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
                    $result = Request::sendMessage($data);
                    break;
 
		}


		$notes['toma'] = $notes['toma'] + 1;
                $notes['sistolica' . $notes['toma']] = $match[0];
                $text             = '';


            // no break
	    case 5:

		  preg_match('/\d+/', $text, $match);
		 
		  if ($text === '' || empty($match)) {
                    $notes['state'] = 5;
                    $this->conversation->update();

		    if ($text === '')
                    $data['text'] = 'And what has been the lowest?:';
		    else
		    $data['text'] = $mensajes['latensiondebesernumero'];

                    $result = Request::sendMessage($data);
                    break;
                }

		// hay match, pero en un rango no admitido
		if (!empty($match) && ($match[0] < 20 || $match[0] > 250)){

			$data['text'] =  $mensajes['latensiondebesernumero'];
			// 'Disculpa. No entiendo. Creo que no has metido bien tu valor de tensión arterial. Tecléalo de nuevo, por favor.';
		    $data['reply_markup'] = Keyboard::remove(['oneTime' => true]);
                    $result = Request::sendMessage($data);
                    break;
 
		}

                $notes['diastolica' . $notes['toma']] = $match[0];
                $text             = '';

	// comprobar si ha metido los datos al revés
		if ($notes['diastolica' . $notes['toma']] > $notes['sistolica'. $notes['toma']]){
			$data['text'] = 'I think you have entered the data backwards. Don\'t worry, I have saved them correctly :)';
			$result = Request::sendMessage($data);
			$auxiliar = $notes['diastolica' . $notes['toma']];
			$notes['diastolica' . $notes['toma']] = $notes['sistolica' . $notes['toma']];
			$notes['sistolica' . $notes['toma']] = $auxiliar;
		}


		// error_log(__FILE__ . PHP_EOL . $notes['sistolica'. $notes['toma']] . ":" . $notes['sistolica'. ($notes['toma'] - 1)] . ":" . $notes['sistolica3'], 3, "/tmp/error.log");
		// error_log(__FILE__ . PHP_EOL . $notes['diastolica' . $notes['toma']] . ":" . $notes['diastolica'. ($notes['toma']-1)] . ":" . $notes['diastolica3'], 3, "/tmp/error.log");
		// error_log(  abs($notes['sistolica' . $notes['toma']] - $notes['sistolica'    . ($notes['toma']-1) ] ) . PHP_EOL, 3, "/tmp/error.log");
		// error_log(  abs($notes['diastolica' . $notes['toma']] - $notes['diastolica' . ($notes['toma']-1) ] ) . PHP_EOL, 3, "/tmp/error.log");


		$suffix =  " and they seem to show a stable blood pressure";
		if ( abs($notes['sistolica' . $notes['toma']] - $notes['sistolica'    . ($notes['toma']-1) ] ) > 5  || 
			abs($notes['diastolica' . $notes['toma']] - $notes['diastolica' . ($notes['toma']-1) ] ) > 5 )
		{
			$suffix = "";
			error_log( $notes['toma'] . ":" . $MAX_TOMAS, 3, "/tmp/error.log");

		        if ( $notes['toma'] < $MAX_TOMAS  ){
				$data['text'] = "The difference between measurements is more than 5 points. You must check your blood pressure one more time";
				$result = Request::sendMessage($data);

				Request::sendChatAction([
					'chat_id' => $chat_id,
					'action' => 'typing'
				]);
				sleep(4);

				$notes['state'] = 3;
				$text = '';
				$this->conversation->update();
				$command = "tension";
				return $this->getTelegram()->executeCommand($command);
			}
		}
                
            // no break
            case 6:

                $this->conversation->update();
                $out_text = 'Very well. The data has been entered correctly' . $suffix . '. The results have been:' . PHP_EOL;
                unset($notes['state']);

		$mediasistolica = ((int)$notes['sistolica' . $notes['toma']] + (int)$notes['sistolica' . ($notes['toma']-1)]) / 2;
		$mediadiastolica = ((int)$notes['diastolica' . $notes['toma']] + (int)$notes['diastolica' . ($notes['toma']-1)]) / 2;

		$out_text .= PHP_EOL . "Mean blood pressure (high): " . $mediasistolica;
		$out_text .= PHP_EOL . "Mean blood pressure (low): " . $mediadiastolica; 

		$this->guardarDatos("ta", $mediasistolica, $user_id);
		$this->guardarDatos("tb", $mediadiastolica, $user_id);


                $data['reply_markup'] = Keyboard::remove(['selective' => true]);
                $data['text']      = $out_text;
                $this->conversation->stop();

                $result = Request::sendMessage($data);

		$data['text'] =  $mensajes['cuandoquieraspuedesvolver'];
	       //	"Cuando quieras, puedes volver a pedirme ayuda para guardar los datos de la siguiente toma de tensión.";

		Request::sendChatAction([
				'chat_id' => $chat_id,
				'action' => 'typing'
			]);
		sleep(4);

		if (rand(1,100) <= 25){

			// error_log( print_r ( $this->obtenerAlertas($user_id) , 1 ) . PHP_EOL, 3, "/tmp/error.log");
			list($alerta1, $alerta2) = $this->obtenerAlertas($user_id);

			$data['text'] .= $this->filter($mensajes['yotelorecordare'], $alerta1, $alerta2);
			// " Yo te lo recordaré a las " . $alerta1 . " y a las " . $alerta2 . "." ;
		}


		$data['text'] .= $mensajes['tambienpuedesvertuhistorial'];
	
		// "\nTambién puedes ver tu historial o ver un vídeo explicativo pulsando en los botones que verás aquí abajo 👇";
	
	
			$data['reply_markup'] = (new Keyboard(['/Check ❤️', '/Video 📺', '/Chart 📈', '/Appt 📅']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(false)
                        ->setSelective(true);

		$result = Request::sendMessage($data);


                break;
        }

        return $result;
    }
}
