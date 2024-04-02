<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");


	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["codice"]) && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
			$codice = $_POST["codice"];
			if (is_numeric($codice)) {
				$bind = array();
				$bind[":codice"] = $codice;
			$strsql = "SELECT attivo FROM b_categorie_progettazione WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$attivo = "S";
			$colore = "#3C0";
			if ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				$attivo = $record["attivo"];
				if ($attivo == "S") {
					$attivo = "N";
					$colore = "#C00";
				} else {
					$attivo = "S";
					$colore = "#3C0";
				}
				$bind[":attivo"] = $attivo;
			$strsql = "UPDATE b_categorie_progettazione  SET attivo = :attivo WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_categorie_progettazione ","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			}
		}
			?>
			if ($("#categoria_<? echo $codice ?>").length > 0){
				$("#categoria_<? echo $codice ?>").slideUp().remove();
			}
<?
	}
}
?>
