<?
	//VERIFICA DATA STIMATA DI SPEDIZIONE AI CANDIDATI PRESCELTI DEGLI INVITI A PRESENTARE OFFERTE O A PARTECIPARE
	//VERIFICA MODALITÀ DI APERTURA DELLE OFFERTE
	if($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"] != "PT_OPEN") {
		if(!empty($_POST["guue"]["PROCEDURE"]["OPENING_CONDITION"]["DATE_OPENING_TENDERS"]) || 
			!empty($_POST["guue"]["PROCEDURE"]["OPENING_CONDITION"]["TIME_OPENING_TENDERS"]) ||
			!empty($_POST["guue"]["PROCEDURE"]["OPENING_CONDITION"]["PLACE"])) {
			?>
			jalert('<div style="text-align:center">Errore di compilazione <strong>cod. #019</strong><br><strong>Le modalit&agrave; di apertura delle offerte IV.2.7) possono essere indicate solo se il tipo di procedura è "Aperta" IV.1.1) </strong></div>');
			<?
			die();
		}
		unset($_POST["guue"]["PROCEDURE"]["OPENING_CONDITION"]);
	} else {
		//VERIFICA DATA STIMATA DI SPEDIZIONE AI CANDIDATI PRESCELTI DEGLI INVITI A PRESENTARE OFFERTE O A PARTECIPARE
		if(!empty($_POST["guue"]["PROCEDURE"]["DATE_DISPATCH_INVITATIONS"])) {
			?>
			jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #018</strong><br><strong>La data stimata di spedizione ai candidati prescelti degli inviti a presentare offerte o a partecipare IV.2.3) può essere indicata solo nel caso in cui non si utilizzi la proceduta aperta IV.1.1) </strong></div>');
			<?
			die();
		}
		unset($_POST["guue"]["PROCEDURE"]["DATE_DISPATCH_INVITATIONS"]);

		//VERIFICA MODALITÀ DI APERTURA DELLE OFFERTE
		if(empty($_POST["guue"]["PROCEDURE"]["OPENING_CONDITION"]["DATE_OPENING_TENDERS"]) && 
			empty($_POST["guue"]["PROCEDURE"]["OPENING_CONDITION"]["TIME_OPENING_TENDERS"]) &&
			empty($_POST["guue"]["PROCEDURE"]["OPENING_CONDITION"]["PLACE"])) {
			?>
			jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #012</strong><br><strong>Selezionando la procedura Aperta nella sezione IV.1.1) è necessario indicare le modalità di apertura delle offerte nella sezione IV.2.7)</strong></div>');
			<?
			die();
		}
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