<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_GET["term"])) {
		$text = $_GET["term"];
		$condizione = suddivisione_pdo($text,"b_comuni.descr");
		$bind = $condizione["bind"];
		$sql = $condizione["sql"];
		$strsql = "SELECT b_comuni.* FROM b_comuni WHERE " . $sql . " ORDER BY b_comuni.descr ASC";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		$rec = array();
		if ($risultato->rowCount()>0) {
			while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				$rec[] = array(
					"value" => ucwords(strtolower(html_entity_decode($record["descr"], ENT_QUOTES, 'UTF-8'))),
					"label" => "({$record["prov"]})",
					"provincia_stipula" => strtoupper(html_entity_decode($record["prov"], ENT_QUOTES, 'UTF-8')),
					"regione_stipula" => ucwords(strtolower(html_entity_decode($record["regione"], ENT_QUOTES, 'UTF-8'))),
					"codice_comune_stipula" => $record["cf"],
				);
			}
		}
		echo json_encode($rec);
	}
?>
