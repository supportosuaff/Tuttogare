<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_GET["term"])) {
			$text = $_GET["term"];
			$esclusioni = "";
			$bind = array();
			if (isset($_GET["esclusioni"]) && $_GET["esclusioni"] != "") {
				$esclusioni = " AND (";
				$selezionati = explode(";",$_GET["esclusioni"]);
				$i = 0;
					foreach($selezionati as $codice) {
						$i++;
						if ($codice != "") {
							$bind[":codice_".$i] = $codice;
							$bind[":no_codice_".$i] = $codice."%";
							$esclusioni .= "(codice <> :codice_" . $i . " AND codice NOT LIKE :no_codice_" . $i . ") AND ";
						}
					}
				$esclusioni = substr($esclusioni,0,-5);
				$esclusioni .= ")";
			}
			$condizione  = "( ";
			$return = suddivisione_pdo($text,"codice");
			if (is_array($return)) {
				$condizione .= $return["sql"] . " OR ";
				$bind = array_merge($bind,$return["bind"]);
			}
			if ($_SESSION["language"] == "IT") {
				$return = suddivisione_pdo($text,"descrizione");
			} else {
				$return = suddivisione_pdo($text,"{$_SESSION["language"]}");
			}
			if (is_array($return)) {
				$condizione .= $return["sql"];
				$bind = array_merge($bind,$return["bind"]);
			}
			$condizione .= ") ";
			if ($_SESSION["language"] == "IT") {
				$strsql = "SELECT * FROM b_cpv ";
			} else {
				$strsql = "SELECT *, b_cpv_dict.{$_SESSION["language"]} AS descrizione
									 FROM b_cpv_dict ";
			}
			$strsql.= " WHERE " . $condizione . $esclusioni ." ORDER BY codice";
			
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$rec = array();
			if ($risultato->rowCount()>0) {
				while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
					$rec[] = array("label"=>$record["codice"],"value"=>$record["descrizione"]);
				}
			}
			echo json_encode($rec);
	}
?>
