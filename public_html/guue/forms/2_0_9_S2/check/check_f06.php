<?

	// VERIFICA VALORE TOTALE DELL'APPALTO
	if(!empty($_POST["guue"]["OBJECT_CONTRACT"]["VAL_TOTAL"]["val"]) ||
		!empty($_POST["guue"]["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["HIGH"]) ||
		!empty($_POST["guue"]["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["LOW"])	) {
		if(!empty($_POST["guue"]["AWARD_CONTRACT"]) && is_array($_POST["guue"]["AWARD_CONTRACT"])) {
			$awarded_contract = FALSE;
			foreach ($_POST["guue"]["AWARD_CONTRACT"] as $award_contract) {
				if($awarded_contract) break;
				$awarded_contract = $award_contract["radio_as_select_for_awarded_contract"] == "AWARDED_CONTRACT_ITEM_TO_IGNORE" ? TRUE : FALSE;
			}
			/*
			if(!$awarded_contract) {
				?>
				jalert('<div style="text-align:center">Errore di compilazione <strong>cod. #301</strong><br><strong>Se nessun appalto &egrave; aggiudicato sez. V) allora non &egrave; possibile specificare nella sez. II.1.7) il valore totale dell&#39;appalto</strong></div>');
				<?
				die();
			}
			*/
		}
	}

	//VERIFICA SUI LOTTI
	if(!empty($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"])) {
		foreach ($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"] as $lot_key => $lot) {

			// VERIFICA CHE SIA SPECIFICATO ALMENO UN CRITERIO
			if(empty($lot["AC"]["AC_QUALITY"]) && empty($lot["AC"]["AC_COST"]) && empty($lot["AC"]["AC_PRICE"])) {
				?>
					jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #031</strong><br><strong> &Egrave; necessario specificare almeno un criterio nella sezione II.2.5) </strong></div>');
				<?
				die();
			}
		}
	}
	
	//VERIFICA AGGIUDICAZIONE A RAGGRUPPAMENTO
	if(!empty($_POST["guue"]["AWARD_CONTRACT"])) {
		foreach ($_POST["guue"]["AWARD_CONTRACT"] as $award_contract_key => $award_contract) {
      if(!empty($_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] == "NO_LOT_DIVISION") {
        unset($_POST["guue"]["AWARD_CONTRACT"][$award_contract_key]["LOT_NO"]);
      }
			if($award_contract["radio_as_select_for_awarded_contract"] == "AWARDED_CONTRACT_ITEM_TO_IGNORE") {
				if(empty($award_contract["AWARDED_CONTRACT"]["COUNTRY_ORIGIN"]["radio_as_select_for_community_origin"])) {
					// unset($_POST["guue"]["AWARD_CONTRACT"][$award_contract_key]["AWARDED_CONTRACT"]["COUNTRY_ORIGIN"]);
					?>
						jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #601</strong><br><strong> &Egrave; necessario specificare nella sez. V.2.8) il Paese di origine del prodotto o del servizio </strong></div>');
					<?
					die();
				}
				
				if(empty($award_contract["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["val"])) {
					unset($_POST["guue"]["AWARD_CONTRACT"][$award_contract_key]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]);
				}

				if(!empty($award_contract["AWARDED_CONTRACT"]["CONTRACTORS"]["radio_as_select_for_awarded_to_group"]) && $award_contract["AWARDED_CONTRACT"]["CONTRACTORS"]["radio_as_select_for_awarded_to_group"] == "AWARDED_TO_GROUP" && ( !is_array($award_contract["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"]) || count($award_contract["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"]) < 2) ) {
					?>
						jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #601</strong><br><strong> Nel caso di aggiudicazione a un raggruppamento di operatori economici sez. V.2) &egrave; necessario specificare tutti gli operatori nella sezione V.2.3) </strong></div>');
					<?
					die();
				} elseif (!empty($award_contract["AWARDED_CONTRACT"]["CONTRACTORS"]["radio_as_select_for_awarded_to_group"]) && $award_contract["AWARDED_CONTRACT"]["CONTRACTORS"]["radio_as_select_for_awarded_to_group"] == "NO_AWARDED_TO_GROUP" && ( !is_array($award_contract["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"]) || count($award_contract["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"]) > 1) ) {
					?>
						jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #602</strong><br><strong> Nel caso di aggiudicazione a un solo operatore economico sez. V.2) &egrave; possibile indicare solo un operatore nella sezione V.2.3) </strong></div>');
					<?
					die();
				}
			}
		}
	}

	$pattern_notice_number_oj = "/(19|20)\d{2}\/(S)\s\d{3}-\d{6}/";
	if(!empty($_POST["guue"]["PROCEDURE"]["NOTICE_NUMBER_OJ"]) && !preg_match($pattern_notice_number_oj, $_POST["guue"]["PROCEDURE"]["NOTICE_NUMBER_OJ"])) {
		?>
		jalert('<div style="text-align:center">Errore di compilazione <strong>cod. #142</strong><br><strong>Verificare che il numero dell&#39;avviso nella GU nella sez. IV.2.1 sia corretto</strong></div>');
		<?
		die();
	}

	/*
	if((!empty($_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] != "NO_LOT_DIVISION" ) || (count($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"]) > 1) ) {
			if(empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["val"])  && empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["LOW"]) && empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["HIGH"])) {
			?>
			jalert('<div style="text-align:center">Errore di compilazione <strong>cod. #501</strong><br><strong>Nel caso di pi&ugrave; lotti &egrave; necessario specificare il valore totale dell&#39;appalto nella sez. II.2.7)</strong></div>');
			<?
			die();
		}
	}
	*/

	// VERIFICA TITOLO LOTTO SE NON SI TRATTA DI UNA GARA MULTILOTTO
	$unset_title = FALSE;
	if(!empty($_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] == "NO_LOT_DIVISION") {
		$unset_title = TRUE;
		if(!empty($_POST["guue"]["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_MAX_ONE_TENDERER"])) {
			?>
			jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #023</strong><br><strong>Nelle informazioni relative ai lotti II.1.6) è stato indicata la presenza di un solo lotto perciò non è possibile indicare il numero massimo di lotti che possono essere aggiudicati a un offerente</strong></div>');
			<?
			die();
		}
		if(!empty($_POST["guue"]["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_COMBINING_CONTRACT_RIGHT"])) {
			?>
			jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #024</strong><br><strong>Nelle informazioni relative ai lotti II.1.6) è stato indicata la presenza di un solo lotto perciò non è possibile indicare la facoltà di aggiudicare i contratti d&#39;appalto combinando lotti o gruppi di lotti</strong></div>');
			<?
			die();
		}
	} else {
		if(empty($_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"])) {
			?>
			jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #020</strong><br><strong>Nelle informazioni relative ai lotti II.1.6) è necessario indicare se l&#39;appalto è suddiviso/non suddiviso in lotti</strong></div>');
			<?
			die();
		} else {
			if(count($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"]) < 2) {
				?>
				jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #021</strong><br><strong>Nelle informazioni relative ai lotti II.1.6) è stato indicata la presenza di più lotti perciò nella sezione II.2) sono necessari almeno 2 lotti</strong></div>');
				<?
				die();
			}
		}
	}
?>