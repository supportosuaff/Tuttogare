<?
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
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

	if ($edit && !$lock)
 	{

		$array_id = array();
		$errore = false;
		foreach($_POST["partecipante"] as $partecipante) {
				$operazione = "INSERT";
				if (is_numeric($partecipante["codice"]))
				{
					$operazione = "UPDATE";
				}
				$partecipante["valutatore"] = (isset($partecipante["valutatore"]) ? "S" : "N");
				$partecipante["codice_gara"] = $_POST["codice_gara"];
				$partecipante["codice_ente"] = $_SESSION["ente"]["codice"];
				if (!empty($partecipante["filechunk"])) {

					if (!empty($partecipante["existing_cv"])) {
						$sql_delete = "DELETE FROM b_allegati WHERE codice = :codice_allegato AND codice_ente = :codice_ente";
						$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_allegato"=>$partecipante["existing_cv"],":codice_ente"=>$_SESSION["ente"]["codice"]));
					}

					$percorso = $config["pub_doc_folder"]."/allegati/concorsi";
					$allegato["online"] = 'S';
					$allegato["sezione"] = 'concorsi';
					$allegato["codice_gara"] = $_POST["codice_gara"];
					$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
					$percorso .= "/".$allegato["codice_gara"];
					if (!is_dir($percorso)) mkdir($percorso,0777,true);
					$copy = copiafile_chunck($partecipante["filechunk"],$percorso."/",$config["chunk_folder"]."/".$_SESSION["codice_utente"]);
					$allegato["nome_file"] = $copy["nome_file"];
					$allegato["riferimento"] = $copy["nome_fisico"];
					$allegato["titolo"] = "Curriculum Vitae " . $partecipante["cognome"] . " " . $partecipante["nome"];

					if (file_exists($percorso."/".$allegato["riferimento"])) {

						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "b_allegati";
						$salva->operazione = "INSERT";
						$salva->oggetto = $allegato;
						$partecipante["cv"] = $salva->save();

					}
				}
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_commissioni_concorsi";
				$salva->operazione = $operazione;
				$salva->oggetto = $partecipante;
				$codice = $salva->save();

				if (!is_numeric($codice))
				{
					?>
						jalert('Errore nel salvataggio.<br>Si prega di riprovare');
					<?
					die();
				}
				?>
	      $("#codice_partecipante_<? echo $partecipante["id"] ?>").val("<? echo $codice ?>");
	      <?

	      log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Commissione di gara",false);
				$href = "/concorsi/pannello.php?codice=".$_POST["codice_gara"];

		}
		$atto = array();
		$atto["codice"] = $_POST["codice_gara"];
		$atto["numero_atto_commissione"] = $_POST["numero_atto_commissione"];
		$atto["data_atto_commissione"] = $_POST["data_atto_commissione"];

		if (!empty($_POST["atto_filechunk"])) {

			if (!empty($_POST["existing_atto"])) {
				$sql_delete = "DELETE FROM b_allegati WHERE codice = :codice_allegato AND codice_ente = :codice_ente";
				$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_allegato"=>$_POST["existing_atto"],":codice_ente"=>$_SESSION["ente"]["codice"]));
			}
			$percorso = $config["pub_doc_folder"]."/allegati/concorsi";
			$allegato["online"] = 'S';
			$allegato["sezione"] = 'concorsi';
			$allegato["codice_gara"] = $_POST["codice_gara"];
			$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
			$percorso .= "/".$allegato["codice_gara"];
			if (!is_dir($percorso)) mkdir($percorso,0777,true);
			$copy = copiafile_chunck($_POST["atto_filechunk"],$percorso."/",$config["chunk_folder"]."/".$_SESSION["codice_utente"]);;
			$allegato["nome_file"] = $copy["nome_file"];
			$allegato["riferimento"] = $copy["nome_fisico"];
			$allegato["titolo"] = "Atto di costituzione della commissione";

			if (file_exists($percorso."/".$allegato["riferimento"])) {

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_allegati";
				$salva->operazione = "INSERT";
				$salva->oggetto = $allegato;
				$atto["allegato_atto_commissione"] = $salva->save();

			}
		}
		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_concorsi";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $atto;
		$codice_gara = $salva->save();
		?>
			alert('Modifica effettuata con successo');
    		window.location.href = '<? echo $href ?>';
		<?

	}
?>
