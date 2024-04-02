<?
    	$html .= "<tr>";
			if (!$sub) {
        $bind = array();
        $bind[":codice_padre"] = $criterio_valutazione["codice"];
        $bind[":codice_gara"] = $_SESSION["gara"]["codice"];
				$strsql = "SELECT * FROM b_valutazione_tecnica WHERE codice_padre = :codice_padre AND codice_gara = :codice_gara";
				$ris_sub_valutazioni = $pdo->bindAndExec($strsql,$bind);
			}
      if (!$sub && isset($ris_sub_valutazioni) && $ris_sub_valutazioni->rowCount()>0) {
				$html .= "<td width=\"10\"><strong>";
					$punteggio_totale = $criterio_valutazione["punteggio"];
					if ($criterio_valutazione["tipo"] == "Q") { $html .= "Qualitativo"; } else { $html .= "Quantitativo"; }
        $html .= "</strong></td>";
				$html .= "<td colspan=\"2\"><strong>" . $criterio_valutazione["descrizione"] . "</strong></td>";
				while($criterio_valutazione = $ris_sub_valutazioni->fetch(PDO::FETCH_ASSOC)) {
					$padre = false;
					$sub = true;
					include("moduli_avanzati/criteri_offerta_tecnica/record_bando.php");
				}
				$html.= "</tr><tr><td colspan=\"2\" style=\"text-align:right\"></td><td><strong>" . $punteggio_totale . "</strong></td>";
			} else {
				if ($padre) {
					$html .= "<td width=\"10\"><strong>";
					$punteggio_totale = $criterio_valutazione["punteggio"];
					if ($criterio_valutazione["tipo"] == "Q") { $html .= "Qualitativo"; } else { $html .= "Quantitativo"; }
    	            $html .= "<strong></td>";
				} else {
					$html.="<td></td>";
				}
				$html .= "<td>"; 
				if ($padre) { $html.= "<strong>"; } 
				$html.= $criterio_valutazione["descrizione"]; 
				if ($padre) { $html.= "</strong>"; } 
				$html.= "</td>";
				if (isset($rif_lotto)) {
					$html.= "<td style='text-align:center'>";
						if ($padre)	 {
							if (!empty($criterio_valutazione["codice_lotto"])) {
								$html .= "<strong>Lotto #{$rif_lotto[$criterio_valutazione["codice_lotto"]]}</strong>";
							} else {
								$html .= "<strong>Tutti i lotti</strong>";
							}
						}
					$html.= "</td>";
				}
				
				$html .= "<td width=\"50\">"; 
				if ($padre) { $html.= "<strong>"; } $html.= $criterio_valutazione["punteggio"]; if ($padre) { $html.= "</strong>"; } $html.= "</td>";
		}
 $html .= "</tr>";
 ?>
