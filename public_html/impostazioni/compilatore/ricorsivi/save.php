<?
	include_once("../../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
		if (isset($_POST)) {

			$operazione = $_POST["operazione"];
			$codice = $_POST["codice"];

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_paragrafi_ricorsivi";
			$salva->operazione = $operazione;
			$salva->oggetto = $_POST;
			$codice = $salva->save();

			if ($_POST["operazione"]=="UPDATE") {
				$href = "/impostazioni/compilatore/ricorsivi/index.php";
				?>
				alert('Modifica effettuata con successo');
				window.location.href = '<? echo $href ?>';
				<?
			} elseif ($_POST["operazione"]=="INSERT") {
				$href = "/impostazioni/compilatore/ricorsivi/index.php";
				?>
				alert('Inserimento effettuato con successo');
				window.location.href = '<? echo $href ?>';
				<?
			}
		}
	}

?>
