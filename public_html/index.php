<?php
	session_start();
	include_once("../config.php");
	$redirect = false;
	$open_page = true;
	$index_page = true;
	include_once($root."/layout/top.php");
	if ($echo_layout) {
		if (isset($_SESSION["ente"])) {
			include_once($root . '/home_ente.php');
		} else {
			include("dashboard.php");
		}
	} else {
		echo '<h1>IMPOSSIBILE ACCEDERE</h1>';
		die();
	}

	include_once($root."/layout/bottom.php");
?>