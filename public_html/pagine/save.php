<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {

		$edit = check_permessi("pagine",$_SESSION["codice_utente"]);
		if (!$edit) {

			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["operazione"])) {


			if (isset($_SESSION["ente"])) $_POST["codice_ente"]=$_SESSION["ente"]["codice"];
			if ($_POST["sezione"] == 'Altro') $_POST["sezione"] = $_POST["sezione_altro"];
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_pagina";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST;
			$codice = $salva->save();
			if ($codice > 0) {
			$href = "/pagine/id" . $codice . "-anteprima";
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			if ($_POST["operazione"]=="UPDATE") {

				?>
				alert('Modifica effettuata con successo');
        	    <?
			} elseif ($_POST["operazione"]=="INSERT") {
				?>
				alert('Inserimento effettuato con successo');
        	    <?
			}
?>

			window.location.href = '<? echo $href ?>';
			<?
} else {
	?>
	alert("Errore nel salvataggio. Si prega di riprovare");
	<?
}
		}
	}



?>
