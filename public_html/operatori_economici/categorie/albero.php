<?
	if (isset($_GET["codice"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
		$lista = preg_replace("/[^A-Za-z0-9 ]/", '', $_GET["lista"]);
		$bind = array(":codice" => $_GET["codice"]."%", ":lunghezza" => strlen($_GET["codice"]) + 1);
		if ($_SESSION["language"] == "IT") {
			$sql_categorie = "SELECT b_cpv.*
												FROM b_cpv
												WHERE LENGTH(codice)= :lunghezza
												AND codice like :codice ORDER BY codice";
		} else {
			$sql_categorie = "SELECT b_cpv.*, b_cpv_dict.{$_SESSION["language"]} AS descrizione
												FROM b_cpv JOIN b_cpv_dict ON b_cpv.codice_completo = b_cpv_dict.codice_completo
												WHERE LENGTH(b_cpv.codice)= :lunghezza
												AND b_cpv.codice like :codice ORDER BY b_cpv.codice";
		}
		$ris_categorie = $pdo->bindAndExec($sql_categorie,$bind);
		if ($ris_categorie->rowCount()>0) {
			while($rec_categorie=$ris_categorie->fetch(PDO::FETCH_ASSOC)) {
				$albero = true;
				include("categoria.php");
			}
		}
	}
?>
