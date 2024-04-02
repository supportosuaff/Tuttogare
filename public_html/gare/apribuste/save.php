<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
	if ($edit && !$lock) {
		$_POST["codice_ente"] = $_SESSION["ente"]["codice"];
		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_date_apertura";
		$salva->operazione = "INSERT";
		$salva->oggetto = $_POST;
		$codice_data = $salva->save();
		?>
					$("<div></div>").load("tr_data.php?codice=<? echo $codice_data ?>",function() {
						$("#date").append($(this).html());
							$("#codice_busta").val("").trigger("chosen:updated");
							$("#data_apertura").val("");
							f_ready();
					});
					<?
		$bind = array();
		$bind[":codice_data"] = $codice_data;
		$strsql  = "SELECT b_date_apertura.data_apertura AS apertura, b_criteri_buste.nome, b_gare.*, b_procedure.nome AS nome_procedura ";
		if ($_POST["codice_lotto"] > 0) $strsql.= ", b_lotti.oggetto AS lotto ";
		$strsql .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
		$strsql .= "JOIN b_date_apertura ON b_gare.codice = b_date_apertura.codice_gara ";
		$strsql .= "JOIN b_criteri_buste ON b_criteri_buste.codice = b_date_apertura.codice_busta ";
		if ($_POST["codice_lotto"] > 0) $strsql .= "LEFT JOIN b_lotti ON b_date_apertura.codice_lotto = b_lotti.codice ";
		$strsql .= "WHERE b_date_apertura.codice = :codice_data ";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount()>0) {

			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
			log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Date di apertura busta " . $record_gara["nome"]);

			$avviso = array();
			$avviso["data"] = date("d-m-Y");
			$avviso["titolo"] = "Comunicazione data di apertura della busta " . $record_gara["nome"] . " della procedura " . $record_gara["nome_procedura"] . ": " . $record_gara["oggetto"];
			if (!empty($record_gara["lotto"])) $avviso["titolo"] .= " Lotto: " . $record_gara["lotto"];
			$avviso["testo"] = "Si comunica che l'apertura della busta " . $record_gara["nome"] . " per la procedura in oggetto avverra in data " . mysql2completedate($record_gara["apertura"]) . "<br><br>";
			$avviso["codice_gara"] = $record_gara["codice"];
			$avviso["codice_ente"] = $_SESSION["ente"]["codice"];

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_avvisi";
			$salva->operazione = "INSERT";
			$salva->oggetto = $avviso;
			$codice = $salva->save();

			if (isset($_POST["invia_comunicazione"])) {

				$oggetto = $avviso["titolo"];
				$corpo = $avviso["testo"];
				$corpo.= "Maggiori informazioni sono disponibili all'indirizzo <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\">";
				$corpo.= $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";
				$corpo.= "</a><br><br>";
				$corpo.= "Distinti Saluti<br><br>";

				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
				$mailer->codice_pec = $record_gara["codice_pec"];
				$mailer->comunicazione = true;
				$mailer->coda = true;
				$mailer->sezione = "gara";
				$mailer->codice_gara = $record_gara["codice"];
				$mailer->codice_lotto = $_POST["codice_lotto"];
				$esito = $mailer->send();
				
			}
			?>
			alert("Operazione Effettuata con successo");
			<?
		}
	}



?>
