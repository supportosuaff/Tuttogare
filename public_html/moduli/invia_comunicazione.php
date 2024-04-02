<?
	session_start();
	$esito = "Nessun invio effettuato";
	if (!empty($_SESSION["ente"]) && !empty($_SESSION["codice_utente"])) {
		if ((isset($_POST["indirizzi"])) && (isset($_SESSION["gerarchia"])) && ($_SESSION["gerarchia"] <= 2)) {
			include("../../config.php");
			include_once($root."/inc/funzioni.php");
			$codici_utenti = explode(";",$_POST["indirizzi"]);
			if (count($codici_utenti)>0) {
				$oggetto = $_POST["oggetto"];
				$corpo = $_POST["corpo"];
				$corpo_allegati = "";
				$cod_allegati = "";
				if (isset($_POST["cod_allegati"]) && $_POST["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$_POST["cod_allegati"])) {
						$bind=array();
						$cod_allegati = $_POST["cod_allegati"];
						$allegati = explode(";",$_POST["cod_allegati"]);
						$i = 0;
						$str_in = "";
						foreach ($allegati as $allegato) {
							if ($allegato != "") {
								$i++;
								$bind[":allegato_".$i] = $allegato;
								$str_in .= ",:allegato_".$i;
							}
						}
						$str_allegati = ltrim($str_in,",");
						if ($str_allegati != "") {
							$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ")";
							$ris_allegati = $pdo->bindAndExec($sql,$bind);
							$corpo_allegati = "<strong>Allegati</strong><br><table width=\"100%\">";
							if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
								$i = 0;
								while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
									$class= "even";
									$i++;
									if ($i%2!=0) $class = "odd";
									$att_path = "/documenti/allegati/";
									if (!empty($allegato["codice_gara"])) {
										$att_path .= $allegato["codice_gara"] . "/";
									}
									$att_path .= $allegato["nome_file"];
									$corpo_allegati  .= "<tr class=\"". $class . "\">";
									$corpo_allegati  .= "<td width=\"10\"><img src=\"".$config["link_sito"] ."/img/" . substr($allegato["nome_file"],-3) . ".png\" alt=\"File " . substr($allegato["nome_file"],0,-3) . "\" style=\"vertical-align:middle\"></td>";
									$corpo_allegati  .= "<td><strong><a href=\"".$config["link_sito"] . $att_path . "\" target=\"_blank\">" . $allegato["titolo"] . "</a></strong></td>";
									$corpo_allegati  .= "</tr>";
								}
							}
						$corpo_allegati .= "</table>";
					}
				}
				$codice_pec = 0;
				if (isset($_POST["codice_pec"])) $codice_pec = $_POST["codice_pec"];
				$protocollo = "";
				if ($_POST["comunicazione"]["numero_protocollo"] != "") $protocollo = "Prot. n." . $_POST["comunicazione"]["numero_protocollo"] . " del " . $_POST["comunicazione"]["data_protocollo"] . " - ";

				$mailer = new Communicator();
				$mailer->oggetto = $protocollo . $oggetto;
				$mailer->corpo = "<h2>" . $protocollo . $oggetto . "</h2>" . $corpo . "<br>" . $corpo_allegati;
				$mailer->codice_pec = $codice_pec;
				if (!empty($_POST["codice_gara"])) $mailer->codice_gara = $_POST["codice_gara"];
				$mailer->comunicazione = true;
				$mailer->coda = true;
				$mailer->comunicazione_tecnica = false;
				$mailer->destinatari = array_unique($codici_utenti);
				$esito = $mailer->send();
			}
		}
		if ($esito !== true) {
				?>
						alert("<? echo $esito ?>");
							<?
			} else {
			?>
				annulla_comunicazione();
				alert("Invio effettuato con successo");
			<?
			}
		} else {
			header('HTTP/1.0 403 Forbidden');
			die();
		}
?>
