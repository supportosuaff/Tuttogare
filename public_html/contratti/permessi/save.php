<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if(!empty($_SESSION["codice_utente"]) && !empty($_SESSION["ente"]) && check_permessi("contratti",$_SESSION["codice_utente"])) {
		if(!empty($_POST["codice_contratto"])) {
			$sql = "DELETE FROM b_permessi_contratti WHERE codice_contratto = :codice_contratto AND codice_ente = :codice_ente";
			$ris = $pdo->bindAndExec($sql, array(':codice_contratto' => $_POST["codice_contratto"], ':codice_ente' => $_SESSION["ente"]["codice"]));
			if(!empty($_POST["utenti"])) {
				$salva = new salva();
				$salva->debug = FALSE;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_permessi_contratti";
				$salva->operazione = "INSERT";
				foreach ($_POST["utenti"] as $codice_utente) {
					$utente = array();
					$utente["codice_contratto"] = $_POST["codice_contratto"];
					$utente["codice_ente"] = $_SESSION["ente"]["codice"];
					$utente["codice_utente"] = $codice_utente;
					$salva->oggetto = $utente;
					$salva->save();
				}
			}
			$href = "/contratti/pannello.php?codice=".$_POST["codice_contratto"];
			?>
				alert('Modifica effettuata con successo');
				window.location.href = '<? echo $href ?>';
			<?
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
		}
	} else {
		header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
	}
?>
