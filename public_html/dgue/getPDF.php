<?
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$echo = true;
	include_once($root."/dgue/config.php");
	include_once($root."/inc/xml2json.php");

	if (isset($_GET["codice_dgue"]) && is_operatore()) {
		$codice_riferimento = $_GET["codice_dgue"];
		$bind = array();
		$bind[":codice_riferimento"] = $codice_riferimento;
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$sql = "SELECT * FROM b_dgue_compilati WHERE codice = :codice_riferimento AND
							codice_utente = :codice_utente";
		$ris_old = $pdo->bindAndExec($sql,$bind);
		if ($ris_old->rowCount() > 0) {
			$db_record = $ris_old->fetchAll(PDO::FETCH_ASSOC)[0];
			$dgue = json_decode($db_record["json"],true);
			if (!empty($db_record["subappalto"])) {
				$subappalto = json_decode($db_record["subappalto"],true);
			}
			if (!empty($db_record["nazionali"])) {
				$nazionali = json_decode($db_record["nazionali"],true);
			}
			$checked = getSelectedCriteriaFromRequest($db_record["codice_riferimento"],$db_record["sezione"]);
			$version = getVersionFromSelectedCriteria($checked);
		}
	} else if (isset($_POST["filechunk"]) && isset($_SESSION["codice_utente"])) {
		$xml = file_get_contents($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);
		$xml = $xml = str_replace("&#xd;", "\r\n", $xml);
		$xml = str_replace("&", "&amp;", $xml);
		$xml = str_replace("&amp;amp;", "&amp;", $xml);
		$xml = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $xml);
		$xml = simplexml_load_string($xml);
		$xml = xmlToArray($xml);
		$dgue = $xml["ESPDResponse"];
		$version = null;
	}
	if (!empty($dgue)) {
			ini_set('max_execution_time', 600);
			array_walk_recursive($dgue, function(&$value,&$key) {
				if (!is_array($value)) {
					$value = htmlentities($value);
					$value = nl2br($value);
				}
			});
			$forms = getDGUECriteria($version);
			$dgue_translate_gruppi = getDGUETranslateGruppi($version);
			if (!empty($forms)) {
				$sezioni = array();
				foreach ($forms AS $form) {
					if (empty($sezioni[$form["livello1"]])) $sezioni[$form["livello1"]] = $dgue_translate_gruppi[$form["livello1"]]['it'];
				}
				unset($sezioni["OTHER"]);
				unset($sezioni["OTHER_FINAL"]);

				if (!empty($dgue["ccv:Criterion"])) {
					$criterion = array();
					foreach($dgue["ccv:Criterion"] AS $criteria) {
						$_id = "";
						if (!empty($criteria["cbc:ID"]['$'])) $_id = $criteria["cbc:ID"]['$'];
						if (empty($_id) && !is_array($criteria["cbc:ID"])) $_id = $criteria["cbc:ID"];
						if (!empty($_id)) {
							if (!empty($criterion[$_id])) {
								if (!isset($criterion[$_id][0])) {
									$tmp = $criterion[$_id];
									$criterion[$_id] = array($tmp);
								}
								$criterion[$_id][] = $criteria;
							} else {
								$criterion[$_id] = $criteria;
							}
						}
					}
					$dgue["ccv:Criterion"] = $criterion;
				}
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
											<?= (!empty($dgue["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"])) ? $dgue["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"]: "" ?>
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
									<td width="50%"><?= (!empty($dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:FileName"])) ? $dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:FileName"]: "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>" width="50%">
										Descrizione breve:
									</th>
									<td width="50%">
										<?= (!empty($dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:Description"])) ? $dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:Description"]: "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>" width="50%">
										Numero di riferimento attribuito al fascicolo dall'amministrazione aggiudicatrice o dall'ente aggiudicatore (se pertinente - es.: CIG, CUP):
									</th>
									<td width="50%">
										<?
										if (empty($dgue["cbc:ContractFolderID"]['$']) && !is_array($dgue["cbc:ContractFolderID"])) $dgue["cbc:ContractFolderID"] = array('$'=>$dgue["cbc:ContractFolderID"]);
										echo (!empty($dgue["cbc:ContractFolderID"]['$'])) ? $dgue["cbc:ContractFolderID"]['$']: "" ?>
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
										<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"]: "" ?>
									</td>
									<th style="<?= $styles["th"] ?>">E-mail:</th>
									<td>
										<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:ElectronicMail"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:ElectronicMail"]: "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Via e numero civico:</th>
									<td>
										<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:StreetName"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:StreetName"]: "" ?>
									</td>
									<th style="<?= $styles["th"] ?>">Telefono:</th>
									<td>
										<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:Telephone"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:Telephone"] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">CAP:</th>
									<td>
										<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:Postbox"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:Postbox"] : "" ?>
									</td>
									<th style="<?= $styles["th"] ?>">Referente:</th>
									<td>
										<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:Name"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:Name"] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Citt&agrave;:</th>
									<td>
										<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:CityName"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:CityName"] : "" ?>
									</td>
									<th style="<?= $styles["th"] ?>">Partita IVA:</th>
									<td>
										<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][0]["cbc:ID"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][0]["cbc:ID"] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Paese:</th>
									<td>
										<?
										if (empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]['$']) && !is_array($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"])) $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"] = array('$'=> $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]);
										echo (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]['$'])) ? $paesi[$dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]['$']] : "" ?>
									</td>

									<th style="<?= $styles["th"] ?>">Se non &egrave; applicabile un numero di partita IVA indicare un altro numero di identificazione nazionale, se richiesto e applicabile:</th>
									<td>
										<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][1]["cbc:ID"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][1]["cbc:ID"] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Website:</th>
									<td colspan="3">
										<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cbc:WebsiteURI"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cbc:WebsiteURI"] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>" colspan="3">
										L'operatore economico &egrave; una microimpresa, oppure una piccola o media impresa?
									</th>
									<td>
										<label for="espd[espd-cac:EconomicOperatorParty][espd-cbc:SMEIndicator]">Si</label>

										[
											<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["espd-cbc:SMEIndicator"]) && $dgue["espd-cac:EconomicOperatorParty"]["espd-cbc:SMEIndicator"]=="true") ? "X" : "&nbsp;&nbsp;&nbsp;"; ?>
										]
										&nbsp;
										&nbsp;
										<label for="espd[espd-cac:EconomicOperatorParty][espd-cbc:SMEIndicator]">No</label>
										[<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["espd-cbc:SMEIndicator"]) && $dgue["espd-cac:EconomicOperatorParty"]["espd-cbc:SMEIndicator"]=="false") ? "X" : "&nbsp;&nbsp;&nbsp;"; ?>]
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
								<?
								$check_array = array_keys($dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"]);
								$create_array = true;
								foreach($check_array AS $chiave) {
									if (is_numeric($chiave)) $create_array = false;
								}
								if ($create_array) {
									$tmp = $dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"];
									$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"] = array($tmp);
								}
								foreach($dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"] AS $rappresentante) {
								?>
								<tr>
									<th style="<?= $styles["th"] ?>">Nome</th>
									<td>
										<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FirstName"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FirstName"] : "" ?>
									</td>
									<th style="<?= $styles["th"] ?>">Cognome</th>
									<td>
										<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FamilyName"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FamilyName"] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Data di nascita</th>
									<td>
										<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthDate"])) ? mysql2date($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthDate"]) : "" ?>
									</td>
									<th style="<?= $styles["th"] ?>">Luogo di nascita</th>
									<td>
										<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthplaceName"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthplaceName"] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Via e numero civico:</th>
									<td>
										<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:StreetName"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:StreetName"] : "" ?>
									</td>
									<th style="<?= $styles["th"] ?>">E-mail:</th>
									<td>
										<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:Contact"]["cbc:ElectronicMail"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:Contact"]["cbc:ElectronicMail"] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">CAP</th>
									<td>
										<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:Postbox"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:Postbox"] : "" ?>
									</td>
									<th style="<?= $styles["th"] ?>">Telefono</th>
									<td>
										<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:Contact"]["cbc:Telephone"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:Contact"]["cbc:Telephone"] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Citt&agrave;</th>
									<td>
										<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:CityName"])) ? $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:CityName"] : "" ?>
									</td>
									<th style="<?= $styles["th"] ?>">Posizione/Titolo ad agire:</th>
									<td>
										<?= (!empty($rappresentante["espd-cbc:NaturalPersonRoleDescription"])) ? $rappresentante["espd-cbc:NaturalPersonRoleDescription"] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>">Paese:</th>
									<td colspan="3">
										<?
										if (empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cac:Country"]["cbc:IdentificationCode"]['$']) && !is_array($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cac:Country"]["cbc:IdentificationCode"])) $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cac:Country"]["cbc:IdentificationCode"]= array('$'=> $rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cac:Country"]["cbc:IdentificationCode"]);
										echo(!empty($rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cac:Country"]["cbc:IdentificationCode"]['$'])) ? $paesi[$rappresentante["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cac:Country"]["cbc:IdentificationCode"]['$']] : "" ?>
									</td>
								</tr>
								<tr>
									<th style="<?= $styles["th"] ?>" colspan="2">
										Se necessario, fornire precisazioni sulla rappresentanza (forma, portata, scopo, firma congiunta...):
									</th>
									<td colspan="2">
										<?= (!empty($rappresentante["cac:PowerOfAttorney"]["cbc:Description"])) ? $rappresentante["cac:PowerOfAttorney"]["cbc:Description"] : "" ?>
									</td>
								</tr>
								<? } ?>
								<tr>
									<th style="<?= $styles["th"] ?>" colspan="2">
										Se applicabile, indicare il lotto o i lotti per i quali si intende presentare offerta:
									</th>
									<td colspan="2">
										<?= (!empty($dgue["cac:ProcurementProjectLot"]["cbc:ID"])) ? $dgue["cac:ProcurementProjectLot"]["cbc:ID"] : "" ?>
									</td>
								</tr>
							</table>
							<br pagebreak="true"/>
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
								</td></tr>
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
				@$pdf->Output("DGUE.pdf","D");
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
	?>
