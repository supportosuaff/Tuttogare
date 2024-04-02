<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("albi_commissione",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
	if ($edit && !empty($_POST)) {
		$array_id = array();
		$errore = false;
		if (!empty($_POST["iscritto"])) {
			foreach($_POST["iscritto"] as $iscritto) {
				$operazione = "INSERT";
				if (is_numeric($iscritto["codice"])) $operazione = "UPDATE";
				$iscritto["codice_albo"] = $_POST["codice_albo"];
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_commissari_albo";
				$salva->operazione = $operazione;
				$salva->oggetto = $iscritto;
				$codice = $salva->save();
				if ($codice != false) {
					?>
						$("#codice_iscritto_<? echo $iscritto["id"] ?>").val("<? echo $codice ?>");
					<?
				} else {
					$errore = true;
					?>
						$("#iscritto_<? echo $iscritto["id"] ?>").addClass("errore");
					<?
				}
			}
			if (!$errore) {
					?>
					alert('Modifica effettuata con successo');
	        window.location.href = window.location.href;
	        <?
			} else {
				?>
				alert('Si sono verificati degli errori durante il salvataggio');
				<?
			}
		} else { ?>
			alert('Nessun record modificato!');
			<?
		}
	}

?>
