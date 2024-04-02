<?

include_once("../../../config.php");
include_once($root . "/layout/top.php");
include_once($root . "/inc/p7m.class.php");
include($root."/inc/pdftotext.phpclass");

$public = true;
if (is_operatore()) {
	if (isset($_POST["codice_bando"])) {
		$codice = $_POST["codice_bando"];
		$bind = array();
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$strsql = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente";
		$ris = $pdo->bindAndExec($strsql, $bind);
		$operatore = $ris->fetch(PDO::FETCH_ASSOC);
		$bind = array();
		$bind[":codice"] = $codice;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT * FROM b_bandi_albo WHERE codice = :codice ";
		$strsql .= "AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = 0) ";
		$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql, $bind);
		if ($risultato->rowCount() > 0) {
			$record_bando = $risultato->fetch(PDO::FETCH_ASSOC);
			if (!empty($record_bando["jsonQuestionario"])) {
				$domande = json_decode($record_bando["jsonQuestionario"],true);
				if (!empty($_POST["questionario"])) {
					$validazione = [];
					$risposte = $_POST["questionario"];
					foreach($domande AS $idDomanda => $domada) {
						if ($domada["type"] != "p") {
							if (!empty($domada["obbligatorio"])) {
								$missing = false;
								if (!isset($risposte[$idDomanda]) || (empty($risposte[$idDomanda]) && $risposte[$idDomanda] !== "0")) {
									if ($domada["obbligatorio"] === true) {
										$missing = true;
									} else if (is_array($domada["obbligatorio"])) {
										$countMissing = 0;
										foreach($domada["obbligatorio"] AS $idDomandaChain => $value) {
											if ($value === "") {
												if (!isset($risposte[$idDomandaChain]) || (empty($risposte[$idDomandaChain]) && $risposte[$idDomandaChain] !== "0")) {
													$countMissing++;
												}
											} else {
												if (isset($risposte[$idDomandaChain]) && $risposte[$idDomandaChain] == $value) {
													$countMissing++;
												}
											}
										}
										if ($countMissing == count($domada["obbligatorio"])) {
											$missing = true;
										}
									}
								}
								if ($missing) {
									$validazione[$idDomanda] = $domada["label"] . "è obbligatorio";
								}
							}
						}
					}
					$bind = array();
					$bind[":codice_bando"] = $record_bando["codice"];
					$bind[":codice_operatore"] = $operatore["codice"];
					$strsql = "SELECT * FROM r_partecipanti_albo WHERE codice_bando = :codice_bando AND codice_operatore = :codice_operatore";
					$risultato = $pdo->bindAndExec($strsql, $bind);
					if ($risultato->rowCount() === 0) {
						$partecipante = array();
						$partecipante["codice_bando"] = $record_bando["codice"];
						$partecipante["codice_operatore"] = $operatore["codice"];
						$partecipante["codice_utente"] = $_SESSION["codice_utente"];
						$partecipante["ammesso"] = "N";
						$partecipante["conferma"] = "N";

						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_partecipanti_albo";
						$salva->operazione = "INSERT";
						$salva->oggetto = $partecipante;
						if ($salva->save()) {
							$strsql = "SELECT * FROM r_partecipanti_albo WHERE codice_bando = :codice_bando AND codice_operatore = :codice_operatore";
							$risultato = $pdo->bindAndExec($strsql, $bind);
						}
					}
					if ($risultato->rowCount() > 0) {
						
						$record_partecipante = $risultato->fetch(PDO::FETCH_ASSOC);

						$partecipante = array();
						$partecipante["codice_bando"] = $record_bando["codice"];
						$partecipante["codice_operatore"] = $operatore["codice"];
						$partecipante["codice_utente"] = $_SESSION["codice_utente"];
						if ($record_partecipante["ammesso"] == "N") $partecipante["timestamp_richiesta"] = date('Y-m-d H:i:s');
						$partecipante["timestamp_aggiornamento"] = date('Y-m-d H:i:s');
						$partecipante["codice"] = $record_partecipante["codice"];
						$partecipante["conferma"] = "N";
						$partecipante["valutato"] = "N";
						$partecipante["visto"] = "N";
						$partecipante["dataQuestionario"] = json_encode($_POST["questionario"]);

						$pdo->go("DELETE FROM b_allegati_albo WHERE codice_modulo = :modulo AND codice_operatore = :operatore",[":modulo"=>($record_bando["codice"] * -1),":operatore"=>$operatore["codice"]]);
						if ($record_partecipante["valutato"] == "S") {
							$oggetto = "Aggiornamento dati: " . $record_bando["oggetto"];

							$corpo = "L'operatore economico " . $operatore["codice_fiscale_impresa"] . " " . $operatore["ragione_sociale"] . ", " . $operatore["indirizzo_legale"] . " " . $operatore["citta_legale"] . " (" . $operatore["provincia_legale"] . "),  ha aggiornato i dati della sua istanza ";
							if ($record_bando["manifestazione_interesse"] == "N") {
								$corpo .= "all'Elenco dei Fornitori:<br>";
							} else if ($record_bando["manifestazione_interesse"] == "S") {
								$corpo .= "all'Indagine di Mercato:<br>";
							}
							$corpo .= "<br><strong>" . $record_bando["oggetto"] . "</strong><br><br>";
							$corpo .= "Distinti Saluti<br><br>";

							$mailer = new Communicator();
							$mailer->oggetto = $oggetto;
							$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
							$mailer->codice_pec = $record_bando["codice_pec"];
							$mailer->comunicazione = true;
							$mailer->coda = false;
							$mailer->sezione = "albo";
							$mailer->codice_gara = $record_bando["codice"];
							$mailer->destinatari = $_SESSION["codice_utente"];
							$esito = $mailer->send();

							$pec_conferma = getIndirizzoConferma($record_bando["codice_pec"]);

							$mailer = new Communicator();
							$mailer->oggetto = $oggetto;
							$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
							$mailer->codice_pec = -1;
							$mailer->destinatari = $pec_conferma;
							$mailer->codice_gara = $record_bando["codice"];
							$mailer->type = 'comunicazione-albo';
							$mailer->sezione = "albo";
							$esito = $mailer->send();
						}
						if (!empty($validazione)) {
							echo "<h1>DATI OBBLIGATORI MANCANTI<h1>";
							echo "<ul><li>" . implode("</li><li>",$validazione) . "</li></ul>";
							$partecipante["hashQuestionario"] = "";
							if (isset($_SESSION["questionarioAlbo"][$record_partecipante["codice"]])) {
								unset($_SESSION["questionarioAlbo"][$record_partecipante["codice"]]);
							}
						} else {
							ob_start();
							echo "<h1>" . $record_bando["oggetto"] . "</h1>";
							$view = true;
							include(__DIR__."/form.php");
							$content = ob_get_clean();
							$html = "<html>
		                      <head>
		                        <style>
		                          table, th, td {
		                            border: 1px solid grey;
		                          }
		                          th {
		                            text-align:center;
		                            background-color:#CCC;
		                          }
		                        </style>
		                      </head>
		                      <body>" . $content ."</body></html>";
							ini_set('memory_limit', '2048M');
							ini_set('max_execution_time', 600);
							
							$tmp_path = $config["chunk_folder"]."/".$record_partecipante["codice"]."_questionarioAlbo.pdf";
							$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");

							class MYPDF extends TCPDF {
								public function Footer() {
									$this->WriteHTML('<div style="border-top:1px solid #000; text-align:right"> Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages() . '</div>');
								}
							}
							$pdf = new MYPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
							@$pdf->setPrintHeader(false);
							@$pdf->setPrintFooter(true);
							@$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
							@$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
							@$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
							@$pdf->SetFont('helvetica', '', 8);
							@$pdf->AddPage();
							@$pdf->WriteHTML(utf8_encode($html));
							@$pdf->lastPage();
							@$pdf->Output($tmp_path,"F");
							$offerFile = file_get_contents($tmp_path);
							$path_file = $config["arch_folder"] ."/allegati_albo/" . $operatore["codice"];
							if (!is_dir($path_file)) {
								mkdir($path_file);
							}
							$fileName = "questionario-{$record_bando["codice"]}-{$record_partecipante["codice"]}.pdf";
							$path_file .= "/{$fileName}";
							file_put_contents($path_file,$offerFile);
							$contentForHash = new PdfToText($tmp_path);
							$contentForHash =  $contentForHash->Text;
							$contentForHash = preg_replace("/[^a-zA-Z0-9]/", '', $contentForHash);
							unlink($tmp_path);
							$download = true;
							$hash["md5"] = hash("md5",$offerFile);
							$hash["sha1"] = hash("sha1",$offerFile);
							$hash["sha256"] = hash("sha256",$offerFile);
							$hash["shaContent"] = hash("sha256",$contentForHash);
							$partecipante["hashQuestionario"] = json_encode($hash);
						}
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_partecipanti_albo";
						$salva->operazione = "UPDATE";
						$salva->oggetto = $partecipante;
						$codice_partecipante = $salva->save();
						
						
						echo "<h1>" . traduci("salvataggio riuscito con successo") . "!</h1>";
						if (!empty($download)) {
							?>
							<a class="submit_big" target="_blank" href="/albo_fornitori/questionario/download.php?partecipante=<?= $record_partecipante["codice"] ?>&codice_bando=<?= $record_bando["codice"] ?>">
								Scarica PDF
							</a>
							<?
						}
					}
				} else {
					echo "<h1>" . traduci('Compilazione del questionario obbligatoria') . "</h1>";
				}
				?>
				<a class="submit_big" style="background-color: #333;" href="edit.php?cod=<? echo $record_bando["codice"] ?>"><?= traduci("Ritorna al questionario") ?></a>
				<?
				if (!empty($download)) {
					?>
					<a class="submit_big" style="background-color: #333;" href="/albo_fornitori/modulo.php?cod=<? echo $record_bando["codice"] ?>"><?= traduci("Ritorna all'invio dell'istanza") ?></a>
					<?
				}
			} else {
				echo "<h1>" . traduci('impossibile accedere') . " - 0</h1>";
			}
		} else {
			echo "<h1>" . traduci('impossibile accedere') . " - 1</h1>";
		}
	} else {
		echo "<h1>" . traduci('impossibile accedere') . " - 2</h1>";
	}
} else {
	echo "<h1>" . traduci('impossibile accedere') . " - 3</h1>";
}
include_once($root . "/layout/bottom.php");

?>