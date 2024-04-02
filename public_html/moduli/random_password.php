<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	echo genpwd(16);
?>