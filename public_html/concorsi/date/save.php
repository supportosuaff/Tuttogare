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
		$bind = array();
		$bind[":codice"] = $_POST["codice"];
		$sql_old = "SELECT chiarimenti, scadenza, apertura FROM b_fasi_concorsi WHERE codice = :codice";
		$ris_old = $pdo->bindAndExec($sql_old,$bind);
		$string_date='';
		$old_date = $ris_old->fetch(PDO::FETCH_ASSOC);
		$string_date.="'".$old_date["chiarimenti"].";".$old_date["scadenza"].";".$old_date["apertura"]."'";

		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_fasi_concorsi";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $_POST;
		$codice_fase = $salva->save();

		$bind = array();
		$bind[":codice_gara"] = $_POST["codice_gara"];
		$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara ORDER BY codice LIMIT 0,1";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount() > 0) {
			$fase = $ris->fetch(PDO::FETCH_ASSOC);
			if ($fase["codice"]==$_POST["codice"]) {
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_concorsi";
				$salva->operazione = "UPDATE";
				$salva->oggetto = array("codice"=>$_POST["codice_gara"],"data_scadenza"=>$_POST["scadenza"]);
				$salva->save();
			}
		}

		log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Scadenze fase");

		$bind = array();
		$bind[":codice"] = $_POST["codice_gara"];
		$strsql= "SELECT b_concorsi.* FROM b_concorsi WHERE b_concorsi.codice = :codice";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$avviso = array();
			$avviso["data"] = date("d-m-Y");
			$avviso["titolo"] = "Modifica date concorso : " . $record_gara["oggetto"];
			$avviso["testo"] = "Si comunica l'avvenuta modifica delle date relative al conconcorso in oggetto<br><br>";
			$avviso["testo"] .= "A seguire la tabella riepilogativa delle date aggiornate<br><br><table>";
			$avviso["testo"] .= "<tr><td class=\"etichetta\"><strong>Termine accesso agli atti</strong></td><td>" . $_POST["chiarimenti"] . "</td></tr>";
			$avviso["testo"] .= "<tr><td class=\"etichetta\"><strong>Scadenza presentazione offerte</strong></td><td>" . $_POST["scadenza"] . "</td></tr>";
			$avviso["testo"] .= "<tr><td class=\"etichetta\"><strong>Apertura delle offerte</strong></td><td>" . $_POST["apertura"] . "</td></tr>";
			$avviso["testo"] .= "</table><br><br>";

			$avviso["codice_gara"] = $record_gara["codice"];
			$avviso["codice_ente"] = $_SESSION["ente"]["codice"];

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_avvisi_concorsi";
			$salva->operazione = "INSERT";
			$salva->oggetto = $avviso;
			$codice = $salva->save();

		}
		$href = "/concorsi/pannello.php?codice=" . $_POST["codice_gara"];
		$href = str_replace('"',"",$href);
		$href = str_replace(' ',"-",$href);
		?>
			alert('Modifica effettuato con successo');
		        window.location.href = '<? echo $href ?>';
	    	<?
	}



?>
