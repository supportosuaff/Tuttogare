<?
	if (isset($_GET["codice"])) {
		session_start();
		include("../../../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$lista = $_GET["lista"];
		$bind = array(":codice" => $_GET["codice"]."%", ":lunghezza" => strlen($_GET["codice"]) + 1);
		$sql_categorie = "SELECT * FROM b_cpv WHERE LENGTH(codice)= :lunghezza AND codice like :codice ORDER BY codice";
		$ris_categorie = $pdo->bindAndExec($sql_categorie,$bind);
		if ($ris_categorie->rowCount()>0) {
			while($rec_categorie=$ris_categorie->fetch(PDO::FETCH_ASSOC)) {
				$albero = true;
				include("categoria.php");
			}
		}
	}
?>
