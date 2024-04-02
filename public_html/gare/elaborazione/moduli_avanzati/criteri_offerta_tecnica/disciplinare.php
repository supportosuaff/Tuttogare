<?
	$bind = array();
	$bind[":codice"] = $record_gara["codice"];
	$strsql = "SELECT * FROM b_valutazione_tecnica WHERE codice_padre = 0 AND codice_gara = :codice";
	$ris_valutazione = $pdo->bindAndExec($strsql,$bind);
	if ($ris_valutazione->rowCount()>0) {
		$html .= "<table style=\"width:100%\">";
		$bind = array();
		$bind[":codice_gara"] = $record_gara["codice"];
		$strsql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara";
		$ris_lotti = $pdo->bindAndExec($strsql,$bind);
		if ($ris_lotti->rowCount()>0) {
			$num_lotto = 0;
			$rif_lotto = [];
			while($tmp = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
				$num_lotto++;
				$rif_lotto[$tmp["codice"]] = $num_lotto;
			}
		}
		while($criterio_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC)) {
			$padre = true;
			$sub = false;
			include("moduli_avanzati/criteri_offerta_tecnica/record_bando.php");
		}
		$colspan = (!empty($rif_lotto)) ? 3 : 2;
		$html .= "<tr><td colspan=\"{$colspan}\" style=\"text-align:right\"><strong>Totale</strong></td><td><strong>100</strong></td></tr>";
		$html .= "</table>";
	}
?>
