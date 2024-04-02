<?
$bind = array();
$bind[":codice_gara"] = $record_gara["codice"];
$strsql = "SELECT b_categorie_soa.*, b_qualificazione_lavori.tipo, SUM(b_qualificazione_lavori.importo_base) AS importo_base
					FROM b_qualificazione_lavori JOIN b_categorie_soa ON b_qualificazione_lavori.codice_categoria = b_categorie_soa.codice
					WHERE codice_gara = :codice_gara GROUP BY codice_gara, tipo, id ORDER BY b_qualificazione_lavori.tipo ";
$ris_qualificazione_gara = $pdo->bindAndExec($strsql,$bind);
$html_og11 = "";
$row = "";
$og11 = array();
if ($ris_qualificazione_gara->rowCount() > 1) {
	while($categoria = $ris_qualificazione_gara->fetch(PDO::FETCH_ASSOC)) {
		if ($categoria["tipo"] != "P") {
			if (($categoria["id"] == "OS 3") || ($categoria["id"] == "OS 28") || ($categoria["id"] == "OS 30")) {
				$og11[$categoria["id"]] = $categoria["importo_base"];
			}
		}
	}

	if (count($og11)==3) {
		$flag = 0;
		foreach($og11 AS $key=>$importo) {
			switch($key) {
				case "OS 3":
				if ((($importo*100)/$record_gara["prezzoBase"]) >= 10) $flag++;
				$row .="<tr><td><strong>OS 3</strong></td><td>". number_format($importo,2,",","."). "</td><td>".number_format((($importo*100)/$record_gara["prezzoBase"]),2,",",".")."%</td></tr>";
				break;
				case "OS 28":
				if ((($importo*100)/$record_gara["prezzoBase"]) >= 25) $flag++;
				$row .="<tr><td><strong>OS 28</strong></td><td>". number_format($importo,2,",","."). "</td><td>".number_format((($importo*100)/$record_gara["prezzoBase"]),2,",",".")."%</td></tr>";
				break;
				case "OS 30":
				if ((($importo*100)/$record_gara["prezzoBase"]) >= 25) $flag++;
				$row .="<tr><td><strong>OS 30</strong></td><td>". number_format($importo,2,",","."). "</td><td>".number_format((($importo*100)/$record_gara["prezzoBase"]),2,",",".")."%</td></tr>";
				break;
			}
		}
		if ($flag == 3) {
			$html_og11 .= "c) ai fini dell&#39;articolo 79, comma 16, terzo periodo, del d.P.R. n. 207 del 2010, ricorrono le condizioni di cui al quarto periodo della stessa norma, per cui la categoria OG11 &egrave; stata individuata in alternativa alle categorie OS3, OS28 e OS30, come segue:<br/>";
			$html_og11 .= "<table width=\"100%\"><thead><tr><th>Categoria</th><th>Importo</th><th>Indicenza sul totale degli impianti</th></tr></thead><tbody>";
			$html_og11 .= $row;
			$html_og11 .= "</tbody><tfoot><tr><td>Totale (OG 11)</td><td></td><td>100,00%</td><td></td></tfoot></table>";
		}else{
			$html_og11 .= "c) fermo restando che la qualificazione nelle categorie scorporabili OS3, OS28 e OS30 &egrave; surrogabile dalla qualificazione nella categoria OG11, ai fini dell&#39;articolo 79, comma 16, terzo periodo, del d.P.R. n. 207 del 2010, non ricorrono le condizioni di cui al quarto periodo della stessa norma, per cui sono state individuate le categorie specializzate OS3, OS28 e OS30 in alternativa alla categoria OG11, come segue:<br/>";
			$html_og11 .= "<table width=\"100%\"><thead><tr><th>Categoria</th><th>Importo</th><th>Indicenza sul totale degli impianti</th></tr></thead><tbody>";
			$html_og11 .= $row;
			$html_og11 .= "</tbody></table>";
		}
	}
}
$html .= $html_og11;
?>
