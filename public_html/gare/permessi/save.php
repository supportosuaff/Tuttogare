<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;

	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
			if ($codice_fase !== false) {
				$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
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
			$strsql = "DELETE FROM b_permessi WHERE codice_gara = :codice AND codice_ente = :codice_ente";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$bind = array();
			$bind[":codice"] = $_POST["codice_gara"];
			$strsql = "DELETE FROM r_permessi_apertura_buste WHERE codice_gara = :codice ";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$salva = new salva();
			if (!isset($_POST["utenti"])) $_POST["utenti"] = [];
			if (isset($_POST["permesso_busta"])) {
				foreach($_POST["permesso_busta"] AS $codice_busta => $codice_utente) {
					if (!empty($codice_utente)) {
						$tmp = array();
						$tmp["codice_gara"] = $_POST["codice_gara"];
						$tmp["codice_utente"] = $codice_utente;
						$tmp["codice_busta"] = $codice_busta;
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_permessi_apertura_buste";
						$salva->operazione = "INSERT";
						$salva->oggetto = $tmp;
						$salva->save();
						$_POST["utenti"][] = $codice_utente;
					}
				}
			}
			if (isset($_POST["utenti"])) {
				$_POST["utenti"] = array_unique($_POST["utenti"]);
				foreach($_POST["utenti"] as $utente_abilitato) {
					$utente = array();
					$utente["codice_gara"] = $_POST["codice_gara"];
					$utente["codice_ente"] = $_SESSION["ente"]["codice"];
					$utente["codice_utente"] = $utente_abilitato;

					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_permessi";
					$salva->operazione = "INSERT";
					$salva->oggetto = $utente;
					$codice_utente = $salva->save();
				}
			}
			log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Permessi");
				$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
				?>
				alert('Modifica effettuata con successo');
				window.location.href = '<? echo $href ?>';
			<?
	}

?>
