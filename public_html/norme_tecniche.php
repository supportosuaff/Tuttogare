<?php
	session_start();
	include_once("../config.php");
	include_once($root."/layout/top.php");
	if (isset($_SESSION["ente"])) {
	$path = $config["path_vocabolario"]."/{$_SESSION["language"]}/norme-tecniche.php";
	if (file_exists($path)) include($path);
	include_once($root."/layout/bottom.php");
} else {
	echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
}
?>
