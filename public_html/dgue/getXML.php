<?
	include_once("../../config.php");
	$echo = true;
	include_once($root."/inc/funzioni.php");
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
				$dgue["@xmlns:cac"] = "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2";
				$dgue["@xmlns:cbc"] = "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2";
				$dgue["@xmlns:ccv-cbc"] = "urn:isa:names:specification:ubl:schema:xsd:CCV-CommonBasicComponents-1";
				$dgue["@xmlns:cev-cbc"] = "urn:isa:names:specification:ubl:schema:xsd:CEV-CommonBasicComponents-1";
				$dgue["@xmlns:cev"] = "urn:isa:names:specification:ubl:schema:xsd:CEV-CommonAggregateComponents-1";
				$dgue["@xmlns:espd"] = "urn:grow:names:specification:ubl:schema:xsd:ESPDResponse-1";
				$dgue["@xmlns:ext"] = "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2";
				$dgue["@xmlns:espd-cbc"] = "urn:grow:names:specification:ubl:schema:xsd:ESPD-CommonBasicComponents-1";
				$dgue["@xmlns:ccv"] = "urn:isa:names:specification:ubl:schema:xsd:CCV-CommonAggregateComponents-1";
				$dgue["@xmlns:espd-cac"] = "urn:grow:names:specification:ubl:schema:xsd:ESPD-CommonAggregateComponents-1";
				$dgue["cbc:UBLVersionID"]["@schemeAgencyID"] = "OASIS-UBL-TC";
				$dgue["cbc:UBLVersionID"]['$'] = "2.1";
				$dgue["cbc:CustomizationID"]["@schemeName"]="CustomizationID";
				$dgue["cbc:CustomizationID"]["@schemeAgencyID"]="BII";
				$dgue["cbc:CustomizationID"]["@schemeVersionID"]="3.0";
				$dgue["cbc:CustomizationID"]['$'] = "urn:www.cenbii.eu:transaction:biitrns092:ver3.0";
				$dgue["cbc:ID"]["@schemeID"]="ISO/IEC 9834-8:2008 - 4UUID";
				$dgue["cbc:ID"]["@schemeAgencyID"]="EU-COM-GROW";
				$dgue["cbc:ID"]["@schemeAgencyName"] = "DG GROW (European Commission)";
				$dgue["cbc:ID"]["@schemeVersionID"]="1.1";
				$dgue["cbc:ID"]['$'] = "b1bd7c20-b1c1-437b-891a-b96bbc188215";
		    $dgue["cbc:CopyIndicator"] = "false";
		    $dgue["cbc:VersionID"]["@schemeAgencyID"] = "EU-COM-GROW";
				$dgue["cbc:VersionID"]['$'] = "2016.04.2";
		    $dgue["cbc:IssueDate"] = date('Y-m-d');
				$dgue["cbc:IssueTime"] = date('H:i:s');

				$dgue["cac:AdditionalDocumentReference"][1]["cbc:ID"]["@schemeID"]="COM-GROW-TEMPORARY-ID";
				$dgue["cac:AdditionalDocumentReference"][1]["cbc:ID"]["@schemeAgencyID"]="EU-COM-GROW";
				$dgue["cac:AdditionalDocumentReference"][1]["cbc:ID"]["@schemeAgencyName"]="DG GROW (European Commission)";
				$dgue["cac:AdditionalDocumentReference"][1]["cbc:ID"]["@schemeVersionID"]="1.1";
				$dgue["cac:AdditionalDocumentReference"][1]["cbc:ID"]['$']= "0000/S 000-000000";

				$dgue["cac:AdditionalDocumentReference"][1]["cbc:DocumentTypeCode"]["@listID"]="ReferencesTypeCodes";
				$dgue["cac:AdditionalDocumentReference"][1]["cbc:DocumentTypeCode"]["@listAgencyID"]="EU-COM-GROW";
				$dgue["cac:AdditionalDocumentReference"][1]["cbc:DocumentTypeCode"]["@listVersionID"]="1.0";
				$dgue["cac:AdditionalDocumentReference"][1]["cbc:DocumentTypeCode"]['$'] = "TED_CN";

				$dgue["cac:AdditionalDocumentReference"][0]["cbc:ID"]["@schemeID"]="ISO/IEC 9834-8:2008 - 4UUID";
				$dgue["cac:AdditionalDocumentReference"][0]["cbc:ID"]["@schemeAgencyID"] ="EU-COM-GROW";
				$dgue["cac:AdditionalDocumentReference"][0]["cbc:ID"]["@schemeAgencyName"]="DG GROW (European Commission)";
				$dgue["cac:AdditionalDocumentReference"][0]["cbc:ID"]["@schemeVersionID"]="1.1";
				$dgue["cac:AdditionalDocumentReference"][0]["cbc:ID"]['$'] = "7ffae894-7839-4c00-97a9-851e9f471faa";

				$dgue["cac:AdditionalDocumentReference"][0]["cbc:DocumentTypeCode"]["@listID"]="ReferencesTypeCodes";
				$dgue["cac:AdditionalDocumentReference"][0]["cbc:DocumentTypeCode"]["@listAgencyID"]="EU-COM-GROW";
				$dgue["cac:AdditionalDocumentReference"][0]["cbc:DocumentTypeCode"]["@listVersionID"]="1.0";
				$dgue["cac:AdditionalDocumentReference"][0]["cbc:DocumentTypeCode"]['$']="ESPD_REQUEST";
				$dgue["cac:AdditionalDocumentReference"][0]["cbc:DocumentDescription"] = "ESPDRequest" . $dgue["cbc:ContractFolderID"]['$'];
				$dgue["cbc:ContractFolderID"]["@schemeAgencyID"]="TeD";
				/* riassegnazione ordine per lettore */
				$tmp = array();
				$tmp[] = $dgue["cac:AdditionalDocumentReference"][0];
				$tmp[] = $dgue["cac:AdditionalDocumentReference"][1];
				$dgue["cac:AdditionalDocumentReference"] = $tmp;
				unset($tmp);

				$dgue["cac:ContractingParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]["@listAgencyID"]="ISO";
				$dgue["cac:ContractingParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]["@listName"]="ISO 3166-1";
				$dgue["cac:ContractingParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]["@listVersionID"]="1.0";
				$dgue["cac:ContractingParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]['$'] = "IT";
				$dgue = array("espd:ESPDResponse"=>$dgue);
				header("content-type: text/xml");
				header("Content-disposition: attachment; filename=DGUE.xml");

				// LA FUNZIONE ELIMINA DALL'ARRAY TUTTI I REQUIREMENT CON ID PERS_
				// $dgue = removePersID($dgue);
				array_walk_recursive($dgue, function(&$value,&$key) {
					if (!is_array($value)) {
						$value = html_entity_decode($value,ENT_COMPAT | ENT_HTML401,"UTF-8");
						$value = preg_replace('#&(?=[a-z_0-9]+=)#', '&amp;', $value);
						// $value = utf8_encode($value);
					}
				});
				$xml = array2XML($dgue);
				$xml = str_replace("&", "&amp;", $xml);
				$xml = str_replace("&amp;amp;", "&amp;", $xml);
				echo $xml;
			} else {
				?>
				<h1>Documento di riferimento non trovato</h1>
				<?
			}
		} else {
			?>
			<h1>Impossibile accedere</h1>
			<?
		}
	?>
