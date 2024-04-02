<?
	$bind = array();
	$bind[":codice_gara"] = $record_gara["codice"];
	$strsql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara";
	$ris_lotti = $pdo->bindAndExec($strsql,$bind);
	if ($ris_lotti->rowCount()>0) {
		$n_lotto=0;
		$html .= "Le offerte vanno presentate per: ";
		switch ($record_gara["modalita_lotti"]) {
			case '1':
				$html .= "<strong>Uno solo lotto</strong><br>";
			break;
			case '2':
				$html .= "<strong>Tutti i lotti</strong><br>";
			break;
			default:
				$html .= "<strong>Uno o pi&ugrave; lotti</strong><br>";
			break;
		}

		/* $offerta = "netto";
		$sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 15";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount()>0) $offerta = "piena"; */

		while ($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
			$n_lotto++;
			$html .= "<br><h4>Lotto n. " . $n_lotto . ": " . $lotto["oggetto"] . "</h4>";
			$html .= "<table width=\"100%\">";
			$html .= "<tr><td><strong>Breve Descrizione:</strong></td></tr><tr><td>" . $lotto["descrizione"] . "</td></tr>";
			$html .= "<tr><td><strong>Vocabolario comune per gli appalti (CPV)</strong></td></tr>";
			$bind = array();
			$bind[":cpv"] = $lotto["cpv"];
			$sql = "SELECT b_cpv.* FROM b_cpv WHERE b_cpv.codice = :cpv";
			$ris_categorie = $pdo->bindAndExec($sql,$bind);
			if ($ris_categorie->rowCount()>0) {
				while ($categoria_cpv = $ris_categorie->fetch(PDO::FETCH_ASSOC)) {
					$html.= "<tr><td><strong>" . str_pad($categoria_cpv["codice"],9,"0") . "</strong> - " . $categoria_cpv["descrizione"] . "</td></tr>";
				}
			}
			$html .= "</table>";
			$html .= "<table style=\"width:100%\">";
			/* if ($offerta == "netto") {
				$totale_lotto = $lotto["importo_base"] + $lotto["importo_oneri_ribasso"] + $lotto["importo_oneri_no_ribasso"] + $lotto["importo_personale"];
				$html.= "<tr><td style=\"width:5%\">1)</td><td style=\"text-align:right\" style=\"width:25%\"><strong>&euro; " . number_format($totale_lotto,2,",",".") . "</strong></td><td style=\"width:70%\"> base d'asta, di cui</td></tr>";
				$html.= "<tr><td style=\"width:5%\">1.a)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($lotto["importo_personale"],2,",",".") . "</td><td style=\"width:70%\">Costo del personale, non soggetto a ribasso;</td></tr>";
				$html.= "<tr><td style=\"width:5%\">1.b)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($lotto["importo_oneri_no_ribasso"],2,",",".") . "</td><td style=\"width:70%\">Costi di sicurezza aziendale, non soggetto a ribasso;</td></tr>";
				$html.= "<tr><td style=\"width:5%\">1.c)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($lotto["importo_oneri_ribasso"],2,",",".") . "</td><td style=\"width:70%\">Costi di sicurezza aziendale, soggetto a ribasso;</td></tr>";
				$html.= "<tr><td style=\"width:5%\">1.d)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($lotto["importo_base"],2,",",".") . "</td><td style=\"width:70%\">Importo netto a base d'asta soggetto a ribasso;</td></tr>";
			} else { */
				$totale_lotto = $lotto["importo_base"] + $lotto["importo_oneri_no_ribasso"]; // + $lotto["importo_personale"];
				$html.= "<tr><td style=\"width:5%\">1)</td><td style=\"text-align:right\" style=\"width:25%\"><strong>&euro; " . number_format($totale_lotto,2,",",".") . "</strong></td><td style=\"width:70%\"> base d'asta, di cui</td></tr>";
				$html.= "<tr><td style=\"width:5%\">1.a)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($lotto["importo_oneri_no_ribasso"],2,",",".") . "</td><td style=\"width:70%\">Costi di sicurezza aziendale, non soggetto a ribasso;</td></tr>";
				$html.= "<tr><td style=\"width:5%\">1.b)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($lotto["importo_personale"],2,",",".") . "</td><td style=\"width:70%\">Costo della manodopera, non soggetto a ribasso;</td></tr>";
				$html.= "<tr><td style=\"width:5%\">1.c)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($lotto["importo_base"],2,",",".") . "</td><td style=\"width:70%\">Importo netto a base d'asta soggetto a ribasso;</td></tr>";

			// }
			$html.= "</table>";

			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$bind[":codice_lotto"] = $lotto["codice"];
			$strsql = "SELECT b_categorie_soa.*, b_qualificazione_lavori.tipo, b_qualificazione_lavori.importo_base FROM b_qualificazione_lavori JOIN b_categorie_soa ON b_qualificazione_lavori.codice_categoria = b_categorie_soa.codice
			WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto ORDER BY b_qualificazione_lavori.tipo ";
			$ris_qualificazione_lotto = $pdo->bindAndExec($strsql,$bind);
			if ($ris_qualificazione_lotto->rowCount() > 0 && $record_gara["modalita_lotti"] < 2) {
					$sios = array();
					$qualificazione_obbligatoria = array();
					$scorporabili_semplici = array();
					while($categoria = $ris_qualificazione_lotto->fetch(PDO::FETCH_ASSOC)) {
						$categoria["classifica"] = "";
						$bind = array();
	          $bind[":importo_base"] = $categoria["importo_base"];
			      $sql_classifica = "SELECT * FROM b_classifiche_soa WHERE attivo = 'S' AND minimo <= :importo_base AND (massimo >= :importo_base OR massimo = 0)";
						$ris_classifica = $pdo->bindAndExec($sql_classifica,$bind);
						if ($ris_classifica->rowCount() > 0) {
							$classifica = $ris_classifica->fetch(PDO::FETCH_ASSOC);
							$categoria["classifica"] = $classifica["id"];
						}
						if ($categoria["tipo"] == "P") {
							$html .= "<table style=\"width:100%\">";
							$html .= "<tr><td style=\"width:20%\"><strong>Categoria Prevalente</strong></td>
												<td style=\"width:50%\">" . $categoria["descrizione"] . "</td>
												<td style=\"width:10%\"><strong>" . $categoria["id"] . "</strong></td>
												<td style=\"width:10%\">classifica:</td>
												<td style=\"width:10%\">" . $categoria["classifica"] . "</td>
							</tr>";
							$html .= "</table>";
						} else {
							$flag_sios = false;
							$flag = false;
							if ($categoria["sios"] == "S") {
								if ((($categoria["importo_base"]*100)/$lotto["importo_base"]) > 15) {
									$flag_sios = true;
									$flag = true;
									$sios[] = $categoria;
								}
							}
							if ($categoria["obbligo_qualificazione"] == "S" && !$flag_sios) {
								if (((($categoria["importo_base"]*100)/$lotto["importo_base"]) > 10) || $categoria["importo_base"] > 150000) {
									$flag = true;
									$qualificazione_obbligatoria[] = $categoria;
								}
							}
							if ($categoria["obbligo_qualificazione"] == "N" || !$flag) {
								$scorporabili_semplici[] = $categoria;
							}
						}
					}
					if (count($sios)>0) {
						$html .= "<table style=\"width:100%\">";
						$html .= "<tr><td colspan=\"5\">a) Categorie scorporabili parzialmente  subappaltabili art. 37, comma 11, d.lgs. n. 163 del 2006</td></tr><tr>";
						foreach ($sios AS $categoria) {
							$html .= "<td style=\"width:50%\">" . $categoria["descrizione"] . "</td>
							<td style=\"width:10%\"><strong>" . $categoria["id"] . "</strong></td>
							<td style=\"width:10%\">classifica:</td>
							<td style=\"width:10%\">" . $categoria["classifica"] . "</td>
							<td style=\"width:20%\">" . number_format($categoria["importo_base"],2,",",".") . "</td>
							</tr>";
						}
						$html .= "</table>";
					}
					if (count($qualificazione_obbligatoria)>0) {
						$html .= "<table style=\"width:100%\">";
						$html .= "<tr><td colspan=\"5\">b) Categorie scorporabili o totalmente subappaltabili a qualificazione obbligatoria</td></tr><tr>";
						foreach ($qualificazione_obbligatoria AS $categoria) {
							$html .= "<td style=\"width:50%\">" . $categoria["descrizione"] . "</td>
							<td style=\"width:10%\"><strong>" . $categoria["id"] . "</strong></td>
							<td style=\"width:10%\">classifica:</td>
							<td style=\"width:10%\">" . $categoria["classifica"] . "</td>
							<td style=\"width:20%\">" . number_format($categoria["importo_base"],2,",",".") . "</td>
							</tr>";
						}
						$html .= "</table>";
					}
					if (count($scorporabili_semplici)>0) {
						$html .= "<table style=\"width:100%\">";
						$html .= "<tr><td colspan=\"5\">c) Categorie scorporabili o totalmente subappaltabili a qualificazione non obbligatoria</td></tr><tr>";
						foreach ($scorporabili_semplici AS $categoria) {
							$html .= "<td style=\"width:50%\">" . $categoria["descrizione"] . "</td>
							<td style=\"width:10%\"><strong>" . $categoria["id"] . "</strong></td>
							<td style=\"width:10%\">classifica:</td>
							<td style=\"width:10%\">" . $categoria["classifica"] . "</td>
							<td style=\"width:20%\">" . number_format($categoria["importo_base"],2,",",".") . "</td>
							</tr>";
						}
						$html .= "</table>";
					}
			}

			$html.= "<table width=\"100%\">";
			$html.= "<tr><td><strong>Indicazione di una durata diversa dell'appalto:</strong></td></tr>";
			$unita_lotto = "Giorni";
			if ($lotto["unita_durata"] == "mm") $unita_lotto = "Mesi";
			$html .= "<tr><td>" . $lotto["durata"] . " " . $unita_lotto . "</td></tr>";
			if ($lotto["ulteriori_informazioni"]!="") $html .= "<tr><td><strong>ulteriori_informazioni:</strong></td></tr><tr><td>" . $lotto["ulteriori_informazioni"] . "</td></tr>";
			$html.= "</table>";
		}
	} 
?>
