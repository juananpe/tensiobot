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

function filter($message, $cita){
	$search = array('{cita}');
	$replace = array($cita);
	return str_replace($search, $replace, $message);
}

$somethingtodo = false;
$enviados = 0;



$users = $conn->query("
SELECT t1.user_id as user_id, t2.valor as username, t1.valor as fechacita 
FROM tensiones t1, tensiones t2
where t1.clave='cita'  and t1.user_id = t2.user_id
and t2.clave='username'
and STR_TO_DATE(t1.valor, '%d/%m/%Y') = CURDATE() + INTERVAL 7 DAY
");

$texts = $conn->query("select * from texts where mnemo = 'citarecordatorio'");
$message = $texts->fetch()['frase'];

foreach($users as $user) {
	$chat_id =  $user['user_id'] ;

	if ($chat_id !== '' && $message !== '') {
	    $data = [
		'chat_id' => $chat_id,
		'text'    => filter($message, $user['fechacita']),
	    ];

	    $result = Request::sendMessage($data);
	    // $result = Request::emptyResponse();
	    // print_r($data);


	    if ($result->isOk()) {
		    echo 'Message succesfully sent to: ' . $chat_id . "\n";

		    $markassent = "insert into tensiones set user_id=". $chat_id . ",
			   	clave='recordatorioenviado', valor=NOW()" ; 
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
