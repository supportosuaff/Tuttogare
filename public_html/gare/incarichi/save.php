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
		if ($edit && !$lock)
	 {
			$array_id = array();
			$errore = false;

			$check_cf = $pdo->prepare("SELECT codice FROM b_incaricati WHERE codice_fiscale = :codice_fiscale");

			foreach($_POST["incarico"] as $incarico) {
				$operazione = "INSERT";
				$check_cf->bindValue(":codice_fiscale",$incarico["codice_fiscale"]);
				$check_cf->execute();
				if ($check_cf->rowCount() > 0) {
					$operazione = "UPDATE";
					$incarico["codice"] = $check_cf->fetch(PDO::FETCH_ASSOC)["codice"];
				}
				$incarico["codice_gara"] = $_POST["codice_gara"];
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_incaricati";
				$salva->operazione = $operazione;
				$salva->oggetto = $incarico;
				$codice = $salva->save();
				if ($codice != false) {
					?>
						$("#codice_incarico_<? echo $incarico["id"] ?>").val("<? echo $codice ?>");
					<?
						if ($incarico["ruolo"] == "14") {
							$agg_gara = array();
							$agg_gara["rup"] = $incarico["cognome"] . " " . $incarico["nome"];
							$agg_gara["codice"] = $_POST["codice_gara"];
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_gare";
							$salva->operazione = "UPDATE";
							$salva->oggetto = $agg_gara;
							$codice_gara = $salva->save();
						}
						$operazione_relazione = "INSERT";
						if (is_numeric($incarico["codice_relazione"])) $operazione_relazione = "UPDATE";
						$relazione = array();
						$relazione["codice"] = $incarico["codice_relazione"];
						$relazione["codice_incaricato"] = $codice;
						$relazione["codice_riferimento"] = $_POST["codice_gara"];
						$relazione["ruolo"] = $incarico["ruolo"];
						$relazione["numero_atto"] = $incarico["numero_atto"];
						$relazione["data_atto"] = $incarico["data_atto"];
						$relazione["sezione"] = "gare";
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_incarichi";
						$salva->operazione = $operazione_relazione;
						$salva->oggetto = $relazione;
						$codice_relazione = $salva->save();
						if ($codice_relazione == false) {
							$errore = true;
							?>
								$("#incarico_<? echo $incarico["id"] ?>").addClass("errore");
							<?
						}
				} else {
					$errore = true;
					?>
						$("#incarico_<? echo $incarico["id"] ?>").addClass("errore");
					<?
				}
			}
			if (!$errore) {
				log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Incarichi",false);
				$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
				?>
				alert('Modifica effettuata con successo');
        window.location.href = '<? echo $href ?>';
        <?
		} else {
			?>
			alert('Si sono verificati degli errori durante il salvataggio');
			<?
		}
	}

?>
