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
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
/**
 * User "/lang" command
 */
class LangCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'lang';
    protected $description = 'Cambiar idioma';
    protected $usage = '/lang';
    protected $version = '1.0.0';
    /**#@-*/

    /**
     * {@inheritdoc}
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

	// obtener lang actual, si existe
	$query = "select id, valor from tensiones where user_id = " . $this->getMessage()->getFrom()->getId(). " and clave = 'lang'";
	$rs = $conn->query($query);
	$extra = "";
	if ($rs->rowCount() > 0){
		list($id, $lang) = $rs->fetch();
		$extra = ", id = " . $id;
		// override user's language code in Telegram
		$user->language_code = $lang;
	}

	$mensajes = [];
	require("l10n.php");

	switch ($state) {
	case 0:
		if ($text === '' || !in_array($text, ['es','en','eu'] )) {
			 $notes['state'] = 0;
			 $this->conversation->update();

			 $data['text'] = $mensajes['changelanguage'];
			 // "Aldatu zure hizkuntza";
			 $opciones = ['es', 'eu', 'en'];

			//   $data['reply_markup'] = Keyboard::remove(['selective' => true]);

		        $data['reply_markup'] = (new Keyboard($opciones))
                       ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                         ->setSelective(true);

                    $result = Request::sendMessage($data);
			
			 break;

		} else { 
			// $text          = '';
			$user->language_code = $text;
	                $notes['state'] = 1;
			$this->conversation->update();
			// no break;
		}

	case 1:
		
	
	// reload messages in new language
	$mensajes = [];
	require("l10n.php");


	// cambiar lang 
	$query = "replace into tensiones set valor='". $text .
			"', user_id = " . $this->getMessage()->getFrom()->getId() . 
			", clave='lang'" . $extra;
	$stmt =  $conn->query($query);

        $data = [
            'chat_id' => $chat_id,
            'text'    => $mensajes['sehacambiadolang']  // . ". Tienes varias opciones, pulsa sobre la que más te interese.",
        ];

/*	$data['reply_markup'] = (new Keyboard(['/Tension', '/Video', '/Historial']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(false)
                        ->setSelective(true);
 */


	$data['reply_markup'] = Keyboard::remove(['selective' => true, 'oneTime' => true, 'resize' => true]);
	$results = Request::sendMessage($data);

	unset($notes['state']);
			$this->conversation->update();	
                $this->conversation->stop();

	}


        return $result;
    }

}
