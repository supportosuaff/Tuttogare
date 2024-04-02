<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	include_once($root."/dgue/config.php");
	include_once($root."/inc/xml2json.php");
	$public = true;
	if (isset($_GET["codice_riferimento"]) && is_operatore()) {
		$codice_riferimento = $_GET["codice_riferimento"];

		$bind = array();
		$bind[":codice"] = $codice_riferimento;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$accedi = false;
		if ($_GET["sezione"] == "gare") {
			$strsql  = "SELECT b_gare.*, b_modalita.online, b_procedure.invito, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
									WHERE b_gare.codice = :codice ";
			$strsql .= "AND b_gare.annullata = 'N' ";
			$strsql .= "AND codice_gestore = :codice_ente ";
			$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$bind = array();
				$bind[":codice"] = $codice_riferimento;
				$record = $risultato->fetch(PDO::FETCH_ASSOC);
				$accedi = true;
				$derivazione = "";
				$sql = "SELECT * FROM b_procedure WHERE codice = :codice";
				$ris = $pdo->bindAndExec($sql,array(":codice"=>$record["procedura"]));
				if ($ris->rowCount()>0) {
					$rec_procedura = $ris->fetch(PDO::FETCH_ASSOC);
					$directory = $rec_procedura["directory"];
					$record["nome_procedura"] = $rec_procedura["nome"];
					$record["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
					if ($rec_procedura["mercato_elettronico"] == "S") $derivazione = "me";
					if ($rec_procedura["directory"] == "sda")  $derivazione = "sda";
				}
				if ($derivazione != "") {
					$sql_abilitato = "SELECT * FROM r_partecipanti_".$derivazione." WHERE codice_bando = :codice_derivazione AND ammesso = 'S' AND codice_utente = :codice_utente ";
					$ris_abilitato = $pdo->bindAndExec($sql_abilitato,array(":codice_derivazione"=>$record["codice_derivazione"],":codice_utente"=>$_SESSION["codice_utente"]));
					if ($ris_abilitato->rowCount() == 0) {
						$accedi = false;
					}
				}
			}
			$bind = array();
			$bind[":codice_gara"] = $record["codice"];
			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
		} else if ($_GET["sezione"] == "concorsi") {
			$strsql  = "SELECT b_concorsi.* FROM b_concorsi
									WHERE b_concorsi.codice = :codice ";
			$strsql .= "AND b_concorsi.annullata = 'N' ";
			$strsql .= "AND codice_gestore = :codice_ente ";
			$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record = $risultato->fetch(PDO::FETCH_ASSOC);
				$accedi = true;
			}
		} else {
			$table = "";
			if ($_GET["sezione"] == "albo") $table = "albo";
			if ($_GET["sezione"] == "dialogo") $table = "dialogo";
			if ($_GET["sezione"] == "mercato") $table = "mercato";
			if ($_GET["sezione"] == "sda") $table = "sda";
			$strsql = "SELECT * FROM b_bandi_$table WHERE codice = :codice ";
			$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount() > 0) {
				$record = $risultato->fetch(PDO::FETCH_ASSOC);
				$accedi = true;
			}
		}
		if ($accedi) {
			$checked = getSelectedCriteriaFromRequest($_GET["codice_riferimento"],$_GET["sezione"]);
			$version = getVersionFromSelectedCriteria($checked);
			$dgue_translate_gruppi = getDGUETranslateGruppi($version);
			?>
			<h1>DGUE - DOCUMENTO DI GARA UNICO EUROPEO</h1>
			<h2><? echo $record["oggetto"] ?></h2>
			<?
			$bind = array();
			$bind[":codice_riferimento"] = $_GET["codice_riferimento"];
			$bind[":sezione"] = $_GET["sezione"];
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$sql = "SELECT * FROM b_dgue_compilati WHERE codice_riferimento = :codice_riferimento AND sezione = :sezione AND
							codice_utente = :codice_utente";
			$ris_old = $pdo->bindAndExec($sql,$bind);
			if ($ris_old->rowCount() > 0) {
				$compiled = $ris_old->fetchAll(PDO::FETCH_ASSOC)[0];
				$dgue = json_decode($compiled["json"],true);
				if (!empty($compiled["soa"])) $soa = json_decode($compiled["soa"],true);
				if (!empty($compiled["subappalto"])) $subappalto = json_decode($compiled["subappalto"],true);
				if (!empty($compiled["nazionali"])) $nazionali = json_decode($compiled["nazionali"],true);
			} else {
				if (empty($_GET["import"])) {
					?>
						<a class="submit_big" href="<?= $_SERVER['REQUEST_URI'] ?>&import=new" title="Nuova compilazione">
							<span class="fa fa-file fa-3x"></span><br>
							Nuova compilazione
						</a><br>
						<script type="text/javascript" src="/js/resumable.js"></script>
						<script type="text/javascript" src="resumable-uploader.js"></script>

							<form id="import_file" action="<?= $_SERVER['REQUEST_URI'] ?>&import=file" method="post" target="_self">
								<input type="hidden" id="filechunk" name="filechunk">
								<div class="scegli_file"><span class="fa fa-folder-open fa-3x"></span><br>Importa da file...</div>
								<script>
									var uploader = (function($){
									return (new ResumableUploader($('.scegli_file')));
									})(jQuery);
								</script>
								<div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
							</form>
						<div class="clear"></div>
						<?
						$bind = array();
						$bind[":codice_utente"] = $_SESSION["codice_utente"];
						$sql = "SELECT * FROM b_dgue_compilati WHERE codice_utente = :codice_utente ORDER BY timestamp DESC";
						$ris_model = $pdo->bindAndExec($sql,$bind);
						if ($ris_model->rowCount() > 0) {
							?><br>
							<table width="100%" class="elenco">
								<thead>
									<tr>
										<td widt="10%">Identificativo</td>
										<td width="50%">Oggetto</td>
										<td width="20%">Ente</td>
										<td width="15%">Data</td>
										<td width="10">PDF</td>
										<td width="10">XML</td>
										<td width="10"></td>
									</tr>
								</thead>
								<tbody>
									<?
										while($modello = $ris_model->fetch(PDO::FETCH_ASSOC)) {
											$model = json_decode($modello["json"],true);
											?>
											<tr>
												<td><?= $model["cbc:ContractFolderID"]['$'] ?></td>
												<td><?= $model["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:FileName"] ?></td>
												<td><?= $model["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"] ?></td>
												<td><?= mysql2datetime($modello["timestamp"]) ?></td>
												<td><a href="getPDF.php?codice_dgue=<?= $modello["codice"] ?>" title="Anteprima PDF"><span class='fa fa-file-pdf-o fa-2x'></span></a></td>
												<td><a href="getXML.php?codice_dgue=<?= $modello["codice"] ?>" title="Download XML"><span style="color:#066" class='fa fa-code fa-2x'></span></a></td>
												<td><a href="<?= $_SERVER['REQUEST_URI'] ?>&import=model&codice_model=<?= $modello["codice"] ?>" title="Usa modello"><span style="color:#0c0" class='fa fa-check fa-2x'></span></a></td>
											</tr>
											<?
										}
									?>
								</tbody>
							</table>
							<?
						}
						?>
					<?
				} else {
					$dgue = array();

					$sql = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente";
					$ris_operatore = $pdo->bindAndExec($sql,array(":codice_utente"=>$_SESSION["codice_utente"]));
					$operatore = $ris_operatore->fetchAll(PDO::FETCH_ASSOC)[0];

					$dgue["espd-cac:EconomicOperatorParty"]["espd-cbc:SMEIndicator"] = false;
					if ($operatore["n_dipendenti"] < 250) $dgue["espd-cac:EconomicOperatorParty"]["espd-cbc:SMEIndicator"] = true;
					if ($_GET["import"] == "file" && !empty($_POST["filechunk"])) {
						$xml = simplexml_load_file($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);
						$xml = xmlToArray($xml);
						if (!empty($xml["ESPDResponse"])) $dgue = $xml["ESPDResponse"];
					} else if ($_GET["import"] == "model" && !empty($_GET["codice_model"])) {
						$bind = array();
						$bind[":codice_model"] = $_GET["codice_model"];
						$bind[":codice_utente"] = $_SESSION["codice_utente"];
						$sql = "SELECT * FROM b_dgue_compilati WHERE codice = :codice_model AND codice_utente = :codice_utente";
						$ris_old = $pdo->bindAndExec($sql,$bind);
						if ($ris_old->rowCount() > 0) {
							$compiled = $ris_old->fetchAll(PDO::FETCH_ASSOC)[0];
							$dgue = json_decode($compiled["json"],true);
							if (!empty($compiled["soa"])) $soa = json_decode($compiled["soa"],true);
							if (!empty($compiled["subappalto"])) $subappalto = json_decode($compiled["subappalto"],true);
							if (!empty($compiled["nazionali"])) $nazionali = json_decode($compiled["nazionali"],true);
						}
					} else {
						$strsql = "SELECT * FROM b_certificazioni_soa WHERE codice_operatore = :codice";
						$risultato_soa = $pdo->bindAndExec($strsql,array(":codice"=>$operatore["codice"]));
						if ($risultato_soa->rowCount() > 0) {
							$soa["tipo"] = "soa";
							$soa["certificati"] = array();
							$i = 0;
							while($certificato_soa = $risultato_soa->fetch(PDO::FETCH_ASSOC)) {
								$soa["certificati"][$i] = $certificato_soa;
								$soa["certificati"][$i]["importo"] = "";
								$soa["certificati"][$i]["numero"] = "";
								$i++;
							}
						}
					}
					if (isset($dgue)) {
						$dgue["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"] = $_SESSION["ente"]["denominazione"];
						if(! empty($record["struttura_proponente"])) $dgue["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"] .= " - {$record["struttura_proponente"]}";
						$dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:FileName"] = htmlentities($record["oggetto"], ENT_QUOTES, 'UTF-8');
						$dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:Description"] = trim(strip_tags($record["descrizione"]));

						if (!empty($record["cig"])) $dgue["cbc:ContractFolderID"]["$"] = $record["cig"];


						$dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][0]["cbc:ID"] = $operatore["codice_fiscale_impresa"];
						$dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"] = $operatore["ragione_sociale"];
						$dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:Name"] = $_SESSION["record_utente"]["nome"] . " " . $_SESSION["record_utente"]["cognome"];
						$dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:Telephone"] = $_SESSION["record_utente"]["telefono"];
						$dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:ElectronicMail"] = $_SESSION["record_utente"]["pec"];
						$dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:StreetName"] = $operatore["indirizzo_legale"];
						$dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:CityName"] = $operatore["citta_legale"];


						if (empty($dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"])) {
							$strsql = "SELECT * FROM b_rappresentanti WHERE codice_operatore = :codice";
							$risultato_rappresentanti = $pdo->bindAndExec($strsql,array(":codice"=>$operatore["codice"]));
							if ($risultato_rappresentanti->rowCount() > 0) {
								$i_r = 0;
								while($rappresentante = $risultato_rappresentanti->fetch(PDO::FETCH_ASSOC)) {
									$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][$i_r]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:Postbox"] = $rappresentante["cap"];
									$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][$i_r]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:StreetName"] = $rappresentante["indirizzo"];
									$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][$i_r]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:CityName"] = $rappresentante["citta"];
									$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][$i_r]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FirstName"] = $rappresentante["nome"];
									$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][$i_r]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FamilyName"] = $rappresentante["cognome"];
									$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][$i_r]["espd-cbc:NaturalPersonRoleDescription"] = $rappresentante["qualita"];
									if (!empty($rappresentante["codice_fiscale"])) {
										$data_nascita = dateFromCF($rappresentante["codice_fiscale"]);
										$citta = cityFromCF($rappresentante["codice_fiscale"]);
										$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][$i_r]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthDate"] = $data_nascita;
										$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][$i_r]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthplaceName"] = $citta;
									}
									$i_r++;
								}
							} else {
								$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][0]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:Contact"]["cbc:Telephone"] = $_SESSION["record_utente"]["telefono"];
								$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][0]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:Contact"]["cbc:ElectronicMail"] = $_SESSION["record_utente"]["email"];
								$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][0]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:Postbox"] = $_SESSION["record_utente"]["cap"];
								$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][0]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:StreetName"] = $_SESSION["record_utente"]["indirizzo"];
								$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][0]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cac:ResidenceAddress"]["cbc:CityName"] = $_SESSION["record_utente"]["citta"];
								$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][0]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FirstName"] = $_SESSION["record_utente"]["nome"];
								$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][0]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:FamilyName"] = $_SESSION["record_utente"]["cognome"];
								$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][0]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthDate"] = $_SESSION["record_utente"]["dnascita"];
								$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][0]["cac:PowerOfAttorney"]["cac:AgentParty"]["cac:Person"]["cbc:BirthplaceName"] = $_SESSION["record_utente"]["luogo"];
								$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"][0]["espd-cbc:NaturalPersonRoleDescription"] = $operatore["ruolo_referente"];
							}
						}

						if (count($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"]) > 1) {
							$i = 0;
							foreach($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"] AS $PartyIdentification) {
								if (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][$i]["cbc:ID"])) $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][$i]["cbc:ID"] = $PartyIdentification["cbc:ID"];
								$i++;
							}
						}
					} else {
						?>
						<h1>Errore nell'importazione - Si prega di contattare l'Help Desk Tecnico</h1>
						<?
					}
				}
			}

			if (isset($dgue)) {
				if(!empty($dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"])) {
					$check_array = array_keys($dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"]);
					$create_array = true;
					foreach($check_array AS $chiave) {
						if (is_numeric($chiave)) $create_array = false;
					}
					if ($create_array) {
						$tmp = $dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"];
						$dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"] = array($tmp);
					}
				}
				$ris = getDGUERequestedCriteria($codice_riferimento,$_GET["sezione"]);
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
				if (!empty($ris)) {
					$sezioni = array("parteI"=>"Parte I - Procedura");
					$forms = $ris;
					foreach ($forms AS $form) {
						if (empty($sezioni[$form["livello1"]])) {
							$sezioni[$form["livello1"]] = $dgue_translate_gruppi[$form["livello1"]]['it'];
						}
					}
					$sezioni["parteV"]="Dichiarazioni Finali";

				?>
				<form name="box" method="post" action="save.php" rel="validate" target="_self">
					<input type="hidden" name="codice_riferimento" value="<?= $record["codice"]; ?>">
					<input type="hidden" name="sezione" value="<?= $_GET["sezione"]; ?>">
						<div id="tabs" class="dgue_edit">
							<ul>
								<?
								foreach($sezioni AS $id => $sezione) {
									?>
									<li><a href="#<?= $id ?>"><?= $sezione ?></a></li>
									<?
								}
								?>
							</ul>
							<?
							foreach($sezioni AS $id => $sezione) {
								?>
								<div id="<?= $id ?>">
									<h2><?= $sezione ?></h2>
									<?
										if ($id == "parteI") {
										?>
											<table width="100%">
												<tr>
													<td width="50%">
														Denominazione committente:
													</td>
													<td width="50%">
														<input type="text" rel="S;3;0;A" title="Denominazione committente" name="espd[cac:ContractingParty][cac:Party][cac:PartyName][cbc:Name]" id="cac_ContractingParty_cac_Party_cac_PartyName_cbc_Name" value="<?= (!empty($dgue["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"])) ? $dgue["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"] : "" ?>" class="dgue_input">
													</td>
												</tr>
												<tr>
													<td width="50%">
														Procedura:
													</td>
													<td width="50%">
														<input type="text" rel="S;3;0;A" title="Procedura" name="espd[cac:AdditionalDocumentReference][1][cac:Attachment][cac:ExternalReference][cbc:FileName]" id="cac_AdditionalDocumentReference_1_cac_Attachment_cac_ExternalReference_cbc_FileName" value="<?= (!empty($dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:FileName"])) ? strip_tags($dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:FileName"]) : "" ?>" class="dgue_input">
													</td>
												</tr>
												<tr>
													<td width="50%">
														Descrizione:
													</td>
													<td width="50%">
														<textarea rel="S;3;0;A" title="Descrizione" name="espd[cac:AdditionalDocumentReference][1][cac:Attachment][cac:ExternalReference][cbc:Description]" id="cac_AdditionalDocumentReference_1_cac_Attachment_cac_ExternalReference_cbc_Description" class="dgue_input " rows="5"><?= (!empty($dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:Description"])) ? strip_tags($dgue["cac:AdditionalDocumentReference"][1]["cac:Attachment"]["cac:ExternalReference"]["cbc:Description"]) : "" ?></textarea>
													</td>
												</tr>
												<tr>
													<td width="50%">
														Numero di riferimento:
													</td>
													<td width="50%">
														<input type="text" rel="N;1;0;A" title="Numero di riferimento" name="espd[cbc:ContractFolderID][$]" id="cbc_ContractFolderID_$" value="<?= (!empty($dgue["cbc:ContractFolderID"]["$"])) ? $dgue["cbc:ContractFolderID"]["$"] : "" ?>" class="dgue_input">
													</td>
												</tr>
											</table>
										<?
										} else if ($id== "parteV") {
											?>
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
											</table>
											<?
										} else if ($id == "OTHER") {
											?>
											<h3 style="text-align:center">A: informazioni sull'operatore economico</h3>
											<table width="100%">
												<tr>
													<td class="etichetta">Nome/denominazione:</td>
													<td>
														<input title="Nome/denominazione" type="text" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][cac:Party][cac:PartyName][cbc:Name]" id="espd-cac_EconomicOperatorParty_cac_Party_cac_PartyName_cbc_Name" value="<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"] : "" ?>" class="dgue_input">
													</td>
													<td class="etichetta">E-mail:</td>
													<td>
														<input title="E-mail" type="text" rel="S;3;0;E" name="espd[espd-cac:EconomicOperatorParty][cac:Party][cac:Contact][cbc:ElectronicMail]" id="espd-cac_EconomicOperatorParty_cac_Party_cac_Contact_cbc_ElectronicMail" value="<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:ElectronicMail"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:ElectronicMail"] : "" ?>"  class="dgue_input">
													</td>
												</tr>
												<tr>
													<td class="etichetta">Via e numero civico:</td>
													<td>
														<input type="text" title="Via e numero civico" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][cac:Party][cac:PostalAddress][cbc:StreetName]" id="espd-cac_EconomicOperatorParty_cac_Party_cac_PostalAddress_cbc_StreetName" value="<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:StreetName"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:StreetName"] : "" ?>" class="dgue_input">
													</td>
													<td class="etichetta">Telefono:</td>
													<td>
														<input type="text" title="Telefono" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][cac:Party][cac:Contact][cbc:Telephone]" id="espd-cac_EconomicOperatorParty_cac_Party_cac_Contact_cbc_Telephone" value="<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:Telephone"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:Telephone"] : "" ?>"  class="dgue_input">
													</td>
												</tr>
												<tr>
													<td class="etichetta">CAP:</td>
													<td>
														<input type="text" title="CAP" rel="S;1;0;A" name="espd[espd-cac:EconomicOperatorParty][cac:Party][cac:PostalAddress][cbc:Postbox]" id="espd-cac_EconomicOperatorParty_cac_Party_cac_PostalAddress_cbc_Postbox" value="<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:Postbox"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:Postbox"] : "" ?>" class="dgue_input">
													</td>
													<td class="etichetta">Referente:</td>
													<td>
														<input type="text" title="Referente" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][cac:Party][cac:Contact][cbc:Name]" id="espd-cac_EconomicOperatorParty_cac_Party_cac_Contact_cbc_Name" value="<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:Name"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:Contact"]["cbc:Name"] : "" ?>"  class="dgue_input">
													</td>
												</tr>
												<tr>
													<td class="etichetta">Citt&agrave;:</td>
													<td>
														<input type="text" title="Citta" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][cac:Party][cac:PostalAddress][cbc:CityName]" id="espd-cac_EconomicOperatorParty_cac_Party_cac_PostalAddress_cbc_CityName" value="<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:CityName"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cbc:CityName"] : "" ?>" class="dgue_input">
													</td>
													<td class="etichetta">Partita IVA:</td>
													<td>
														<input type="text" title="Partita IVA" rel="S;3;0;A" name="espd[espd-cac:EconomicOperatorParty][cac:Party][cac:PartyIdentification][0][cbc:ID]" id="espd-cac_EconomicOperatorParty_cac_Party_cac_PartyIdentification_0_cbc_ID" value="<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][0]["cbc:ID"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][0]["cbc:ID"] : "" ?>" class="dgue_input">
													</td>
												</tr>
												<tr>
													<td class="etichetta">Paese:</td>
													<td>
														<select rel="S;1;0;A" title="Paese"  name="espd[espd-cac:EconomicOperatorParty][cac:Party][cac:PostalAddress][cac:Country][cbc:IdentificationCode][$]"
														id="espd-cac_EconomicOperatorParty_cac_Party_cac_PostalAddress_cac_Country_cbc_IdentificationCode"
														 class="dgue_input">
													    <option value="" selected="selected">---</option>
													    <optgroup label="EU">
														    <option value="AT">Austria</option>
																<option value="BE">Belgio</option>
																<option value="BG">Bulgaria</option>
																<option value="CY">Cipro</option>
																<option value="HR">Croazia</option>
																<option value="DK">Danimarca</option>
																<option value="EE">Estonia</option>
																<option value="FI">Finlandia</option>
																<option value="FR">Francia</option>
																<option value="DE">Germania</option>
																<option value="GR">Grecia</option>
																<option value="IE">Irlanda</option>
																<option value="IT">Italia</option>
																<option value="LV">Lettonia</option>
																<option value="LT">Lituania</option>
																<option value="LU">Lussemburgo</option>
																<option value="MT">Malta</option>
																<option value="NL">Paesi Bassi</option>
																<option value="PL">Polonia</option>
																<option value="PT">Portogallo</option>
																<option value="GB">Regno Unito</option>
																<option value="CZ">Repubblica ceca</option>
																<option value="RO">Romania</option>
																<option value="SK">Slovacchia</option>
																<option value="SI">Slovenia</option>
																<option value="ES">Spagna</option>
																<option value="SE">Svezia</option>
																<option value="HU">Ungheria</option>
															</optgroup>
													    <optgroup label="EFTA">
													      <option value="NO">Norvegia</option>
																<option value="CH">Svizzera</option>
															</optgroup>
															</select>
															<? if (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]['$'])) {
																?>
																<script>
																	$("#espd-cac_EconomicOperatorParty_cac_Party_cac_PostalAddress_cac_Country_cbc_IdentificationCode").val("<?= $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]['$'] ?>");
																</script>
																<?
															}
															?>
													</td>

													<td class="etichetta">Se non &egrave; applicabile un numero di partita IVA indicare un altro numero di identificazione nazionale, se richiesto e applicabile:</td>
													<td>
														<input type="text" title="Altro numero di identificazione" rel="N;3;0;A" name="espd[espd-cac:EconomicOperatorParty][cac:Party][cac:PartyIdentification][1][cbc:ID]" id="espd-cac_EconomicOperatorParty_cac_Party_cac_PartyIdentification_1_cbc_ID" value="<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][1]["cbc:ID"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cac:PartyIdentification"][1]["cbc:ID"] : "" ?>"  class="dgue_input">
													</td>
												</tr>
												<tr>
													<td class="etichetta">Website:</td>
													<td colspan="3">
														<input title="Website" type="text" rel="N;3;0;L" name="espd[espd-cac:EconomicOperatorParty][cac:Party][cbc:WebsiteURI]" id="espd-cac_EconomicOperatorParty_cac_Party_cbc_WebsiteURI" value="<?= (!empty($dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cbc:WebsiteURI"])) ? $dgue["espd-cac:EconomicOperatorParty"]["cac:Party"]["cbc:WebsiteURI"] : "" ?>"  class="dgue_input">
													</td>
												</tr>
												<tr>
													<td colspan="3">
														L'operatore economico &egrave; una microimpresa, oppure una piccola o media impresa?
													</td>
													<td>
														<label for="espd[espd-cac:EconomicOperatorParty][espd-cbc:SMEIndicator]">Si</label>
														<input type="radio" value="true" name="espd[espd-cac:EconomicOperatorParty][espd-cbc:SMEIndicator]"
														id="espd-cac_EconomicOperatorParty_espd-cbc_SMEIndicator"
														<? if (!empty($dgue["espd-cac:EconomicOperatorParty"]["espd-cbc:SMEIndicator"]) && $dgue["espd-cac:EconomicOperatorParty"]["espd-cbc:SMEIndicator"]=="true") echo "checked='checked'"; ?>
														>
														<label for="espd[espd-cac:EconomicOperatorParty][espd-cbc:SMEIndicator]">No</label>
														<input type="radio" value="false" name="espd[espd-cac:EconomicOperatorParty][espd-cbc:SMEIndicator]"
														id="espd-cac_EconomicOperatorParty_espd-cbc_SMEIndicator"
														<? if (empty($dgue["espd-cac:EconomicOperatorParty"]["espd-cbc:SMEIndicator"]) || $dgue["espd-cac:EconomicOperatorParty"]["espd-cbc:SMEIndicator"]=="false") echo "checked='checked'"; ?>
														>
													</td>
												</tr>
											</table>
											<h3 style="text-align:center">B: Informazioni sui rappresentanti dell'operatore economico
												<? if ($version == "2023-36") { ?>
													<br>Soggetti di cui all'art. 94 c.3 del D.Lgs 36/2023
												<? } ?>
											</h3>
											<table width="100%">
												<tr>
													<td colspan="4" id="rappresentanti">
														<?
															foreach($dgue["espd-cac:EconomicOperatorParty"]["espd-cac:RepresentativeNaturalPerson"] AS $id_rappresentante => $rappresentante) {
																include("rappresentanti_legali.php");
															}
														?>
													</td>
												</tr>
												<tr>
													<td colspan="4">
														<button style="font-size:14px;"
															class="aggiungi"
															onClick="aggiungi('rappresentanti_legali.php','#rappresentanti');return false;">
																<img src="/img/add.png" alt="Aggiungi rappresentante"> <strong>Aggiungi rappresentante</strong>
															</button>
													</td>
												</tr>
												<tr>
													<td colspan="2">
														Se applicabile, indicare il lotto o i lotti per i quali si intende presentare offerta:
													</td>
													<td colspan="2">
														<input type="text" rel="N;3;0;A" title="Lotti interessati" name="espd[cac:ProcurementProjectLot][cbc:ID]" id="cac_ProcurementProjectLot_cbc_ID" value="<?= (!empty($dgue["cac:ProcurementProjectLot"]["cbc:ID"])) ? $dgue["cac:ProcurementProjectLot"]["cbc:ID"] : "" ?>" class="dgue_input">
													</td>
												</tr>
											</table>
											<?
											writeForm($id);
										} else {
											writeForm($id);
										}
									?>
									<div class="clear"></div>
									<a class="precedente" style="float:left" href="#">Step precedente</a>
									<a class="successivo" style="float:right" href="#">Step successivo</a>
									<div class="clear"></div>
								</div>
								<?
							}
							?>
						</div>
						<input type="submit" class="submit_big" value="Salva">
						</form>
						<script>
							$("#tabs").tabs();
							$(".precedente").each(function() {
							var id_parent = $("#tabs").children("div").index($(this).parent("div"));
							if (id_parent == 0) {
							$(this).remove();
							} else {
							var target = id_parent - 1;
							$(this).click(function() { $('#tabs').tabs('option','active',target) });
							}
							});

							$(".successivo").each(function() {
							var id_parent = $("#tabs").children("div").index($(this).parent("div"));
							if (id_parent == ($("#tabs").children("div").length - 1)) {
							$(this).remove();
							} else {
							var target = id_parent + 1;
							$(this).click(function() { $('#tabs').tabs('option','active',target) });
							}
							});
							<?
								if (!empty($dgue["ccv:Criterion"]["7e7db838-eeac-46d9-ab39-42927486f22d"]["ccv:RequirementGroup"]["ccv:Requirement"]["ccv:Response"]["ccv-cbc:Indicator"])) {
									if ($dgue["ccv:Criterion"]["7e7db838-eeac-46d9-ab39-42927486f22d"]["ccv:RequirementGroup"]["ccv:Requirement"]["ccv:Response"]["ccv-cbc:Indicator"]=="true") {
										?>
										$(".SELECTION").slideUp();
										$(".ALL_SATISFIED").slideDown();
										<?
									} else {
										?>
										$(".SELECTION").slideDown();
										<?
									}
								}
							?>

								function show_hide(element) {
									if (typeof element.data('hide') !== 'undefined') $(element.data('hide')).slideUp('fast');
									if (typeof element.data('show') !== 'undefined') $(element.data('show')).slideDown('fast');
								}
						</script>
						<?
					}
				}
				$href = $_GET["sezione"];
				switch($_GET["sezione"]) {
					case "dialogo": $href = "dialogo_competitivo"; break;
					case "albo": $href = "albo_fornitori"; break;
					case "mercato": $href = "mercato_elettronico"; break;
				}
				?>
				<a href="/<?= $href ?>/id<?= $_GET["codice_riferimento"] ?>-dettaglio" class="ritorna_button submit_big" style="background-color:#999;">Ritorna al bando</a>
				<?
		} else {
			echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
		}
	} else {
		echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
