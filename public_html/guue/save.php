<?
	session_start();
	include_once "../../config.php";
	include_once $root . "/inc/funzioni.php";
	include_once "post2xml.class.php";

	/* ini_set("display_errors", "1");
	error_reporting(E_ALL); */

	if(empty($_POST["titolo_pubblicazione"])) {
		?>
			jalert('<div style="text-align:center">Prima di salvare questa bozza &egrave; necessario specificare un titolo per il form. <strong>cod. #001</strong><br><strong>Inserire il titolo e riprovare!</strong></div>');
		<?
		die();
	}

	$_POST["guue"]["replaced_key"] = array();
	if(!empty($_POST["guue"]) && isset($_POST["codice"]) && is_numeric($_POST["codice"])) {

		if(!empty($_POST["keys_to_replace"])) {
			foreach($_POST["keys_to_replace"] as $keys) {
				$replaced_key = str_replace(";", "_", $keys);
				$key_group = array_filter(explode(';', $keys));
				$data_to_separate = $_POST["guue"];
				foreach ($key_group as $key) {
					if(!empty($data_to_separate[$key])) {
						$data_to_separate = $data_to_separate[$key];
					}
				}
				if(!empty($data_to_separate) && is_array($data_to_separate)) {
					foreach ($data_to_separate as $key => $value) {
						$_POST["guue"][$replaced_key][$key] = $value;
						$_POST["guue"]["replaced_key"][] = $replaced_key;
					}
				}
			}
		}

		if($_POST["bozza"] != 1) {

			$reset_radio_as_select_for_lot_division = FALSE;
			//VALIDAZIONE FORM 3
			if($_POST["numero_form"] == 3) {
				if(!empty($_POST["guue"]["AWARD_CONTRACT"])) {
					foreach ($_POST["guue"]["AWARD_CONTRACT"] as $key => $val) {
						if(!empty($val["NO_AWARDED_CONTRACT"]["radio_as_select_for_unsuccessful_discontinued"])) {
							if($val["NO_AWARDED_CONTRACT"]["radio_as_select_for_unsuccessful_discontinued"] == "PROCUREMENT_DISCONTINUED_ITEM_TO_IGNORE" && empty($val["NO_AWARDED_CONTRACT"]["PROCUREMENT_DISCONTINUED"]["DATE_DISPATCH_ORIGINAL_PUBBLICATION_NO"])) {
								?>
								jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #023</strong><br><strong>V.1) Data di conclusione del contratto d&#39;appalto &egrave; obbligatoria quando è indicata l&#39;interruzione della procedura.</strong></div>');
								<?
								die();
							} elseif($val["NO_AWARDED_CONTRACT"]["radio_as_select_for_unsuccessful_discontinued"] != "PROCUREMENT_DISCONTINUED_ITEM_TO_IGNORE" && !empty($val["NO_AWARDED_CONTRACT"]["PROCUREMENT_DISCONTINUED"]["DATE_DISPATCH_ORIGINAL_PUBBLICATION_NO"])) {
								?>
								jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #023</strong><br><strong>V.1) Non &egrave; possibile indicare la data di conclusione del contratto d&#39;appalto se la mancata aggiudicazione non &egrave; dovuta all&#39;interruzione della procedura.</strong></div>');
								<?
								die();
							}
						}
					}
				}
			}


			// if($_POST["numero_form"] == 3)  {
			// 	if(!empty($_POST["guue"]["AWARD_CONTRACT"]) && is_array($_POST["guue"]["AWARD_CONTRACT"])) {
			// 		foreach ($_POST["guue"]["AWARD_CONTRACT"] as $el => $info) {
			// 			if(!empty($info["radio_as_select_for_information_not_to_be_published"]) &&
			// 				($info["radio_as_select_for_information_not_to_be_published"] == "ORIGINAL_ENOTICES_PUBBLICATION_NO" || $info["radio_as_select_for_information_not_to_be_published"] == "ORIGINAL_TED_ESENDER_PUBBLICATION_NO")) {

			// 				unset($_POST["guue"]["AWARD_CONTRACT"][$el]);
			// 				$name = str_replace("_PUBBLICATION_NO", "", $info["radio_as_select_for_information_not_to_be_published"]);
			// 				$_POST["guue"]["AWARD_CONTRACT"][$el][$name]["ATTRIBUTE"]["PUBLICATION"] = "NO";
			// 				if(in_array($name, array('ORIGINAL_ENOTICES', 'ORIGINAL_TED_ESENDER'))) {

			// 				}


			// 			}
			// 		}
			// 	}
			// }
			$v_f_check = "2_0_9_S3";
			if($_SESSION["developEnviroment"]) $v_f_check = "2_0_9_S3";
			$numero_form = (int) number_format($_POST["numero_form"], 0, '', '');
			if ($numero_form == 41) {
				include "forms/{$v_f_check}/check/check_f41.php";
			} elseif ($numero_form == 5) {
				include "forms/{$v_f_check}/check/check_f5.php";
			} elseif(in_array($numero_form, array(101, 102, 103, 41, 42, 43, 221, 222))) {
				include "forms/{$v_f_check}/check/check_f101.php";
			} elseif(in_array($numero_form, array(211, 212, 213))) {
				include "forms/{$v_f_check}/check/check_f211.php";
			} elseif ($numero_form == 2) {
				include "forms/{$v_f_check}/check/check_f02.php";
			} elseif ($numero_form == 225) {
				include "forms/{$v_f_check}/check/check_f225.php";
			} elseif ($numero_form == 214 || $numero_form == 226) {
				include "forms/{$v_f_check}/check/check_f214.php";
			} elseif ($numero_form == 3) {
				include "forms/{$v_f_check}/check/check_f03.php";
			} elseif ($numero_form == 14) {
				include "forms/{$v_f_check}/check/check_f14.php";
			} elseif ($numero_form == 5) {
				include "forms/{$v_f_check}/check/check_f02.php";
			} elseif ($numero_form == 6) {
				include "forms/{$v_f_check}/check/check_f06.php";
			} elseif ($numero_form == 24) {
				include "forms/{$v_f_check}/check/check_f24.php";
			} elseif ($numero_form == 25) {
				include "forms/{$v_f_check}/check/check_f25.php";
			} elseif ($numero_form == 231 || $numero_form == 232 || $numero_form == 233 || $numero_form == 234) {
				include "forms/{$v_f_check}/check/check_f231.php";
				include "forms/{$v_f_check}/check/check_f214.php";
			}



			if(!empty($_POST["guue"]["PROCEDURE"]["ACCELERATED_PROC"]) && !empty($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"]) && !in_array($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"], array("PT_OPEN", "PT_RESTRICTED", "PT_COMPETITIVE_NEGOTIATION"))) {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #030</strong><br><strong> La procedura accellerata IV.1.1) può essere indicata solo nel caso di: Procedura Aperta, Procedura Ristretta o Procedura competitiva con negoziazione</strong></div>');
				<?
				die();
			}

			$pattern_notice_number_oj = "/(19|20)\d{2}\/(S)\s\d{3}-\d{6}/";
			if(!empty($_POST["guue"]["PROCEDURE"]["NOTICE_NUMBER_OJ"]) && !preg_match($pattern_notice_number_oj, $_POST["guue"]["PROCEDURE"]["NOTICE_NUMBER_OJ"])) {
				?>
				jalert('<div style="text-align:center">Errore di compilazione <strong>cod. #142</strong><br><strong>Verificare che il numero dell&#39;avviso nella GU S nella sez. VI.6 sia corretto</strong></div>');
				<?
				die();
			}

			if(!empty($_POST["guue"]["AWARD_CONTRACT"])) {
				$awarded = 0;
				$non_awarded = 0;
				foreach ($_POST["guue"]["AWARD_CONTRACT"] as $contract) {
					if(!empty($contract["radio_as_select_for_awarded_contract"])) {
						if($contract["radio_as_select_for_awarded_contract"] == "AWARDED_CONTRACT_ITEM_TO_IGNORE") $awarded++;
						if($contract["radio_as_select_for_awarded_contract"] != "AWARDED_CONTRACT_ITEM_TO_IGNORE") $non_awarded++;
					}
				}

				/*
				if($non_awarded > 0 && (!empty($_POST["guue"]["OBJECT_CONTRACT"]["VAL_TOTAL"]["val"]) || !empty($_POST["guue"]["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["HIGH"]))) {
					?>
						jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #029</strong><br><strong> Nel caso di un contratto d&#39;appalto/lotto non aggiudicato non è possibile indicare il valore totale dell&#39;appalto II.1.7)</strong></div>');
					<?
					die();
				}
				*/
				if($awarded > 0 && empty($_POST["guue"]["OBJECT_CONTRACT"]["VAL_TOTAL"]["val"]) && empty($_POST["guue"]["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["HIGH"])) {
					?>
						jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #028</strong><br><strong> Nel caso di un contratto d&#39;appalto/lotto aggiudicato è necessario specificare il valore totale dell&#39;appalto II.1.7)</strong></div>');
					<?
					die();
				} elseif ($awarded < 1 && (!empty($_POST["guue"]["OBJECT_CONTRACT"]["VAL_TOTAL"]["val"]) || !empty($_POST["guue"]["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["LOW"]))) {
					?>
						jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #028</strong><br><strong> Nel caso in cui nessun contratto d&#39;appalto/lotto aggiudicato non è necessario specificare il valore totale dell&#39;appalto II.1.7)</strong></div>');
					<?
					die();
				}


			}

			// VERIFICA ORGANISMO DI MEDIAZIONE
			if(!empty($_POST["guue"]["COMPLEMENTARY_INFO"]["ADDRESS_MEDIATION_BODY"])) {
				$test_mediation_body = $_POST["guue"]["COMPLEMENTARY_INFO"]["ADDRESS_MEDIATION_BODY"];
				if(empty($test_mediation_body["OFFICIALNAME"]) || empty($test_mediation_body["TOWN"]) || empty($test_mediation_body["COUNTRY"]["ATTRIBUTE"]["VALUE"])) {
					unset($_POST["guue"]["COMPLEMENTARY_INFO"]["ADDRESS_MEDIATION_BODY"]);
				}

			}

			// VERIFICA SE POSSIBILE ISTITUIRE UN SISTEMA DINAMICO DI ACQUISIZIONE UTILIZZATO DA ALTRI COMMITTENTI
			if(!empty($_POST["guue"]["PROCEDURE"]["DPS_ADDITIONAL_PURCHASERS"]) && (empty($_POST["guue"]["CONTRACTING_BODY"]["CENTRAL_PURCHASING"]))) {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #027</strong><br><strong> Il sistema dinamico di acquisizione può essere utilizzato da altri committenti IV.1.3) solo nel caso in cui l&#39;appalto è aggiudicato da una centrale di committenza I.2)</strong></div>');
				<?
				die();
			}

			if(!empty($guue["PROCEDURE"]["TERMINATION_DPS"]) && empty($_POST["guue"]["PROCEDURE"]["DPS"])) {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #026</strong><br><strong>L&#39;avviso può comportare la chiusura del sistema dinamico di acquisizione pubblicato nel bando di gara di cui sopra IV.2.8) solo se &egrave; stato istituito un sistema dinamico di acquisizione sez. IV.1.3)</strong></div>');
				<?
				die();
			}


			// VERIFICA SE POSSIBILE ISTITUIRE UN SISTEMA DINAMICO DI ACQUISIZIONE
			if(!empty($_POST["guue"]["PROCEDURE"]["DPS"]) && (empty($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"]) || ($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"] != "PT_RESTRICTED"))) {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #026</strong><br><strong>L&#39;avviso può comportare l&#39;istituzione di un sistema dinamico di acquisizione IV.1.3) solo nel caso in cui la procedura sia ristretta IV.1.1)</strong></div>');
				<?
				die();
			}

			// VERIFICA DELLA PRESENZA DELLE AMMINISTRAZIONI AGGIUDICATRICI SUPPLEMENTARI IN CASO DI APPALTO CONGIUNTO
			if(!empty($_POST["guue"]["CONTRACTING_BODY"]["JOINT_PROCUREMENT_INVOLVED"]) && empty($_POST["guue"]["CONTRACTING_BODY"]["ADDRESS_CONTRACTING_BODY_ADDITIONAL"])) {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #025</strong><br><strong>In caso di appalto congiunto I.2) è necessario indicare nella sezione I.1) le amministrazioni aggiudicatrici supplementari</strong></div>');
				<?
				die();
			} elseif (empty($_POST["guue"]["CONTRACTING_BODY"]["JOINT_PROCUREMENT_INVOLVED"]) && !empty($_POST["guue"]["CONTRACTING_BODY"]["ADDRESS_CONTRACTING_BODY_ADDITIONAL"])) {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #025</strong><br><strong>In caso di amministrazioni aggiudicatrici supplementari è necessario completare la sezione I.2) Appalto Congiunto</strong></div>');
				<?
				die();
			}

			// VERIFICA NUMERO DI AVVISO RELATIVO ALLA PUBBLICAZIONE
			if(!empty($_POST["guue"]["PROCEDURE"]["NOTICE_NUMBER_OJ"])) {
				$regex = '/20\d{2}\/S \d{3}-\d{6}/';
				if(preg_match($regex, $_POST["guue"]["PROCEDURE"]["NOTICE_NUMBER_OJ"]) == 0)
				{
					?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #021</strong><br><strong>IV.2.1) il numero dell&#39;avviso relativo alla pubblicazione precedente non è valido.</strong></div>');
					<?
					die();
				}
			}

			//VERIFICA INFORMAZIONI RELATIVE AD UNA PARTICOLARE PROFESSIONE
			if(!empty($_POST["guue"]["OBJECT_CONTRACT"]["TYPE_CONTRACT"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_type_contract"]) && $_POST["guue"]["OBJECT_CONTRACT"]["TYPE_CONTRACT"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_type_contract"] != "SERVICES") {
				if(!empty($_POST["guue"]["LEFTI"]["REFERENCE_TO_LAW"])) {
					?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #013</strong><br><strong>Le informazioni relative ad una particolare professione III.2.1) possono essere fornite solo se nella sezione II.1.3) è selezionata la tipologia "SERVIZI"!</strong></div>');
					<?
					die();
				}
			}

			//VERIFICA ISTITUZIONE SISTEMA DINAMICO DI ACQUISIZIONE
			if(!empty($_POST["guue"]["PROCEDURE"]["DPS"]) && !empty($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"]) && $_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"] != "PT_RESTRICTED") {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #014</strong><br><strong>L&#39;avviso può comporta l&#39;istituzione di un sistema dinamico di acquisizione IV.1.3) solo nel caso di una procedura ristretta IV.1.1)</strong></div>');
				<?
				die();
			}

			if(!empty($_POST["guue"]["LEFTI"]["CRITERIA_SELECTION"]) && !empty($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"]) && $_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"] != "PT_RESTRICTED") {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #121</strong><br><strong>I criteri di selezione dei partecipanti sez. III.1.10) possono essere specificati solo nel caso di una procedura ristretta IV.1.1)</strong></div>');
				<?
				die();
			}

			//VERIFICA UTILIZZO SISTEMA DINAMICO DI ACQUISIZIONE DA ALTRI COMMITTENTI
			if(!empty($_POST["guue"]["PROCEDURE"]["DPS_ADDITIONAL_PURCHASERS"]) && !isset($_POST["guue"]["CONTRACTING_BODY"]["CENTRAL_PURCHASING"])) {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #015</strong><br><strong>Il sistema dinamico di acquisizione pu&ograve; essere utilizzato da altri committenti IV.1.3) solo se l&#39;appalto è aggiudicato da una centrale di committenza I.2)</strong></div>');
				<?
				die();
			}

			//VERIFICA RICORSO AD UNA PROCEDURA IN PIÙ FASI
			if(!empty($_POST["guue"]["PROCEDURE"]["REDUCTION_RECOURSE"])) {
				if(!in_array($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"], array('PT_COMPETITIVE_NEGOTIATION', 'PT_COMPETITIVE_DIALOGUE', 'PT_INNOVATION_PARTNERSHIP'))) {
					?>
						jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #016</strong><br><strong>Si pu&ograve; fare ricorso ad una procedura in più fasi IV.1.4) solo con le seguenti procedure: "Procedura competitiva con negoziazione", "Dialogo competitivo", "Partenariato per l&#39;innovazione" IV.1.1)</strong></div>');
					<?
					die();
				}
			}

			//VERIFICA FACOLTÀ DI AGGIUDICARE IL CONTRATTO D'APPALTO SULLA BASE DELLE OFFERTE INIZIALI SENZA CONDURRE UNA NEGOZIAZIONE
			if(!empty($_POST["guue"]["PROCEDURE"]["RIGHT_CONTRACT_INITIAL_TENDERS"]) && $_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"] != "PT_COMPETITIVE_NEGOTIATION") {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #017</strong><br><strong>Si pu&ograve; fare ricorso alla facoltà di aggiudicare il contratto d&#39;appalto sulla base delle offerte iniziali senza condurre una negoziazione IV.1.5) solo con la procedura: "Procedura competitiva con negoziazione" IV.1.1)</strong></div>');
				<?
				die();
			}

			//VERIFICA LOTTI
			if(!empty($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"])) {
				foreach ($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"] as $lot_key => $lot) {

					//SE SELEZIONATO CRITERI INDICATI DI SEGUITO
					if(!empty($lot["radio_as_select_for_award_criteria_doc"]) && $lot["radio_as_select_for_award_criteria_doc"] == "AWARD_CRITERIA_ITEM_TO_IGNORE") {
						if(!empty($lot["AC"]["AC_COST"]) && !empty($lot["AC"]["AC_PRICE"])) {
							?>
								jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #032</strong><br><strong> Se indicato un criterio di costo II.2.5) non pu&ograve; essere indicato anche un criterio di prezzo</strong></div>');
							<?
							die();
						}
					}

					if((!isset($lot["AC"]["AC_QUALITY"]) || empty($lot["AC"]["AC_QUALITY"])) && (!empty($lot["AC"]["AC_PRICE"]))) {
						foreach ($lot["AC"]["AC_PRICE"] as $ac_price_criteria) {
							if(!empty($ac_price_criteria["AC_WEIGHTING"])) {
								?>
								jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #039</strong><br><strong> Se indicato solo il criterio di prezzo II.2.5) non deve essere indicato il valore della ponderazione</strong></div>');
								<?
								die();
							}
						}
					}

					if(!empty($lot["AC"]["AC_QUALITY"]) && empty($lot["AC"]["AC_COST"]) && empty($lot["AC"]["AC_PRICE"]) && $_POST["attributi_form"]["FORM"] != "F25") {
						?>
							jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #031</strong><br><strong> Se indicato un criterio di qualit&agrave; II.2.5) deve essere indicato anche un criterio di costo o prezzo</strong></div>');
						<?
						die();
					}

					if(isset($unset_title) && $unset_title) {
						unset($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"][$lot_key]["TITLE"]);
						unset($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"][$lot_key]["LOT_NO"]);
						if (isset($_POST["guue"]["AWARD_CONTRACT"][$lot_key]["LOT_NO"])) unset($_POST["guue"]["AWARD_CONTRACT"][$lot_key]["LOT_NO"]);
						if (isset($_POST["guue"]["AWARD_CONTRACT"][$lot_key]["TITLE"])) unset($_POST["guue"]["AWARD_CONTRACT"][$lot_key]["TITLE"]);
					}

					if(!empty($lot["VAL_OBJECT"]["val"]) && empty($lot["VAL_OBJECT"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"])) {
						?>
						jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #011</strong><br><strong>Se nella sezione II.2.6) è necessario indicare una valuta!</strong></div>');
						<?
						die();
					} elseif (empty($lot["VAL_OBJECT"]["val"])) {
						unset($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"][$lot_key]["VAL_OBJECT"]);
					}

					//VERIFICA SEZIONE 2_2_9
					if(!empty($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"]) && $_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"] == "PT_OPEN") {
						unset($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"][$lot_key]["NB_ENVISAGED_CANDIDATE"]);
						unset($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"][$lot_key]["NB_MIN_LIMIT_CANDIDATE"]);
						unset($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"][$lot_key]["NB_MAX_LIMIT_CANDIDATE"]);
						unset($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"][$lot_key]["CRITERIA_CANDIDATE"]);
					}

					if(empty($lot["CRITERIA_CANDIDATE"]) && (!empty($lot["NB_ENVISAGED_CANDIDATE"]) || !empty($lot["NB_MIN_LIMIT_CANDIDATE"]) || !empty($lot["NB_MAX_LIMIT_CANDIDATE"]))) {
						?>
						jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #010</strong><br><strong>Se nella sezione II.2.9) è indicato un limite relativo al numero di candidati che saranno invitati a partecipare è necessario indicare anche i Criteri obiettivi per la selezione del numero limitato di candidati</strong></div>');
						<?
						die();
					} elseif (empty($lot["NB_ENVISAGED_CANDIDATE"]) && empty($lot["NB_MIN_LIMIT_CANDIDATE"]) && empty($lot["NB_MAX_LIMIT_CANDIDATE"])) {
						if(isset($lot["CRITERIA_CANDIDATE"])) {
							?>
							jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #032</strong><br><strong>Se nella sezione II.2.9) sono indicati i criteri obiettivi per la selezione del numero limitato di candidati è necessario indicare il numero previsto di candidati o max/min </strong></div>');
							<?
							die();
						}
					}
				}
			}

			if(!empty($_POST["guue"]["PROCEDURE"]["DATE_TENDER_VALID"]) && !empty($_POST["guue"]["PROCEDURE"]["DURATION_TENDER_VALID"]["val"])) {
				?>
				jalert('<div style="text-align:center">Errore di compilazione <strong>cod. #022</strong><br><strong>Nella sezione IV.2.6) il periodo minimo durante il quale l&#39;offerente è vincolato alla propria offerta può essere una data oppure un numero di mesi. </strong></div>');
				<?
				die();
			}

			if(!empty($_POST["guue"]["PROCEDURE"]["DATE_DISPATCH_INVITATIONS"]) && !empty($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"]) && $_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"] !== "PT_RESTRICTED") {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #121</strong><br><strong>La data stimata di spedizione ai candidati prescelti degli inviti a presentare offerte o a partecipare sez. IV.2.3) pu&ograve; essere specificatio solo nel caso di una procedura ristretta IV.1.1)</strong></div>');
				<?
				die();
			}

			$reset_lot_division = FALSE;
			if(in_array($_POST["numero_form"], array(101, 102, 103, 41, 42, 43))) {
				if(!empty($_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] == "LOT_DIVISION" && !empty($_POST["guue"]["OBJECT_CONTRACT"]["LOT_DIVISION"])) {
					unset($_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]);
					$reset_lot_division = TRUE;
				}
			}

			$reset_no_lot_division = FALSE;
			$reset_lot_division_item_to_ignore = FALSE;
			if(in_array($_POST["numero_form"], array(221, 222, 225, 226))) {
				if(!empty($_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] == "NO_LOT_DIVISION") {
					unset($_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]);
					$reset_no_lot_division = TRUE;
				} elseif(!empty($_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] == "LOT_DIVISION_ITEM_TO_IGNORE" && empty($_POST["guue"]["OBJECT_CONTRACT"]["LOT_DIVISION"])) {
					$_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] = "LOT_DIVISION";
					$reset_lot_division_item_to_ignore = TRUE;
				}
			}

			$reset_particular_profession = FALSE;
			if(!empty($_POST["guue"]["LEFTI"]["radio_as_select_for_particular_profession"]) && $_POST["guue"]["LEFTI"]["radio_as_select_for_particular_profession"] == "PARTICULAR_PROFESSION") {
				unset($_POST["guue"]["LEFTI"]["radio_as_select_for_particular_profession"]);
				$reset_particular_profession = TRUE;
			}

		}


		$no_guue = 0;
	    $bind = array(':current_year' => date('Y'));
	    $sql = "SELECT MAX(no_guue) AS no_guue FROM b_pubb_guue WHERE anno_no_guue = :current_year";
	    $ris = $pdo->bindAndExec($sql, $bind);
	    if($ris->rowCount() > 0) $no_guue = $ris->fetch(PDO::FETCH_ASSOC)["no_guue"] + 1;
		session_write_close();

		// if(!empty($_POST["guue"]["AWARD_CONTRACT"])) {
		// 	foreach ($_POST["guue"]["AWARD_CONTRACT"] as $lot) {
		// 		var_dump($lot["AWARDED_CONTRACT"]["VALUE"]);
		// 		die();
		// 	}
		// }

		$uuid = gen_uuid();
		if(! empty($_POST["codice"])) {
			$ris_uuid = $pdo->bindAndExec("SELECT uuid FROM b_pubb_guue WHERE codice = :codice", array(':codice' => $_POST["codice"]));
			$rec_uuid = $ris_uuid->fetch(PDO::FETCH_ASSOC);
			if(! empty($rec_uuid["uuid"])) $uuid = $rec_uuid["uuid"];
		}

		$xmlgen = new post2xml();
		$xmlgen->setNoGuue($no_guue);
		$xmlgen->setUUID($uuid);
		$xmlgen->codice_gara = 1;
		$xmlgen->customer_login = $_SESSION["ente"]["id_guue"];
		$xmlgen->form = $_POST["tipologia_form"];
		$xmlgen->form_attribute = array(
			'CATEGORY' => 'ORIGINAL',
			// 'FORM' => 'F' . (strlen($_POST["numero_form"]) > 1 ? $_POST["numero_form"] : '0' . $_POST["numero_form"]),
			'FORM' => $_POST["attributi_form"]["FORM"],
			'LG' => 'IT',
			);
		$xmlgen->post = $_POST;
		$xmlgen->setMainCpv($_POST["guue"]["main_cpv"]);
		$xmlgen->setSupplementaryCpv($_POST["guue"]["supplementary_cpv"]);

		$xml = $xmlgen->createXML();

		if($_POST["bozza"] != 1) {
			if(!empty($reset_lot_division) && $reset_lot_division) $_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] = "LOT_DIVISION";
			if(!empty($reset_lot_division_item_to_ignore) && $reset_lot_division_item_to_ignore) $_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] = "LOT_DIVISION_ITEM_TO_IGNORE";
			if(!empty($reset_no_lot_division) && $reset_no_lot_division) $_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] = "NO_LOT_DIVISION";
			if(!empty($reset_particular_profession) && $reset_particular_profession) $_POST["guue"]["LEFTI"]["radio_as_select_for_particular_profession"] = "PARTICULAR_PROFESSION";
			if(!empty($reset_radio_as_select_for_lot_division) && $reset_radio_as_select_for_lot_division && !in_array($_POST["numero_form"], array(233, 234)) ) $_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] = "LOT_DIVISION_ITEM_TO_IGNORE";
			if(!empty($reset_radio_as_select_for_public_agreement) && is_string($reset_radio_as_select_for_public_agreement)) $_POST["guue"]["PROCEDURE"]["radio_as_select_for_public_agreement"] = $reset_radio_as_select_for_public_agreement;
			if(!empty($reset_no_awarded_to_group) && is_array($reset_no_awarded_to_group)) {
				foreach ($reset_no_awarded_to_group as $key => $value) {
					if($value) $_POST["guue"]["AWARD_CONTRACT"][$key]["AWARDED_CONTRACT"]["radio_as_select_for_awarded_to_group"] = "NO_AWARDED_TO_GROUP";
				}
			}
			if(!empty($reset_no_sme) && is_array($reset_no_sme)) {
				foreach ($reset_no_sme as $award_contract_key => $award_contract) {
					if(!empty($award_contract) && is_array($award_contract)) {
						foreach ($award_contract as $contractor_key => $contractor_value) {
							$_POST["guue"]["AWARD_CONTRACT"][$award_contract_key]["AWARDED_CONTRACT"]["CONTRACTOR"][$contractor_key]["radio_as_select_for_is_an_sme"] = "NO_SME";
						}
					}
				}
			}
		}

		$data = array(
			"codice" => $_POST["codice"],
			"v_form" => $_POST["v_form"],
			"numero_form" => $_POST["numero_form"],
			"uuid" => $uuid,
			"tipologia_form" => $_POST["tipologia_form"],
			"titolo_pubblicazione" => $_POST["titolo_pubblicazione"],
			"codice_ente" => $_SESSION["ente"]["codice"],
			"codice_gara" => !empty($_POST["codice_gara"]) ? $_POST["codice_gara"] : null,
			"post_form" => json_encode($_POST["guue"]),
			"xml" => $xml,
			"data_trasmissione" => '00-00-0000 00:00:00',
			"data_pubblicazione" => '00-00-0000 00:00:00',
			);

		if(!empty($_SESSION["guue"]["json_form"])) {
			$data["json_form"] = json_encode($_SESSION["guue"]["json_form"]);
		}

		$msg = "Il modello è stato salvato ed è pronto per la trasmissione.";
		$data["stato"] = "PRONTO PER LA TRASMISSIONE";
		if($_POST["bozza"] == 1) {
			$data["stato"] = "BOZZA";
			$msg = "La bozza è stata salvata correttamente.";
		}

		$salva = new salva();
		$salva->debug = FALSE;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_pubb_guue";
		$salva->operazione = empty($_POST["codice"]) ? 'INSERT' : 'UPDATE';
		$salva->oggetto = $data;
		$codice = $salva->save();
		if(is_numeric($codice)) {
			$href = "/guue";
			if(!empty($_POST["codice_gara"])) $href = "/gare/guue/index.php?codice=".$_POST["codice_gara"];
			?>
			alert('<?= $msg ?>');
			window.location.href = "<?= $href ?>";
	  <?
		} else {
			?>
			jalert('<div style="text-align:center">Impossibile salvare la bozza del modello <strong>cod. #002</strong><br><strong>Si prega di riprovare!</strong></div>');
			<?
		}
	} else {
		?>
		jalert('<div style="text-align:center">Impossibile salvare la bozza del modello <strong>cod. #002</strong><br><strong>Si prega di riprovare!</strong></div>');
		<?
		die();
	}
?>
