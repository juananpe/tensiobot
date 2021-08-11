<?php
/**
 * Usage on CLI: $ php broadcast.php [telegram-chat-id] 
 */

require __DIR__ . '/vendor/autoload.php';

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

require("db.php");


$telegram = new Telegram($API_KEY, $BOT_NAME);

// Get the chat id and message text from the CLI parameters.
// $chat_id = isset($argv[1]) ? $argv[1] : '';

$somethingtodo = false;
$enviados = 0;
$users = $conn->query("

select t.id, t.user_id, t.clave, t.valor, a.finalizar, t.notes
 from tensiones t, alertas a
  where t.clave = 'alerta' AND TIMESTAMPDIFF(MINUTE, t.valor,NOW()) >= 2 AND t.notes IS NULL
  and t.user_id = a.user_id"); # and a.finalizar > NOW() ");



foreach($users as $user) {
	require("l10n.php");
	
	$chat_id =  $user['user_id'] ;

	$message = $mensajes['yahanpasado2minutos'];

	// "Ya han pasado dos minutos. Puedes tomarte la tensión 😃";

	if ($chat_id !== '' && $message !== '') {
	    $data = [
		'chat_id' => $chat_id,
		'text'    => $message,
	    ];

	    $result = Request::sendMessage($data);

	    // $result = Request::emptyResponse();

	    if ($result->isOk()) {
		    echo 'Message succesfully sent to: ' . $chat_id . "\n";

		    	$markassent = "update tensiones set notes = 'sent' where user_id=". $chat_id . " AND clave='alerta' AND notes IS NULL"; 
		    	$sent = $conn->query($markassent);
			$somethingtodo = true;
	   		$enviados++;
	    } else {
		echo 'Sorry message not sent to: ' . $chat_id;
	    }
	}
}

print_r("Enviados: $enviados \n");

if (!$somethingtodo)
	echo "Nothing to do\n";
