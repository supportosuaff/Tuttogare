<?
	include_once("../../config.php");
	$echo = true;
	include_once($root."/inc/funzioni.php");
	include_once($root."/dgue/config.php");
	include_once($root."/inc/xml2json.php");

	if (isset($_GET["codice_riferimento"]) && $_GET["sezione"]) {

			$ris = getDGUERequestedCriteria($_GET["codice_riferimento"],$_GET["sezione"]);
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
						$record = $risultato->fetch(PDO::FETCH_ASSOC);
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
						$record = $risultato->fetch(PDO::FETCH_ASSOC);
					}
				} else if ($_GET["sezione"] == "free") {
					$strsql="SELECT * FROM b_dgue_free WHERE codice = :codice AND codice_gestore = :codice_ente";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
						$record = $risultato->fetch(PDO::FETCH_ASSOC);
						$record["oggetto"] = $record["procedura"];
						$record["cig"] = $record["identificativo"];
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
						$record = $risultato->fetch(PDO::FETCH_ASSOC);
					}
				}
			if (!empty($record)) {
				if (empty($record["denominazione"])) $record["denominazione"] = $_SESSION["ente"]["denominazione"];
				header("content-type: text/xml");
				header("Content-disposition: attachment; filename=DGUE-Request.xml");
				$dgue = array();

				$dgue["cac:ContractingParty"]["cac:Party"]["cac:PartyName"]["cbc:Name"] = $record["denominazione"];
				$dgue["cac:AdditionalDocumentReference"]["cac:Attachment"]["cac:ExternalReference"]["cbc:FileName"] = $record["oggetto"];
				$dgue["cac:AdditionalDocumentReference"]["cac:Attachment"]["cac:ExternalReference"]["cbc:Description"] = trim(strip_tags($record["descrizione"]));

				if (!empty($record["cig"])) $dgue["cbc:ContractFolderID"]["$"] = $record["cig"];

				$dgue["@xmlns:cac"]="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" ;
				$dgue["@xmlns:cbc"]="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" ;
				$dgue["@xmlns:ccv-cbc"]="urn:isa:names:specification:ubl:schema:xsd:CCV-CommonBasicComponents-1" ;
				$dgue["@xmlns:cev-cbc"]="urn:isa:names:specification:ubl:schema:xsd:CEV-CommonBasicComponents-1" ;
				$dgue["@xmlns:cev"]="urn:isa:names:specification:ubl:schema:xsd:CEV-CommonAggregateComponents-1" ;
				$dgue["@xmlns:ext"]="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" ;
				$dgue["@xmlns:ccv"]="urn:isa:names:specification:ubl:schema:xsd:CCV-CommonAggregateComponents-1" ;
				$dgue["@xmlns:espd-req"]="urn:grow:names:specification:ubl:schema:xsd:ESPDRequest-1";
				$dgue["cbc:UBLVersionID"]["@schemeAgencyID"] = "OASIS-UBL-TC";
				$dgue["cbc:UBLVersionID"] = "2.1";
				$dgue["cbc:CustomizationID"]["@schemeName"]="CustomizationID";
				$dgue["cbc:CustomizationID"]["@schemeAgencyID"]="BII";
				$dgue["cbc:CustomizationID"]["@schemeVersionID"]="3.0";
				$dgue["cbc:CustomizationID"] = "urn:www.cenbii.eu:transaction:biitrns070:ver3.0";
				$dgue["cbc:ID"]["@schemeID"]="ISO/IEC 9834-8:2008 - 4UUID";
				$dgue["cbc:ID"]["@schemeAgencyID"]="EU-COM-GROW";
				$dgue["cbc:ID"]["@schemeAgencyName"] = "DG GROW (European Commission)";
				$dgue["cbc:ID"]["@schemeVersionID"]="1.1";
				$dgue["cbc:ID"] = "7ffae894-7839-4c00-97a9-851e9f471faa";
		    $dgue["cbc:CopyIndicator"] = "false";
		    $dgue["cbc:VersionID"]["@schemeAgencyID"] = "EU-COM-GROW";
				$dgue["cbc:VersionID"] = "2016.04.2";
		    $dgue["cbc:IssueDate"] = date('Y-m-d');
				$dgue["cbc:IssueTime"] = date('H:i:s');
				$dgue["cbc:ContractFolderID"]["@schemeAgencyID"]="TeD";
				$dgue["cac:AdditionalDocumentReference"]["cbc:ID"]["@schemeID"]="COM-GROW-TEMPORARY-ID";
				$dgue["cac:AdditionalDocumentReference"]["cbc:ID"]["@schemeAgencyID"]="EU-COM-GROW";
				$dgue["cac:AdditionalDocumentReference"]["cbc:ID"]["@schemeAgencyName"]="DG GROW (European Commission)";
				$dgue["cac:AdditionalDocumentReference"]["cbc:ID"]["@schemeVersionID"]="1.1";
				$dgue["cac:AdditionalDocumentReference"]["cbc:ID"]= "0000/S 000-000000";
				$dgue["cac:AdditionalDocumentReference"]["cbc:DocumentTypeCode"]["@listID"]="ReferencesTypeCodes";
				$dgue["cac:AdditionalDocumentReference"]["cbc:DocumentTypeCode"]["@listAgencyID"]="EU-COM-GROW";
				$dgue["cac:AdditionalDocumentReference"]["cbc:DocumentTypeCode"]["@listVersionID"]="1.0";
				$dgue["cac:AdditionalDocumentReference"]["cbc:DocumentTypeCode"] = "TED_CN";
				$dgue["cac:ContractingParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]["@listAgencyID"]="ISO";
				$dgue["cac:ContractingParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]["@listName"]="ISO 3166-1";
				$dgue["cac:ContractingParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"]["@listVersionID"]="1.0";
				$dgue["cac:ContractingParty"]["cac:Party"]["cac:PostalAddress"]["cac:Country"]["cbc:IdentificationCode"] = "IT";
				$dgue["ccv:Criterion"] = array();
				foreach($ris AS $form) {
					if (strpos($form["uuid"],"PERS_") === false) {
						$criterion = array();
						$criterion["cbc:ID"]["@schemeID"]="CriteriaID";
						$criterion["cbc:ID"]["@schemeAgencyID"]="EU-COM-GROW";
						$criterion["cbc:ID"]["@schemeVersionID"]="1.0";
						$criterion["cbc:ID"]["$"] = $form["uuid"];
						$criterion["cbc:TypeCode"]["@listID"] = "CriteriaTypeCode";
						$criterion["cbc:TypeCode"]["@listAgencyID"] = "EU-COM-GROW";
						$criterion["cbc:TypeCode"]["$"] = $form["taxa"];
						$dgue["ccv:Criterion"][] = $criterion;
					}
				}
				$dgue = array("espd-req:ESPDRequest"=>$dgue);
				array_walk_recursive($dgue, function(&$value,&$key) {
					if (!is_array($value)) {
						$value = html_entity_decode($value,ENT_COMPAT | ENT_HTML401,"UTF-8");
						$value = preg_replace('#&(?=[a-z_0-9]+=)#', '&amp;', $value);
						$value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
						$value = utf8_encode($value);
					}
				});
				echo array2XML($dgue);
			} else {
				?>
				<h1>Bando di riferimento non trovato</h1>
				<?
			}
		} else {
			?>
			<h1>Bando di riferimento non trovato</h1>
			<?
		}
	} else {
		?>
		<h1>Documento non esistente</h1>
		<?
	}
	?>
