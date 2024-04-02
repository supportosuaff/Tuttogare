<?php
	session_start();
	include_once("../config.php");
	include_once($root."/layout/top.php");
	if (isset($_SESSION["ente"])) {
		?>
		<div style="line-height:1.5em; text-align:justify">
			<h1>Policy privacy</h1>
			<?
			if (empty($_SESSION["ente"]["informativa_privacy"])) {
				$path = $config["path_vocabolario"]."/{$_SESSION["language"]}/privacy.php";
				if (file_exists($path)) include($path);
			} else {
				echo $_SESSION["ente"]["informativa_privacy"];
			}
		?>
			<br>
			<br>
		</div>
		<?
	} 
	include_once($root."/layout/bottom.php");
?>
