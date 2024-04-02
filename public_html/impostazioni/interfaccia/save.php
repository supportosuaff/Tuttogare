<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}


	if (!$edit) {
		die();
	} else {
		if (isset($_POST)) {


			$strsql= "SELECT * FROM b_interfaccia WHERE codice_ente = :codice_ente";
			$risultato = $pdo->bindAndExec($strsql,array(":codice_ente"=>$_SESSION["ente"]["codice"]));
			$operazione = "INSERT";
			$_POST["interfaccia"]["codice_ente"] = $_SESSION["ente"]["codice"];
				if ($risultato->rowCount()>0) {
				$record = $risultato->fetch(PDO::FETCH_ASSOC);
				$operazione = "UPDATE";
				$_POST["interfaccia"]["codice"] = $record["codice"];
			}

			$salva = new salva();
			$salva->debug = FALSE;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_interfaccia";
			$salva->operazione = $operazione;
			$salva->oggetto = $_POST["interfaccia"];
			$codice = $salva->save();

			?>
               alert('Modifica effettuata con successo');
		       window.location.href = window.location.href;
             <?
		}
	}



?>
