<?
	if($_POST["guue"]["NOTICE"]["ATTRIBUTE"]["TYPE"] == "PER_ONLY") {
		if(empty($_POST["guue"]["OBJECT_CONTRACT"]["DATE_PUBLICATION_NOTICE"])) {
			?>
			jalert('<div style="text-align:center">Impossibile salvare questo modello <strong>cod. #0001</strong><br><strong>La data prevista di pubblicazione del bando di gara sez. II.3) è obbligatoria per gli avvisi periodici indicativi</strong></div>');
			<?
			die();
		}
	}
	include "check_f211.php";
?>