<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && !empty($_POST["codice_gara"])) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/avvisi/index.php'";
			$risultato = $pdo->query($strsql);
			if ($risultato->rowCount()>0) {
				$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
				$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
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
		if (isset($_POST["operazione"])) {

			$_POST["codice_ente"] = $_SESSION["ente"]["codice"];

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_avvisi";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST;
			$codice = $salva->save();
			if ($codice != false) {
				log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],$_POST["operazione"],"Avviso di gara - " . $_POST["titolo"]);
				if (isset($_POST["cod_allegati"]) && $_POST["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$_POST["cod_allegati"])) {
					$cod_allegati = $_POST["cod_allegati"];
					$allegati = explode(";",$_POST["cod_allegati"]);
					$str_allegati = ltrim(implode(",",$allegati),",");
					$sql = "UPDATE b_allegati SET sezione = 'gara' WHERE sezione = 'tmp_avv' AND codice IN (" . $str_allegati . ") AND online = 'S'";
					$ris_allegati = $pdo->query($sql);
				}
					$sql = "SELECT * FROM b_gare WHERE codice = :codice_gara";
					$ris = $pdo->bindAndExec($sql,array(":codice_gara"=>$_POST["codice_gara"]));
					$record_gara = $ris->fetch(PDO::FETCH_ASSOC);
					if ($ris->rowCount() > 0 && isset($_POST["invia"])) {
						$oggetto = "{$record_gara["oggetto"]} - Avviso di gara: " . $_POST["titolo"];
						$corpo = "E' stato pubblicato un avviso riguardante la gara:<br>";
						$corpo.= "<br><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" . $record_gara["oggetto"] . "</strong></a><br><br>";
						$corpo = $_POST["testo"];
						$corpo.= "<br><br>Distinti Saluti<br><br>";

						$corpo_allegati = "";
						$cod_allegati = "";
						if (isset($_POST["cod_allegati"]) && $_POST["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$_POST["cod_allegati"])) {
							$cod_allegati = $_POST["cod_allegati"];
							$allegati = explode(";",$_POST["cod_allegati"]);
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
					$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo.$corpo_allegati;
					$mailer->codice_pec = $record_gara["codice_pec"];
					$mailer->comunicazione = true;
					$mailer->coda = true;
					$mailer->sezione = "gara";
					$mailer->codice_gara = $record_gara["codice"];
					$mailer->cod_allegati = $cod_allegati;
					$mailer->comunicazione_tecnica = false;
					$mailer->sendOpen = (isset($_POST["invia_cpv"])) ? true : false;
					$esito = $mailer->send();
				}
				if (class_exists("syncERP")) {
					$syncERP = new syncERP();
					if (method_exists($syncERP,"sendUpdateRequest")) {
						$syncERP->sendUpdateRequest($record_gara["codice"]);
					}
				}
				if ($_POST["operazione"]=="UPDATE") {

					$href = "/gare/avvisi/index.php?codice=".$_POST["codice_gara"];
					?>
					alert('Modifica effettuata con successo');
	    	        window.location.href = '<? echo $href ?>';
	        	    <?
				} elseif ($_POST["operazione"]=="INSERT") {
					$href = "/gare/avvisi/index.php?codice=".$_POST["codice_gara"];
					?>
					alert('Inserimento effettuato con successo');
	    	        window.location.href = '<? echo $href ?>';
	        	    <?
				}
			} else {
				?>
				alert('Errore nel salvataggio. Si prega di riprovare');
				<?
			}
		}
	}



?>
