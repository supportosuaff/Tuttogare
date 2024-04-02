<?
	include_once("../../../config.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && !empty($_POST["codice_gara"])) {
		$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/configura_offerta/index.php'";
		$risultato = $pdo->query($strsql);
		if ($risultato->rowCount()>0) {
			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
			$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
	if ($edit && !$lock)
	 {
		if (isset($_POST["operazione"])) {
			$_POST["options"] = "";
			if ($_POST["valutazione"] == "S") {
				if (empty($_POST["option-S"])) {
					?>
					alert('Range obbligatori');
					<?
					die();
				} else {
					$_POST["options"] = json_encode($_POST["option-S"]);
				}
			} else if ($_POST["valutazione"] == "B") {
				if (empty($_POST["option-B"]) || !is_numeric($_POST["option-B"]) || $_POST["option-B"] >= 1) {
					?>
					alert('Coefficiente è obbligatorio');
					<?
					die();
				} else {
					$_POST["options"] = $_POST["option-B"];
				}
			} else if ($_POST["valutazione"] == "Q") {
				if (empty($_POST["option-Q"]) || !is_numeric($_POST["option-Q"]) || $_POST["option-Q"] < 0) {
					?>
					alert('Coefficiente è obbligatorio');
					<?
					die();
				} else {
					$_POST["options"] = $_POST["option-Q"];
				}
			} else if ($_POST["valutazione"] == "K") {
				if (empty($_POST["option-K"]) || !is_numeric($_POST["option-K"]) || $_POST["option-K"] < 0) {
					?>
					alert('Coefficiente è obbligatorio');
					<?
					die();
				} else {
					$_POST["options"] = $_POST["option-K"];
				}
			} else if ($_POST["valutazione"] == "W") {
				if (empty($_POST["option-W"]) || !is_numeric($_POST["option-W"]) || $_POST["option-W"] < 0) {
					?>
					alert('Coefficiente è obbligatorio');
					<?
					die();
				} else {
					$_POST["options"] = $_POST["option-W"];
				}
			} else if ($_POST["valutazione"] == "E") {
				if (!empty($_POST["option-E"])) {
					$_POST["options"] = $_POST["option-E"];
				}
			}
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_valutazione_tecnica";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST;
			$codice = $salva->save();
			if ($codice != false) {
				$href = "/gare/configura_offerta/index.php?codice=".$_POST["codice_gara"]."&codice_lotto=" . $_POST["codice_lotto"];
				?>
				alert('Operazione effettuata con successo');
				window.location.href = '<? echo $href ?>';
       	<?
			} else {
				?>
				alert('Errore nel salvataggio. Si prega di riprovare');
				<?
			}
		}
	}



?>
