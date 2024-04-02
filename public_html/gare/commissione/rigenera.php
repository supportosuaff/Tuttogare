<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("gare/elaborazione",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["codice"]) && $_SESSION["gerarchia"]<=2) {
			$codice = $_POST["codice"];
			if (is_numeric($codice)) {
				$bind = array();
				$bind[":codice"] = $codice;
				$strsql = "SELECT * FROM b_commissioni WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$codice_gara = $record["codice_gara"];
					$token = $record["token"];
					$destinatario = $record["pec"];
					$password = randomPassword(14);
					$cryptPassword = password_hash(md5($password), PASSWORD_BCRYPT);
					$bind[":password"] = $cryptPassword;
					$strsql = "UPDATE b_commissioni SET password = :password  WHERE codice = :codice";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					scrivilog("b_commissioni","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
					log_gare($_SESSION["ente"]["codice"],$record["codice_gara"],"UPDATE","Credenziali commissario - " . $record["titolo"] . "  " . $record["cognome"] . " " . $record["nome"]);
					include_once('invia.php');
					?>
					alert('Credenziali Rigenerate!');
					<?
				}
				else
				{
					?>
					alert('Errore si prega di Riprovare!');
					<?
				}
			}
			else
			{
				?>
					alert('Errore si prega di Riprovare!');
				<?
			}
		}
	}

?>
