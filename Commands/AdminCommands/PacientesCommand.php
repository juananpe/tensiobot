<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\AdminCommands;

use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Request;
use \XLSXWriter;

/**
 * User "/pacientes" command
 */
class PacientesCommand extends AdminCommand
{
    /**#@+
     * {@inheritdoc}
     */
	protected $name = 'pacientes';
	protected $description = 'Ver datos de pacientes';
	protected $usage = '/pacientes';
	protected $version = '1.0.0';
	/**#@-*/


	protected $filename = "pacientes.xlsx";

	protected $conn = null;

    private function obtenerPacientes(){
	$pacientes = "SELECT user_id, valor as username, datecreated FROM tensiones where clave='username' and datecreated >= '2017-10-19'";
	$rs = $this->conn->query($pacientes);
	return $rs;
    }

    private function obtenerCita($user_id){
	$cita = "SELECT valor FROM tensiones where clave = 'cita' and user_id = $user_id";
	$rs = $this->conn->query($cita);
	return $rs->fetch()['valor'];
    }

    private function writeXLS($rows){
	    include_once("xlsxwriter.class.php");
	    ini_set('display_errors', 0);
	    ini_set('log_errors', 1);
	    error_reporting(E_ALL & ~E_NOTICE);

        	$file_path    = $this->telegram->getDownloadPath() . '/' . $this->filename;

	    $writer = new XLSXWriter();
	    $writer->setAuthor('Tensiobot');
	    foreach($rows as $row)
		    $writer->writeSheetRow('Pacientes', $row);

	    $writer->writeToFile($file_path);

    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = trim($message->getText(true));


        if (! in_array( $chat_id, $this->telegram->getAdminList())) 
		   return ;

        if ($text === '') {
            $text = 'Uso: ' . $this->getUsage();
        }

	require("db.php");
	$this->conn = $conn;

	$rs = $this->obtenerPacientes();

	$text = "";
	$rows = array();
	array_push($rows, array("UserID", "Username", "FechaAltaTensiobot", "FechaCita"));
	while ($paciente = $rs->fetch()){
		$text .= "User ID:" . $paciente['user_id'] . "\nUser name: " . $paciente['username'] . "\nDate created:" . $paciente['datecreated'] . "\n" ;
		$cita = $this->obtenerCita($paciente['user_id']);
		$text .= "Cita:" . $cita . "\n";
		$text .= "================== \n";

		array_push($rows, array($paciente['user_id'], $paciente['username'], $paciente['datecreated'], $cita)); 
	}	

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
	    ];

        Request::sendMessage($data);

	$this->writeXLS($rows);
	
 	return Request::sendDocument($data, $this->telegram->getUploadPath() . '/' . $this->filename );


    }
}
