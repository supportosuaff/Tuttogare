<?
	if (count($ris_importi)>0) {
		// $offerta = "netto";
		/* $bind = array();
		$bind[":codice_gara"] = $record_gara["codice"];
		$sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 15";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount()>0) $offerta = "piena"; */
		$html.= "<table style=\"width:100%\">";
		$importi_i = 0;
		foreach($ris_importi AS $rec_importo) {
			$importi_i++;
			/* if ($offerta == "netto") {
				$totale_tipologia = $rec_importo["importo_base"] + $rec_importo["importo_oneri_no_ribasso"] + $rec_importo["importo_oneri_ribasso"] + $rec_importo["importo_personale"];
				$html.= "<tr><td style=\"width:5%\">" . $importi_i . ")</td><td style=\"text-align:right\" style=\"width:25%\"><strong>&euro; " . number_format($totale_tipologia,2,",",".") . "</strong></td><td style=\"width:70%\">" . $rec_importo["tipologia"] . ", di cui</td></tr>";
				$html.= "<tr><td style=\"width:5%\">" . $importi_i . ".a)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($rec_importo["importo_personale"],2,",",".") . "</td><td style=\"width:70%\">Costo del personale, non soggetto a ribasso;</td></tr>";
				$html.= "<tr><td style=\"width:5%\">" . $importi_i . ".b)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($rec_importo["importo_oneri_no_ribasso"],2,",",".") . "</td><td style=\"width:70%\">Costi di sicurezza aziendale, non soggetto a ribasso;</td></tr>";
				$html.= "<tr><td style=\"width:5%\">" . $importi_i . ".c)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($rec_importo["importo_base"],2,",",".") . "</td><td style=\"width:70%\">Importo netto " . $rec_importo["tipologia"] . " soggetto a ribasso;</td></tr>";
				$html.= "<tr><td style=\"width:5%\">" . $importi_i . ".d)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($rec_importo["importo_oneri_ribasso"],2,",",".") . "</td><td style=\"width:70%\">Oneri di sicurezza non soggetti a ribasso;</td></tr>";
			} else { */
				$totale_tipologia = $rec_importo["importo_base"] + $rec_importo["importo_oneri_no_ribasso"]; // + $rec_importo["importo_personale"];
				$html.= "<tr><td style=\"width:5%\">" . $importi_i . ")</td><td style=\"text-align:right\" style=\"width:25%\"><strong>&euro; " . number_format($totale_tipologia,2,",",".") . "</strong></td><td style=\"width:70%\">" . $rec_importo["tipologia"] . ", di cui</td></tr>";
				$html.= "<tr><td style=\"width:5%\">" . $importi_i . ".a)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($rec_importo["importo_base"],2,",",".") . "</td><td style=\"width:70%\">Importo netto " . $rec_importo["tipologia"] . " soggetto a ribasso;</td></tr>";
				$html.= "<tr><td style=\"width:5%\">" . $importi_i . ".b)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($rec_importo["importo_oneri_no_ribasso"],2,",",".") . "</td><td style=\"width:70%\">Oneri di sicurezza non soggetti a ribasso;</td></tr>";
				$html.= "<tr><td style=\"width:5%\">" . $importi_i . ".c)</td><td style=\"text-align:right\" style=\"width:25%\">&euro; " .  number_format($rec_importo["importo_personale"],2,",",".") . "</td><td style=\"width:70%\">Costo della manodopera soggetto a ribasso;</td></tr>";
			// }
		}
		$html.= "</table>";
	}
?>
