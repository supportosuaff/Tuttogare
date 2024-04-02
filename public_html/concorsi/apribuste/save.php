<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
			if ($codice_fase !== false) {
				$esito = check_permessi_concorso($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}
		if ($edit && !$lock)
	{
			$_POST["codice_ente"] = $_SESSION["ente"]["codice"];
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_date_apertura_concorsi";
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
			$strsql  = "SELECT b_date_apertura_concorsi.data_apertura AS apertura, b_fasi_concorsi_buste.nome, b_concorsi.*
									FROM b_concorsi
									JOIN b_date_apertura_concorsi ON b_concorsi.codice = b_date_apertura_concorsi.codice_gara
									JOIN b_fasi_concorsi_buste ON b_fasi_concorsi_buste.codice = b_date_apertura_concorsi.codice_busta
									JOIN b_fasi_concorsi ON b_date_apertura_concorsi.codice_fase = b_fasi_concorsi.codice
									WHERE b_date_apertura_concorsi.codice = :codice_data ";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount()>0) {

				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Date di apertura busta " . $record_gara["nome"]);

				$avviso = array();
				$avviso["data"] = date("d-m-Y");
				$avviso["titolo"] = "Comunicazione date di apertura della busta " . $record_gara["nome"] . " del concorso " . $record_gara["oggetto"] . ": " . $record_gara["nome_fase"];
				$avviso["testo"] = "Si comunica che l'apertura della busta " . $record_gara["nome"] . " per la concorso in oggetto avverra in data " . mysql2completedate($record_gara["apertura"]) . "<br><br>";
				$avviso["codice_gara"] = $record_gara["codice"];
				$avviso["codice_ente"] = $_SESSION["ente"]["codice"];

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_avvisi";
				$salva->operazione = "INSERT";
				$salva->oggetto = $avviso;
				$codice = $salva->save();

		}
	}



?>
