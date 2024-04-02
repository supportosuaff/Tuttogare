<?
	$bind = array();
	$bind[":codice_gara"] = $record_gara["codice"];
	$sql_classificazioni  = "SELECT b_categorie_progettazione.id as id, b_categorie_progettazione.corrispondenze_143 as classe, b_categorie_progettazione.complessita as complessita, b_qualificazione_progettazione.importo as importo FROM b_qualificazione_progettazione ";
	$sql_classificazioni .= "JOIN b_categorie_progettazione ON b_categorie_progettazione.codice = b_qualificazione_progettazione.codice_categoria ";
	$sql_classificazioni .= "WHERE b_qualificazione_progettazione.codice_gara = :codice_gara";
	$ris_classificazioni = $pdo->bindAndExec($sql_classificazioni,$bind);
	if ($ris_classificazioni->rowCount()>0) {
		$html.= "d.4) ferma restando l’applicazione dell’articolo 8 del d.m. n. 143 del 2013, i lavori sono così identificati nella tavola Z-1 del predetto d.m.:<br>";
		$html.= "<ul>";
		while ($rec_qualificazione = $ris_classificazioni->fetch(PDO::FETCH_ASSOC)) {
			$html.= "<li> ID <strong> " . $rec_qualificazione["id"] . "</strong>, (classe/categoria <strong>" . $rec_qualificazione["classe"] . "</strong>), \"G\" <strong>" . number_format($rec_qualificazione["complessita"],2,",",".") . "</strong>, Importo <strong>&euro; " . number_format($rec_qualificazione["importo"],2,",",".") . "</strong></li>";
		}
		$html.= "</ul>";
	}
?>
