<?
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$strsql = "SELECT * FROM b_gestione_gare WHERE link LIKE '/gare/commissione/edit.php%'";
		$risultato = $pdo->query($strsql);
		if ($risultato->rowCount()>0) {
			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
			$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
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
		$tecnica = isset($_POST["tecnica"]) ? true : false;
		$array_id = array();
		$errore = false;
		foreach($_POST["partecipante"] as $partecipante) {
				$operazione = "INSERT";
				$partecipante["token"] = strtoupper(bin2hex(openssl_random_pseudo_bytes(32)));
				$password = randomPassword(14);
				$partecipante["password"] = password_hash(md5($password), PASSWORD_BCRYPT);
				if (is_numeric($partecipante["codice"]))
				{
					$operazione = "UPDATE";
					unset($partecipante["password"]);
					unset($partecipante["token"]);
				}
				$partecipante["valutatore"] = $tecnica ? "S" : "N";
				$partecipante["codice_gara"] = $_POST["codice_gara"];
				$partecipante["codice_ente"] = $_SESSION["ente"]["codice"];
				if (!empty($partecipante["filechunk"])) {

					ini_set('memory_limit', '-1');
					ini_set('max_execution_time', 600);
					if (!empty($partecipante["existing_cv"])) {
						$sql_delete = "DELETE FROM b_allegati WHERE codice = :codice_allegato AND codice_ente = :codice_ente";
						$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_allegato"=>$partecipante["existing_cv"],":codice_ente"=>$_SESSION["ente"]["codice"]));
					}

					$percorso = $config["pub_doc_folder"]."/allegati";
					$allegato["online"] = 'S';
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
				$salva->nome_tabella = "b_commissioni";
				$salva->operazione = $operazione;
				$salva->oggetto = $partecipante;
				$codice = $salva->save();

				if (is_numeric($codice))
				{
					if($operazione == "INSERT")
					{
						$codice_gara = $partecipante["codice_gara"];
						$token = $partecipante["token"];
						$destinatario = $partecipante["pec"];
						if ($partecipante["valutatore"] == "S") {
							/* include('invia.php');
							if ($errore_mail)
							{
								$bind = array();
								$bind[":codice"] = $codice;
								$sql_delete = "DELETE FROM b_commissioni WHERE codice = :codice";
								$ris_delete = $pdo->bindAndExec($sql_delete,$bind);
								die();
							} */
						}
					}
				}
				else
				{
					?>
						jalert('Errore nel salvataggio.<br>Si prega di riprovare');
					<?
					die();
				}
				?>
	      $("#codice_partecipante_<? echo $partecipante["id"] ?>").val("<? echo $codice ?>");
	      <?

	      log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Commissione di gara",false);
				
		}
		$denominazioni = ($tecnica) ? "commissione" : "seggio";

		$atto = array();
		$atto["codice"] = $_POST["codice_gara"];
		$atto["numero_atto_" . $denominazioni] = $_POST["numero_atto_" . $denominazioni];
		$atto["data_atto_" . $denominazioni] = $_POST["data_atto_" . $denominazioni];

		if (!empty($_POST["atto_filechunk"])) {

			if (!empty($_POST["existing_atto"])) {
				$sql_delete = "DELETE FROM b_allegati WHERE codice = :codice_allegato AND codice_ente = :codice_ente";
				$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_allegato"=>$_POST["existing_atto"],":codice_ente"=>$_SESSION["ente"]["codice"]));
			}
			$percorso = $config["pub_doc_folder"]."/allegati";
			$allegato["online"] = 'S';
			$allegato["codice_gara"] = $_POST["codice_gara"];
			$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
			$percorso .= "/".$allegato["codice_gara"];
			if (!is_dir($percorso)) mkdir($percorso,0777,true);
			$copy = copiafile_chunck($_POST["atto_filechunk"],$percorso."/",$config["chunk_folder"]."/".$_SESSION["codice_utente"]);;
			$allegato["nome_file"] = $copy["nome_file"];
			$allegato["riferimento"] = $copy["nome_fisico"];
			$allegato["titolo"] = "Atto di costituzione della commissione";$allegato["titolo"] = "Atto di costituzione";
			$allegato["titolo"] .= ($tecnica) ? "della Commissione valutatrice" : "del Seggio di gara";

			if (file_exists($percorso."/".$allegato["riferimento"])) {

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_allegati";
				$salva->operazione = "INSERT";
				$salva->oggetto = $allegato;
				$atto["allegato_atto_" . $denominazioni] = $salva->save();

			}
		}
		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_gare";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $atto;
		$codice_gara = $salva->save();
		?>
			alert('Modifica effettuata con successo');
    		window.location.reload();
		<?

	}
?>
