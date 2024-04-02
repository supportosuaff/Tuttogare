<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("gare/elaborazione",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["codice"])) {
			$codice = $_POST["codice"];
			if (is_numeric($codice)) {
				$bind=array();
				$bind[":codice"] = $codice;
				$strsql = "SELECT * FROM b_incassi WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$strsql = "DELETE FROM b_incassi  WHERE codice = :codice";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					scrivilog("b_incassi","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
					
					log_gare($_SESSION["ente"]["codice"],$record["codice_gara"],"DELETE","Eliminata Rendicontazione incassi - " . $record["descrizione"]);
				}
			}
				?>
				if ($("#incasso_<? echo $codice ?>").length > 0){
					$("#incasso_<? echo $codice ?>").remove();
					var prev_somma=parseFloat($(".importo_totale").text());
					var somma = 0;
					$(".importo").each(function(){
						somma+=parseFloat($(this).val());
					});
					$(".importo_totale").text(somma)
				}
			<?
		}
	}
?>
