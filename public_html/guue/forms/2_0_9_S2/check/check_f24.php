<?
	//VERIFICA INFORMAZIONI RELATIVE AGLI ACCORDI DEGLI APPALTI PUBBLICI AAP 
	if(!empty($_POST["guue"]["PROCEDURE"]["radio_as_select_for_public_agreement"]) && $_POST["guue"]["OBJECT_CONTRACT"]["TYPE_CONTRACT"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_type_contract"] != "WORKS") {
		?>
		jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #013</strong><br><strong>Le informazioni relative all&#39;accordo sugli appalti pubblici (AAP) sez. IV.1.8) possono essere fornite solo se nella sezione II.1.3) è selezionata la tipologia "LAVORI"!</strong></div>');
		<?
		die();
	} elseif ($_POST["guue"]["OBJECT_CONTRACT"]["TYPE_CONTRACT"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_type_contract"] == "WORKS" && empty($_POST["guue"]["PROCEDURE"]["radio_as_select_for_public_agreement"])) {
		?>
		jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #013</strong><br><strong>Le informazioni relative all&#39;accordo sugli appalti pubblici (AAP) sez. IV.1.8) sono obbligatorie se nella sezione II.1.3) è selezionata la tipologia "LAVORI"!</strong></div>');
		<?
		die();
	}

	$reset_radio_as_select_for_public_agreement = FALSE;
	if(!empty($_POST["guue"]["PROCEDURE"]["radio_as_select_for_public_agreement"])) {
		$tmp = $_POST["guue"]["PROCEDURE"];
		$value = $_POST["guue"]["PROCEDURE"]["radio_as_select_for_public_agreement"];
		$keys = array_keys($tmp);
		$values = array_values($tmp);
		$index = array_search("radio_as_select_for_public_agreement", $keys);
		$keys[$index] = $value;
		$values[$index] = array('ATTRIBUTE' => array('CTYPE' => 'WORKS'));
		$_POST["guue"]["PROCEDURE"] = array_combine($keys, $values);
		$reset_radio_as_select_for_public_agreement = $value;
	}

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
		if(count($_POST["guue"]["OBJECT_CONTRACT"]["OBJECT_DESCR"]) > 1) {
			?>
			jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #025</strong><br><strong>Nelle informazioni relative ai lotti II.1.6) è stato indicata la presenza di un singolo lotto perciò non è possibile inserire le informazioni per più di un lotto nella sezione II.2</strong></div>');
			<?
			die();
		}
	} else {
		if(empty($_POST["guue"]["OBJECT_CONTRACT"]["LOT_DIVISION"])) {
			$reset_radio_as_select_for_lot_division = TRUE;
			$_POST["guue"]["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] = "LOT_DIVISION";
		}
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