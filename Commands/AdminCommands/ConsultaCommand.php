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
 * User "/consulta" command
 */
class ConsultaCommand extends AdminCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'consulta';
    protected $description = 'Ver datos de paciente X';
    protected $usage = '/consulta idPaciente';
    protected $version = '1.0.0';
    /**#@-*/

    protected $filename = "datosconsulta.xlsx";

    protected $conn = null;

    private function obtenerTensiones($userid){
	$tensiones = "select clave, valor, datecreated from tensiones
where user_id = $userid and (clave = 'ta' or clave = 'tb')
order by datecreated desc"; 
	$rs = $this->conn->query($tensiones);
	return $rs;
    }


    private function obtenerCita($userid){
	$cita = "select valor from tensiones
	where user_id = $userid and clave = 'cita'"; 
	$rs = $this->conn->query($cita);
	return $rs->fetch();
    }


    private function writeXLS($rows, $paciente){
	    include_once("xlsxwriter.class.php");
	    ini_set('display_errors', 0);
	    ini_set('log_errors', 1);
	    error_reporting(E_ALL & ~E_NOTICE);

        	$file_path    = $this->telegram->getDownloadPath() . '/' . $this->filename;

	    $writer = new XLSXWriter();
	    $writer->setAuthor('Tensiobot');
	    foreach($rows as $row)
		    $writer->writeSheetRow($paciente, $row);

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
	    $usuario = $text;

	    if (! in_array( $chat_id, $this->telegram->getAdminList())) 
		   return ;

	    if ($text === '') {
		    $text = 'Uso: ' . $this->getUsage();
	    } else {

		    require("db.php");
		    $this->conn = $conn;


		    $rs = $this->obtenerTensiones($usuario);
		    $rows = array();
		    array_push($rows, array("Clave", "Valor", "Fecha"));
		    $text = "";

		    while ($tensiones = $rs->fetch()){
			    $text .= $tensiones['clave'] . " : " . $tensiones['valor'] . " :" . $tensiones['datecreated'] . "\n" ;
			    $text .= "================== \n";


			   array_push($rows, array($tensiones['clave'], $tensiones['valor'], $tensiones['datecreated'])); 
		    }	

		    list($cita) = $this->obtenerCita($usuario);
		    if ($cita == "") $cita = "Sin especificar";
		    $text .= "Fecha próxima cita: " . $cita . "\n";
		    
	    }

	    $data = [
		    'chat_id' => $chat_id,
		    'text'    => $text,
	    ];


	    Request::sendMessage($data);
	    $this->writeXLS($rows, $usuario);
 	    return Request::sendDocument($data, $this->telegram->getUploadPath() . '/' . $this->filename );

    }
}
