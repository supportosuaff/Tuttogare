<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_GET["term"])) {
			$text = $_GET["term"];
			$bind = array();
			$condizione  = "( ";
			$return = suddivisione_pdo($text,"b_cpv.codice");
			if (is_array($return)) {
				$condizione .= $return["sql"] . " OR ";
				$bind = array_merge($bind,$return["bind"]);
			}
			$return = suddivisione_pdo($text,"b_cpv.descrizione");
			if (is_array($return)) {
				$condizione .= $return["sql"];
				$bind = array_merge($bind,$return["bind"]);
			}
			$condizione .= ") ";

			$strsql = "SELECT * FROM b_cpv ";
			$strsql.= " WHERE " . $condizione ." ORDER BY codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$rec = array();
			if ($risultato->rowCount()>0) {
				while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
					$rec[] = array("value"=>$record["codice"],"label"=>$record["descrizione"]);
				}
			}
			echo json_encode($rec);
	}
?>
