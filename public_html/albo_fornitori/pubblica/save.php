<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit) {
			$_POST["bando"]["codice"] = $_POST["codice_bando"];
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_bandi_albo";
			$salva->operazione = "UPDATE";
			$salva->oggetto = $_POST["bando"];
			$codice_bando = $salva->save();
			$href = "/albo_fornitori/pannello.php?codice=" . $_POST["codice_bando"];
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			?>
				alert('Pubblicazione effettuata con successo');
				window.location.href = '<? echo $href ?>';
			<?
	}



?>
