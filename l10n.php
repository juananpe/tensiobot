<?php

	// obtener lang actual, si existe. Si no existe lang en la BBDD, usar el lang que indica Telegram para este usuario
// $query = "select valor from tensiones where user_id = " . $this->getMessage()->getFrom()->getId(). " and clave = 'lang'";

// print_r($user);

$user_id = gettype($user) == 'object'?$user->getId():$user['user_id'];
$lang = 'es';

	$query = "select valor from tensiones where user_id = " . $user_id. " and clave = 'lang'";
	$rs = $conn->query($query);
	if ($rs->rowCount() > 0){
		list($lang) = $rs->fetch();
		// override user's language code in Telegram
		// $user->language_code = $lang;
	}


	$texts = $conn->query("select * from texts where lang='". $lang ."'");
	foreach($texts as $mensaje){
		$mensajes[$mensaje['mnemo']]= $mensaje['frase'];
	}

