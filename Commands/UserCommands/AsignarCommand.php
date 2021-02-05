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
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
/**
 * User "/asignar" command
 */
class AsignarCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'asignar';
    protected $description = 'Asignar nombre al usuario';
    protected $usage = '/asignar';
    protected $version = '1.0.0';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = trim($message->getText(true));
/*
        if ($text === '') {
            $text = 'Uso: ' . $this->getUsage();
        }
*/
	require("db.php");

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

        $data = [
            'chat_id' => $chat_id,
            'text'    => "Hola " . $text. ". Tienes varias opciones, pulsa sobre la que más te interese.",
        ];

	$data['reply_markup'] = (new Keyboard(['/Tension', '/Video', '/Historial']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(false)
                        ->setSelective(true);

        return Request::sendMessage($data);
    }
}
