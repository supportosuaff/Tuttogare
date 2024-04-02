<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$public = true;
		if (isset($_GET["cod"]) && isset($_SESSION["ente"]["codice"])) {
				$codice = $_GET["cod"];
				$bind = array(":codice"=>$codice,":codice_ente"=>$_SESSION["ente"]["codice"]);
				if (!isset($_SESSION["codice_utente"])) {
					$strsql  = "SELECT b_concorsi.*, b_conf_stati_concorsi.titolo AS fase, b_conf_stati_concorsi.colore FROM
								b_concorsi JOIN b_conf_stati_concorsi ON b_concorsi.stato = b_conf_stati_concorsi.fase WHERE b_concorsi.codice = :codice ";
					$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
					$strsql .= "AND pubblica = '2' ";
				} else {
					$strsql  = "SELECT b_concorsi.*,b_conf_stati_concorsi.titolo AS fase, b_conf_stati_concorsi.colore FROM
								b_concorsi JOIN b_conf_stati_concorsi ON b_concorsi.stato = b_conf_stati_concorsi.fase WHERE b_concorsi.codice = :codice ";
					$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
					$strsql .= "AND (pubblica > 0)";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);

					$current_color = $record["colore"];
					$current_fase = $record["fase"];

					$string_cpv = "";
					$cpv = array();
					$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_concorsi ON b_cpv.codice = r_cpv_concorsi.codice WHERE r_cpv_concorsi.codice_gara = :codice_gara ORDER BY codice";
					$risultato_cpv = $pdo->bindAndExec($strsql,array(":codice_gara"=>$record["codice"]));
					if ($risultato_cpv->rowCount()>0) {
						$string_cpv = "<ul>";
						while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
							$string_cpv .= "<li><strong>" . $rec_cpv["codice"] . "</strong> - " . $rec_cpv["descrizione"] . "</li>";
						}
						$string_cpv .= "</ul>";
					}

					$sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice_gara AND sezione = 'concorsi' AND online = 'S' AND hidden = 'N' ";
					$ris_allegati = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));


					if ($record["codice_ente"] != $_SESSION["ente"]["codice"]) {
						$strsql="SELECT * FROM b_enti WHERE codice = :codice_ente";
						$ris = $pdo->bindAndExec($strsql,array(":codice_ente"=>$record["codice_ente"]));
						if ($ris->rowCount()>0) $rec_ente = $ris->fetch(PDO::FETCH_ASSOC);
					}

					$sql_fasi = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara ";
					$ris_fasi = $pdo->bindAndExec($sql_fasi,array(":codice_gara"=>$record["codice"]));
					if ($ris_fasi->rowCount() > 0) {
						$ris_fasi = $ris_fasi->fetchAll(PDO::FETCH_ASSOC);
						$i = 0;
						$open = true;
						$last = array();
						$fase_attiva = array();
						foreach($ris_fasi AS $fase) {
							if ($fase["attiva"]=="S") {
								if ($i > 0) $open = false;
								$last = $fase_attiva;
								$fase_attiva = $fase;
							}
							$i++;
						}

						if (($record["stato"]==3) && (strtotime($fase_attiva["scadenza"])<time())) {

							$record["colore"] = $config["colore_scaduta"];
							$record["fase"] = "Scaduta";
						}

		?>
		<h1 style="border:0px;">DETTAGLI GARA - ID <? echo $record["id"] ?></h1>
		<h3 style="text-align:right">Stato: <strong id="stato_label"><? echo $record["fase"] ?></strong></h3>
		<div id="stato_border" style="background-color:#<? echo $record["colore"] ?>; padding:5px;"></div>
		<br>
  	<? if ($record["annullata"] == "S") {
			echo "<h2 class=\"errore\">Annullata con atto n. " . $record["numero_annullamento"] . " del " . mysql2date($record["data_annullamento"]) . "</h2>";
		}
		ob_start();
		?><div class="box">
			<h3>Pannello di partecipazione</h3>
			<?
		$dgue = false;

		$sql = "SELECT * FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'concorsi' ";
		$ris_dgue = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));
		if ($ris_dgue->rowCount() > 0) $dgue = true;
		if (is_operatore()) {
			$accedi = false;
			if (($record["annullata"] == "N") && (strtotime($fase_attiva["scadenza"]) > time())) {
					if ($open) {
						$accedi = true;
					} else if (!empty($last["codice"])) {
						$sql_check = "SELECT * FROM r_partecipanti_concorsi JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
										WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase AND r_partecipanti_concorsi.conferma = 1 AND r_partecipanti_concorsi.ammesso = 'S'
										AND r_partecipanti_concorsi.escluso = 'N' AND r_partecipanti_utenti_concorsi.codice_utente = :codice_utente ";
						$ris_check = $pdo->bindAndExec($sql_check,array(":codice_gara"=>$record["codice"],":codice_fase"=>$last["codice"],":codice_utente"=>$_SESSION["codice_utente"]));
						if ($ris_check->rowCount() > 0) $accedi = true;
					}

					if ($accedi) {
						if ($ris_dgue->rowCount() > 0) {
							if (strtotime($fase_attiva["scadenza"]) > time()) {
								if (is_operatore()) {
									?>
									<a href="/dgue/edit.php?sezione=concorsi&codice_riferimento=<?= $record["codice"] ?>" style="background-color:#055" class="submit_big">Compila Documento di Gara Unico Europeo - DGUE</a>
									<?
								}
							}
						}

			?>
					<? if ($current_color != $record["colore"]) { ?>
						<script>
							$("#stato_border").css("background-color","#<?= $current_color ?>");
							$("#stato_label").html("<?= $current_fase ?>");

						</script>
					<? } ?>
        	<a href="/concorsi/partecipa/modulo.php?cod=<? echo $record["codice"] ?>" onClick="partecipa()" class="submit_big" title="Partecipa">
						Partecipa
					</a>
        <?
			}
		}
		$bind=array();
		$bind[":codice_gara"] = $record["codice"];
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$strsql = "SELECT r_integrazioni_concorsi.* FROM r_integrazioni_concorsi JOIN b_integrazioni_concorsi ON r_integrazioni_concorsi.codice_integrazione = b_integrazioni_concorsi.codice ";
		$strsql.= " WHERE b_integrazioni_concorsi.codice_gara = :codice_gara AND r_integrazioni_concorsi.codice_utente = :codice_utente";
		$ris_int = $pdo->bindAndExec($strsql,$bind);
		if ($ris_int->rowCount() >0) {
			?>
			<a href="/concorsi/integrazioni/view.php?cod=<? echo $record["codice"] ?>" class="submit_big" title="Integrazioni">Integrazioni</a>
			<?
		}
	} else if (!isset($_SESSION["codice_utente"])) {
		?>
		<h3><a href="/operatori_economici/registrazione.php" title="Registrazione operatori economici">Registrati</a> o <a href="/accesso.php" title="Accedi all'area riservata">Accedi</a> per partecipare</h3>
		<? }?>
	</div>
	<?
	$form_operatore = ob_get_clean();
	echo $form_operatore;
	?>
<table width="100%">
	<? if (isset($rec_ente)) { ?>
    	<tr><td class="etichetta">Ente beneficiario</td><td colspan="3"><strong><? echo $rec_ente["denominazione"]; ?></strong></td></tr>
    <? } ?>
    <tr><td class="etichetta">Oggetto</td><td colspan="3"><? echo $record["oggetto"] ?></td></tr>
		<tr>
	    	<td class="etichetta">CIG</td><td><strong><? echo $record["cig"] ?></strong><br></td>
			<td class="etichetta">CUP</td><td><strong><? echo $record["cup"] ?></strong><br></td>
		</tr>
    <tr>
			<td class="etichetta">Premio</td><td><strong>&euro; <? echo number_format($record["premio"],2,",","."); ?></strong></td>
			<td class="etichetta">Data pubblicazione</td><td><? echo mysql2date($record["data_pubblicazione"]) ?></td>
    </tr>
		<? if ($string_cpv != "") { ?>
			<tr>
				<td class="etichetta">Categorie</td>
				<td colspan="3">
					<? echo $string_cpv; ?>
				</td>
			</tr>
		<? } ?>
		<tr><td class="etichetta">Breve descrizione</td><td colspan="3"><? echo $record["descrizione"] ?></td></tr>
		<tr><td class="etichetta">Struttura proponente</td><td colspan="3"><? echo $record["struttura_proponente"] ?></td></tr>
		<tr><td class="etichetta">Responsabile del servizio</td><td><? echo $record["responsabile_struttura"] ?></td><td class="etichetta">Responsabile del procedimento</td><td><? echo $record["rup"] ?></td></tr>
		<? if ((isset($ris_allegati) && ($ris_allegati->rowCount() > 0)) || $dgue) { ?>
		<tr>
			<td class="etichetta">Allegati</td>
			<td colspan="3">
        <table width="100%" id="tab_allegati">
					<? if ($dgue) { ?>
						<tr>
							<td width="10" style="text-align:center"><span class="fa fa-code fa-2x"></span></td>
			        <td>
								<strong><a href="/dgue/getRequestXML.php?sezione=concorsi&codice_riferimento=<?= $record["codice"] ?>">XML Richiesta DGUE</a></strong>
							</td>
			      </tr>
					<? } ?>
					<?
					while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
						include($root."/allegati/tr_allegati.php");
					}
					?>
				</table>
			</td>
		</tr>
  	<? } ?>
	</table>
<?

	if (!empty($ris_fasi)) {
		foreach($ris_fasi AS $fase) {
			?>
				<div class="box">
					<h3><strong><?= $fase["oggetto"] ?></strong></h3><br>
					<? if (!empty($fase["chiarimenti"])) { ?>
						<table width="100%">
							<tr>
								<td class="etichetta">
									Termine richieste chiarimenti
								</th>
								<td><strong><?= mysql2completedate($fase["chiarimenti"]) ?></strong></td>
								<td class="etichetta">
									Data scadenza
								</th>
								<td><strong><?= mysql2completedate($fase["scadenza"]) ?></strong></td>
								<td class="etichetta">
									Apertura offerte
								</th>
								<td><strong><?= mysql2completedate($fase["apertura"]) ?></strong></td>
							</tr>
						</table><br>
					<? } ?>
					<?= $fase["descrizione"] ?>
				</div>
			<?
		}
		$sql_partecipanti = "SELECT r_partecipanti_utenti_concorsi.* FROM r_partecipanti_concorsi JOIN r_partecipanti_utenti_concorsi
												 ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
												 WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase AND primo = 'S'
												 AND (r_partecipanti_concorsi.conferma = TRUE OR r_partecipanti_concorsi.conferma IS NULL)";
		$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,array(":codice_gara"=>$record["codice"],":codice_fase"=>$fase["codice"]));
		if ($ris_partecipanti->rowCount()>0) {
			?><div class="box">
				<h2><?
			if ($record["numero_atto_esito"] != "") {
				?>
				Aggiudicazione definitiva - <strong>Atto n. <?= $record["numero_atto_esito"] ?> <? if (!empty($record["data_atto_esito"])) { ?>del <?= mysql2date($record["data_atto_esito"]) ?><? } ?></strong>
				<?
			} else {
				?>
				Proposta di Aggiudicazione
				<?
			} ?>
			</h2>
			<div style="background-color:#<? echo $record["colore"] ?>; padding:5px;"></div>
			<table width="100%">
			<?
				while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
					if (is_numeric($partecipante["codice_utente"])) {
						echo "<tr><td width=\"1\"><h3>".$partecipante["partita_iva"] . "</h3></td>
						<td><h3><strong>" . $partecipante["ragione_sociale"] . "</strong></h3></td></tr>";
					} else {
						echo "<tr><td colspan=\"2\"><h3>PARTECIPANTE NON AMMESSO</h3></td></tr>";
					}
				}
			?></table>

			</div>
		<?
			$sql_partecipanti = "SELECT r_partecipanti_utenti_concorsi.* FROM r_partecipanti_concorsi JOIN r_partecipanti_utenti_concorsi
													 ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
													 WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase
													 AND (r_partecipanti_concorsi.conferma = TRUE OR r_partecipanti_concorsi.conferma IS NULL)";
			$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,array(":codice_gara"=>$record["codice"],":codice_fase"=>$fase["codice"]));
			if ($ris_partecipanti->rowCount() > 0) {
				?>
				<div class="box">
				<h2>Partecipanti</h2>
				<table width="100%">
					<?
					while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
						if (is_numeric($partecipante["codice_utente"])) {
							echo "<tr><td width=\"1\"><h3>".$partecipante["partita_iva"] . "</h3></td>
							<td><h3><strong>" . $partecipante["ragione_sociale"] . "</strong></h3></td></tr>";
						} else {
							echo "<tr><td colspan=\"2\"><h3>PARTECIPANTE NON AMMESSO</h3></td></tr>";
						}
					}
					?>
				</table>
			</div>
				<?
			}
		}
	}

	$bind = array(":codice" => $record["codice"]);

	$sql = "SELECT * FROM b_commissioni_concorsi WHERE codice_gara = :codice";
	$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
	if ($ris_partecipanti->rowCount()>0) {
		?>
		<div class="box">
			<h2>Commissione</h2>
			<?
				if (!empty($record["numero_atto_commissione"])) {
					if (!empty($record["allegato_atto_commissione"])) {
						$sql = "SELECT * FROM b_allegati WHERE codice = :codice_allegato";
						$ris_allegato = $pdo->bindAndExec($sql,array(":codice_allegato"=>$record["allegato_atto_commissione"]));
						if ($ris_allegato->rowCount() > 0) {
							$allegato = $ris_allegato->fetch(PDO::FETCH_ASSOC);
							$percorso_html = "/documenti/allegati/concorsi/". $allegato["codice_gara"] . "/" . $allegato["nome_file"];
							$percorso_fisico = $config["pub_doc_folder"] . "/allegati/concorsi/". $allegato["codice_gara"] . "/" . $allegato["riferimento"];
							if (file_exists($percorso_fisico)) {
								$estensione = explode(".",$allegato["nome_file"]);
								$estensione = end($estensione);
								?>
								<a href="<?= $percorso_html ?>" target="_blank" title="Allegato"><?
									if (file_exists($root."/img/".$estensione.".png")) { ?><img src="/img/<? echo $estensione ?>.png" alt="File <? echo $estensione ?>" style="vertical-align:middle"><? } else {
										echo $allegato["nome_file"];
									 }
								?></a>
								<?
							}
						}
					?>
					 Atto di costituzione <strong><? echo $record["numero_atto_commissione"] ?></strong> del <? echo mysql2date($record["data_atto_commissione"]) ?>
					<?
					}
				}
				?>
				<table width="100%">
					<tr>
						<th>Nominativo</th>
						<th>Ruolo</th>
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
										$percorso_html = "/documenti/allegati/concorsi/". $allegato["codice_gara"] . "/" . $allegato["nome_file"];
										$percorso_fisico = $config["pub_doc_folder"] . "/allegati/concorsi/". $allegato["codice_gara"] . "/" . $allegato["riferimento"];
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

	$bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ":codice" => $record["codice"]);

	$strsql  = "SELECT b_avvisi_concorsi.* ";
	$strsql .= "FROM b_avvisi_concorsi ";
	$strsql .= "WHERE codice_ente = :codice_ente AND codice_gara = :codice ";
	$strsql .= " ORDER BY data DESC,  timestamp DESC " ;


	$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

	if ($risultato->rowCount()>0) {
		?>
        <div class="box">
        <h2>Avvisi</h2>
        <table class="elenco" style="width:100%">
        <thead style="display:none;"><tr><td></td><td></td></tr></thead>
        <tbody>
        <?
		while ($record_avvisi = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$codice			= $record_avvisi["codice"];
			$titolo			= $record_avvisi["titolo"];
			$data			= mysql2date($record_avvisi["data"]);
			$testo			= strip_tags($record_avvisi["testo"]);
			$href = "/concorsi/avvisi/dettaglio.php?cod=".$codice;
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
	$strsql = "SELECT b_risposte_concorsi.quesito, b_risposte_concorsi.testo AS risposta, b_quesiti_concorsi.timestamp AS data_quesito, b_risposte_concorsi.timestamp AS data_risposta FROM
			 b_quesiti_concorsi JOIN b_risposte_concorsi ON b_quesiti_concorsi.codice = b_risposte_concorsi.codice_quesito
			 WHERE b_risposte_concorsi.quesito <> '' AND b_quesiti_concorsi.attivo = 'S' AND b_quesiti_concorsi.codice_gara = :codice  AND b_quesiti_concorsi.codice_ente = :codice_ente  ORDER BY b_risposte_concorsi.timestamp";

	$ris_quesiti = $pdo->bindAndExec($strsql,$bind);
	if ($ris_quesiti->rowCount()>0) {
		?><div class="box"><h2>Chiarimenti</h2>
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
	echo $form_operatore;
	if (strtotime($fase_attiva["chiarimenti"]) > time()) {
		if (is_operatore() && $accedi) {
		?><div class="box">
			<input type="button" class="submit_big" value="Formula quesito" onClick="$(this).slideUp(); $('#form_quesiti').slideDown();">
			<div id="form_quesiti" style="display:none">
        <h2>Richiesta di chiarimenti</h2>
        <form name="box" method="post" action="/concorsi/chiarimenti/save_quesito.php" rel="validate">
                    <input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
                    <input type="hidden" name="operazione" value="INSERT">
		           <textarea rows='10' class="ckeditor_simple" name="testo" cols='80' id="testo" title="Testo" rel="S;3;0;A"></textarea>
                   <input type="submit" class="submit_big" value="Invia quesito">
        </form></div>
			</div>
        <?
		} else if (!isset($_SESSION["codice_utente"])) {
			?><div class="box">
	        <h3><a href="/operatori_economici/registrazione.php" title="Registrazione operatori economici">Registrati</a> o <a href="/accesso.php" title="Accedi all'area riservata">Accedi</a> per chiedere chiarimenti</h3></div>
	        <?
		}
	}
	} else {

	echo "<h1>Concorso inesistente o privilegi insufficienti</h1>";

	}
			} else {

				echo "<h1>Concorso inesistente o privilegi insufficienti</h1>";

				}
	} else {

			echo "<h1>Concorso inesistente</h1>";
		}
	include_once($root."/layout/bottom.php");
	?>
