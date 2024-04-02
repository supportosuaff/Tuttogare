<?
	@session_start();
	include_once '../../config.php';
	include_once $root . '/inc/funzioni.php';
	include_once "tedesender.class.php";

	$_SESSION["guue"]["numero_del_form"] = "";

	if(empty($_SESSION["guue"]["post_form"])) {
		if(!empty($_SESSION["guue"]["codice_gara"])) {
			$guue = array();
			$record_gara = $_SESSION["guue"]["gara"];

			$replaced_key = str_replace(array('[',']'), array("", "_"), "[CONTRACTING_BODY][ADDRESS_CONTRACTING_BODY]");
			$guue[$replaced_key]["OFFICIALNAME"] = $_SESSION["ente"]["denominazione"];
			$guue[$replaced_key]["NATIONALID"] = "";
			$guue[$replaced_key]["ADDRESS"] = $_SESSION["ente"]["indirizzo"];
			$guue[$replaced_key]["TOWN"] = $_SESSION["ente"]["citta"];
			$guue[$replaced_key]["NUTS"]["ATTRIBUTE"]["CODE"] = "";
			$guue[$replaced_key]["POSTAL_CODE"] = $_SESSION["ente"]["cap"];
			$guue[$replaced_key]["CONTACT_POINT"] = "";
			$guue[$replaced_key]["PHONE"] = $_SESSION["ente"]["telefono"];
			$guue[$replaced_key]["E_MAIL"] = getIndirizzoConferma($record_gara["codice_pec"]);
			$guue[$replaced_key]["FAX"] = $_SESSION["ente"]["fax"];
			$guue[$replaced_key]["URL_GENERAL"] = $_SESSION["ente"]["url"];
			$guue[$replaced_key]["URL_BUYER"] = $_SESSION["ente"]["url"];
			$guue[$replaced_key]["NUTS"]["ATTRIBUTE"]["CODE"] = $record_gara["nuts"];

			// $guue["CONTRACTING_BODY"]["JOINT_PROCUREMENT_INVOLVED"] = "";
			// $guue["CONTRACTING_BODY"]["PROCUREMENT_LAW"] = "";
			// $guue["CONTRACTING_BODY"]["CENTRAL_PURCHASING"] = "";

			$guue["CONTRACTING_BODY"]["radio_as_select_for_document_url_opt"] = "DOCUMENT_FULL";
			$guue["CONTRACTING_BODY"]["URL_DOCUMENT"] = "https://".$_SERVER["SERVER_NAME"]."/gare/id".$_SESSION["guue"]["codice_gara"]."-dettagli";
			$guue["CONTRACTING_BODY"]["radio_as_select_for_information"] = "ADDRESS_FURTHER_INFO_IDEM";

			$guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"] = "ADDRESS_PARTICIPATION_IDEM";
			$sql = "SELECT online FROM b_modalita WHERE codice = :codice_modalita";
			$ris = $pdo->bindAndExec($sql, array(':codice_modalita' => $record_gara["modalita"]));
			if($ris->rowCount() > 0) {
				$rec = $ris->fetch(PDO::FETCH_ASSOC);
				if($rec["online"] == "S") {
					$guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"] = "URL_PARTICIPATION_ITEM_TO_IGNORE";
					$guue["CONTRACTING_BODY"]["URL_PARTICIPATION"] = "https://".$_SERVER["SERVER_NAME"]."/gare/id".$_SESSION["guue"]["codice_gara"]."-dettagli";
				}
			}

			$sql_tipologia = "SELECT esender FROM b_tipologie_ente WHERE codice = :tipologia_ente";
			$ris_tipologia = $pdo->bindAndExec($sql_tipologia,array(":tipologia_ente"=>$_SESSION["ente"]["tipologia_ente"]));
			if ($ris_tipologia->rowCount() > 0) {
				$guue["CONTRACTING_BODY"]["CA_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_type"] = $ris_tipologia->fetch(PDO::FETCH_ASSOC)["esender"];
			}
			// $guue["CONTRACTING_BODY"]["CA_TYPE_OTHER"] = "";

			$guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] = $_SESSION["ente"]["tipo_attivita"];
			// $guue["CONTRACTING_BODY"]["CA_ACTIVITY_OTHER"] = "";

			$sql = "SELECT * FROM r_cpv_gare WHERE codice_gara = :codice_gara";
			$ris = $pdo->bindAndExec($sql, array(':codice_gara' => $record_gara["codice"]));
			if($ris->rowCount() > 0) {
				$guue["supplementary_cpv"] = "";
				while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
					$guue["supplementary_cpv"] .= ";".$rec["codice"];
				}
			}

			$guue["OBJECT_CONTRACT"]["TITLE"] = $record_gara["oggetto"];
			$guue["OBJECT_CONTRACT"]["REFERENCE_NUMBER"] = $record_gara["id"];

			$sql = "SELECT esender FROM b_tipologie WHERE b_tipologie.codice = :codice";
			$ris = $pdo->bindAndExec($sql, array(':codice' => $record_gara["tipologia"]));
			if($ris->rowCount() > 0) {
				$rec = $ris->fetch(PDO::FETCH_ASSOC);
				$guue["OBJECT_CONTRACT"]["TYPE_CONTRACT"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_type_contract"] = $rec["esender"];
			}

			$guue["OBJECT_CONTRACT"]["SHORT_DESCR"] = $record_gara["descrizione"];

			$guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["val"] = number_format($record_gara["prezzoBase"], 2, '.', '');
			$guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] = "EUR";

			$sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara";
			$ris = $pdo->bindAndExec($sql, array(':codice_gara' => $record_gara["codice"]));
			if($ris->rowCount() > 0) {
				$guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] = "LOT_DIVISION_ITEM_TO_IGNORE";
			// $guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["radio_as_select_for_lot_numbers"] = "";
				$j = 1;
				while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["TITLE"] = $rec["oggetto"];
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["NUTS"]["ATTRIBUTE"]["CODE"] = $record_gara["nuts"];
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["CPV_ADDITIONAL"]["CPV_CODE"]["ATTRIBUTE"]["CODE"] = $rec["cpv"];
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["SHORT_DESCR"] = $rec["descrizione"];
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["radio_as_select_for_award_criteria_doc"] = "AC_PROCUREMENT_DOC";
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["VAL_OBJECT"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] = "EUR";
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["VAL_OBJECT"]["val"] = number_format($rec["importo_base"], 2, '.', '');
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["DURATION"]["ATTRIBUTE"]["TYPE"]["radio_as_select_for_type_of_duration"] = $rec["unita_durata"] == "gg" ? "DAY" : "MONTH";
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["DURATION"]["val"] = $record_gara["durata"];
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["radio_as_select_for_eu_union_funds"] = "NO_EU_PROGR_RELATED";
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["MAIN_SITE"] = $_SESSION["ente"]["citta"];
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["radio_as_select_for_variants"] = "NO_ACCEPTED_VARIANTS";
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j]["INFO_ADD"] = $rec["ulteriori_informazioni"];
					$j++;
				}
			} else {
				$guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] = "NO_LOT_DIVISION";
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["TITLE"] = $record_gara["oggetto"];
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["NUTS"]["ATTRIBUTE"]["CODE"] = $record_gara["nuts"];
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["SHORT_DESCR"] = $record_gara["descrizione"];
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["radio_as_select_for_award_criteria_doc"] = "AC_PROCUREMENT_DOC";
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["VAL_OBJECT"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] = "EUR";
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["VAL_OBJECT"]["val"] = number_format($record_gara["prezzoBase"], 2, '.', '');
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["DURATION"]["ATTRIBUTE"]["TYPE"]["radio_as_select_for_type_of_duration"] = $record_gara["unita_durata"] == "gg" ? "DAY" : "MONTH";
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["DURATION"]["val"] = $record_gara["durata"];
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["radio_as_select_for_eu_union_funds"] = "NO_EU_PROGR_RELATED";
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["MAIN_SITE"] = $_SESSION["ente"]["citta"];
				$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_1"]["radio_as_select_for_variants"] = "NO_ACCEPTED_VARIANTS";
			}

			$guue["LEFTI"]["radio_as_select_for_economic_criteria"] = "ECONOMIC_CRITERIA_DOC";
			$guue["LEFTI"]["radio_as_select_for_tecnical_criteria"] = "TECHNICAL_CRITERIA_DOC";

			$sql = "SELECT esender FROM b_procedure WHERE codice = :codice";
			$ris = $pdo->bindAndExec($sql, array(':codice' => $record_gara["procedura"]));
			if($ris->rowCount() > 0) {
				$rec = $ris->fetch(PDO::FETCH_ASSOC);
				if(!empty($rec["esender"])) {
					$guue["PROCEDURE"]["radio_as_select_for_procedure_type"] = $rec["esender"];
				}
			}

			$guue["PROCEDURE"]["DATE_RECEIPT_TENDERS"] = mysql2date($record_gara["data_scadenza"]);
			$guue["PROCEDURE"]["TIME_RECEIPT_TENDERS"] = substr($record_gara["data_scadenza"], 11, 5);
			$guue["PROCEDURE"]["OPENING_CONDITION"]["DATE_OPENING_TENDERS"] = mysql2date($record_gara["data_apertura"]);
			$guue["PROCEDURE"]["OPENING_CONDITION"]["TIME_OPENING_TENDERS"] = substr($record_gara["data_apertura"], 11, 5);
			$guue["PROCEDURE"]["OPENING_CONDITION"]["PLACE"] = $_SESSION["ente"]["citta"];

			$sql = "SELECT * FROM b_aste WHERE codice_gara = :codice_gara";
			$ris = $pdo->bindAndExec($sql, array(':codice_gara' => $record_gara["codice"]));
			if($ris->rowCount() > 0) {
				$rec = $ris->fetch(PDO::FETCH_ASSOC);
				$guue["PROCEDURE"]["EAUCTION_USED"] = TRUE;
				$guue["PROCEDURE"]["EAUCTION_USED"] = "Data e ora di inizio: " . mysql2datetime($rec["data_inizio"]) . "<br>";
				$guue["PROCEDURE"]["EAUCTION_USED"] = "Data e ora di fine: " . mysql2datetime($rec["data_fine"]) . "<br>";
				$guue["PROCEDURE"]["EAUCTION_USED"] = "Tempo Base: " . mysql2datetime($rec["tempo_base"]) . "<br>";
				$guue["PROCEDURE"]["EAUCTION_USED"] = "Rilancio minimo: " . mysql2datetime($rec["rilancio_minimo"]) . "<br>";
			}

			$sql = "SELECT * FROM b_organismi_ricorso WHERE codice_ente = :codice";
			$ris = $pdo->bindAndExec($sql, array(':codice' => $record_gara["codice_ente"]));
			if($ris->rowCount() > 0) {
				$rec = $ris->fetch(PDO::FETCH_ASSOC);
				$replaced_key = str_replace(array('[',']'), array("", "_"), "[COMPLEMENTARY_INFO][ADDRESS_REVIEW_BODY]");
				$guue[$replaced_key]["OFFICIALNAME"] = $rec["denominazione"];
				$guue[$replaced_key]["ADDRESS"] = $rec["indirizzo"];
				$guue[$replaced_key]["TOWN"] = $rec["citta"];
				$guue[$replaced_key]["POSTAL_CODE"] = $rec["cap"];
				$guue[$replaced_key]["PHONE"] = $rec["telefono"];
				$guue[$replaced_key]["E_MAIL"] = $rec["pec"];
				$guue[$replaced_key]["FAX"] = $rec["fax"];
				$guue[$replaced_key]["URL"] = $rec["url"];

			}

			$sql_opzioni  = "SELECT b_opzioni.guue as esender_tag, b_gruppi_opzioni.guue as main_esender_tag ";
			$sql_opzioni .= "FROM b_opzioni_selezionate ";
			$sql_opzioni .= "JOIN b_opzioni ON b_opzioni.codice = b_opzioni_selezionate.opzione ";
			$sql_opzioni .= "JOIN b_gruppi_opzioni ON b_gruppi_opzioni.codice = b_opzioni.codice_gruppo ";
			$sql_opzioni .= "WHERE b_opzioni_selezionate.codice_gara = :codice_gara ";
			$sql_opzioni .= "AND b_opzioni.guue IS NOT NULL ";
			$sql_opzioni .= "AND b_opzioni.guue <> '' ";

			$ris_opzioni = $pdo->bindAndExec($sql_opzioni, array(':codice_gara' => $_SESSION["guue"]["codice_gara"]));

			if($ris_opzioni->rowCount() > 0) {
				while ($rec_opzioni = $ris_opzioni->fetch(PDO::FETCH_ASSOC)) {
					$tags_key = explode('::', $rec_opzioni["esender_tag"]);
					$temp = &$guue;
					foreach ($tags_key as $key) {
						if(empty($temp[$key])) {
							$temp[$key] = array();
						}
						$temp = &$temp[$key];
					}
					$temp = "on";
				}
			}

			$sql_documentale = "SELECT corpo FROM b_documentale WHERE codice_gara = :codice_gara AND codice_ente = :codice_ente AND tipo = 'bando' AND attivo = 'S'";
			$ris_documentale = $pdo->bindAndExec($sql_documentale, array(':codice_gara' => $_SESSION["guue"]["codice_gara"], ':codice_ente' => $record_gara["codice_ente"]));
			if($ris_documentale->rowCount() > 0) {
				$rec_documentale = $ris_documentale->fetch(PDO::FETCH_ASSOC);
				preg_match_all('/(<!--\sINIZIO\s(.*?)\s-->)(([\s\S]*?))(<!--\sFINE\s(.*?)\s-->)/', $rec_documentale["corpo"], $tags);
				foreach ($tags[0] as $n => $tag) {
					$esender_tag = $tags[2][$n];
					$esender_tag = explode('----', $esender_tag);
					$temp = &$guue;
					foreach ($esender_tag as $key) {
						if(empty($temp[$key])) {
							$temp[$key] = array();
						}
						$temp = &$temp[$key];
					}
					$temp = $tag;
				}
			}

			// $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["val"];
			// $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["LOW"];
			// $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["HIGH"];
			// $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"];


			// $guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_MAX_NUMBER"] = "";
			// $guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_MAX_ONE_TENDERER"] = "";
			// $guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_COMBINING_CONTRACT_RIGHT"] = "";

			// $guue["OBJECT_CONTRACT"]["DATE_PUBLICATION_NOTICE"] = "";

			// $guue["LEFTI"]["SUITABILITY"] = "";


			// $guue["LEFTI"]["ECONOMIC_FINANCIAL_INFO"] = "";
			// $guue["LEFTI"]["ECONOMIC_FINANCIAL_MIN_LEVEL"] = "";
			// $guue["LEFTI"]["radio_as_select_for_tecnical_criteria"] = "";
			// $guue["LEFTI"]["TECHNICAL_PROFESSIONAL_INFO"] = "";
			// $guue["LEFTI"]["TECHNICAL_PROFESSIONAL_INFO"] = "";
			// $guue["LEFTI"]["RESTRICTED_SHELTERED_WORKSHOP"] = "";
			// $guue["LEFTI"]["RESTRICTED_SHELTERED_PROGRAM"] = "";
			// $guue["LEFTI"]["PARTICULAR_PROFESSION"] = "";
			// $guue["LEFTI"]["REFERENCE_TO_LAW"] = "";
			// $guue["LEFTI"]["PERFORMANCE_CONDITIONS"] = "";
			// $guue["LEFTI"]["PERFORMANCE_STAFF_QUALIFICATION"] = "";


			// $guue["PROCEDURE"]["FRAMEWORK"] = "";
			// $guue["PROCEDURE"]["radio_as_select_for_operators_number"] = "";
			// $guue["PROCEDURE"]["NB_PARTICIPANTS"] = "";
			// $guue["PROCEDURE"]["JUSTIFICATION"] = "";
			// $guue["PROCEDURE"]["EAUCTION_USED"] = "";
			// $guue["PROCEDURE"]["INFO_ADD_EAUCTION"] = "";
			// $guue["PROCEDURE"]["radio_as_select_for_public_agreement"] = "";
			// $guue["PROCEDURE"]["DATE_RECEIPT_TENDERS"] = "";
			// $guue["PROCEDURE"]["TIME_RECEIPT_TENDERS"] = "";
			// $guue["PROCEDURE"]["DATE_AWARD_SCHEDULED"] = "";

			// $guue["COMPLEMENTARY_INFO"]["EORDERING"] = "";
			// $guue["COMPLEMENTARY_INFO"]["EINVOICING"] = "";
			// $guue["COMPLEMENTARY_INFO"]["EPAYMENT"] = "";
			// $guue["COMPLEMENTARY_INFO"]["INFO_ADD"] = "";
			// $guue["COMPLEMENTARY_INFO"]["REVIEW_PROCEDURE"] = "";
		} else {
			$guue = array();
			$replaced_key = str_replace(array('[',']'), array("", "_"), "[CONTRACTING_BODY][ADDRESS_CONTRACTING_BODY]");
			$guue[$replaced_key]["OFFICIALNAME"] = $_SESSION["ente"]["denominazione"];
			$guue[$replaced_key]["NATIONALID"] = "";
			$guue[$replaced_key]["ADDRESS"] = $_SESSION["ente"]["indirizzo"];
			$guue[$replaced_key]["TOWN"] = $_SESSION["ente"]["citta"];
			$guue[$replaced_key]["NUTS"]["ATTRIBUTE"]["CODE"] = "";
			$guue[$replaced_key]["POSTAL_CODE"] = $_SESSION["ente"]["cap"];
			$guue[$replaced_key]["CONTACT_POINT"] = "";
			$guue[$replaced_key]["PHONE"] = $_SESSION["ente"]["telefono"];
			$guue[$replaced_key]["E_MAIL"] = getIndirizzoConferma($record_gara["codice_pec"]);;
			$guue[$replaced_key]["FAX"] = $_SESSION["ente"]["fax"];
			$guue[$replaced_key]["URL_GENERAL"] = $_SESSION["ente"]["url"];
			$guue[$replaced_key]["URL_BUYER"] = $_SESSION["ente"]["url"];
		}
	} else {
		$guue = $_SESSION["guue"]["post_form"];
		if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"])) {
			$j = 1;
			$tmp_object_contract_object_descr = $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"];
			$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"] = array();
			foreach ($tmp_object_contract_object_descr as $key_lot => $lot) {
				if(strpos($key_lot,"ITEM_") !== FALSE) {
					$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$j] = $lot;
					$j++;
				}
			}
		}
		if(!empty($guue["AWARD_CONTRACT"])) {
			$j = 1;
			$tmp_award_contract = $guue["AWARD_CONTRACT"];
			$guue["AWARD_CONTRACT"] = array();
			foreach ($tmp_award_contract as $key_lot => $lot) {
				$guue["AWARD_CONTRACT"]["ITEM_".$j] = $lot;
				$j++;
			}
		}
	}
	$titolo_pubblicazione = !empty($_SESSION["guue"]["titolo_pubblicazione"]) ? $_SESSION["guue"]["titolo_pubblicazione"] : "";
	$codice_pubblicazione = !empty($_SESSION["guue"]["codice_pubblicazione"]) ? $_SESSION["guue"]["codice_pubblicazione"] : 0;
	$codice_gara = !empty($_SESSION["guue"]["codice_gara"]) ? $_SESSION["guue"]["codice_gara"] : null;
	$v_form = !empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : "2_0_9";
?>
<div class="comandi">
	<input type="image" src="/img/save.png" title="Salva Bozza" placeholder="Salva Bozza" onclick="salvabozza()">
</div>
<table style="width: 100%">
	<tbody>
		<tr class="even">
			<td class="etichetta"><label>Titolo Pubblicazione:</label></td>
			<td>
				<input type="text" name="titolo_pubblicazione" rel="S;5;0;A" value="<?= $titolo_pubblicazione ?>" title="Titolo pubblicazione">
				<i>(Necessario per l&#39;identificare di questo form nella piattaforma in fasi successive)</i>
			</td>
		</tr>
		<?
		if(!empty($_SESSION["guue"]["id_pubblicazione"])) {
			$tedesender = new TedEsender();
			$errori = json_decode(file_get_contents($root.'/guue/errori.json'), TRUE);
			echo '<tr><td colspan="2" class="etichetta"><h1 style="color: #FF0000;"><i class="fa fa-times-circle"></i> Validazione Form</h1>';
			try {
				$errors = array();
				$notice = $tedesender->getNoticeInfo($_SESSION["guue"]["id_pubblicazione"]);
				echo '<ul style="color:#FF0000;">';
				if(!empty($notice["technical_validation_report"]["items"])) {
					foreach ($notice["technical_validation_report"]["items"] as $validation) {
						if(!in_array($validation["name"], $errors)) {
							if(!$validation["valid"]) {
								$errors[] = $validation["name"];
								echo '<li>';
									echo $validation["message"];
									if(!empty($validation["details"])) {
										echo '<ul><li>'.$validation["details"].'</li></ul>';
									}
								echo '</li>';
							}
						}
					}
				}
				if(!empty($notice["validation_rules_report"]["items"])) {
					foreach ($notice["validation_rules_report"]["items"] as $validation) {
						if(!in_array($validation["name"], $errors)) {
							if(!$validation["valid"]) {
								$errors[] = $validation["name"];
								$sezione = $tedesender->getSezione($errori[$validation["name"]]["section"]);
								$message = $errori[$validation["name"]]["message"];
								echo '<li><b>'.$validation["name"].'</b>: '.$sezione.' - '.$message.'</li>';
							}
						}
					}
				}
				echo '</ul>';
			} catch (Exception $e) {
				?>
					Si &egrave; verificato un errore nel tentativo di recupero della validazione del form inviato. Si prega di aggiornare la pagina.
				<?
			}
			echo '</td></tr>';
		}
		?>
	</tbody>
</table>
