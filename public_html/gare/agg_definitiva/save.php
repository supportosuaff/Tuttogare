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

	$ribasso = $esito = array();
	$contributo = floatval(0);

	$bind = array();
	$bind[":codice"] = $_POST["codice_gara"];
	$sql = "SELECT codice_ente, codice_gestore, prezzoBase FROM b_gare WHERE codice = :codice";
	$ris = $pdo->bindAndExec($sql,$bind);
	if($ris->rowCount()>0) {
		$rec = $ris->fetch(PDO::FETCH_ASSOC);
		$codice_ente = $rec["codice_ente"];
		$codice_gestore = $rec["codice_gestore"];
		$prezzoBase = $rec["prezzoBase"];

		$bind = array();
		$bind[":codice_gara"]=$_POST["codice_gara"];
		$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
		$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
		if ($ris_lotti->rowCount()>0) {
			$errore = $vuoto = false;
			while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
				if (isset($_POST["lotto"][$lotto["codice"]]["ribasso"])) {
					$ribasso["ribasso"] = $_POST["lotto"][$lotto["codice"]]["ribasso"];
					$ribasso["importoAggiudicazione"] = $_POST["lotto"][$lotto["codice"]]["importoAggiudicazione"];
					if(isset($_POST["lotto"][$lotto["codice"]]["data_atto_esito"])) $ribasso["data_atto_esito"] = $_POST["lotto"][$lotto["codice"]]["data_atto_esito"];
					if(isset($_POST["lotto"][$lotto["codice"]]["numero_atto_esito"])) $ribasso["numero_atto_esito"] = $_POST["lotto"][$lotto["codice"]]["numero_atto_esito"];
					$ribasso["codice"] = $lotto["codice"];
					$contributo += (float)number_format($lotto["importo_base"] - ($lotto["importo_base"] * (float)($ribasso["ribasso"]/100)), 2, ".","");
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_lotti";
					$salva->operazione = "UPDATE";
					$salva->oggetto = $ribasso;
					if ($salva->save() === false) $errore = true;
					if (empty($_POST["lotto"][$lotto["codice"]]["data_atto_esito"]) && empty($_POST["lotto"][$lotto["codice"]]["numero_atto_esito"])) $vuoto = true;
				}
			}
			$esito["stato"] = 7;
			if($errore || $vuoto) {
				$esito["stato"] = 4;
			} else {
				$esito["pubblica"] = 2;
			}
		} else {
			$esito["numero_atto_esito"] = $_POST["numero_atto_esito"];
			$esito["data_atto_esito"] = $_POST["data_atto_esito"];
			if (isset($_POST["ribasso"])) $esito["ribasso"] = $_POST["ribasso"];
			if (isset($_POST["importoAggiudicazione"])) $esito["importoAggiudicazione"] = $_POST["importoAggiudicazione"];
			$esito["allegati_esito"] = $_POST["allegati_esito"];
			$esito["stato"] = 7;
			if ($_POST["numero_atto_esito"] == "" && $_POST["data_atto_esito"] == "") {
				$esito["stato"] = 4;
			} else {
				$esito["pubblica"] = 2;
			}
			$ribasso["ribasso"]=$esito["ribasso"];
			$contributo = (float)number_format($prezzoBase - ($prezzoBase * (float)($ribasso["ribasso"]/100)), 2, ".","");
		}

		include('contributo.php');

		$esito["codice"] = $codice_gara;

		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_gare";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $esito;
		$codice_gara = $salva->save();
		if ($codice_gara === false || (isset($errore) && $errore)) {
			?>
			alert('Errore durante il salvataggio. Riprova');
			<?
		} else {
			log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Aggiudicazione definitiva",false);

			$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];

			$bind = array();
			$bind[":codice_gara"]=$_POST["codice_gara"];

			$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice_gara";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount()>0) {

				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				$avviso = array();
				$avviso["data"] = date("d-m-Y");
				$avviso["titolo"] = "Pubblicazione Esito - Procedura " . $record_gara["nome_procedura"] . ": " . $record_gara["oggetto"];
				$avviso["testo"] = "Si comunica che &egrave; stato pubblicato l'esito per la gara in oggetto";
				$avviso["codice_gara"] = $record_gara["codice"];
				$avviso["codice_ente"] = $_SESSION["ente"]["codice"];


				$corpo_allegati = "";
				$cod_allegati = "";
				if (isset($_POST["allegati_esito"]) && $_POST["allegati_esito"] != "" && preg_match("/^[0-9\;]+$/",$_POST["allegati_esito"])) {
					$cod_allegati = $_POST["allegati_esito"];
				}
				$avviso["cod_allegati"] = $cod_allegati;
				if (isset($_POST["avviso"])) {
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_avvisi";
					$salva->operazione = "INSERT";
					$salva->oggetto = $avviso;
					$codice = $salva->save();
				}
				if (isset($_POST["pec"])) {
					$bind = array();
					$bind[":codice_gara"]=$record_gara["codice"];

					$oggetto = $avviso["titolo"];

					$corpo = "Si comunica che &egrave; stato pubblicato l'esito per la gara:<br>";
					$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
					$corpo.= "Maggiori informazioni sono disponibili all'indirizzo <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\">";
					$corpo.= $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";
					$corpo.= "</a><br><br>";
					$corpo.= "Distinti Saluti<br><br>";

					if (!empty($cod_allegati) && preg_match("/^[0-9\;]+$/",$cod_allegati)) {
						$allegati = explode(";",$cod_allegati);
						$str_allegati = ltrim(implode(",",$allegati),",");
						$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ") AND online = 'S'";
						$ris_allegati = $pdo->query($sql);
						$corpo_allegati = "<strong>Allegati</strong><br><table width=\"100%\">";
											if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
							$i = 0;
												while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
								$class= "even";
								$i++;
								if ($i%2!=0) $class = "odd";
								$corpo_allegati  .= "<tr class=\"". $class . "\">";
								$corpo_allegati  .= "<td width=\"10\"><img src=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/img/" . substr($allegato["nome_file"],-3) . ".png\" alt=\"File " . substr($allegato["nome_file"],0,-3) . "\" style=\"vertical-align:middle\"></td>";
								$corpo_allegati  .= "<td><strong><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/documenti/allegati/".$allegato["codice_gara"]. "/" . $allegato["nome_file"] . "\" target=\"_blank\">" . $allegato["titolo"] . "</a></strong></td>";
								$corpo_allegati  .= "</tr>";
							}
						}
						$corpo_allegati .= "</table>";
					}
					$mailer = new Communicator();
					$mailer->oggetto = $oggetto;
					$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo . $corpo_allegati;
					$mailer->codice_pec = $record_gara["codice_pec"];
					$mailer->comunicazione = true;
					$mailer->coda = true;
					$mailer->sezione = "gara";
					$mailer->codice_gara = $record_gara["codice"];
					if (!empty($cod_allegati)) $mailer->cod_allegati = $cod_allegati;
					$esito = $mailer->send();
				}

				$bind = array();
				$bind[":codice_gara"] = $record_gara["codice"];
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql = "SELECT * from b_guue where number = 3 AND codice_gara = :codice_gara AND codice_ente = :codice_ente";
				$risultato_guue = $pdo->bindAndExec($strsql,$bind);

				if($risultato_guue->rowCount()==0){
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$strsql = "INSERT INTO b_guue (codice_ente, codice_gara, number , dataRichiesta) VALUES (:codice_ente,:codice_gara, 3, NOW()) ";
					$risultato_guue = $pdo->bindAndExec($strsql,$bind);
				}
			}
			if (class_exists("syncERP")) {
        $syncERP = new syncERP();
        if (method_exists($syncERP,"sendUpdateRequest")) {
          $syncERP->sendUpdateRequest($record_gara["codice"],"definitiva");
        }
      }
			
			?>
			alert('Modifica effettuata con successo');
			window.location.href = '<? echo $href ?>';
		<? }
		} else { ?>
	alert('Si è verificato un errore');
	<? }

} ?>
