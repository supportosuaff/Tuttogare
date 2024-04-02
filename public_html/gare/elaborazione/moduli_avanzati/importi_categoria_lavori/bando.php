<?
  $bind = array();
  $bind[":codice_gara"] = $record_gara["codice"];
  $strsql = "SELECT b_categorie_soa.*, b_qualificazione_lavori.tipo, SUM(b_qualificazione_lavori.importo_base) AS importo_base
 	FROM b_qualificazione_lavori JOIN b_categorie_soa ON b_qualificazione_lavori.codice_categoria = b_categorie_soa.codice
	WHERE codice_gara = :codice_gara GROUP BY codice_gara, tipo, id ORDER BY b_qualificazione_lavori.tipo ";
	$ris_qualificazione_gara = $pdo->bindAndExec($strsql,$bind);
  if ($ris_qualificazione_gara->rowCount() > 0) {
	   $scorporabili = array();
	    while($categoria = $ris_qualificazione_gara->fetch(PDO::FETCH_ASSOC)) {
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
      				if ($categoria["sios"] == "S") {
      					if ((($categoria["importo_base"]*100)/$record_gara["prezzoBase"]) > 15) {
      						$flag_sios = true;
      						$scorporabili[] = $categoria;
      					}
      				}
      				if ($categoria["obbligo_qualificazione"] == "S" && !$flag_sios) {
      					if (((($categoria["importo_base"]*100)/$record_gara["prezzoBase"]) > 10) || $categoria["importo_base"] > 150000) {
      						$scorporabili[] = $categoria;
      					}
      				}
      			}
      		}
      		if (count($scorporabili)>0) {
      			$importo_scorporabili = 0;
      			$html .= "<table style=\"width:100%\">";
      				$html .= "<tr><td colspan=\"3\">Lavorazioni per le quali è richiesta una specifica qualificazione</td></tr><tr>";
      					foreach ($scorporabili AS $categoria) {
      						$html .= "<td style=\"width:50%\">" . $categoria["descrizione"] . "</td>
      						<td style=\"width:10%\"><strong>" . $categoria["id"] . "</strong></td>
      						<td style=\"width:20%\">" . number_format($categoria["importo_base"],2,",",".") . "</td>
      					</tr>";
      					$importo_scorporabili += $categoria["importo_base"];
      				}
      				$html.= "<td>Valore stimato:</td><td>" . number_format($importo_scorporabili,2,",",".") . "</td><td>Valuta:</td><td>Euro</td></tr>";
      				$html .= "</table>";
      		}
  }
?>
