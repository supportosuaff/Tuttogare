<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = true;
		if (((isset($_GET["cod"])) && ((isset($_SESSION["codice_utente"])) && ($_GET["cod"] == $_SESSION["codice_utente"])) || (isset($_SESSION["tmp_codice_utente"])) && ($_GET["cod"] == $_SESSION["tmp_codice_utente"])) ||
				(isset($_SESSION["amministratore"]) && $_SESSION["amministratore"])) {
					$codice = $_GET["cod"];
					$bind = array(':codice' => $codice);
					$strsql = "SELECT b_utenti.*, b_gruppi.id as tipo FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice WHERE b_utenti.codice = :codice";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
							$record_utente = $risultato->fetch(PDO::FETCH_ASSOC);
							$strsql = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice";
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount() > 0) {
								$record_operatore = $risultato->fetch(PDO::FETCH_ASSOC);
								$bind = array(':codice' => $record_operatore["codice"]);
								$operazione = "UPDATE";
								$strsql = "SELECT * FROM b_ccnl WHERE codice_operatore = :codice";
								$risultato_ccnl = $pdo->bindAndExec($strsql,$bind);
								$strsql = "SELECT * FROM b_rappresentanti WHERE codice_operatore = :codice";
								$risultato_rappresentanti = $pdo->bindAndExec($strsql,$bind);

								$string_cpv = "";
								$cpv = array();
								if ($_SESSION["language"] == "IT") {
									$strsql = "SELECT b_cpv.* FROM b_cpv
														 JOIN r_cpv_operatori ON b_cpv.codice = r_cpv_operatori.codice
														 WHERE r_cpv_operatori.codice_operatore = :codice ORDER BY codice";
								} else {
									$strsql = "SELECT b_cpv.*, b_cpv_dict.{$_SESSION["language"]} AS descrizione
														 FROM b_cpv
														 JOIN b_cpv_dict ON b_cpv.codice_completo = b_cpv_dict.codice_completo
														 JOIN r_cpv_operatori ON b_cpv.codice = r_cpv_operatori.codice
														 WHERE r_cpv_operatori.codice_operatore = :codice ORDER BY b_cpv.codice";
								}

								$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
								if ($risultato_cpv->rowCount()>0) {
									$risultato_cpv = $risultato_cpv->fetchAll(PDO::FETCH_ASSOC);
									foreach ($risultato_cpv AS $rec_cpv) {
										$cpv[] = $rec_cpv["codice"];
									}
									$string_cpv = implode(";",$cpv);
								}
								$strsql = "SELECT * FROM b_committenti WHERE codice_operatore = :codice";
								$risultato_committenti = $pdo->bindAndExec($strsql,$bind);
								$strsql = "SELECT * FROM b_certificazioni_qualita WHERE codice_operatore = :codice";
								$risultato_qualita = $pdo->bindAndExec($strsql,$bind);
								$strsql = "SELECT * FROM b_certificazioni_soa WHERE codice_operatore = :codice AND codice_classifica > 0";
								$risultato_soa = $pdo->bindAndExec($strsql,$bind);
								$strsql = "SELECT * FROM b_certificazioni_soa WHERE codice_operatore = :codice AND codice_classifica = 0";
								$risultato_soa_fatturato = $pdo->bindAndExec($strsql,$bind);
								$strsql = "SELECT * FROM b_esperienze_progettazione WHERE codice_operatore = :codice";
								$risultato_progettazione = $pdo->bindAndExec($strsql,$bind);
								$strsql = "SELECT * FROM b_certificazioni_ambientali WHERE codice_operatore = :codice";
								$risultato_ambientali = $pdo->bindAndExec($strsql,$bind);
								$strsql = "SELECT * FROM b_altre_certificazioni WHERE codice_operatore = :codice";
								$risultato_certificazioni = $pdo->bindAndExec($strsql,$bind);
								$strsql = "SELECT * FROM b_brevetti WHERE codice_operatore = :codice";
								$risultato_brevetti = $pdo->bindAndExec($strsql,$bind);
								$bozza = true;
								include("form.php");
								$enter = true;
						}
					}
      	}
	if (!isset($enter)) {
		?>
		<h1 style="text-align:center; color:#F30">IMPOSSIBILE ACCEDERE</h1>
		<br>
		<h2 style="text-align:center">Controllare i dati e riprovare. Se il problema persiste si prega di contattare l'Help Desk</h2>
		<?
	}
	include_once($root."/layout/bottom.php");
	?>
