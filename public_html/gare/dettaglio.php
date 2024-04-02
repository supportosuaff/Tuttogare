<?
	include_once("../../config.php");
	$disable_alert_sessione = true;
	include_once($root."/layout/top.php");
	$public = true;
	$echo_invitati = FALSE;
		if (isset($_GET["cod"]) && isset($_SESSION["ente"]["codice"])) {
				$codice = $_GET["cod"];
				$bind = array(":codice"=>$codice,":codice_ente"=>$_SESSION["ente"]["codice"]);
				if (!isset($_SESSION["codice_utente"])) {
					$strsql  = "SELECT b_gare.*, b_stati_gare.titolo AS fase, b_stati_gare.colore, b_enti.dominio
											FROM b_gare
											JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase
											JOIN b_enti ON b_gare.codice_gestore = b_enti.codice
											WHERE b_gare.codice = :codice
											AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
											AND pubblica = '2' ";
				} else {
					if (is_operatore()) {
						$bind[":codice_utente"] = $_SESSION["codice_utente"];
						$strsql  = "SELECT b_gare.*, b_stati_gare.titolo AS fase, b_stati_gare.colore, b_enti.dominio
									FROM b_gare 
									JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase
									LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara
									JOIN b_procedure ON b_gare.procedura = b_procedure.codice
									JOIN b_enti ON b_gare.codice_gestore = b_enti.codice
									WHERE b_gare.codice = :codice ";
						$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
						$strsql .= "AND (pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente)))) ";
					} else {
						$strsql  = "SELECT b_gare.*,b_stati_gare.titolo AS fase, b_stati_gare.colore, b_enti.dominio
									FROM b_gare
									JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase
									JOIN b_enti ON b_gare.codice_gestore = b_enti.codice
									WHERE b_gare.codice = :codice ";
						$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
						if (!check_permessi_gara(1, $codice, $_SESSION["codice_utente"])["permesso"]) {
							$strsql .= "AND (pubblica > 0)";
						}
					}
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					if ($record["codice_gestore"] != $_SESSION["ente"]["codice"] && !empty($record["dominio"])) {
						echo '<meta http-equiv="refresh" content="0;URL='.$config["protocollo"] . $record["dominio"] .'/gare/id'. $record["codice"] .'-dettaglio">';
						die();
					}
					if (($record["stato"]==3) && (strtotime($record["data_scadenza"])<time())) {
						$record["colore"] = $config["colore_scaduta"];
						$record["fase"] = "Scaduta";
					}
					$record["tipologie_gara"] = "";
					$sql = "SELECT tipologia FROM b_tipologie WHERE codice = :tipologia";
					$ris_tipologie = $pdo->bindAndExec($sql,array(":tipologia"=>$record["tipologia"]));
					if ($ris_tipologie->rowCount()>0) {
								$rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC);
								$record["tipologie_gara"] .= $rec_tipologia["tipologia"];
						}

					$sql = "SELECT * FROM b_modalita WHERE codice = :modalita";
					$ris = $pdo->bindAndExec($sql,array(":modalita"=>$record["modalita"]));
					if ($ris->rowCount()>0) {
						$rec = $ris->fetch(PDO::FETCH_ASSOC);
						$directory = $rec["directory"];
						$record["nome_modalita"] = $rec["modalita"];
						$record["online"] = $rec["online"];
						$record["directory_modalita"] = $rec["directory"];
					}

					$sql = "SELECT * FROM b_criteri WHERE codice = :criterio";
					$ris = $pdo->bindAndExec($sql,array(":criterio"=>$record["criterio"]));
					if ($ris->rowCount()>0) {
						$rec = $ris->fetch(PDO::FETCH_ASSOC);
						$directory = $rec["directory"];
						$record["nome_criterio"] = $rec["criterio"];
						$record["riferimento_criterio"] = $rec["riferimento_normativo"];
					}

					$derivazione = "";
					$sql = "SELECT * FROM b_procedure WHERE codice = :procedura";
					$ris = $pdo->bindAndExec($sql,array(":procedura"=>$record["procedura"]));
					if ($ris->rowCount()>0) {
						$rec_procedura = $ris->fetch(PDO::FETCH_ASSOC);
						$directory = $rec_procedura["directory"];
						$record["nome_procedura"] = $rec_procedura["nome"];
						$record["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
						if ($rec_procedura["mercato_elettronico"] == "S") { $derivazione = "me"; $tabella_sorgente = "mercato"; }
						if ($rec_procedura["directory"] == "sda") { $derivazione = "sda"; $tabella_sorgente = "sda"; }
					}

					$string_cpv = "";
					$cpv = array();
					if ($_SESSION["language"] != "IT") {
						$strsql = "SELECT b_cpv_dict.*, b_cpv_dict.{$_SESSION["language"]} AS descrizione FROM b_cpv_dict JOIN r_cpv_gare ON b_cpv_dict.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice_gara ORDER BY b_cpv_dict.codice";
					} else {
						$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice_gara ORDER BY b_cpv.codice";
					}
					$risultato_cpv = $pdo->bindAndExec($strsql,array(":codice_gara"=>$record["codice"]));
					if ($risultato_cpv->rowCount()>0) {
						$string_cpv = "<ul>";
						while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
							$string_cpv .= "<li><strong>" . $rec_cpv["codice"] . "</strong> - " . $rec_cpv["descrizione"] . "</li>";
						}
						$string_cpv .= "</ul>";
					}

					$sql = "SELECT MAX(data_fine) AS data_asta FROM b_aste WHERE codice_gara = :codice_gara GROUP BY codice_gara";
					$ris_aste = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));
					if ($ris_aste->rowCount()>0) {
						$asta = $ris_aste->fetch(PDO::FETCH_ASSOC);
					}
					$sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice_gara AND sezione = 'gara' AND online = 'S' AND hidden = 'N' ";
					$ris_allegati = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));


					if ($record["codice_ente"] != $_SESSION["ente"]["codice"]) {
						$strsql="SELECT * FROM b_enti WHERE codice = :codice_ente";
						$ris = $pdo->bindAndExec($strsql,array(":codice_ente"=>$record["codice_ente"]));
						if ($ris->rowCount()>0) $rec_ente = $ris->fetch(PDO::FETCH_ASSOC);
					}
?>
<h1 style="border:0px;"><?= traduci("GARA") ?> - ID <?= $record["id"]; ?><?= (!empty($record["id_suaff"])) ? (" - ID SUAFF: " . $record["id_suaff"]) : "" ?></h1>
<h3 style="text-align:right"><?= traduci("Stato") ?>: <strong><? echo traduci($record["fase"]) ?></strong></h3>
<div style="background-color:#<? echo $record["colore"] ?>; padding:5px;"></div>
<br>
  <? if ($record["annullata"] == "S") {
			echo "<h2 class=\"errore\">" . traduci("Annullata") . " - " . $record["numero_annullamento"] . "/" . mysql2date($record["data_annullamento"]) . "</h2>";
		}


		?><div class="box"><?
		$dgue = false;

		$sql = "SELECT * FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'gare' ";
		$ris_dgue = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));
		if ($ris_dgue->rowCount() > 0) $dgue = true;
		ob_start();
		if (is_operatore()) { ?>
			<h3><?= traduci("Pannello di partecipazione") ?></h3>
			<?
			$force = false;
		/*	$abilitati = array();
			$abilitati[] = 1466;
			$abilitati[] = 1460;
			$abilitati[] = 1495;
			$abilitati[] = 1492;
			$abilitati[] = 1468;
			$abilitati[] = 1480;
			$abilitati[] = 1447;
			$abilitati[] = 1472;
			if ((($codice == 308)) && (in_array($_SESSION["codice_utente"],$abilitati)!==false) && strtotime('2016-02-23 00:00:00') > time()) $force = true; */
			if (($record["annullata"] == "N") && ($record["online"]=="S") &&
					(
						(strtotime($record["data_scadenza"]) > time()) ||
						(strtotime($record["data_scadenza"]) < time() && $rec_procedura["fasi"] == 'S') ||
						(isset($asta["data_asta"]) && (strtotime($asta["data_asta"]) > time()))
					) || $force || $record["modalita"]==4) {
					$accedi = false;
					$bind = array(':codice' => $codice);
					$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice ";
					$ris_inviti = $pdo->bindAndExec($strsql,$bind);
					if ($ris_inviti->rowCount() >0) {
						$bind[":codice_utente"] = $_SESSION["codice_utente"];
						$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice  AND r_inviti_gare.codice_utente = :codice_utente";
						$ris_invitato = $pdo->bindAndExec($strsql,$bind);
						if ($ris_invitato->rowCount() >0) $accedi = true;
					} else {
						if ($rec_procedura["invito"] == 'N' || !empty($derivazione)) {
							$accedi = true;
						}
					}
					if (strtotime($record["data_scadenza"]) < time()) {
						$accedi = false;
						$bind = array(':codice' => $codice, ":codice_utente" => $_SESSION["codice_utente"]);
						$sql_fase = "SELECT r_partecipanti.* FROM r_partecipanti JOIN b_2fase ON r_partecipanti.codice_gara = b_2fase.codice_gara AND r_partecipanti.codice_lotto = b_2fase.codice_lotto
									WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_gara = :codice
									AND ammesso = 'S' AND escluso = 'N' AND b_2fase.data_inizio <= now() AND b_2fase.data_fine > now()";
						$ris_fase = $pdo->bindAndExec($sql_fase,$bind);
						
						if ($ris_fase->rowCount()>0) $accedi = true;

						$sql_aste = "SELECT r_partecipanti.* FROM r_partecipanti JOIN b_aste ON r_partecipanti.codice_gara = b_aste.codice_gara AND r_partecipanti.codice_lotto = b_aste.codice_lotto
									WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_gara = :codice
									AND ammesso = 'S' AND escluso = 'N' AND b_aste.data_inizio <= now() AND b_aste.data_fine > now()";
						$ris_aste = $pdo->bindAndExec($sql_aste,$bind);
						if ($ris_aste->rowCount()>0) $accedi = true;

						if (!$accedi && $record["modalita"] == 4) {
							$bind = array(':codice' => $codice, ":codice_utente" => $_SESSION["codice_utente"]);
							$sql_partecipato = "SELECT r_partecipanti.* FROM r_partecipanti WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_gara = :codice AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
							$ris_partecipato = $pdo->bindAndExec($sql_partecipato,$bind);
							if ($ris_partecipato->rowCount() > 0) $pannello = true;
						}
					}
					if ($derivazione != "") {
						$sql_abilitato = "SELECT * FROM r_partecipanti_".$derivazione." WHERE codice_bando = :codice_derivazione AND ammesso = 'S' AND codice_utente = :codice_utente ";
						$ris_abilitato = $pdo->bindAndExec($sql_abilitato,array(":codice_derivazione"=>$record["codice_derivazione"],":codice_utente"=>$_SESSION["codice_utente"]));
						if ($ris_abilitato->rowCount() == 0) {
							$accedi = false;
							$force = false;
							unset($pannello);
						}
					}
					if ($accedi || $force || isset($pannello)) {
						$bind = array(':codice' => $record["codice"], ":codice_utente" => $_SESSION["codice_utente"]);
						$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_utente = :codice_utente AND (conferma = TRUE OR conferma IS NULL)";
						$ris_partecipazione_confermata = $pdo->bindAndExec($sql,$bind);
			?>
        	<a href="/gare/<? echo $record["directory_modalita"] ?>/modulo.php?cod=<? echo $record["codice"] ?>" onClick="partecipa()" class="submit_big" title="Partecipa">
						<?
						echo ($ris_partecipazione_confermata->rowCount() == 0) ? traduci("Partecipa") : traduci("Pannello di gara");
						?>
					</a>
        <?
				$bind = array();
				$bind[":codice_gara"] = $codice;
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql  = "SELECT b_rdo_ad.*, r_rdo_ad.timestamp_trasmissione,r_rdo_ad.codice AS codice_rdo, r_rdo_ad.nome_file FROM
										r_rdo_ad JOIN b_rdo_ad ON r_rdo_ad.codice_rdo = b_rdo_ad.codice
										JOIN b_gare ON b_rdo_ad.codice_gara = b_gare.codice
										WHERE b_rdo_ad.codice_gara = :codice_gara AND r_rdo_ad.codice_utente = :codice_utente
										AND b_gare.annullata = 'N'
										AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
										AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') ORDER BY b_rdo_ad.timestamp DESC";

				$ris_rdo = $pdo->bindAndExec($strsql,$bind);

      if ($ris_partecipazione_confermata->rowCount()>0 && (strtotime($record["data_scadenza"]) > time()) && $ris_rdo->rowCount() == 0) {
				$already = true;
				while($par = $ris_partecipazione_confermata->fetch(PDO::FETCH_ASSOC)) {
					$sql = "SELECT * FROM b_lotti WHERE codice = :codice_lotto";
					$oggetto_lotto = "";
					$ris_revoca_lotto = $pdo->bindAndExec($sql,array(":codice_lotto"=>$par["codice_lotto"]));
					if ($revoca_lotto = $ris_revoca_lotto->fetch(PDO::FETCH_ASSOC)) $oggetto_lotto = "<br>" . $revoca_lotto["oggetto"];
					?>
					<a id="button_revoca_<? echo $par["codice"] ?>" href="#" onClick="elimina('<? echo $par["codice"] ?>','gare/<? echo $record["directory_modalita"] ?>');" class="submit_big" style="background-color:#C30" title="<?= traduci("Revoca partecipazione") ?>"><?= traduci("Revoca partecipazione") ?> <? echo $oggetto_lotto ?></a>
                	<?
            	}
						}
			}
		}
		if ($ris_dgue->rowCount() > 0) {
			$show_dgue = TRUE;
			if(strtotime($record["data_scadenza"]) < time()) {
				$show_dgue = FALSE;
				$check_dgue = $pdo->bindAndExec("SELECT codice FROM b_dgue_compilati WHERE codice_riferimento = :codice_gara AND codice_utente = :codice_utente AND sezione = 'gare'", array(':codice_utente' => $_SESSION["codice_utente"], ':codice_gara' => $codice));
				if($check_dgue->rowCount() > 0) $show_dgue = TRUE;
				if(isset($ris_partecipazione_confermata) && $ris_partecipazione_confermata->rowCount() > 0) $show_dgue = TRUE;
			}

			if (($show_dgue && $record["annullata"] == "N")) {
				?>
				<a href="/dgue/edit.php?sezione=gare&codice_riferimento=<?= $record["codice"] ?>" style="background-color:#055" class="submit_big"><?= traduci("Compila Documento di Gara Unico Europeo") ?> - <?= traduci("DGUE") ?></a>
				<?
			}
		}
		$bind=array();
		$bind[":codice_gara"] = $record["codice"];
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$strsql = "SELECT r_integrazioni.* FROM r_integrazioni JOIN b_integrazioni ON r_integrazioni.codice_integrazione = b_integrazioni.codice ";
		$strsql.= " WHERE b_integrazioni.codice_gara = :codice_gara AND r_integrazioni.codice_utente = :codice_utente";
		$ris_int = $pdo->bindAndExec($strsql,$bind);
		if ($ris_int->rowCount() >0) {
			?>
			<a href="/gare/integrazioni/view.php?cod=<? echo $record["codice"] ?>" class="submit_big" title="<?= traduci("Integrazioni") ?>"><?= traduci("Integrazioni") ?></a>
			<?
		}
	} else if (!isset($_SESSION["codice_utente"])) {
		?>
		<h3><a href="/operatori_economici/registrazione.php" title="<?= traduci("Registrazione operatori economici") ?>"><?= traduci("Registrati") ?></a> o <a href="/accesso.php" title="<?= traduci("Accedi") ?>"><?= traduci("Accedi") ?></a> <?= traduci("per partecipare") ?></h3>
		<? }
		$form_operatore = ob_get_clean();
		echo $form_operatore;
		?>
	</div>
<table width="100%">
	<? if (isset($rec_ente)) { ?>
		<tr><td class="etichetta"><?= traduci("Stazione appaltante") ?></td>
			<td colspan="3">
				<strong>
				<?
					echo $rec_ente["denominazione"];
				?>
				</strong>
			</td></tr>
  <? } ?>
	<tr>
    	<td class="etichetta"><?= traduci("Procedura") ?></td><td><strong><? echo traduci($record["nome_procedura"]) ?></strong><br></td>
		<td class="etichetta"><?= traduci("Criterio") ?></td><td><strong><? echo traduci($record["nome_criterio"]) ?></strong><br></td>
	</tr>
	<? if ($derivazione != "") {
		$sql_derivazione = "SELECT * FROM b_bandi_".$tabella_sorgente . " WHERE codice = :codice_derivazione";
		$ris_derivazione = $pdo->bindAndExec($sql_derivazione,array(":codice_derivazione"=>$record["codice_derivazione"]));
		if ($ris_derivazione->rowCount() > 0) {
			$bando_derivazione = $ris_derivazione->fetch(PDO::FETCH_ASSOC);
			$link = $derivazione;
			if ($link == "me") $link = "mercato_elettronico";
			?>
			<tr>
				<td class="etichetta"><?= traduci("Riferimento") ?></td>
				<td colspan="3">
					<a href="/<?= $link ?>/id<?= $bando_derivazione["codice"] ?>-dettaglio" title="<?= traduci("riferimento") ?>">
						<strong><?= $bando_derivazione["oggetto"] ?></strong>
					</a>
				</td>
				</td>
			</tr>
			<?
		}
		?>

	<? } ?>
    <tr><td class="etichetta"><?= traduci("Oggetto") ?></td><td colspan="3"><strong><? echo traduci($record["tipologie_gara"]) ?></strong><br><? echo $record["oggetto"] ?></td></tr>
		<tr>
	    	<td class="etichetta">CIG</td><td><strong><? echo $record["cig"] ?></strong><br></td>
			<td class="etichetta">CUP</td><td><strong><? echo $record["cup"] ?></strong><br></td>
		</tr>
    <tr>
			<td class="etichetta"><?= traduci("Totale appalto") ?></td><td><strong>&euro; <? echo number_format($record["prezzoBase"],2,",","."); ?></strong></td>
			<? if(!empty($record["importoAggiudicazione"])) { ?>
				<td class="etichetta"><?= traduci("Importo aggiudicazione") ?></td>
				<td><strong>&euro; <? echo number_format($record["importoAggiudicazione"],2,",","."); ?></strong></td>
			<? } ?>
		</tr>
    <?
    if ($record["procedura"] != 11) {
    	?>
    	<tr>
    		<td class="etichetta"><?= traduci("Data pubblicazione") ?></td>
				<td><? echo mysql2date($record["data_pubblicazione"]) ?></td>
    	  <? if (mysql2date($record["data_accesso"])!="") { ?>
					<td class="etichetta"><?= traduci("Termine richieste chiarimenti") ?></td>
					<td><? echo mysql2completedate($record["data_accesso"]) ?></td>
				<? } ?>
    	</tr>

    <tr>
			<td class="etichetta"><?= traduci("Scadenza presentazione offerte") ?></td><td><strong><? echo mysql2completedate($record["data_scadenza"]) ?></strong></td>
			<? if ($record["flag_show_apertura"] != "N") { ?>
				<td class="etichetta"><?= traduci("Apertura delle offerte") ?></td><td><? $data_apertura = mysql2date($record["data_apertura"]); if (!empty($data_apertura)) echo mysql2completedate($record["data_apertura"]) ?></td>
			<? } ?>
    </tr>
		<? } ?>
   <? if ($string_cpv != "") { ?>
		 <tr>
			 <td class="etichetta"><?= traduci("Categorie merceologiche") ?></td>
			 <td colspan="3">
			 	<? echo $string_cpv; ?>
			</td>
		</tr>
	<? } ?>
	<tr><td class="etichetta"><?= traduci("descrizione") ?></td><td colspan="3"><? echo $record["descrizione"] ?></td></tr>
	<tr><td class="etichetta"><?= traduci("Struttura proponente") ?></td><td colspan="3">
	<?
		echo $record["struttura_proponente"];
	?></td></tr>
		<tr>
			<td class="etichetta"><?= traduci("Responsabile del servizio") ?></td>
			<td><? echo $record["responsabile_struttura"] ?></td>
			<td class="etichetta"><?= traduci("Responsabile del procedimento") ?></td>
			<td><? echo $record["rup"] ?></td>
		</tr>
		<? if ((isset($ris_allegati) && ($ris_allegati->rowCount() > 0)) || $dgue) { ?>
			<tr>
				<td class="etichetta"><?= traduci("Allegati") ?></td><td colspan="3">
                            <table width="100%" id="tab_allegati">
															<? if ($dgue) { ?>
																<tr>
													 				<td width="10" style="text-align:center"><span class="fa fa-code fa-2x"></span></td>
													        <td>
																		<strong><a href="/dgue/getRequestXML.php?sezione=gare&codice_riferimento=<?= $record["codice"] ?>"><?= traduci("Richiesta DGUE") ?> XML</a></strong>
																	</td>
													      </tr>
															<? } ?>
                            <?
                       			while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
									include($root."/allegati/tr_allegati.php");
                       			}
							?>
                        </table></td></tr>
                        <?
						}

?></table>
<?

	$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice ";
	$ris_lotti = $pdo->bindAndExec($sql_lotti,array(":codice_gara"=>$record["codice"]));

		if ($ris_lotti->rowCount()==0) {
			$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = 0 AND primo = 'S' AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
			$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,array(":codice_gara"=>$record["codice"]));
			if ($ris_partecipanti->rowCount()>0 && $record["pubblica_partecipanti"] == "S") {
				$echo_invitati = TRUE;
				?><div class="box">
					<h2><?
				if ($record["numero_atto_esito"] != "") {
					?>
					<?= traduci("Aggiudicazione definitiva") ?> - <strong><?= $record["numero_atto_esito"] ?> <? if (!empty($record["data_atto_esito"])) { ?>\<?= mysql2date($record["data_atto_esito"]) ?><? } ?></strong>
					<?
				} else {
					?>
					<?= traduci("Proposta di Aggiudicazione") ?>
					<?
				} ?>
				</h2>
				<?
					if (!empty($record["algoritmo_anomalia"])) {
						?>
						<strong><?= traduci("calcolo offerte anomale") ?>: </strong>Ai sensi dell'art. 97 c.2 lett. <?= strtoupper($record["algoritmo_anomalia"]) ?> del D.Lgs. 50/2016 - <strong><?= traduci("soglia di anomalia individuata") ?>:</strong> <?= $record["soglia_anomalia"] ?>
						<?
					}
				?>
				<div style="background-color:#<? echo $record["colore"] ?>; padding:5px;"></div>
				<table width="100%">
				<?
					while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
						$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = 0 AND codice_capogruppo = :codice_capogruppo";
						$ris_partecipanti_gruppo = $pdo->bindAndExec($sql_partecipanti,array(":codice_gara"=>$record["codice"],":codice_capogruppo"=>$partecipante["codice"]));

						echo "<tr><td width=\"1\"><h3>".$partecipante["partita_iva"] . "</h3></td><td><h3><strong>" . $partecipante["ragione_sociale"] . "</strong> - " . traduci($partecipante["tipo"]) . "</h3></td></tr>";
						if ($ris_partecipanti_gruppo->rowCount()>0) {
							while ($partecipante = $ris_partecipanti_gruppo->fetch(PDO::FETCH_ASSOC)) {
								echo "<tr><td width=\"1\"><h3>".$partecipante["partita_iva"] . "</h3></td><td><h3><strong>" . $partecipante["ragione_sociale"] . "</strong> - " . traduci($partecipante["tipo"]) . "</h3></td></tr>";
							}
						}
					}
				?></table>

				</div>
			<?
				$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = 0 AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
				$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,array(":codice_gara"=>$record["codice"]));
				if ($ris_partecipanti->rowCount() > 0) {
					?>
					<div class="box">
					<h2><?= traduci("Partecipanti") ?></h2>
					<table width="100%">
						<?
						while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
							$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = 0 AND codice_capogruppo = :codice_capogruppo";
							$ris_partecipanti_gruppo = $pdo->bindAndExec($sql_partecipanti,array(":codice_gara"=>$record["codice"],":codice_capogruppo"=>$partecipante["codice"]));
							if ($ris_partecipanti_gruppo->rowCount()>0) {
								echo "<tr><th colspan='2'>" . strtoupper(traduci("RAGGRUPPAMENTO")) . "</th></tr>";
							}
							echo "<tr><td width=\"1\"><h3>".$partecipante["partita_iva"] . "</h3></td><td><h3><strong>" . $partecipante["ragione_sociale"] . "</strong> - " . traduci($partecipante["tipo"]) . "</h3></td></tr>";
							if ($ris_partecipanti_gruppo->rowCount()>0) {
								while ($partecipante = $ris_partecipanti_gruppo->fetch(PDO::FETCH_ASSOC)) {
									echo "<tr><td width=\"1\"><h3>".$partecipante["partita_iva"] . "</h3></td><td><h3><strong>" . $partecipante["ragione_sociale"] . "</strong> - " . traduci($partecipante["tipo"]) . "</h3></td></tr>";
								}
								echo "<tr><th colspan='2'></th></tr>";
							}
						}
						?>
					</table>
				</div>
					<?
				}
			}
		} else {
		?>
		<br>
		<h2><?= traduci("Lotti") ?></h2>
		<?
			$n_lotto = 0;
			while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
				$n_lotto++;
				if ($record["norma"] == "2023-36") {
					$lotto_prezzoBase = $lotto["importo_base"] + $lotto["importo_oneri_no_ribasso"]+ $lotto["importo_personale"]; // + $lotto["importo_oneri_ribasso"] 
				} else {
					$lotto_prezzoBase = $lotto["importo_base"] + $lotto["importo_oneri_no_ribasso"]; // + $lotto["importo_oneri_ribasso"] + $lotto["importo_personale"];
				}
				?>
				<div class="box">
					<table width="100%">
						<tr><td class="etichetta"><?= traduci("Oggetto") ?></td><td><strong><? echo $lotto["oggetto"] ?></strong></td></tr>
						<tr><td class="etichetta">CIG</td><td><strong><? echo $lotto["cig"] ?></strong></td></tr>

					    <tr><td class="etichetta"><?= traduci("Totale lotto") ?></td>
								<td><strong>&euro; <? echo number_format($lotto_prezzoBase,2,",","."); ?></strong></td></tr>
							<? if(!empty($lotto["importoAggiudicazione"])) { ?>
								<tr><td class="etichetta"><?= traduci("Importo aggiudicazione") ?></td>
								<td><strong>&euro; <? echo number_format($lotto["importoAggiudicazione"],2,",","."); ?></strong></td>
								</tr>
							<? } ?>
						<? if (!empty($lotto["durata"])) { ?>
							<tr><td class="etichetta"><?= traduci("Durata contrattuale") ?></td><td>
							<?
								$unita = "Giorni";
								if ($lotto["unita_durata"] == "mm") $unita = "Mesi";
								echo $lotto["durata"] . " " . traduci($unita);
							?>
							</td></tr>
						<? } ?>
					    <tr><td class="etichetta"><?= traduci("descrizione") ?></td>
							<td><? echo $lotto["descrizione"] ?></td></tr>
					    <tr><td class="etichetta"><?= traduci("Categorie merceologiche") ?></td><td>
									<?
										if ($_SESSION["language"] == "IT") {
											$strsql = "SELECT b_cpv.* FROM b_cpv WHERE b_cpv.codice = :cpv";
										} else {
											$strsql = "SELECT b_cpv_dict.*, b_cpv_dict.{$_SESSION["language"]} AS descrizione
																 FROM b_cpv_dict WHERE b_cpv_dict.codice = :cpv";
										}

										$risultato_cpv = $pdo->bindAndExec($strsql,array(":cpv"=>$lotto["cpv"]));
										if ($risultato_cpv->rowCount()>0) {
											while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
												echo "<strong>" . $rec_cpv["codice"] . "</strong> - " . $rec_cpv["descrizione"] . "<br>";
											}
										}
									?></td></tr>
						  <tr><td class="etichetta"><?= traduci("Ulteriori informazioni") ?></td><td><? echo $lotto["ulteriori_informazioni"] ?></td></tr>
					</table>
					<?
					$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND primo = 'S' AND codice_capogruppo = 0";
					$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,array(":codice_gara"=>$record["codice"],":codice_lotto"=>$lotto["codice"]));
					if ($ris_partecipanti->rowCount()>0 && $lotto["pubblica_partecipanti"] == "S") {
						$echo_invitati = TRUE;
						?><br>
							<h2>
								<?
								$colore = $record["colore"];
								if ($lotto["numero_atto_esito"] != "" || $record["numero_atto_esito"] != "") {
									if(! empty($lotto["numero_atto_esito"]) && $record["stato"] < 7) $colore = $pdo->bindAndExec("SELECT b_stati_gare.colore FROM b_stati_gare WHERE b_stati_gare.fase = 7")->fetchColumn(0);
									$numero_atto_esito = ! empty($lotto["numero_atto_esito"]) ? $lotto["numero_atto_esito"] : $record["numero_atto_esito"];
									$data_atto_esito = ! empty($lotto["data_atto_esito"]) ? mysql2date($lotto["data_atto_esito"]) : (! empty($record["data_atto_esito"]) ? mysql2date($record["data_atto_esito"]) : null);
									?>
									<?= traduci("Aggiudicazione definitiva") ?> - <strong><?= $numero_atto_esito ?> <? if (!empty($data_atto_esito)) { ?>\<?= $data_atto_esito ?><? } ?></strong>
									<?
								} else {
									?>
									<?= traduci("Proposta di Aggiudicazione") ?>
									<?
								}
								?>
							</h2>
							<?
								if (!empty($lotto["algoritmo_anomalia"])) {
									?>
									<strong><?= traduci("calcolo offerte anomale") ?>: </strong>Ai sensi dell'art. 97 c.2 lett. <?= strtoupper($lotto["algoritmo_anomalia"]) ?> del D.Lgs. 50/2016 - <strong><?= traduci("soglia di anomalia individuata") ?>:</strong> <?= $lotto["soglia_anomalia"] ?>
									<?
								}
							?>
						<div style="background-color:#<? echo $colore ?>; padding:5px;"></div>
						<?
							while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
								$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = :codice_capogruppo";
								$ris_partecipanti_gruppo = $pdo->bindAndExec($sql_partecipanti,array(":codice_gara"=>$record["codice"],":codice_lotto"=>$lotto["codice"],":codice_capogruppo"=>$partecipante["codice"]));

								echo "<table width='100%'><tr><td width='1'><h3>".$partecipante["partita_iva"] . "</h3></td><td><h3><strong>" . $partecipante["ragione_sociale"] . "</strong> - " . traduci($partecipante["tipo"]) . "</h3></td></tr>";
								if ($ris_partecipanti->rowCount()>0) {
									while ($partecipante = $ris_partecipanti_gruppo->fetch(PDO::FETCH_ASSOC)) {
										echo "<tr><td width='1'><h3>".$partecipante["partita_iva"] . "</h3></td><td><h3><strong>" . $partecipante["ragione_sociale"] . "</strong> - " . traduci($partecipante["tipo"]) . "</h3></td></tr>";
									}
								}
								?>
								</table>
								<?
							}
							$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
							$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,array(":codice_gara"=>$record["codice"],":codice_lotto"=>$lotto["codice"]));
							if ($ris_partecipanti->rowCount() > 0) {
								?>
								<div class="box">
								<h2><?= traduci("Partecipanti") ?></h2>
								<table width="100%">
									<?
									while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
										$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = :codice_capogruppo";
										$ris_partecipanti_gruppo = $pdo->bindAndExec($sql_partecipanti,array(":codice_gara"=>$record["codice"],":codice_lotto"=>$lotto["codice"],":codice_capogruppo"=>$partecipante["codice"]));
										if ($ris_partecipanti_gruppo->rowCount()>0) {
											echo "<tr><th colspan='2'>".traduci("RAGGRUPPAMENTO")."</th></tr>";
										}
										echo "<tr><td width=\"1\"><h3>".$partecipante["partita_iva"] . "</h3></td><td><h3><strong>" . $partecipante["ragione_sociale"] . "</strong> - " . traduci($partecipante["tipo"]) . "</h3></td></tr>";
										if ($ris_partecipanti_gruppo->rowCount()>0) {
											while ($partecipante = $ris_partecipanti_gruppo->fetch(PDO::FETCH_ASSOC)) {
												echo "<tr><td width=\"1\"><h3>".$partecipante["partita_iva"] . "</h3></td><td><h3><strong>" . $partecipante["ragione_sociale"] . "</strong> - " . traduci($partecipante["tipo"]) . "</h3></td></tr>";
											}
											echo "<tr><th colspan='2'></th></tr>";
										}
									}
									?>
								</table>
							</div>
							<?
								}
					} else if ($lotto["deserta"] == "S") {
						?>
						<br><h2><?= traduci("Lotto deserto") ?></h2>
						<div style="background-color:#666; padding:5px;"></div><br>
						<?
					} else if ($lotto["deserta"] == "Y") {
						?>
						<br><h2><?= traduci("Lotto non aggiudicato") ?></h2>
						<div style="background-color:#666; padding:5px;"></div><br>
						<?
					}
					?>
				</div>
				<?
			}
	}

	if(isset($echo_invitati) && $echo_invitati) {
		$rec_oe_invitati = [];
		$sql_oe_invitati = "SELECT partita_iva, ragione_sociale FROM b_operatori_economici JOIN b_utenti ON b_utenti.codice = b_operatori_economici.codice_utente JOIN r_inviti_gare ON r_inviti_gare.codice_utente = b_utenti.codice WHERE r_inviti_gare.codice_gara = :codice_gara";
		$ris_oe_invitati = $pdo->bindAndExec($sql_oe_invitati, array(':codice_gara' => $record["codice"]));
		if($ris_oe_invitati->rowCount() > 0) $rec_oe_invitati = array_merge($rec_oe_invitati,$ris_oe_invitati->fetchAll(PDO::FETCH_ASSOC));
		$sql_manuali = "SELECT * FROM temp_inviti WHERE codice_gara = :codice_gara AND attivo = 'S'";
		$ris_manuali = $pdo->bindAndExec($sql_manuali,array(':codice_gara' => $record["codice"]));
		if($ris_manuali->rowCount() > 0) $rec_oe_invitati = array_merge($rec_oe_invitati,$ris_manuali->fetchAll(PDO::FETCH_ASSOC));
		if (count($rec_oe_invitati) > 0) {
			?>
			<br><h2><?= traduci("invitati") ?></h2>
			<div class="box">
				<table style="width:100%; table-layout: fixed;">
					<thead>
						<tr>
							<th width="150px" style="text-align: left;"><?= traduci("partita_iva") ?></th>
							<th style="text-align: left;"><?= traduci("RAGIONE SOCIALE") ?></th>
						</tr>
					</thead>
					<tbody>
						<?
						foreach ($rec_oe_invitati as $oe_invitato) {
							?>
							<tr>
								<td><?= $oe_invitato["partita_iva"] ?></td>
								<td><?= $oe_invitato["ragione_sociale"] ?></td>
							</tr>
							<?
						}
						?>
					</tbody>
				</table>
			</div>
			<?
		}
	}

	$bind = array(":codice" => $record["codice"]);
	$valutatori = ["N","S"];
	foreach($valutatori AS $tecnica) {
		$sql = "SELECT * FROM b_commissioni WHERE codice_gara = :codice AND valutatore = :tecnica";
		$bind[":tecnica"] = $tecnica;
		$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
		if ($ris_partecipanti->rowCount()>0) {
			$denominazioni = "seggio";
			if ($tecnica == "S") $denominazioni = "commissione";

			?>
			<div class="box">
				<h2><?= ($tecnica == "S") ? traduci("Commissione valutatrice") : traduci("Seggio di gara") ?></h2>
				<?
					if (!empty($record["numero_atto_".$denominazioni])) {
						if (!empty($record["allegato_atto_".$denominazioni])) {
							$sql = "SELECT * FROM b_allegati WHERE codice = :codice_allegato";
							$ris_allegato = $pdo->bindAndExec($sql,array(":codice_allegato"=>$record["allegato_atto_".$denominazioni]));
							if ($ris_allegato->rowCount() > 0) {
								$allegato = $ris_allegato->fetch(PDO::FETCH_ASSOC);
								$percorso_html = "/documenti/allegati/". $allegato["codice_gara"] . "/" . $allegato["nome_file"];
								$percorso_fisico = $config["pub_doc_folder"]."/allegati/". $allegato["codice_gara"] . "/" . $allegato["riferimento"];
								if (file_exists($percorso_fisico)) {
									$estensione = explode(".",$allegato["nome_file"]);
									$estensione = end($estensione);
									?>
									<a href="<?= $percorso_html ?>" target="_blank" title="<?= traduci("Allegato") ?>"><?
										if (file_exists($root."/img/".$estensione.".png")) { ?><img src="/img/<? echo $estensione ?>.png" alt="File <? echo $estensione ?>" style="vertical-align:middle"><? } else {
											echo $allegato["nome_file"];
										 }
									?></a>
									<?
								}
							}
						}
						?>
						 <?= traduci("Atto di costituzione") ?> <strong><? echo $record["numero_atto_".$denominazioni] ?></strong> - <? echo mysql2date($record["data_atto_".$denominazioni]) ?>
						<?
					}
					?>
					<table width="100%">
						<tr>
							<th><?= traduci("nome") ?></th>
							<th><?= traduci("Ruolo") ?></th>
							<th>CV</th>
						</tr>
						<?
							while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
								?>
								<tr>
									<td><?= $record_partecipante["titolo"] . " " . $record_partecipante["cognome"] . " " .  $record_partecipante["nome"] ?></td>
									<td><?= $record_partecipante["ruolo"] ?></td>
									<td width="10"><? if (!empty($record_partecipante["cv"])) {
										$sql = "SELECT * FROM b_allegati WHERE codice = :codice_allegato";
										$ris_allegato = $pdo->bindAndExec($sql,array(":codice_allegato"=>$record_partecipante["cv"]));
										if ($ris_allegato->rowCount() > 0) {
											$allegato = $ris_allegato->fetch(PDO::FETCH_ASSOC);
											$percorso_html = "/documenti/allegati/". $allegato["codice_gara"] . "/" . $allegato["nome_file"];
											$percorso_fisico = $config["pub_doc_folder"]."/allegati/". $allegato["codice_gara"] . "/" . $allegato["riferimento"];
											if (file_exists($percorso_fisico)) {
												$estensione = explode(".",$allegato["nome_file"]);
												$estensione = end($estensione);
												?>
												<a href="<?= $percorso_html ?>" target="_blank" title="Allegat0">
												<?
													if (file_exists($root."/img/".$estensione.".png")) { ?>
														<img src="/img/<? echo $estensione ?>.png" alt="File <? echo $estensione ?>" style="vertical-align:middle">
													<? } else {
														echo $allegato["nome_file"];
													 }
												?>
												</a>
												<?
											}
										}
									}
									?></td>
								</tr>
								<?
							}
						?>
					</table>
			</div>
			<?
		}
	}

	$bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ":codice" => $record["codice"]);

	$strsql  = "SELECT b_avvisi.* ";
	$strsql .= "FROM b_avvisi ";
	$strsql .= "WHERE codice_ente = :codice_ente AND codice_gara = :codice ";
	$strsql .= " ORDER BY data DESC,  timestamp DESC " ;


	$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

	if ($risultato->rowCount()>0) {
		?>
        <div class="box">
        <h2><?= traduci("Avvisi di gara") ?></h2>
        <table class="elenco" style="width:100%">
        <thead style="display:none;"><tr><td></td><td></td></tr></thead>
        <tbody>
        <?
		while ($record_avvisi = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$codice			= $record_avvisi["codice"];
			$titolo			= $record_avvisi["titolo"];
			$data			= mysql2date($record_avvisi["data"]);
			$testo			= strip_tags($record_avvisi["testo"]);
			$href = "/gare/avvisi/dettaglio.php?cod=".$codice;
					?>
                    <tr id="<? echo $codice ?>"><td width="10"><strong><? echo $data ?></strong></td><td><strong><a style="text-transform:uppercase;" href="<? echo $href ?>" title="<? echo $titolo ?>"><? echo $titolo; ?></a></strong><br>
          	          <? echo substr($testo,0,255); ?>...
                      </td>
                        </tr>
		<?php } ?>
		</tbody></table>
    <div class="clear"></div>
    </div>
    <? } ?>
    <div class="clear"></div>
<?
	$strsql = "SELECT b_risposte.quesito, b_risposte.testo AS risposta, b_quesiti.timestamp AS data_quesito, b_risposte.timestamp AS data_risposta
						 FROM b_quesiti JOIN b_risposte ON b_quesiti.codice = b_risposte.codice_quesito
						 WHERE b_risposte.testo <> ''
						 AND b_quesiti.attivo = 'S'
						 AND b_quesiti.codice_gara = :codice  AND b_quesiti.codice_ente = :codice_ente  ORDER BY b_risposte.timestamp";

	$ris_quesiti = $pdo->bindAndExec($strsql,$bind);
	if ($ris_quesiti->rowCount()>0) {
		?><div class="box"><h2><?= traduci("Chiarimenti") ?></h2>
		<ol><?
		while ($quesito = $ris_quesiti->fetch(PDO::FETCH_ASSOC)) {
			?>
            <li class="box"><? echo mysql2datetime($quesito["data_quesito"]) ?> - <strong><? echo $quesito["quesito"] ?></strong><br><br>
            <? echo $quesito["risposta"] ?>
             <div class="label" style="text-align:right; font-size:10px"><? echo mysql2datetime($quesito["data_risposta"]) ?></div></li>
            <?
		}
		?></div></ol><?
	}
	?>
	<div class="box">
		<?= $form_operatore; ?>
	</div>
	<?
	if (strtotime($record["data_accesso"]) > time()) {
		if (is_operatore() && (!isset($accedi) || $accedi)) {
			?>
			<style type="text/css">
				table.row-bordered tr td {
				  border-bottom:1pt solid black;
				}
				.resumable-button {
					cursor: pointer;
					width:100%;
					padding:10px;
					background-color:#F60;
					margin-top: 10px;
					font-size: 0.9rem;
					position: relative;
				  overflow: hidden;
				  display: inline-block;
				}
				.resumable-button input[type=file] {
					cursor: pointer;
					font-size: 100px;
				  position: absolute;
				  left: 0;
				  top: 0;
				  opacity: 0;
				}
			</style>
			<div class="box">
        <h3><?= traduci("Richiesta di chiarimenti") ?></h3>
				<script type="text/javascript" src="/js/resumable.js"></script>
        <script type="text/javascript">
        	$(function() {
	        	var r = resumable = new Resumable({
		        												chunkSize:3*1024*1024,
														        maxFileSize:this.maxFileSize*1024*1024*1024,
														        simultaneousUploads:1,
														        target:'/allegati/chunk.php',
														        prioritizeFirstAndLastChunk:true,
														        throttleProgressCallbacks:1,
														      });
	        	if(!r.support) {
	        		alert();
	        		corpo_alert = '<div style="text-align:center; font-weight:bold">Il tuo browser non supporta la procedura di upload dei file.<br>';
	        		corpo_alert += 'Si consiglia di aggiornare il browser in uso o di utilizzare uno dei seguenti';
	        		corpo_alert += '<table width="100%"><tr>';
	        		corpo_alert += '<td style="text-align:center; width:50%;"><a target="_blank" title="Sito esterno" href="http://www.google.it/intl/it/chrome/browser/">';
	        		corpo_alert += '<img src="/img/chrome.png" alt="Google Chrome"><br>Google Chrome';
	        		corpo_alert += '</a></td>';
	        		corpo_alert += '<td style="text-align:center; width:50%;"><a target="_blank" title="Sito esterno" href="http://www.mozilla.org/it/firefox/new/">';
	        		corpo_alert += '<img src="/img/firefox.png" alt="Firefox"><br>Firefox';
	        		corpo_alert += '</a></td>';
	        		corpo_alert += '</tr>';
	        		corpo_alert += '</table></div>';
	        		jalert(corpo_alert);
	        		$('#allegati_chiarimenti').empty();
	        	}
	      		r.assignBrowse($('#browser-button'));
	      		r.on('fileError', function(file, message){
				      $("#progress_bar_allegati_chiarimenti").removeClass("progress_bar").addClass("error_bar");
				    });
				    r.on('fileSuccess', function(file, message) {
				    	$("#progress_bar_allegati_chiarimenti").find(".progress_bar").removeClass("progress_bar").addClass("complete_bar");
				    	$.ajax({
				    		url: '/gare/chiarimenti/upload_file.php',
				    		type: 'post',
				    		dataType: 'json',
				    		data: {filename: file.fileName, gara: '<?= $record["codice"] ?>'},
				    	})
				    	.done(function(response) {
				    		$('#tab_allegati_gara').append(response.html);
				    		var codici = $("#cod_allegati").val();
				    		if (codici != undefined) {
				    			codici = codici.split(";");
				    			codici.push(response.codice);
				    			codici = codici.join(";");
				    			$("#cod_allegati").val(codici);
				    		}
				    	})
				    	.fail(function() {
				    		jalert('Si è verificato un errore nel caricamento del file.<br>Si prega di riprovare');
				    	});
				    });
				    r.on('fileProgress', function(file) {
				    	progress = Math.floor(file.progress() * 100);
				    	$("#progress_bar_allegati_chiarimenti").find(".progress_bar").css("width",progress+"%");
				    });
				    r.on('complete', function() {
				    	$("#progress_bar_allegati_chiarimenti").find(".progress_bar").removeClass("progress_bar").addClass("complete_bar");
				    });
				    r.on('progress',function() {
				      progress = Math.floor(this.progress() * 100);
				      $("#progress_bar_allegati_chiarimenti").find(".progress_bar").css("width",progress+"%");
	   				});
	   				r.on('pause', function(file){
	   				});
	   				r.on('fileRetry', function(file){
	   				});
	   				r.on('fileAdded', function(file) {
					    var allReg = /\.(jpg|jpeg|png|gif|doc|docx|xlsx|xls|pdf|zip|rar|ods|odt|rtf|p7m|xml|P7M|JPG|JPEG|PNG|GIF|DOC|DOCX|XLSX|XLS|PDF|ZIP|RAR|ODS|ODT|RTF|XML)$/;	// espressione regolare per il controllo dei file allegati
			        if (allReg.test(file.fileName)) {
			          $("#progress_bar_allegati_chiarimenti").find(".complete_bar").addClass("progress_bar").removeClass("complete_bar");
			          $("#progress_bar_allegati_chiarimenti").show();
			          r.upload();
			        } else {
			          jalert("<div style=\"text-align:center\">Impossibile caricaricare il file<br><br><strong>" + file.fileName + "</strong></br><br>La tipologia pu&ograve; solo essere: P7M | JPG | JPEG | PNG | GIF | DOC | DOCX | XLSX | XLS | PDF | ZIP | RAR | ODS | ODT | RTF | XML</div>");
			          r.file.cancel();
			        }
	   				});
        	});
        </script>
				<div class="box">
					<input type="button" class="submit_big" value="<?= traduci("Formula quesito") ?>" onClick="$(this).slideUp(); $('#form_quesiti').slideDown();">
					<div id="form_quesiti" style="display:none">
		        <form name="box" method="post" action="/gare/chiarimenti/save_quesito.php" rel="validate">
		          <input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
			        <input type="hidden" value="" name="cod_allegati" title="Allegati" id="cod_allegati" rel="N;0;0;A">
		          <input type="hidden" name="operazione" value="INSERT">
			        <textarea rows='10' class="ckeditor_simple" name="testo" cols='80' id="testo" title="<?= traduci("Testo") ?>" rel="S;3;0;A"></textarea>
			        <div id="allegati_chiarimenti">
			        	<table width="100%" id="tab_allegati_gara" class="row-bordered"></table>
			        	<div class="big_progress_bar" id="progress_bar_allegati_chiarimenti"><div class="progress_bar"></div></div>
			        	<button type="button" class="submit resumable-button" id="browser-button">
			        		<i class="fa fa-paperclip"></i> <?= traduci("Allega un file") ?>
			        		<!-- <input id="" type="file" multiple class="resumable-input" id="resumable-browse"> -->
			        	</button>
			        </div>
			        <input type="submit" class="submit_big" value="<?= traduci("Invia quesito") ?>">
		        </form>
					</div>
				</div>
			</div>
			<? 
				if ($record["attivaSopralluogo"]=="S") {
			?>
				<div class="box">
					<h3><?= traduci("Richiesta di soprallugo") ?></h3>
					<div class="box">
						<input type="button" class="submit_big" value="<?= traduci("Richiedi sopralluogo") ?>" onClick="$(this).slideUp(); $('#form_sopralluogo').slideDown();">
						<div id="form_sopralluogo" style="display:none">
							<form name="box" method="post" action="/gare/sopralluogo/save_richiesta.php" rel="validate">
								<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
								<input type="hidden" name="operazione" value="INSERT">
								<textarea rows='10' class="ckeditor_simple" name="note" cols='80' id="note" title="<?= traduci("note") ?>" rel="S;3;0;A"></textarea>
								<input type="submit" class="submit_big" value="<?= traduci("Invia richiesta") ?>">
							</form>
						</div>
					</div>
				</div>
			<?
			}
		}
	}
			} else {
				echo "<h1>" . traduci('impossibile accedere') . " - 1</h1>";
				echo "<h2>Potrebbe essere necessario <a href='/operatori_economici/registrazione.php' title='Registrazione'>Registrarsi</a> o effettuare il <a href='/accesso.php' title='Accedi'>Login</a> per visualizzare le informazioni</h2>";
				}
	} else {

			echo "<h1>" . traduci('impossibile accedere') . " - 2</h1>";
		}
	include_once($root."/layout/bottom.php");
	?>
