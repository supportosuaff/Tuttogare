<?
	$bind = array();
	$bind[":codice_gara"] = $record_gara["codice"];
	$sql_classificazioni  = "SELECT b_qualificazione_lavori.tipo, b_categorie_soa.id AS categoria, b_categorie_soa.descrizione, b_classifiche_soa.id AS classifica FROM b_qualificazione_lavori ";
	$sql_classificazioni .= "JOIN b_categorie_soa ON b_qualificazione_lavori.codice_categoria = b_categorie_soa.codice ";
	$sql_classificazioni .= "JOIN b_classifiche_soa ON (b_classifiche_soa.minimo <= b_qualificazione_lavori.importo_base AND b_classifiche_soa.massimo >= b_qualificazione_lavori.importo_base) ";
	$sql_classificazioni .= "WHERE b_qualificazione_lavori.codice_gara = :codice_gara ";
	$ris_classificazioni = $pdo->bindAndExec($sql_classificazioni,$bind);
	if ($ris_classificazioni->rowCount()>0) {
		$html.= "Dichiarazioni sostitutive ai sensi del d.P.R. n. 445 del 2000, in conformit&agrave; al disciplinare di gara, indicanti quanto segue:<br>";
		$html.= "Di possedere l'attestazione SOA nelle seguenti categorie <ul>";
		while ($rec_qualificazione = $ris_classificazioni->fetch(PDO::FETCH_ASSOC)) {
			$tipo = "Scorporabile";
			if ($rec_qualificazione["tipo"] == "P") $tipo = "Prevalente";
			$html.= "<li>" . $tipo . " <strong>" . $rec_qualificazione["categoria"] . "</strong> / Classifica <strong>" . $rec_qualificazione["classifica"] . "</strong> - " . $rec_qualificazione["descrizione"] . "</li>";
		}
		$html.= "</ul>";
	}
?>
