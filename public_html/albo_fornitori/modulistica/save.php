<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["modulo"]) && isset($_POST["codice_bando"])) {
			foreach ($_POST["modulo"] as $modulo) {
				$array = array();
				$array["titolo"] = $modulo["titolo"];
				$array["obbligatorio"] = $modulo["obbligatorio"];
				$array["codice_bando"] = $_POST["codice_bando"];
				$array["codice_ente"] = $_SESSION["ente"]["codice"];
				$operazione = "INSERT";
				$codice = 0;
				if (is_numeric($modulo["codice"])) {
					$array["codice"] = $modulo["codice"];
					$operazione = "UPDATE";
				}
				if ($modulo["filechunk"] != "")	{
					$percorso = $config["pub_doc_folder"]."/allegati/albo/" . $array["codice_bando"];
					if (!is_dir($percorso)) mkdir($percorso,0777,true);
					$copy = copiafile_chunck($modulo["filechunk"],$percorso."/",$config["chunk_folder"]."/".$_SESSION["codice_utente"]);
					$array["nome_file"] = $copy["nome_file"];
					$array["riferimento"] = $copy["nome_fisico"];
				}
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_modulistica_albo";
				$salva->operazione = $operazione;
				$salva->oggetto = $array;
				$codice_modulo = $salva->save();
			}
				$href = "/albo_fornitori/pannello.php?codice=" . $_POST["codice_bando"];
				?>
				alert('Modifica effettuata con successo');
				window.location.href = '<? echo $href ?>';
				<?

		}
	}



?>
