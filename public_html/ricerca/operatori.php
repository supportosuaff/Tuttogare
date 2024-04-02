<?
	/* session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_GET["term"])) {
			$text = $_GET["term"];
			$bind = array(":text"=>$text);
			$strsql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici ";
			$strsql.= "JOIN b_utenti on b_utenti.codice = b_operatori_economici.codice_utente ";
			$strsql.= " WHERE b_operatori_economici.ragione_sociale LIKE :text ORDER BY ragione_sociale";
			$risultato = $pdo->bindAndExec($strsql);
			$rec = array();
			if ($risultato->numRows()>0) {
				while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
					$rec[] = array("value"=>$record["codice_fiscale_impresa"],"label"=>$record["ragione_sociale"],"ragione_sociale"=>$record["ragione_sociale"],"identificativoEstero"=>$record["identificativoEstero"],"codice_operatore"=>$record["codice"],"codice_utente"=>$record["codice_utente"],"pec"=>$record["pec"]);
				}
			}
			echo json_encode($rec);
	} */
	echo json_encode(array());
?>
