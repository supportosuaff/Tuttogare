<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_GET["term"])) {
			$text = $_GET["term"];
			$condizione = suddivisione_pdo($text,"b_operatori_economici.codice_fiscale_impresa");
			$bind = $condizione["bind"];
			$sql = $condizione["sql"];
			$strsql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici ";
			$strsql.= "JOIN b_utenti on b_utenti.codice = b_operatori_economici.codice_utente ";
			$strsql.= " WHERE " . $sql . " AND b_operatori_economici.codice_fiscale_impresa <> '' AND b_operatori_economici.codice_fiscale_impresa IS NOT NULL ORDER BY ragione_sociale";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$rec = array();
			if ($risultato->rowCount()>0) {
				while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
					$rec[] = array("value"=>$record["codice_fiscale_impresa"],
													"label"=>$record["ragione_sociale"] . " - " . $record["pec"],
													"ragione_sociale"=>$record["ragione_sociale"],
													"identificativoEstero"=>$record["identificativoEstero"],
													"codice_operatore"=>$record["codice"],
													"codice_utente"=>$record["codice_utente"],
													"partita_iva"=>$record["partita_iva"],
													"indirizzo_legale"=>$record["indirizzo_legale"],
													"citta_legale"=>$record["citta_legale"],
													"stato_legale"=>$record["stato_legale"],
													"indirizzo_operativa"=>$record["indirizzo_operativa"],
													"citta_operativa"=>$record["citta_operativa"],
													"stato_operativa"=>$record["stato_operativa"],
													"pec"=>$record["pec"]);
				}
			}
			echo json_encode($rec);
	}
?>
