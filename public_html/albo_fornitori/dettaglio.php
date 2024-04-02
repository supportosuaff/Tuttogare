<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$public = true;
		if (isset($_GET["cod"]) && isset($_SESSION["ente"]["codice"])) {
				$codice = $_GET["cod"];
				$bind = array(':codice' => $codice,":codice_ente"=>$_SESSION["ente"]["codice"]);
				if (!isset($_SESSION["codice_utente"])) {
					$strsql  = "SELECT * FROM b_bandi_albo WHERE codice = :codice ";
					$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
					$strsql .= "AND pubblica = '2' ";
				} else {
						$strsql  = "SELECT b_bandi_albo.* FROM b_bandi_albo WHERE b_bandi_albo.codice = :codice ";
						$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
						$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
				}

				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					if(empty($_SESSION["record_utente"])) $_SESSION["REDIRECT_BACK_ALBO"] = $record["codice"];
					$string_cpv = "";
					$cpv = array();
					if ($_SESSION["language"] != "IT") {
						$strsql = "SELECT b_cpv_dict.*, b_cpv_dict.{$_SESSION["language"]} AS descrizione FROM b_cpv_dict JOIN r_cpv_bandi_albo ON b_cpv_dict.codice = r_cpv_bandi_albo.codice WHERE r_cpv_bandi_albo.codice_bando = :codice ORDER BY b_cpv_dict.codice";
					} else {
						$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_bandi_albo ON b_cpv.codice = r_cpv_bandi_albo.codice WHERE r_cpv_bandi_albo.codice_bando = :codice ORDER BY b_cpv.codice";
					}
					$bind = array(':codice' => $record["codice"]);
					$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
					if ($risultato_cpv->rowCount()>0) {
						$string_cpv = "<ul>";
						while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
							$string_cpv .= "<li><strong>" . $rec_cpv["codice"] . "</strong> - " . $rec_cpv["descrizione"] . "</li>";
						}
						$string_cpv .= "</ul>";
					}

					$sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice AND sezione = 'albo' AND online = 'S'";
					$ris_allegati = $pdo->bindAndExec($sql,$bind);


					if ($record["codice_ente"] != $_SESSION["ente"]["codice"]) {
						$bind = array(":codice_ente"=>$record["codice_ente"]);
						$strsql="SELECT * FROM b_enti WHERE codice = :codice_ente ";
						$ris = $pdo->bindAndExec($strsql,$bind);
						if ($ris->rowCount()>0) $rec_ente = $ris->fetch(PDO::FETCH_ASSOC);
					}
?>
<h1><?= ($record["manifestazione_interesse"] == "S") ? traduci("Indagine di mercato") : traduci("Elenco dei fornitori") ?> - ID <? echo $record["id"] ?></h1>
  <? if ($record["annullata"] == "S") {
			echo "<h2 class=\"errore\">" . traduci("Annullata") . " - " . $record["numero_annullamento"] . " \ " . mysql2date($record["data_annullamento"]) . "</h2>";
		}
	ob_start();
	if (($record["annullata"] == "N") && ((strtotime($record["data_scadenza"]) > time())||($record["data_scadenza"] == 0))) {
		?><div class="box"><?
		$dgue = false;
		$sql = "SELECT * FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'albo' ";
		$ris_dgue = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));
		if ($ris_dgue->rowCount() > 0) {
			$dgue = true;
			if (strtotime($record["data_scadenza"]) > time() || $record["data_scadenza"]==0) {
				if (is_operatore()) {
					?>
					<a href="/dgue/edit.php?sezione=albo&codice_riferimento=<?= $record["codice"] ?>" style="background-color:#055" class="submit_big"><?= traduci("Compila Documento di Gara Unico Europeo") ?> - <?= traduci("DGUE") ?></a>
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
			$sql = "SELECT * FROM r_partecipanti_albo WHERE codice_bando = :codice AND codice_utente = :codice_utente ";
			$ris = $pdo->bindAndExec($sql,$bind);
			$partecipato = false;
			$testo = traduci("Richiedi Abilitazione");
			if ($ris->rowCount()>0) {
				$partecipato = true;
				$par = $ris->fetch(PDO::FETCH_ASSOC);
				if ($par["conferma"] == "S") {
					$testo = traduci("Aggiorna i dati");
				}
			}

		?>
        	<a href="/albo_fornitori/modulo.php?cod=<? echo $record["codice"] ?>" class="submit_big" title="<?= $testo ?>"><?= $testo ?></a>
        <?
        	if ($partecipato) {
				?>
				<a id="button_revoca_<? echo $par["codice"] ?>" href="#" onClick="elimina('<? echo $par["codice"] ?>','albo_fornitori/abilitazione');" class="submit_big" style="background-color:#C30" title="<?= traduci("Revoca partecipazione") ?>"><?= traduci("Revoca partecipazione") ?></a>
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
	echo $form_operatore;
	?>
<table width="100%">
	<? if (isset($rec_ente)) { ?>
		<tr><td class="etichetta"><?= traduci("Stazione appaltante") ?></td><td colspan="3"><strong><? echo $_SESSION["ente"]["denominazione"]; ?></strong></td></tr>
		<tr><td class="etichetta"><?= traduci("Ente committente") ?></td><td colspan="3"><strong><? echo $rec_ente["denominazione"]; ?></strong></td></tr>
    <? } ?>
	<tr><td class="etichetta"><?= traduci("Oggetto") ?></td><td colspan="3"><? echo $record["oggetto"] ?></td></tr>
   <tr>
    	<td class="etichetta"><?= traduci("Data pubblicazione") ?></td><td><? echo mysql2date($record["data_pubblicazione"]) ?></td>
    	<td class="etichetta"><?= traduci("Scadenza presentazione istanze") ?></td><td><strong><? if ($record["data_scadenza"] > 0) echo mysql2completedate($record["data_scadenza"]) ?></strong></td>

    </tr>
   <? if ($string_cpv != "") { ?>
<tr><td class="etichetta"><?= traduci("Categorie merceologiche") ?></td><td colspan="3">
<? echo $string_cpv; ?>
</td></tr>
<? } ?>

<tr><td class="etichetta"><?= traduci("descrizione") ?></td><td colspan="3"><? echo $record["descrizione"] ?></td></tr>
<tr><td class="etichetta"><?= traduci("struttura proponente") ?></td><td colspan="3"><? echo $record["struttura_proponente"] ?></td></tr>
<tr><td class="etichetta"><?= traduci("Responsabile del servizio") ?></td><td><? echo $record["responsabile_struttura"] ?></td>
	<td class="etichetta"><?= traduci("Responsabile del procedimento") ?></td><td><? echo $record["rup"] ?></td></tr>
<?
	if($record["manifestazione_interesse"] == "N" && $record["visualizza_elenco"] != "N") {
		?>
		<tr>
			<td class="etichetta"><?= traduci("Elenco") ?></td>
			<td colspan="3"><a href="/albo_fornitori/elenco/id<?= $record["codice"] ?>-dettaglio" target="_blank"><?= traduci("Visualizza l'elenco completo degli operatori ammessi") ?></a></td>
		</tr>
		<?
	}
?>
  <? if ((isset($ris_allegati) && ($ris_allegati->rowCount() > 0)) || (!empty($dgue) && $dgue)) { ?>
      <tr><td class="etichetta"><?= traduci("Allegati") ?></td><td colspan="3">
      <table width="100%" id="tab_allegati">
				<? if (!empty($dgue)) { ?>
					<tr>
		 				<td width="10" style="text-align:center"><span class="fa fa-code fa-2x"></span></td>
		        <td>
							<strong><a href="/dgue/getRequestXML.php?sezione=albo&codice_riferimento=<?= $record["codice"] ?>">XML <?= traduci("Richiesta DGUE") ?></a></strong>
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
				$sql = "SELECT * FROM b_modulistica_albo WHERE codice_bando = :codice AND attivo = 'S' ORDER BY codice";
				$risultato = $pdo->bindAndExec($sql,$bind);
				if ($risultato->rowCount()>0) { ?>
					<div class="box">
						<h2><?= traduci("Modulistica richiesta") ?></h2>
						<table width="100%">
							<thead>
								<tr><td><?= traduci("nome") ?></td><td><?= traduci("Obbligatorio") ?></td></tr>
							</thead>
						<? while ($record_modulo = $risultato->fetch(PDO::FETCH_ASSOC)) {
							if ($record["codice"] == 1775 && $record_modulo["obbligatorio"] == "N") $record_modulo["obbligatorio"] = "-";
							?>
							<tr>
								<td>
									<? echo $record_modulo["titolo"];
									if ($record_modulo["nome_file"]!= "") {
										?>
										<br>
										<a href="/documenti/allegati/albo/<?= $record["codice"] ?>/<?= $record_modulo["nome_file"] ?>" target="_blank"><?= traduci("Scarica") ?></a>
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
