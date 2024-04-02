<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}


	if (!$edit) {
		die();
	} else {
		if (isset($_POST)) {
					if (isset($_POST["criterio"]) && $_SESSION["gerarchia"] === "0") {
						foreach($_POST["criterio"] as $criterio) {
								$operazione_criterio = "UPDATE";
							if ($criterio["codice"] == "") {
								$criterio["codice"] = 0;
								$operazione_criterio = "INSERT";
							}
							if (isset($criterio["opzioni"])) $criterio["opzioni"] = implode(",",$criterio["opzioni"]);
							if (isset($criterio["script"])) $criterio["script"] = implode(",",$criterio["script"]);

							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_criteri";
							$salva->operazione = $operazione_criterio;
							$salva->oggetto = $criterio;
							$codice_criterio = $salva->save();
							if ($codice_criterio > 0) {
								foreach($_POST["punteggio"][$criterio["id"]] as $punteggio) {
									$operazione = "INSERT";
									if ($punteggio["codice"] != "") $operazione = "UPDATE";
									$punteggio["codice_criterio"] = $codice_criterio;

									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "b_criteri_punteggi";
									$salva->operazione = $operazione;
									$salva->oggetto = $punteggio;
									$codice_punteggio = $salva->save();

									?>
										$("#codice_punteggio_<? echo $punteggio["id"] ?>").val("<? echo $codice_punteggio ?>");
									<?
								}
								foreach($_POST["busta"][$criterio["id"]] as $busta) {
									$operazione = "INSERT";
									if ($busta["codice"] != "") $operazione = "UPDATE";
									$busta["codice_criterio"] = $codice_criterio;

									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "b_criteri_buste";
									$salva->operazione = $operazione;
									$salva->oggetto = $busta;
									$codice_busta = $salva->save();

									?>
									$("#codice_busta_<? echo $busta["id"] ?>").val("<? echo $codice_busta ?>");
									<?
								}
							} else {
								?>
								alert("Errore nel salvataggio.");
								<?
								die();
							}
						}
					}
			?>
			alert('Modifica effettuata con successo');
			window.location.href = window.location.href;
			<?
		}
	}
?>
