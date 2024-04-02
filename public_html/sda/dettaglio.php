<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$public = true;
		if (isset($_GET["cod"]) && isset($_SESSION["ente"]["codice"])) {
				$codice = $_GET["cod"];
				$bind = array(':codice' => $codice,":codice_ente"=>$_SESSION["ente"]["codice"]);
				if (!isset($_SESSION["codice_utente"])) {
					$strsql  = "SELECT * FROM b_bandi_sda WHERE codice = :codice ";
					$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
					$strsql .= "AND pubblica = '2' ";
				} else {
						$strsql  = "SELECT b_bandi_sda.* FROM b_bandi_sda WHERE b_bandi_sda.codice = :codice ";
						$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
						$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
				}

				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);

					$string_cpv = "";
					$cpv = array();
					$bind = array(':codice' => $record["codice"]);
					if ($_SESSION["language"] != "IT") {
						$strsql = "SELECT b_cpv_dict.*, b_cpv_dict.{$_SESSION["language"]} AS descrizione FROM b_cpv_dict JOIN r_cpv_bandi_mercato ON b_cpv_dict.codice = r_cpv_bandi_mercato.codice WHERE r_cpv_bandi_mercato.codice_bando = :codice ORDER BY b_cpv_dict.codice";
					} else {
						$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_bandi_mercato ON b_cpv.codice = r_cpv_bandi_mercato.codice WHERE r_cpv_bandi_mercato.codice_bando = :codice ORDER BY b_cpv.codice";
					}

					$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
					if ($risultato_cpv->rowCount()>0) {
						$string_cpv = "<ul>";
						while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
							$string_cpv .= "<li><strong>" . $rec_cpv["codice"] . "</strong> - " . $rec_cpv["descrizione"] . "</li>";
						}
						$string_cpv .= "</ul>";
					}

					$sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice AND sezione = 'sda' AND online = 'S'";
					$ris_allegati = $pdo->bindAndExec($sql,$bind);


					if ($record["codice_ente"] != $_SESSION["ente"]["codice"]) {
						$bind = array(":codice_ente"=>$record["codice_ente"]);
						$strsql="SELECT * FROM b_enti WHERE codice = :codice_ente ";
						$ris = $pdo->bindAndExec($strsql,$bind);
						if ($ris->rowCount>0) $rec_ente = $ris->fetch(PDO::FETCH_ASSOC);
					}
?>
<h1><?= traduci('s.d.a.') ?> - ID <? echo $record["id"] ?></h1>
  <? if ($record["annullata"] == "S") {
			echo "<h2 class=\"errore\">Annullata con atto n. " . $record["numero_annullamento"] . " del " . mysql2date($record["data_annullamento"]) . "</h2>";
		}
		ob_start();
	if (($record["annullata"] == "N") && ((strtotime($record["data_scadenza"]) > time())||($record["data_scadenza"] == 0))) {
		?><div class="box"><?
		$dgue = false;
		$sql = "SELECT * FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'sda' ";
		$ris_dgue = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));
		if ($ris_dgue->rowCount() > 0) {
			$dgue = true;
			if (strtotime($record["data_scadenza"]) > time()) {
				if (is_operatore()) {
					?>
					<a href="/dgue/edit.php?sezione=mercato&codice_riferimento=<?= $record["codice"] ?>" style="background-color:#055" class="submit_big"><?= traduci("Compila Documento di Gara Unico Europeo") ?> - <?= traduci("DGUE") ?></a>
					<?
					} else if (!isset($_SESSION["codice_utente"])) {
					?>
					<h3><a href="/operatori_economici/registrazione.php" title="traduci("registrazione-oe")"><?= traduci("Registrati") ?></a> \ <a href="/accesso.php" title="<?= traduci("Accedi") ?>"><?= traduci("Accedi") ?></a> <?= traduci("per partecipare") ?></h3>
				<?
				}
			}
		}


		if (is_operatore()) {
			$bind = array(':codice' => $record["codice"],":codice_utente"=>$_SESSION["codice_utente"]);
			$sql = "SELECT * FROM r_partecipanti_sda WHERE codice_bando = :codice AND codice_utente = :codice_utente ";
			$ris = $pdo->bindAndExec($sql,$bind);
			$partecipato = false;
			$testo = traduci("Richiedi Abilitazione");
			if ($ris->rowCount()>0) {
				$partecipato = true;
				$testo = traduci("Aggiorna i dati");
			}

		?>
        	<a href="/sda/modulo.php?cod=<? echo $record["codice"] ?>" class="submit_big" title="<?= $testo ?>"><?= $testo ?></a>
        <?
        	if ($partecipato) {
				$par = $ris->fetch(PDO::FETCH_ASSOC);
				?>
				<a id="button_revoca_<? echo $par["codice"] ?>" href="#" onClick="elimina('<? echo $par["codice"] ?>','mercato_elettronico/abilitazione');" class="submit_big" style="background-color:#C30" title="<?= traduci("Revoca partecipazione") ?>"><?= traduci("Revoca partecipazione") ?></a>
								<? }
		} else if (!isset($_SESSION["codice_utente"])) {
			if ($ris_dgue->rowCount() == 0) {
			?>
			<h3><a href="/operatori_economici/registrazione.php" title="traduci("registrazione-oe")"><?= traduci("Registrati") ?></a> \ <a href="/accesso.php" title="<?= traduci("Accedi") ?>"><?= traduci("Accedi") ?></a> <?= traduci("per partecipare") ?></h3>
			<?
			}
		} ?></div><?
	}
	$form_operatore = ob_get_clean();
	echo $form_operatore;?>
<table width="100%">
	<? if (isset($rec_ente)) { ?>
    	<tr><td class="etichetta"><?= traduci("Ente beneficiario") ?></td><td colspan="3"><strong><? echo $rec_ente["denominazione"]; ?></strong></td></tr>
    <? } ?>
	<tr><td class="etichetta"><?= traduci("Oggetto") ?></td><td colspan="3"><? echo $record["oggetto"] ?></td></tr>
   <tr>
    	<td class="etichetta"><?= traduci("Data pubblicazione") ?></td><td><? echo mysql2completedate($record["data_pubblicazione"]) ?></td>
    	<td class="etichetta"><?= traduci("Scadenza presentazione istanze") ?></td><td><strong><? echo mysql2completedate($record["data_scadenza"]) ?></strong></td>

    </tr>
   <? if ($string_cpv != "") { ?>
<tr><td class="etichetta"><?= traduci("Categorie merceologiche") ?></td><td colspan="3">
<? echo $string_cpv; ?>
</td></tr>
<? } ?>

<tr><td class="etichetta"><?= traduci("descrizione") ?></td><td colspan="3"><? echo $record["descrizione"] ?></td></tr>
<tr><td class="etichetta"><?= traduci("Struttura proponente") ?></td><td colspan="3"><? echo $record["struttura_proponente"] ?></td></tr>
<tr><td class="etichetta"><?= traduci("Responsabile del servizio") ?></td><td><? echo $record["responsabile_struttura"] ?></td>
	<td class="etichetta"><?= traduci("Responsabile del procedimento") ?></td><td><? echo $record["rup"] ?></td></tr>
                        <? if ((isset($ris_allegati) && ($ris_allegati->rowCount() > 0)) || (!empty($dgue) && $dgue)) { ?>
                            <tr><td class="etichetta"><?= traduci("Allegati") ?></td><td colspan="3">
                            <table width="100%" id="tab_allegati">
															<? if (!empty($dgue)) { ?>
																<tr>
													 				<td width="10" style="text-align:center"><span class="fa fa-code fa-2x"></span></td>
													        <td>
																		<strong><a href="/dgue/getRequestXML.php?sezione=sda&codice_riferimento=<?= $record["codice"] ?>">XML <?= traduci("Richiesta DGUE") ?></a></strong>
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

				$bind = array(':codice' => $record["codice"]);
				$sql = "SELECT * FROM b_modulistica_sda WHERE codice_bando = :codice AND attivo = 'S' ORDER BY codice";
				$risultato = $pdo->bindAndExec($sql,$bind);
				if ($risultato->rowCount()>0) { ?>
					<div class="box">
						<h2><?= traduci("Modulistica richiesta") ?></h2>
						<table width="100%">
							<thead>
								<tr><td><?= traduci("modulo") ?></td><td><?= traduci("Obbligatorio") ?></td></tr>
							</thead>
						<? while ($record_modulo = $risultato->fetch(PDO::FETCH_ASSOC)) { ?>
							<tr>
								<td>
									<? echo $record_modulo["titolo"];
									if ($record_modulo["nome_file"]!= "") {
										?>
										<br>
										<a href="/documenti/allegati/sda/<?= $record["codice"] ?>/<?= $record_modulo["nome_file"] ?>" target="_blank"><?= traduci("Scarica") ?></a>
										<?
									}
									?>
								</td>
								<td width="10" style="text-align: center"><?= $record_modulo["obbligatorio"] ?></td>

							</tr>
							<? } ?>

					</table>
					</div>
				<?
			}
			echo $form_operatore;
			} else {
				echo "<h1>".traduci('impossibile accedere')."</h1>";
			}
		} else {
			echo "<h1>".traduci('impossibile accedere')."</h1>";
		}
	include_once($root."/layout/bottom.php");
	?>
