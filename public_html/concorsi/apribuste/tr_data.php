<?
	if (isset($_GET["codice"]) && !isset($record_data)) {
		@session_start();
		include_once("../../../config.php");
		include_once($root."/inc/funzioni.php");
		$bind=array();
		$bind[":codice"] = $_GET["codice"];
		$sql = "SELECT b_date_apertura_concorsi.*, b_fasi_concorsi_buste.nome FROM b_date_apertura_concorsi JOIN b_fasi_concorsi_buste ON b_date_apertura_concorsi.codice_busta = b_fasi_concorsi_buste.codice ";
		$sql .= "WHERE b_date_apertura_concorsi.codice = :codice ORDER BY codice";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount()>0) $record_data = $ris->fetch(PDO::FETCH_ASSOC);
	}
?>
<tr><td><strong><? echo $record_data["nome"] ?></strong></td><td width="25%"><? echo mysql2completedate($record_data["data_apertura"]); ?></td></tr>
