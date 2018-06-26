<?php
	session_start();
	session_destroy();
	$_SESSION['cognome_nome_utente'] = null;
	$_SESSION['utente'] 			= 	null;
	$_SESSION['id_utente'] 			=	null; 
	$_SESSION['livello_utente'] 	=	null;
	$_SESSION['nome_utente'] 		=	null; 
	$_SESSION['cognome_utente'] 	=	null; 
	$_SESSION['username_utente'] 		=	null; 
	$_SESSION['cellulare_utente'] 	=	null;
	$_SESSION['cognome_nome_utente']	= null; 
	$_SESSION['email_utente'] 		=	null;
	$_SESSION['stato_utente'] 		=	null;
	header("location: /login.php");
?>