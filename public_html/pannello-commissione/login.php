<?
	include_once("../../config.php");
	if (!empty($_GET["token"]) && !empty($_GET["codice"]))
	{
		$pagina_login = true;
		include_once($root."/layout/top.php");
		$token = $_GET["token"];
		$codice_gara = $_GET["codice"];
		if (!isset($_SESSION["codice_commissario"])) {
			$errore_login = false;
			if (isset($_POST["password"])) {
				$errore_login = true;
				$password = md5($_POST["password"]);
				$bind = array();
				$bind[":codice_gara"] = $codice_gara;
				$bind[":token"] = $token;
				$sql_login = "SELECT b_commissioni.*
											FROM `b_commissioni` JOIN b_gare ON b_commissioni.codice_gara = b_gare.codice
											WHERE `b_commissioni`.`token` = :token
											AND b_gare.stato = 3
											AND `b_commissioni`.`codice_gara` = :codice_gara
											AND `b_commissioni`.`valutatore` = 'S'";
				$ris_login = $pdo->bindAndExec($sql_login,$bind);
				if ($ris_login->rowCount() > 0)
				{
					$rec_login = $ris_login->fetch(PDO::FETCH_ASSOC);
					if (password_verify($password,$rec_login["password"])) {
						$_SESSION["codice_commissario"] = $rec_login["codice"];
						$_SESSION["token_commissario"] = $rec_login["token"];
						$_SESSION["commissario"] = $rec_login["titolo"] . " " . $rec_login["cognome"] . " " . $rec_login["nome"];
						echo '<meta http-equiv="refresh" content="0;URL=/pannello-commissione/pannello.php?codice='.$codice_gara.'">';
						die();
					}
				}
			}
			?>
			<div id="div_login">
				<div style="padding:50px">
					<div style="text-align:center">
						<img src="/documenti/enti/<?= $_SESSION["ente"]["logo"] ?>" width="30%" alt="<?= $_SESSION["ente"]["denominazione"] ?>"><br>
						<strong><?= $_SESSION["ente"]["denominazione"] ?></strong><br><br>
						<div class="clear"></div>
					</div>
					<br>
					<form name="login" method="post" target="_self" rel="validate" action="/pannello-commissione/login.php?token=<?= $token?>&codice=<?= $codice_gara ?>">
						<label for="Password">Password</label><br>
						<input type="password" name="password" title="Password" rel="S;3;16;A" class="titolo_edit <?= ($errore_login ? "ui-state-error" : "") ?>" maxlength="16" placeholder="Password"><br>
						<input type="submit" value="login" class="submit_big">
					</form>
					<? if ($errore_login) { ?>
						<div id="msg_login" style="color:#000; font-weight:bold;">
							Utente non riconosciuto o gara
						</div>
					<? } ?>
				</div>
			</div>
			<?
		} else {
			echo '<meta http-equiv="refresh" content="0;URL=/pannello-commissione/pannello.php?codice='.$codice_gara.'">';
			die();
		}
		include_once($root."/layout/bottom.php");
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	?>
