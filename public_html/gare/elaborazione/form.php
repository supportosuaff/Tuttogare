<?
if (isset($in_elaborazione) && $in_elaborazione) {
	if ($ris_importi->rowCount() > 0) {
		$totale_gara = 0;
		while($rec_importo = $ris_importi->fetch(PDO::FETCH_ASSOC)) {
			$totale_gara = $totale_gara + $rec_importo["importo_base"] + $rec_importo["importo_oneri_no_ribasso"]; // + $rec_importo["importo_oneri_ribasso"] + $rec_importo["importo_personale"];
		}
		$bind = array();
		$bind[":tipologia"] = $record_gara["tipologia"];
		$bind[":criterio"] = $record_gara["criterio"];
		$bind[":procedura"] = $record_gara["procedura"];
		$bind[":totale_gara"] = $totale_gara;
		$strsql  = "SELECT b_modelli_new.* FROM b_modelli_new WHERE attivo = 'S' AND (tipologia = :tipologia OR tipologia = 0)";
		$strsql .= " AND (criterio = :criterio OR criterio = 0)";
		$strsql .= " AND procedura = :procedura";
		$strsql .= " AND (importo_massimo >= :totale_gara OR importo_massimo = 0)";
		$strsql .= " AND (importo_minimo <= :totale_gara OR importo_minimo = 0)";
		$risultato_modelli = $pdo->bindAndExec($strsql,$bind);

		$array_opzioni = array();
		$gruppi_opzioni = array();
		$array_script = array();

		$bind = array();
		$bind[":criterio"] = $record_gara["criterio"];
		$strsql = "SELECT * FROM b_criteri WHERE codice = :criterio";
		$risultato_criterio = $pdo->bindAndExec($strsql,$bind);
		if ($risultato_criterio->rowCount()>0) {
			$specifiche_criterio = $risultato_criterio->fetch(PDO::FETCH_ASSOC);
			if ($specifiche_criterio["opzioni"]!="") $gruppi_opzioni = explode(",",$specifiche_criterio["opzioni"]);
			if ($specifiche_criterio["script"]!="") $array_script = explode(",",$specifiche_criterio["script"]);
		}

		$bind = array();
		$bind[":procedura"] = $record_gara["procedura"];
		$strsql = "SELECT * FROM b_procedure WHERE mercato_elettronico = 'S' AND directory = 'rdo' AND codice = :procedura";
		$risultato_me = $pdo->bindAndExec($strsql,$bind);
		$mercato_elettronico = false;
		if ($risultato_me->rowCount()>0) $mercato_elettronico = true;
		$strsql = "SELECT * FROM b_procedure WHERE directory = 'sda' AND codice = :procedura";
		$risultato_sda = $pdo->bindAndExec($strsql,$bind);
		$sda = false;
		if ($risultato_sda->rowCount()>0) $sda = true;

		$strsql = "SELECT * FROM b_procedure WHERE directory = 'dialogo' AND codice = :procedura";
		$risultato_dialogo = $pdo->bindAndExec($strsql,$bind);
		$dialogo = false;
		if ($risultato_dialogo->rowCount()>0) $dialogo = true;

		$strsql = "SELECT * FROM b_procedure WHERE derivata > 0 AND codice = :procedura";
		$risultato_derivata = $pdo->bindAndExec($strsql,$bind);
		$derivata = false;
		if ($risultato_derivata->rowCount()>0) $derivata = true;
		if ($risultato_modelli->rowCount()>0) {
			while($modello = $risultato_modelli->fetch(PDO::FETCH_ASSOC)) {

				$bind = array();
				$bind[":codice_modello"] = $modello["codice"];
				$bind[":modalita"] = $record_gara["modalita"];
				// $bind[":tipologia"] = $record_gara["tipologia"];
				//$bind[":criterio"] = $record_gara["criterio"];
				$bind[":totale_gara"] = $totale_gara;

				$strsql = "SELECT * FROM b_paragrafi_new WHERE attivo = 'S' AND eliminato = 'N' AND codice_modello = :codice_modello AND codice_opzione <> '' ";
				$strsql .= " AND (modalita = :modalita OR modalita = 0)";
				$strsql .= " AND (criteri REGEXP '[[:<:]]{$record_gara['criterio']}[[:>:]]' OR criteri = '' OR criteri IS NULL)";
				$strsql .= " AND (tipologie REGEXP '[[:<:]]{$record_gara['tipologia']}[[:>:]]' OR tipologie = '' OR tipologie IS NULL)";
				$strsql .= " AND (importo_massimo >= :totale_gara OR importo_massimo = 0)";
				$strsql .= " AND (importo_minimo <= :totale_gara OR importo_minimo = 0)";
				$risultato_opzioni = $pdo->bindAndExec($strsql,$bind);
				// echo $pdo->getSQL();
				if ($risultato_opzioni->rowCount()>0) {
					while($paragrafo = $risultato_opzioni->fetch(PDO::FETCH_ASSOC)) {
						$opzioni = explode(",",$paragrafo["codice_opzione"]);
						$array_opzioni = array_merge($array_opzioni,$opzioni);
					}
				}

				$strsql = "SELECT * FROM b_paragrafi_new WHERE attivo = 'S' AND eliminato = 'N' AND codice_modello = :codice_modello AND tipo = 'avanzato' ";
				$strsql .= " AND (modalita = :modalita OR modalita = 0)";
				$strsql .= " AND (criteri REGEXP '[[:<:]]:criterio[[:>:]]' OR criteri = '' OR criteri IS NULL)";
				$strsql .= " AND (tipologie = criteri REGEXP '[[:<:]]:tipologia[[:>:]]' OR tipologie = '' OR tipologie IS NULL)";
				$strsql .= " AND (importo_massimo >= :totale_gara OR importo_massimo = 0)";
				$strsql .= " AND (importo_minimo <= :totale_gara OR importo_minimo = 0)";
				$risultato_script = $pdo->bindAndExec($strsql,$bind);
				if ($risultato_script->rowCount()>0) {
					while($paragrafo = $risultato_script->fetch(PDO::FETCH_ASSOC)) {
						$array_script[] = $paragrafo["directory"];
					}
				}
			}
		}
		if ($dialogo) $array_script[] = "dialogo";
		$array_opzioni = array_unique($array_opzioni);
		$array_opzioni = array_filter($array_opzioni);
		sort($array_opzioni);
		if ((count($gruppi_opzioni) > 0) || (count($array_opzioni)>0)) {
			$strsql = "SELECT * FROM b_gruppi_opzioni WHERE attivo = 'S' AND eliminato = 'N' AND (";
			if (count($array_opzioni)>0) {
				$under_flag = true;
				$strsql .= " (codice IN (SELECT codice_gruppo FROM b_opzioni WHERE attivo = 'S' AND eliminato = 'N' AND codice IN (" . implode(",",$array_opzioni) . ")))";
			}
			if (count($gruppi_opzioni)>0) {
				if (isset($under_flag)) $strsql .= " OR ";
				$strsql .= " (codice IN (" . implode(",",$gruppi_opzioni) . "))";
			}
			$strsql .= ")";
			$risultato_opzioni = $pdo->query($strsql);
		}

		$array_script = array_unique($array_script);
		$array_script = array_filter($array_script);
		sort($array_script);
		if (count($array_script)>0) {
			foreach($array_script as $script) {
				$continua = true;
				if ($script == "criteri_offerta_tecnica" && $record_gara["nuovaOfferta"] == "S") $continua = false;
				if ($continua) {
					if (file_exists($root."/gare/elaborazione/moduli_avanzati/".$script."/form.php"))
						include($root."/gare/elaborazione/moduli_avanzati/".$script."/form.php");
				}
			}
		}

			if (isset($risultato_opzioni) && $risultato_opzioni->rowCount()>0) {
				if ($specifiche_criterio["opzioni"]!="") $obbligatorio = explode(",",$specifiche_criterio["opzioni"]);
				while($gruppo = $risultato_opzioni->fetch(PDO::FETCH_ASSOC)) {
					if ($gruppo["codice"] != "41" || strtotime($record_gara["timestamp_creazione"]) < strtotime('2019-04-19')) {
						$valida = "";
						$rel = "";
						if (!empty($_SESSION["record_utente"]["codice_ente"]) && ($_SESSION["ente"]["codice"] != $_SESSION["record_utente"]["codice_ente"]) && $gruppo["tipo"]=='radio') $valida = "class=\"valida\" rel=\"S;0;0;checked;group_validate\"";
						if (in_array($gruppo["codice"],$obbligatorio,true)) $valida = "class=\"valida\" rel=\"S;0;0;checked;group_validate\"";

					?>
					<table title="<? echo $gruppo["titolo"] ?>" width="100%" <? echo $valida ?>>
						<tr><td class="etichetta" colspan="2" style="background-color: #CCC; text-align:left;"><? echo "<strong>" . $gruppo["titolo"] . "</strong>" ?>
							<?
							if ($gruppo["suggerimenti"]!="") { ?>
								<div id="suggerimento_<? echo $gruppo["codice"] ?>" style="display:none">
									<? echo $gruppo["suggerimenti"] ?>
								</div>
								<input type="image" src="/img/info.png" style="vertical-align:middle; cursor:pointer;" onClick="$('#suggerimento_<? echo $gruppo["codice"] ?>').dialog({title:'<?= str_replace("'","\'",$gruppo["titolo"]) ?>',modal:'true'}); return false;">
								<? } ?>
							</td></tr>
							<?
							$bind = array();
							$bind[":codice_gruppo"] = $gruppo["codice"];
							$str_opzioni = "SELECT * FROM b_opzioni WHERE attivo = 'S' AND eliminato = 'N' AND codice_gruppo = :codice_gruppo";
							$risultato_voci = $pdo->bindAndExec($str_opzioni,$bind);
							if ($risultato_voci->rowCount()>0) {
							while($opzione = $risultato_voci->fetch(PDO::FETCH_ASSOC)) {
								$checked = "";
								$bind = array();
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":opzione"] = $opzione["codice"];
								$strsql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara";
								$strsql .= " AND opzione = :opzione";
								$ris_opzione = $pdo->bindAndExec($strsql,$bind);
								if ($ris_opzione->rowCount()>0) $checked = "checked";
								$id_opzione = "gruppo_" . $gruppo["codice"] . "_" . $opzione["codice"];
								$name = "gruppo[" . $gruppo["codice"] . "]";
								if ($gruppo["tipo"] == "checkbox") $name .= "[" . $opzione["codice"] . "]";

								?>
								<tr>
									<td><? echo $opzione["titolo"] ?></td>
									<td width="10"><input type="<? echo $gruppo["tipo"] ?>" title="<? echo $opzione["titolo"] ?>" <? echo $checked ?> name="<? echo $name ?>" value="<? echo $opzione["codice"] ?>" id="<? echo $id_opzione ?>"></td>
								</tr>
								<?
								}
							}
						?>
						</table>
						<?
					}
				}
			} else {
				echo '<h2>Non sono necessarie ulteriori scelte</h2>';
			}
		} else {
			echo '<h2>Completare i dati preliminari</h2>';
		}
	}
?>
