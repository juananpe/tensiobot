<?php

	$texts = $conn->query("select * from texts");
	foreach($texts as $mensaje){
		$mensajes[$mensaje['mnemo']]= $mensaje['frase'];
	}

