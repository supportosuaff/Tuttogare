<?
	include_once("../../config.php");
	$echo = true;
	include_once($root."/inc/funzioni.php");
	include_once($root."/dgue/config.php");
	include_once($root."/inc/xml2json.php");
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("dgue_ca",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if (isset($_GET["codice_riferimento"]) && $_GET["sezione"]) {

			$ris = getDGUERequestedCriteria($_GET["codice_riferimento"],$_GET["sezione"]);
			$checked = getSelectedCriteriaFromRequest($_GET["codice_riferimento"],$_GET["sezione"]);
			$version = getVersionFromSelectedCriteria($checked);
			$dgue_translate_gruppi = getDGUETranslateGruppi($version);
			if (!empty($ris)) {
				$bind = array();
				$bind[":codice"] = $_GET["codice_riferimento"];
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				if ($_GET["sezione"] == "gare") {
					$strsql  = "SELECT b_gare.* FROM b_gare
											WHERE b_gare.codice = :codice ";
					$strsql .= "AND b_gare.annullata = 'N' ";
					$strsql .= "AND codice_gestore = :codice_ente ";
					if (!isset($_SESSION["codice_utente"]) || is_operatore()) {
						$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
					}
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
						$dati = $risultato->fetch(PDO::FETCH_ASSOC);
					}
				} else if ($_GET["sezione"] == "concorsi") {
					$strsql  = "SELECT b_concorsi.* FROM b_concorsi
											WHERE b_concorsi.codice = :codice ";
					$strsql .= "AND b_concorsi.annullata = 'N' ";
					$strsql .= "AND codice_gestore = :codice_ente ";
					if (!isset($_SESSION["codice_utente"]) || is_operatore()) {
						$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
					}
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
						$dati = $risultato->fetch(PDO::FETCH_ASSOC);
					}
				} else if ($_GET["sezione"] == "free") {
					$strsql="SELECT * FROM b_dgue_free WHERE codice = :codice AND codice_gestore = :codice_ente";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
						$dati = $risultato->fetch(PDO::FETCH_ASSOC);
						$dati["oggetto"] = $dati["procedura"];
						$dati["cig"] = $dati["identificativo"];
					}
				} else {
					$table = "";
					if ($_GET["sezione"] == "albo") $table = "albo";
					if ($_GET["sezione"] == "dialogo") $table = "dialogo";
					if ($_GET["sezione"] == "mercato") $table = "mercato";
					if ($_GET["sezione"] == "sda") $table = "sda";
					$strsql = "SELECT * FROM b_bandi_$table WHERE codice = :codice ";
					$strsql .= "AND codice_gestore = :codice_ente ";
					$risultato = $pdo->bindAndExec($strsql,$bind);

					if ($risultato->rowCount() > 0) {
						$dati = $risultato->fetch(PDO::FETCH_ASSOC);
					}
				}
				if (!empty($dati)) {
					if (empty($dati["denominazione"])) $dati["denominazione"] = $_SESSION["ente"]["denominazione"];
					$forms = $ris;
					$sezioni = array();
					$dgue["ccv:Criterion"] = array();
					foreach ($forms AS $form) {
						if (empty($sezioni[$form["livello1"]])) $sezioni[$form["livello1"]] = $dgue_translate_gruppi[$form["livello1"]]['it'];
						$dgue["ccv:Criterion"][$form["uuid"]] = "show_empty";
					}

					$list = get_html_translation_table(HTML_ENTITIES);
					// unset($list['"']);
					unset($list['<']);
					unset($list['>']);
					unset($list['&']);

					$search = array_keys($list);
					// $search = array_map('utf8_encode', $search);
					$values = array_values($list);

					array_walk_recursive($dati, function (&$valore) use ($search, $values) {
					  if(is_string($valore)) $valore =  str_replace($search, $values, $valore);
					});

					unset($sezioni["OTHER"]);
					unset($sezioni["OTHER_FINAL"]);

					ob_start();
					$styles["th"] = "font-weight: bold;background-color:#DDD;";
					?>
					<html>
						<style>
							h1 {
								text-align: center;
							}
							table {
								width:100%;
								border-spacing: 2px;
								padding:5px;
							}
							th {
								width:25%;
							}
							th,td {
								padding:10;
								border:1px solid #999;
							}
							.padding {
								padding: 20px;
							}

							.box {
								border-bottom:1px solid #999;
							}

							.dgue_label {
								background-color: #CCC;
								padding:20px;
								width:49%;
								float:left;
							}

							.clear {
								clear:both;
								margin:0px;
								padding:0px;
							}
						</style>
						<body>
							<h1>Documento di Gara Unico Europeo (DGUE)</h1>
							<h2>Parte I: Informazioni sulla procedura di appalto e sull'amministrazione aggiudicatrice o ente aggiudicatore</h2>
							<table>
								<tr><th style="<?= $styles["th"] ?> text-align:center" colspan="2">Identit&agrave; del committente</th></tr>
								<tr>
									<th style="<?= $styles["th"] ?>" width="50%">
										Denominazione Ufficiale:
									</th>
									<td width="50%">
										<?= (!empty($dati["denominazione"])) ? $dati["denominazione"] : "" ?><? if(! empty($dati["struttura_proponente"])) { echo "<br>{$dati["struttura_proponente"]}"; }  ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>" width="50%">
										Paese:
									</th>
									<td width="50%">
											Italia
									</td>
								</tr>
								<tr><th style="<?= $styles["th"] ?> text-align:center" colspan="2">Informazioni sulla procedura di appalto</th></tr>
								<tr>
									<th style="<?= $styles["th"] ?>" width="50%">
										Titolo:
									</th>
									<td width="50%"><?= (!empty($dati["oggetto"])) ? $dati["oggetto"]: "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>" width="50%">
										Descrizione breve:
									</th>
									<td width="50%">
										<?= (!empty($dati["descrizione"])) ? $dati["descrizione"]: "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>" width="50%">
										Numero di riferimento attribuito al fascicolo dall'amministrazione aggiudicatrice o dall'ente aggiudicatore (se pertinente - es.: CIG, CUP):
									</th>
									<td width="50%">
										<?
										echo (!empty($dati["cig"])) ? $dati["cig"]: "" ?>
									</td>
								</tr>
							</table>
							<h2>Parte II: Informazioni sull'operatore economico</h2>
							<table>
								<tr>
									<th style="<?= $styles["th"] ?> text-align:center;" colspan="4"><strong>A: Informazioni sull'operatore economico</strong></th>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Nome/denominazione:</th>
									<td>

									</td>
									<th style="<?= $styles["th"] ?>">E-mail:</th>
									<td>

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Via e numero civico:</th>
									<td>

									</td>
									<th style="<?= $styles["th"] ?>">Telefono:</th>
									<td>

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">CAP:</th>
									<td>

									</td>
									<th style="<?= $styles["th"] ?>">Referente:</th>
									<td>

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Citt&agrave;:</th>
									<td>

									</td>
									<th style="<?= $styles["th"] ?>">Partita IVA:</th>
									<td>

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Paese:</th>
									<td>

									</td>

									<th style="<?= $styles["th"] ?>">Se non &egrave; applicabile un numero di partita IVA indicare un altro numero di identificazione nazionale, se richiesto e applicabile:</th>
									<td>

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Website:</th>
									<td colspan="3">

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>" colspan="3">
										L'operatore economico &egrave; una microimpresa, oppure una piccola o media impresa?
									</th>
									<td>
										<label for="espd[espd-cac:EconomicOperatorParty][espd-cbc:SMEIndicator]">Si</label>
										[&nbsp;&nbsp;&nbsp;]
										&nbsp;
										&nbsp;
										<label for="espd[espd-cac:EconomicOperatorParty][espd-cbc:SMEIndicator]">No</label>
										[&nbsp;&nbsp;&nbsp;]
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?> text-align:center;" colspan="4">
										<strong>
											B: Informazioni sui rappresentanti dell'operatore economico
											<? if ($version == "2023-36") { ?>
												<br>Soggetti di cui all'art. 94 c.3 del D.Lgs 36/2023
											<? } ?>
										</strong>
									</th>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Nome</th>
									<td>

									</td>
									<th style="<?= $styles["th"] ?>">Cognome</th>
									<td>

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Data di nascita</th>
									<td>

									</td>
									<th style="<?= $styles["th"] ?>">Luogo di nascita</th>
									<td>

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Via e numero civico:</th>
									<td>

									</td>
									<th style="<?= $styles["th"] ?>">E-mail:</th>
									<td>

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">CAP</th>
									<td>

									</td>
									<th style="<?= $styles["th"] ?>">Telefono</th>
									<td>

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Citt&agrave;</th>
									<td>

									</td>
									<th style="<?= $styles["th"] ?>">Posizione/Titolo ad agire:</th>
									<td>

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Paese:</th>
									<td colspan="3">

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>" colspan="2">
										Se necessario, fornire precisazioni sulla rappresentanza (forma, portata, scopo, firma congiunta...):
									</th>
									<td colspan="2">

									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>" colspan="2">
										Se applicabile, indicare il lotto o i lotti per i quali si intende presentare offerta:
									</th>
									<td colspan="2">

									</td>
								</tr>
							</table>
							<?
								showDGUE("OTHER");
								$all_satisfied = false;
								 if (!empty($dgue["ccv:Criterion"]["7e7db838-eeac-46d9-ab39-42927486f22d"]["ccv:RequirementGroup"]["ccv:Requirement"]["ccv:Response"]["ccv-cbc:Indicator"]) &&  ($dgue["ccv:Criterion"]["7e7db838-eeac-46d9-ab39-42927486f22d"]["ccv:RequirementGroup"]["ccv:Requirement"]["ccv:Response"]["ccv-cbc:Indicator"]=="true")) $all_satisfied = true;
								foreach ($sezioni as $id => $titolo) {
									?>
									<h2><?= $titolo ?></h2>
									<?
									showDGUE($id,$all_satisfied);
								}
							?><br><br>
							<?
								showDGUE("OTHER_FINAL");
							?>
							<h2>Parte VI: Dichiarazioni Finali</h2>
							<table width="100%">
									<tr><td>
									<? if ($version == "2023-36") { ?>
										Il sottoscritto dichiara formalmente che le informazioni riportate nelle precedenti parti da II a V sono veritiere e corrette e che il sottoscritto &egrave; consapevole delle conseguenze di una grave falsa dichiarazione,  ai sensi dell’articolo 76 del DPR 445/2000.<br>
										Il sottoscritto autorizza formalmente <?= $dgue["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"] ?> ad accedere ai documenti complementari alle informazioni di cui ai punti del presente documento di gara unico europeo, ai fini della procedura <?= $dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:FileName"] ?>.<br><br>
										Data luogo e firma<br><br>
									<? } else { ?>
										Il sottoscritto dichiara formalmente che le informazioni riportate nelle precedenti parti da II a V sono veritiere e corrette e che il sottoscritto &egrave; consapevole delle conseguenze di una grave falsa dichiarazione.<br>
										Il sottoscritto dichiara formalmente di essere in grado di produrre, su richiesta e senza indugio, i certificati e le altre forme di prove documentali del caso, con le seguenti eccezioni:
										<br><br>
										a) se l'amministrazione aggiudicatrice o l'ente aggiudicatore hanno la possibilit&agrave; di acquisire direttamente la documentazione complementare accedendo a una banca dati nazionale che sia disponibile gratuitamente in un qualunque Stato membro (a condizione che l'operatore economico abbia fornito le informazioni necessarie - indirizzo web, autorit&agrave; o organismo di emanazione, riferimento preciso della documentazione - in modo da consentire all'amministrazione aggiudicatrice o all'ente aggiudicatore di ottenere la documentazione; se necessario, va allegato il pertinente assenso all'accesso) oppure
										<br><br>
										b) a decorrere al pi&ugrave; tardi dal 18 ottobre 2018 (in funzione dell'attuazione nazionale dell'articolo 59, paragrafo 5, secondo comma della direttiva 2014/24/UE) l'amministrazione aggiudicatrice o l'ente aggiudicatore sono gi&agrave; in possesso della documentazione in questione.
										<br><br>
										Il sottoscritto autorizza formalmente <?= $dgue["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"] ?> ad accedere ai documenti complementari alle informazioni di cui ai punti del presente documento di gara unico europeo, ai fini della procedura <?= $dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:FileName"] ?>.<br><br>
										Data luogo e firma<br><br>
										<br></td></tr>
									<? } ?>
								<br></td></tr>
							</table>
						</body>
					</html>
					<?
					$html = ob_get_clean();
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
					@$pdf->Output("Modello DGUE.pdf","D");
			} else {
				?>
				<h1>Documento non esistente</h1>
				<?
			}
		} else {
			?>
			<h1>Impossibile accedere</h1>
			<?
		}
	} else {
		?>
		<h1>Impossibile accedere</h1>
		<?
	}
	?>
