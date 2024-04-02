<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;

	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
			if ($codice_fase !== false) {
				$esito = check_permessi_concorso($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}
		if ($edit && !$lock) {
			$bind = array();
			$bind[":codice"] = $_POST["codice_gara"];
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "DELETE FROM b_permessi_concorsi WHERE codice_gara = :codice AND codice_ente = :codice_ente";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if (isset($_POST["utenti"])) {
				foreach($_POST["utenti"] as $utente_abilitato) {
					$utente = array();
					$utente["codice_gara"] = $_POST["codice_gara"];
					$utente["codice_ente"] = $_SESSION["ente"]["codice"];
					$utente["codice_utente"] = $utente_abilitato;

					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_permessi_concorsi";
					$salva->operazione = "INSERT";
					$salva->oggetto = $utente;
					$codice_utente = $salva->save();
				}
			}
			log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Permessi");
			$href = "/concorsi/pannello.php?codice=".$_POST["codice_gara"];
			?>
			alert('Modifica effettuata con successo');
			window.location.href = '<? echo $href ?>';
		<?
	}

?>
