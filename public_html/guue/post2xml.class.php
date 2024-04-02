<?
	/**
	* POST2XML
	*/
	class post2xml
	{

		private $xml;
		private $esender;
		private $cpv_main_code;
		private $cpv_supplementary_code;
		private $replaced_key;
		private $text_ft_single_line;
		private $text_ft_multi_lines;
		private $optional_key;
		private $date;
		private $no_doc_ext;
		private $uuid;

		public $codice_gara;
		public $customer_login;
		public $form_attribute;
		public $form;
		public $post;


		function __construct()
		{
			$this->xml = new XMLWriter();

			$this->esender = array();
			$this->esender["class"] = "";
			$this->esender["login"] = "";
			$this->esender["wsse_username"] = "";
			$this->esender["wsse_password"] = "";
			$this->esender["off_name"] = "";
			$this->esender["address"] = "";
			$this->esender["postal_code"] = "";
			$this->esender["town"] = "";
			$this->esender["phone"] = "";
			$this->esender["fax"] = "";
			$this->esender["url"] = "";
			$this->esender["funct_email"] = "";
			$this->esender["tech_email"] = "";
			$this->esender["cont_email"] = "";
			$this->esender["resp_email"] = "";
			$this->esender["coord_email"] = "";

			$this->text_ft_single_line = array(
				"TITLE",
				"PLACE",
				"ESTIMATED_TIMING",
				"RENEWAL_DESCR",
				"PROCUREMENT_LAW",
				"TEXT",
				"RULES_CRITERIA",
				"DEPOSIT_GUARANTEE_REQUIRED",
				"LEGAL_FORM",
				"CALCULATION_METHOD",
				"EU_PROGR_RELATED",
				"INFO_ADD_VALUE"
				);

			$this->text_ft_multi_lines = array(
				"D_JUSTIFICATION",
				"PARTICULAR_PROFESSION",
				"SHORT_DESCR",
				"LOT_COMBINING_CONTRACT_RIGHT",
				"MAIN_SITE",
				"INFO_ADD",
				"SUITABILITY",
				"ECONOMIC_FINANCIAL_INFO",
				"JUSTIFICATION",
				"ECONOMIC_FINANCIAL_MIN_LEVEL",
				"TECHNICAL_PROFESSIONAL_INFO",
				"TECHNICAL_PROFESSIONAL_MIN_LEVEL",
				"REFERENCE_TO_LAW",
				"PERFORMANCE_CONDITIONS",
				"INFO_ADD_EAUCTION",
				"REVIEW_PROCEDURE",
				"OPTIONS_DESCR",
				"ACCELERATED_PROC",
				"CRITERIA_CANDIDATE",
				"INFO_ADD_SUBCONTRACTING",
				"RULES_CRITERIA",
				"DEPOSIT_GUARANTEE_REQUIRED",
				"MAIN_FINANCING_CONDITION",
				"CRITERIA_SELECTION",
				"CRITERIA_EVALUATION",
				"NUMBER_VALUE_PRIZE",
				"DETAILS_PAYMENT",
				"MAIN_FEATURES_AWARD",
				"CONDITIONS",
				"METHODS",
				);

			$this->optional_key = array(
				"CALCULATION_METHOD",
				"CA_ACTIVITY",
				"VAL_REVENUE",
        "NUMBER_VALUE_PRIZE",
				"VAL_PRICE_PAYMENT",
				"INFO_ADD_VALUE",
				"DETAILS_PAYMENT",
				"MAIN_FEATURES_AWARD",
				"DATE_DISPATCH_ORIGINAL_PUBBLICATION_NO",
				"radio_as_select_for_currencies",
				"NO_DOC_EXT_PUBBLICATION_NO",
				"DURATION_TENDER_VALID",
				"VAL_RANGE_TOTAL",
				"VAL_SUBCONTRACTING",
				"INFO_ADD",
				"ADDRESS",
				"POSTAL_CODE",
				"PHONE",
				"URL",
				"FAX",
				"NATIONALID",
				"TIME_RECEIPT_TENDERS",
				"DATE_AWARD_SCHEDULED",
				"DATE_RECEIPT_TENDERS",
				"VAL_OBJECT",
				"EU_PROGR_RELATED",
				"NO_EU_PROGR_RELATED",
				"DATE_PUBLICATION_NOTICE",
				"DOCUMENT_FULL",
				"NOTICE_NUMBER_OJ",
				"DATE_DISPATCH_INVITATIONS",
				"DATE_TENDER_VALID",
				"REFERENCE_NUMBER",
				"MAIN_SITE",
				"SUITABILITY",
				"PERFORMANCE_CONDITIONS",
				"E_MAIL",
				"REVIEW_PROCEDURE",
				"TITLE",
				"CRITERIA_SELECTION",
				"val",
				// "NB_TENDERS_RECEIVED_SME",
				// "NB_TENDERS_RECEIVED_OTHER_EU",
				// "NB_TENDERS_RECEIVED_NON_EU",
				// "NB_TENDERS_RECEIVED_EMEANS",
				"PLACE",
				"CONTRACT_NO",
				"LOT_NO",
				"PCT_SUBCONTRACTING",
				"AC_WEIGHTING",
				"CA_TYPE",
				"RULES_CRITERIA",
				"DEPOSIT_GUARANTEE_REQUIRED",
				"ESTIMATED_TIMING",
				"MAIN_FINANCING_CONDITION",
				"LEGAL_FORM",
				"COUNTRY_ORIGIN",
				"NB_PARTICIPANTS_SME",
				"NB_PARTICIPANTS_OTHER_EU",
				"VAL_PRIZE",
				"radio_as_select_for_community_origin",
				"radio_as_select_for_main_activity",
				"CE_ACTIVITY",
				"URL_NATIONAL_PROCEDURE",
				"CE_TYPE",
				"METHODS"
				);

			$this->date = array(
				"DATE_DECISION_JURY",
				"DATE_PUBLICATION_NOTICE",
				"DATE_RECEIPT_TENDERS",
				"DATE_AWARD_SCHEDULED",
				"DATE_DISPATCH_NOTICE",
				"DATE_OPENING_TENDERS",
				"DATE_DISPATCH_INVITATIONS",
				"DATE_TENDER_VALID",
				"DATE_START",
				"DATE_END",
				"DATE_DISPATCH_ORIGINAL_PUBBLICATION_NO",
				"DATE_CONCLUSION_CONTRACT",
				"DATE"
				);
		}

		private function stringToDate($normal_date)
		{
			$anno = substr($normal_date, 6, 4);
			$mese = substr($normal_date, 3, 2);
			$giorno = substr($normal_date, 0, 2);
			$reversed_data = "$anno-$mese-$giorno";

			if ($reversed_data == "--") {
				$reversed_data = "";
			}

			return $reversed_data;
		}

		private function multipleLine($string)
		{
			if(!empty($string)) {
				$string = nl2br($string);
				$string = str_replace(array('<p>', '</p>', '</ p>' , '<br/>', '<br />'), array('', '<br>', '<br>', '<br>', '<br>'), $string);
				$strings = explode("<br>", $string);
				if(count($strings) == 1) {
					$string = strip_tags($strings[0]);
					$string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
					$string = mb_convert_encoding($string, "UTF-8", "auto");
					$string = iconv("UTF-8", "UTF-8//ignore", $string);
					$string = trim($string);
					$string = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $string);
					$re = '/(?<=[.?!;:])\s+(?=[\w])/';
					$strings = preg_split($re, $string, -1, PREG_SPLIT_NO_EMPTY);
				}
				foreach ($strings as $string) {
					$txt = $string;
					$txt = strip_tags($txt);
					$txt = html_entity_decode($txt, ENT_QUOTES, 'UTF-8');
					$txt = mb_convert_encoding($txt, "UTF-8", "auto");
					$txt = iconv("UTF-8", "UTF-8//ignore", $txt);
					$txt = trim($txt);
					$txt = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $txt);
					$test = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $txt);
					if(!empty($test)) {
						// $txt = preg_replace('/[^(\x20-\x7F)]*/','', $txt);
						$this->xml->writeElement("tg:P", $txt);
					}
				}
			}
		}

		private function singleLine($string)
		{
			$txt = $string;
			$txt = strip_tags($txt);
			$txt = html_entity_decode($txt, ENT_QUOTES, 'UTF-8');
			$txt = mb_convert_encoding($txt, "UTF-8", "auto");
			$txt = iconv("UTF-8", "UTF-8//ignore", $txt);
			$txt = trim($txt);
			$txt = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $txt);
			$test = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $txt);
			if(!empty($test)) {
				// $txt = preg_replace('/[^(\x20-\x7F)]*/','', $txt);
				$this->xml->writeElement("tg:P", $txt);
			}
		}

		private function cleaner(&$data = array()) {

			if(empty($data)) $data = &$this->post["guue"];

			foreach ($data as $key => &$value) {

				if(strpos($key, "_PUBBLICATION_NO") !== FALSE) {
					$tmp = $data[$key];
					$name = str_replace("_PUBBLICATION_NO", "", $key);
					$keys = array_keys($data);
					$index = array_search($key, $keys);

					// unset($data[$key]);

					if(!empty($value) && !is_array($value)) {
						$keys[$index] = $name;
						$data = array_combine($keys, $data);
						$data[$name] = array();
						$data[$name]["ATTRIBUTE"]["PUBLICATION"] = "NO";
						$data[$name]["val"] = $tmp;
						if(in_array($key, $this->date)) {
							$data[$name]["val"] = $this->stringToDate($tmp);
						}
					}
				}

				if($key == "VAL_ESTIMATED_TOTAL" || $key == "VAL_OBJECT" || $key == "VAL_TOTAL") {
					if (is_array($value) && empty($value["val"])) {
						unset($data[$key]);
					}
				}

				if(in_array($key, $this->optional_key)) {
					if (is_array($value)) {
						if((isset($value["val"]) && empty($value["val"]))) {
							unset($data[$key]);
						} elseif (isset($value["ATTRIBUTE"]) && isset($value["ATTRIBUTE"]["VALUE"])) {
							$to_unset = FALSE;
							foreach ($value["ATTRIBUTE"]["VALUE"] as $attribute_value) {
								if($to_unset) break;
								if(empty($attribute_value)) {
									$to_unset = TRUE;
								}
							}
							if($to_unset) unset($data[$key]);
						}
					} else {
						if(empty($value)) unset($data[$key]);
					}
				}

				if($key == "CPV_MAIN") {
					$value["CPV_CODE"]["ATTRIBUTE"]["CODE"] = $this->cpv_main_code;
				}

				if($key == "supplementary_cpv" || $key === "main_cpv") {
					unset($data[$key]);
				}

				if($key == "CPV_ADDITIONAL") {
					$value["CPV_CODE"]["ATTRIBUTE"]["CODE"] = $this->completeCpv($value["CPV_CODE"]["ATTRIBUTE"]["CODE"]);
					// $i = 0;
					// unset($value["CPV_CODE"]);
					// foreach ($this->cpv_supplementary_code as $cpv) {
					// 	$i++;
					// 	$value["ITEM_".$i] = array("CPV_CODE" => array("ATTRIBUTE" => array("CODE" => $cpv)));
					// }
				}

				if (!is_array($value) && (strpos($value, "_ITEM_TO_IGNORE") !== FALSE || strpos($value, "_ELEMENT_TO_IGNORE") !== FALSE)) {
					unset($data[$key]);
				}

				if(in_array($key, $this->replaced_key) && !in_array($key, $this->text_ft_single_line)) {
					unset($data[$key]);
				}

				if(is_array($value) && !empty($value)) {
					if(array_key_exists("ATTRIBUTE",$value)) {
						foreach ($value["ATTRIBUTE"] as $attribute => &$val) {
							if(empty($val)) {
								unset($value["ATTRIBUTE"][$attribute]);
							} else {
								if(is_array($val)) {
									foreach ($val as $attrName => &$attrVal) {
										if (strpos($attrVal, "_ITEM_TO_IGNORE") !== FALSE) {
											unset($val[$attrName]);
										}
									}
									if(empty($val)) unset($value["ATTRIBUTE"][$attribute]);
								} else {
									if (strpos($value["ATTRIBUTE"][$attribute], "_ITEM_TO_IGNORE") !== FALSE) {
										unset($value["ATTRIBUTE"][$attribute]);
									}
								}
							}
						}
						if(empty($value["ATTRIBUTE"])) unset($data[$key]);
					}
					$this->cleaner($value);
				}

				if(!is_array($value) && !empty($value)) {
					$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
				}
			}
		}

		private function convertData() {
			if(empty($this->post)) {
				throw new Exception("No data found!", 1);
			} else {
				if(empty($this->post["guue"]) || !is_array($this->post["guue"]))  {
					throw new Exception("Error with data. Missing key guue.", 1);
				} else {
					$this->replaced_key = array();
					if(!empty($this->post["guue"]["replaced_key"])) {
						$this->replaced_key = array_values(array_unique($this->post["guue"]["replaced_key"]));
						unset($this->post["guue"]["replaced_key"]);
					}
					$this->cleaner();
					// echo '<pre>';
					// var_dump($this->post["guue"]);
					// echo '</pre>';
					// exit;
					$this->iterate($this->post["guue"]);
				}
			}
		}

		private function iterateAttributes ($elements) {
			foreach ($elements as $key => $value) {
				if(!is_array($value)) {
					if(strpos($key, "radio_as_select_for_") !== FALSE) {
						if(!empty($value)) $this->xml->text($value);
					} else {
						$this->xml->writeAttribute($key, $value);
					}
				} else {
					$this->xml->startAttribute($key);
						$this->iterateAttributes($value);
					$this->xml->endAttribute();
				}
			}
		}

		private function writeEmptyElement($el) {
			if (strpos($el, "_PUBBLICATION_NO") !== FALSE) {
				$name = str_replace("_PUBBLICATION_NO", "", $el);
				$this->xml->startElement("tg:{$name}");
					$this->xml->writeAttribute("PUBLICATION", "NO");
				$this->xml->endElement();
				if(!is_array($el) && in_array($el, array('ORIGINAL_ENOTICES_PUBBLICATION_NO', 'ORIGINAL_TED_ESENDER_PUBBLICATION_NO'))) {
					$this->xml->startElement("tg:ESENDER_LOGIN");
						$this->xml->writeAttribute("PUBLICATION", "NO");
						$this->xml->text($this->esender["wsse_username"]);
					$this->xml->endElement();
					$this->xml->startElement("tg:CUSTOMER_LOGIN");
						$this->xml->writeAttribute("PUBLICATION", "NO");
						$this->xml->text($this->customer_login);
					$this->xml->endElement();
				}
			} else {
				$this->xml->writeElement("tg:{$el}", "");
			}
		}

		private function iterate($elements) {
			foreach ($elements as $key => $element) {
				if ($key == "VALUES") {
					if(isset($element['VAL_ESTIMATED_TOTAL']) && isset($element['VAL_TOTAL'])) {
						if(isset($element["ATTRIBUTE"]["PUBLICATION"])) unset($element["ATTRIBUTE"]["PUBLICATION"]);
					}
				} elseif ($key == "TENDERS") {
					if(strpos($this->form, "F06") ==! FALSE) {
						if(isset($element["ATTRIBUTE"]["PUBLICATION"])) unset($element["ATTRIBUTE"]["PUBLICATION"]);
					}
				} elseif ($key == "CONTRACTORS") {
					if(strpos($this->form, "F06") ==! FALSE) {
						if(isset($element["ATTRIBUTE"]["PUBLICATION"])) unset($element["ATTRIBUTE"]["PUBLICATION"]);
					}
				}

				if($key == "val" && (!empty($element) && !is_array($element))) {
					$this->xml->text($element);
				} elseif(!empty($element) && !is_array($element) && in_array($key, $this->date)) {
					$this->xml->writeElement("tg:{$key}", $this->stringToDate($element));
				} elseif(strpos($key, "i_") !== FALSE) {
					$this->iterate($element);
        } elseif ($key == "NUTS") {
          $this->xml->startElement("tgn:{$key}");
            $this->iterate($element);
          $this->xml->endElement();
				} elseif ($key == "AWARD_CONTRACT") {
					if(is_array($element)) {

						$n = 1;
						foreach ($element as $lotKey => $lot) {
							if(strpos($lotKey, "ITEM_") !== FALSE && is_numeric(str_replace("ITEM_","",$lotKey))) {
								$this->xml->startElement("tg:AWARD_CONTRACT");
								$this->xml->writeAttribute("ITEM", $n);
									$this->iterate($lot);
								$this->xml->endElement();
								$n++;
							}
						}
					}
				} elseif ($key == "PT_AWARD_CONTRACT_WITHOUT_CALL" || $key == "radio_as_select_for_annex_d1") {
					continue;
				} elseif ($key == "radio_as_select_for_procedure_type" && $element == "PT_AWARD_CONTRACT_WITHOUT_CALL" /* && ! empty($this->post["guue"]["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]) */) {
					$this->xml->startElement("tg:PT_AWARD_CONTRACT_WITHOUT_CALL");
						$this->iterate($this->post["guue"]["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]);
					$this->xml->endElement();
				} elseif ($key == "CONTRACTOR") {
					if(is_array($element)) {
						foreach ($element as $contractor) {
							$this->xml->startElement("tg:CONTRACTOR");
								$this->iterate($contractor);
							$this->xml->endElement();
						}
					}
				} elseif ($key == "MEMBER_NAME") {
					if(is_array($element)) {
						foreach ($element as $member) {
							$this->xml->writeElement('tg:MEMBER_NAME', $member);
						}
					}
				} elseif ($key == "OBJECT_DESCR") {
					if(is_array($element)) {
						if(!in_array($this->form_attribute["FORM"], array('F12','F13', 'F07'))) {
							$n = 1;
							foreach ($element as $lotKey => $lot) {
								if(strpos($lotKey, "ITEM_") !== FALSE && is_numeric(str_replace("ITEM_","",$lotKey))) {
									$this->xml->startElement("tg:OBJECT_DESCR");
									$this->xml->writeAttribute("ITEM", $n);
										$this->iterate($lot);
									$this->xml->endElement();
									$n++;
								}
							}
						} else {
							foreach ($element as $lotKey => $lot) {
								$this->xml->startElement("tg:OBJECT_DESCR");
									$this->iterate($lot);
								$this->xml->endElement();
							}
						}
					}
				} elseif($key == "RESULTS") {
					if(is_array($element)) {
						foreach ($element as $lotKey => $lot) {
							if(strpos($lotKey, "ITEM_") !== FALSE && is_numeric(str_replace("ITEM_","",$lotKey))) {
								$this->xml->startElement("tg:RESULTS");
									$this->iterate($lot);
								$this->xml->endElement();
							}
							break;
						}
					}
				}elseif ($key == "CHANGE") {
					if(is_array($element)) {
						$n = 1;
						foreach ($element as $changeKey => $change) {
							if(strpos($changeKey, "ITEM_") !== FALSE && is_numeric(str_replace("ITEM_","",$changeKey))) {
								$this->xml->startElement("tg:CHANGE");
								// $this->xml->writeAttribute("ITEM", $n);
									$this->iterate($change);
								$this->xml->endElement();
								$n++;
							}
						}
					}
				} elseif ($key == "ADDRESS_CONTRACTING_BODY_ADDITIONAL") {
					if(is_array($element)) {
						$n = 1;
						foreach ($element as $contracting_body_key => $contracting_body) {
							if(strpos($contracting_body_key, "ITEM_") !== FALSE && is_numeric(str_replace("ITEM_","",$contracting_body_key))) {
								$this->xml->startElement("tg:ADDRESS_CONTRACTING_BODY_ADDITIONAL");
								// $this->xml->writeAttribute("ITEM", $n);
									$this->iterate($contracting_body);
								$this->xml->endElement();
								$n++;
							}
						}
					}
				} elseif($key == "ATTRIBUTE") {
					$this->iterateAttributes($element);
				} elseif (strpos($key, "radio_as_select_for_") !== FALSE) {
					if(is_array($element)) {
						$this->iterate($element);
					} elseif (!empty($element)) {
						$this->writeEmptyElement($element, "");
					}
				} else if(in_array($key, array('VAL_ESTIMATED_TOTAL', 'VAL_OBJECT', 'VAL_TOTAL'))) {
						$this->xml->startElement("tg:{$key}");
							$this->iterate($element);
						$this->xml->endElement();
				} elseif($key == "EU_PROGR_RELATED") {
					$this->xml->startElement("tg:{$key}");
						$this->xml->writeElement("tg:P", $element["val"]);
					$this->xml->endElement();
				} elseif($key == "CRITERIA_EVALUATION") {
						$this->xml->startElement("tg:{$key}");
							if(! empty($element["ATTRIBUTE"]["PUBLICATION"]["radio_as_select_for_contractors_publication"])) $this->xml->writeAttribute("PUBLICATION", $element["ATTRIBUTE"]["PUBLICATION"]["radio_as_select_for_contractors_publication"]);
							if(! empty($element["val"])) $this->multipleLine($element["val"]);
						$this->xml->endElement();
				} elseif($key == "AC_CRITERION") {
					if(is_array($element)) {
						foreach ($element as $ac_criterion) {
							$ac_criterion = trim($ac_criterion);
							if(!empty($ac_criterion)) $this->xml->writeElement("tg:AC_CRITERION", $ac_criterion);
							break;
						}
					} else {
						$this->xml->writeElement("tg:AC_CRITERION", trim($element));
					}
				} else {
					if(is_array($element)) {
						$multiple_item = TRUE;
						$keys = array_keys($element);
						foreach ($keys as $k) {
							if($multiple_item){
								if (strpos($k, "i_") === FALSE) $multiple_item = FALSE;
							} else {
								break;
							}
						}
						if($multiple_item) {
							foreach ($element as $el) {
								$this->xml->startElement("tg:{$key}");
									$this->iterate($el);
								$this->xml->endElement();
							}
						} else {
							$this->xml->startElement("tg:{$key}");
								$this->iterate($element);
							$this->xml->endElement();
						}
					} elseif(is_string($element) && in_array($key, $this->optional_key) && ((!is_array($element) && trim($element) == "") || empty($element))) {
						continue;
					} elseif(is_string($element) && in_array($key, $this->text_ft_single_line)) {
						$this->xml->startElement("tg:{$key}");
							$this->singleLine($element);
						$this->xml->endElement();
					} elseif(is_string($element) && in_array($key, $this->text_ft_multi_lines)) {
						$this->xml->startElement("tg:{$key}");
							$this->multipleLine($element);
						$this->xml->endElement();
					} else {
						if($element == "on") $element = "";
						$this->xml->writeElement("tg:{$key}", $element);
					}
				}
			}
		}

		private function return_xml() {
			return $this->xml->outputMemory(TRUE);
		}

		private function completeCpv($cpv) {
			if(strlen($cpv) < 8) {
				$cpv .= str_repeat("0",8-strlen($cpv));
			}
			return $cpv;
		}

		private function completeNoGuue($number)
		{
			if(is_numeric($number)) {
				if(strlen($number) < 6) {
					$number = str_repeat("0",6-strlen($number)).$number;
				}
				return $number;
			} else {
				throw new Exception("GUUE NUMBER IS NOT VALID", 1);
			}
		}

		public function setNoGuue($number)
		{
			$this->no_doc_ext = $this->completeNoGuue($number);
		}

		public function setUUID($uuid)
		{
			$this->uuid = $uuid;
		}

		public function setMainCpv($val) {
			$this->cpv_main_code = $this->completeCpv($val);
		}

		public function setSupplementaryCpv($val) {
			if(empty($this->cpv_main_code)) {
				throw new Exception("CPV MAIN CODE IS REQUIRED", 1);
			}
			$cpv = array_filter(explode(";", $val));
			foreach ($cpv as $index => $cpv_code) {
				$cpv[$index] = $this->completeCpv($cpv_code);
			}
			if(in_array($this->cpv_main_code, $cpv)) unset($cpv[$this->cpv_main_code]);
			$this->cpv_supplementary_code = $cpv;
		}

		public function createXML() {

				$this->xml->openMemory();
				$this->xml->setIndent(8);
				$this->xml->setIndentString("    ");
				$this->xml->startDocument("1.0", "UTF-8");

          $this->xml->startElement("tg:TED_ESENDERS");
          	if($_SESSION["developEnviroment"]) {
          		$this->xml->writeAttributeNS("xmlns","tg", null, "http://publications.europa.eu/resource/schema/ted/R2.0.9/reception");
          		$this->xml->writeAttributeNS("xmlns","tgn", null, "http://publications.europa.eu/resource/schema/ted/2016/nuts");
          		$this->xml->writeAttributeNS("xmlns","cur", null, "http://publications.europa.eu/resource/authority/currency");
          		$this->xml->writeAttribute("xmlns", "http://publications.europa.eu/resource/schema/ted/R2.0.9/reception");
          		$this->xml->writeAttributeNS("xmlns", "xsi", null, "http://www.w3.org/2001/XMLSchema-instance");
          		$this->xml->writeAttributeNS("xsi", "schemaLocation", null, "http://publications.europa.eu/resource/schema/ted/R2.0.9/publication TED_EXPORT.xsd");
          		$this->xml->writeAttribute("VERSION", "R2.0.9.S03");
          	} else {
							$this->xml->writeAttributeNS("xmlns","tg", null, "http://publications.europa.eu/resource/schema/ted/R2.0.9/reception");
          		$this->xml->writeAttributeNS("xmlns","tgn", null, "http://publications.europa.eu/resource/schema/ted/2016/nuts");
          		$this->xml->writeAttributeNS("xmlns","cur", null, "http://publications.europa.eu/resource/authority/currency");
          		$this->xml->writeAttribute("xmlns", "http://publications.europa.eu/resource/schema/ted/R2.0.9/reception");
          		$this->xml->writeAttributeNS("xmlns", "xsi", null, "http://www.w3.org/2001/XMLSchema-instance");
          		$this->xml->writeAttributeNS("xsi", "schemaLocation", null, "http://publications.europa.eu/resource/schema/ted/R2.0.9/publication TED_EXPORT.xsd");
          		$this->xml->writeAttribute("VERSION", "R2.0.9.S03");
          	}

            // $this->xml->writeAttribute("VERSION", "R2.0.9.S02");
            // $this->xml->writeAttribute("xmlns","http://formex.publications.europa.eu/ted/schema/reception/R2.0.9.S02");
            // $this->xml->writeAttributeNS("xmlns", "xsi", null, "http://www.w3.org/2001/XMLSchema-instance");
            // $this->xml->writeAttributeNS("xsi","schemaLocation", null, "http://formex.publications.europa.eu/ted/schema/reception/R2.0.9.S02 TED_ESENDERS.xsd");

						$this->xml->startElement("tg:SENDER");
							$this->xml->startElement('tg:IDENTIFICATION');
								$this->xml->writeElement("tg:ESENDER_LOGIN", $this->esender["wsse_username"]);
                $this->xml->writeElement("tg:CUSTOMER_LOGIN", $this->customer_login);
								// $xml->writeElement("NO_DOC_EXT", date("Y")."-".str_pad($record_gara["codice"], 6, "0", STR_PAD_LEFT));
								$this->xml->writeElement("tg:NO_DOC_EXT", date("Y")."-".$this->no_doc_ext);
								$this->xml->writeElement("tg:SOFTWARE_VERSION", "1.1");
							$this->xml->endElement();
							$this->xml->startElement("tg:CONTACT");
								$this->xml->writeElement("tg:ORGANISATION", "");
								$this->xml->startElement("tg:COUNTRY");
									$this->xml->writeAttribute("VALUE", "IT");
								$this->xml->endElement();
								// $this->xml->writeElement("PHONE", "");
								$this->xml->writeElement("tg:E_MAIL", "");
							$this->xml->endElement();
							// if(! empty($this->post["guue"]["DATE_EXPECTED_PUBLICATION"])) {

							// }
							// if(! $_SESSION["developEnviroment"]){
							// 	$this->xml->startElement("tg:NOTIFICATION");
							// 		$this->xml->startElement("tg:TECHNICAL");
							// 			$this->xml->writeAttribute("ESENDER", "YES");
							// 			$this->xml->writeAttribute("CONTRACTING_BODY", "YES");
							// 		$this->xml->endElement();
							// 		$this->xml->startElement("tg:PUBLICATION");
							// 			$this->xml->writeAttribute("ESENDER", "YES");
							// 		$this->xml->endElement();
							// 	$this->xml->endElement();
							// }
						$this->xml->endElement();
						$this->xml->startElement("tg:FORM_SECTION");
							if($_SESSION["developEnviroment"]) $this->xml->writeElement("tg:NOTICE_UUID", $this->uuid);
							$this->xml->startElement("tg:{$this->form}");
								if(!empty($this->form_attribute) && is_array($this->form_attribute)) {
									$this->iterateAttributes($this->form_attribute);
								}
								$this->convertData();
							$this->xml->endElement();
						$this->xml->endElement();
					$this->xml->endElement();
				$this->xml->endDocument();
				return $this->return_xml();
		}

	}
?>
