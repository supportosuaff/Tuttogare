<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("news",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["operazione"])) {
			if (isset($_POST["servizio"])) {
				$_POST["servizio"] = 1;
			} else {
				$_POST["servizio"] = 0;
			}
			if (isset($_SESSION["ente"])) $_POST["codice_ente"] = $_SESSION["ente"]["codice"];

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_news";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST;
			$codice = $salva->save();

			$titolo = $_POST["titolo"];
			$href = "/news/id".$codice."-".sanitize_string($titolo);
			if ($_POST["operazione"]=="UPDATE") {
				?>
				alert('Modifica effettuata con successo');
				<?
			} elseif ($_POST["operazione"]=="INSERT") {
				?>
				alert('Inserimento effettuato con successo');
				<?
			}
			?>
			window.location.href = '<? echo $href ?>';
			<?
		}
	}



?>
