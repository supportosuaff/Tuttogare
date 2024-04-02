<?
	if($_POST["guue"]["PROCEDURE"]["DURATION_TENDER_VALID"]["ATTRIBUTE"]["TYPE"] == "MONTH") {
		if(empty($_POST["guue"]["PROCEDURE"]["DURATION_TENDER_VALID"]["val"]) || ! is_numeric($_POST["guue"]["PROCEDURE"]["DURATION_TENDER_VALID"]["val"])) {
			?>
			jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #0002</strong><br><strong>Durata in Mesi per il periodo minimo durante il quale l&#39;offerente è vincolato alla propria offerta sez. IV.2.6) è obbligatoria </strong></div>');
			<?
			die();
		}
	}
	if($_POST["guue"]["PROCEDURE"]["radio_as_select_for_procedure_type"] == "PT_NEGOTIATED_WITH_PRIOR_CALL") {
		unset($_POST["guue"]["PROCEDURE"]["OPENING_CONDITION"]["DATE_OPENING_TENDERS"]);
		unset($_POST["guue"]["PROCEDURE"]["OPENING_CONDITION"]["TIME_OPENING_TENDERS"]);
		unset($_POST["guue"]["PROCEDURE"]["OPENING_CONDITION"]["PLACE"]);
	}
	include "check_f211.php";
?>