<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("dialogo_competitivo",$_SESSION["codice_utente"]);
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
			if ($_POST["operazione"]=="INSERT") {
				$bind=array(":codice_ente"=>$_SESSION["ente"]["codice"]);
				$sql = "SELECT max(id) as id FROM b_bandi_dialogo WHERE codice_gestore = :codice_ente GROUP BY codice_gestore ";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount()>0) {
					$rec = $ris->fetch(PDO::FETCH_ASSOC);
					$_POST["bando"]["id"] = $rec["id"] + 1;
				} else {
					$_POST["bando"]["id"] = 1;
				}
				$_POST["bando"]["stato"] = 1;
			}

			$_POST["bando"]["codice"] = $_POST["codice"];
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_bandi_dialogo";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST["bando"];
			$codice_bando = $salva->save();

			if (isset($_POST["cpv"])) {
						if ($_POST["cpv"] != "")  {

							$bind = array(":codice_bando"=>$codice_bando);
							$strsql = "DELETE FROM r_cpv_bandi_dialogo WHERE codice_bando = :codice_bando ";
							$risultato = $pdo->bindAndExec($strsql,$bind);

							$array_cpv = explode(";",$_POST["cpv"]);
							$codici_cpv = array();
							foreach($array_cpv as $cpv) {
								if ($cpv != "") {
									$insert_cpv=array();
									$insert_cpv["codice"] = $cpv;
									$insert_cpv["codice_bando"] = $codice_bando;

									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "r_cpv_bandi_dialogo";
									$salva->operazione = "INSERT";
									$salva->oggetto = $insert_cpv;
									$codici_cpv[] = $salva->save();
								}
							}
						}
					}
			$href = "/dialogo_competitivo/pannello.php?codice=" . $codice_bando;
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
			?>window.location.href = '<? echo $href ?>';<?
		}
	}



?>
