<?
	$bind = array();
	$bind[":codice"] = $record_gara["codice"];
	$sql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice";
	$ris_categorie = $pdo->bindAndExec($sql,$bind);
	if ($ris_categorie->rowcount()>0) {
		$html.= "<table width=\"100%\">";
		while ($categoria_cpv = $ris_categorie->fetch(PDO::FETCH_ASSOC)) {
			$html.= "<tr><td><strong>" . str_pad($categoria_cpv["codice"],9,"0") . "</strong></td><td>" . $categoria_cpv["descrizione"] . "</td></tr>";
		}
		$html.= "</table>";
	}
?>
