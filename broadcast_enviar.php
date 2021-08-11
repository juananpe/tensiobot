<?php
/**
 * Usage on CLI: $ php broadcast.php [telegram-chat-id] 
 */

require __DIR__ . '/vendor/autoload.php';

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;

require("db.php");

$telegram = new Telegram($API_KEY, $BOT_NAME);

// Get the chat id and message text from the CLI parameters.
// $chat_id = isset($argv[1]) ? $argv[1] : '';

function filter($message, $user){
	$search = array('{username}', '{date}', '{tiempo}');
	$replace = array($user['username'], date('H:i', time()), (date('H', time()) < 12)?"mañana":"tarde");
	return str_replace($search, $replace, $message);
}


$somethingtodo = false;
$enviados = 0;
// $users = $conn->query("select distinct(evaluator) from voicegrades where sent=0 and grade is null");
$users = $conn->query("
select t.valor as username, a.user_id, hora1, hora2, iniciar, finalizar
 from alertas a, tensiones t
  where iniciar < NOW() AND finalizar > NOW()
			AND   (
				    (	(HOUR(hora1)=HOUR(NOW()) AND MINUTE(hora1) = MINUTE(NOW()) )  AND a.finalizar > hora1 )
				   OR  
					( (HOUR(hora2)=HOUR(NOW()) AND MINUTE(hora2) = MINUTE(NOW()) )) AND a.finalizar > hora2 )
			AND a.user_id = t.user_id and t.clave = 'username'
					");

// $texts = $conn->query("select * from texts where id = 1");
// $message = $texts->fetch()['frase'];


foreach($users as $user) {

	require("l10n.php");

	$chat_id =  $user['user_id'] ;

	    $data = [
		'chat_id' => $chat_id,
		'text'    => filter($mensajes['holasonlas'], $user),
	    ];

	    $data['reply_markup'] = (new Keyboard([$mensajes['tension'] . ' ❤️', $mensajes['video'].' 📺', $mensajes['historial'] . ' 📈', $mensajes['cita'] . ' 📅']))
		// $data['reply_markup'] = (new Keyboard(['/Tension', '/Video', '/Historial', '/Cita']))
		->setResizeKeyboard(true)
		->setOneTimeKeyboard(true)
		->setSelective(true);


	    $result = Request::sendMessage($data);

/*
	    $tipselect =  "SELECT tip FROM tips ORDER BY RAND() LIMIT 1";
	    $tipresult = $conn->query($tipselect);
	    $data['text'] = "Recuerda: " . $tipresult->fetch()[0];

	    Request::sendChatAction([
				'chat_id' => $chat_id,
				'action' => 'typing'
			]);
	    sleep(3);
	    $result = Request::sendMessage($data);
 */

	    // $result = Request::emptyResponse();

	    if ($result->isOk()) {
		    echo 'Message succesfully sent to: ' . $chat_id . "\n";

		    // $markassent = "update voicegrades set sent=sent+1 where evaluator=". $chat_id ; 
		    // $sent = $conn->query($markassent);
			$somethingtodo = true;
	   		$enviados++;
	    } else {
		echo 'Sorry message not sent to: ' . $chat_id;
	    }
}

print_r("Enviados: $enviados \n");

if (!$somethingtodo)
	echo "Nothing to do\n";
