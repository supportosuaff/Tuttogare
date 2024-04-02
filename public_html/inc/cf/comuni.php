<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

		$bind = array(":provincia" => $_POST["provincia"]);

		$sql = "SELECT descr FROM b_comuni WHERE prov = :provincia ORDER BY descr ASC";

		$ris = $pdo->bindAndExec($sql,$bind);
		$html = "<option value=\"\">Seleziona...</option>";
		if ($ris->rowCount()>0) {
			while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
				$html .= "<option>" . $rec["descr"] . "</option>";
			}
		}
		echo $html;
?>
