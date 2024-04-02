<?
			if (!empty($record_gara["messaggio_anomalia"])) {
				$html.= "Soglia di anomalia calcolata ai sensi del D.Lgs 50/2016 art. 97 c. 2 " . substr($record_gara["messaggio_anomalia"],0,-2);
			}
			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 59";
			$ris_clausola = $pdo->bindAndExec($sql,$bind);
			if ($ris_clausola->rowCount()>0 && $numero_partecipanti >= 10) {
					$html.= "<br><br>&Egrave; stato effettuata l'esclusione automatica delle offerte anomale, individuate
					cos&igrave; come indicato dall' art. 97 c.8 del D.Lgs 50/2016";
			}
?>
