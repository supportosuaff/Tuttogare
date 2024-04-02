<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_GET["term"]) && !empty($_SESSION["codice_utente"])) {
			$text = $_GET["term"];
			$condizione = suddivisione_pdo($text,"b_commissari_albo.codice_fiscale");
			$bind = $condizione["bind"];
			$sql = $condizione["sql"];
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "SELECT b_commissari_albo.* FROM b_commissari_albo JOIN b_albi_commissione ON b_commissari_albo.codice_albo = b_albi_commissione.codice
								 WHERE " . $sql . " AND b_albi_commissione.codice_gestore = :codice_ente AND b_commissari_albo.attivo = 'S' ORDER BY b_commissari_albo.cognome, b_commissari_albo.nome";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$rec = array();
			if ($risultato->rowCount()>0) {
				while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
					$rec[] = array(
						"value"=>$record["codice_fiscale"],
						"label"=>$record["cognome"] . " " . $record["nome"],
						"cognome"=>$record["cognome"],
						"nome"=>$record["nome"],
						"codice"=>$record["codice"],
						"telefono"=>$record["telefono"],
						"email"=>$record["email"],
						"fax"=>$record["fax"],
						"indirizzo"=>$record["indirizzo"],
						"cap"=>$record["cap"],
						"comune"=>$record["comune"]
					);
				}
			}
			echo json_encode($rec);
	}
?>
