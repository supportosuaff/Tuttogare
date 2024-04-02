<?
	session_start();
	include_once("../../../config.php");
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
	if (!$edit) {
		die();
	} else {

		if (isset($_POST) && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
			$codice = $_POST["codice"];
			$operazione_generale = $_POST["operazione"];
			$tabella = "b_modelli";
			$duplica = false;
			if ($_POST["duplica"]=="S") {
				$operazione_generale = "INSERT";
				$codice_origine = $codice;
				$codice = 0;
				$_POST["codice"] = 0;
				$duplica = true;
			}
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = $tabella;
			$salva->operazione = $operazione_generale;
			$salva->oggetto = $_POST;
			$codice = $salva->save();

			if ($codice !== false) {
				if (!$duplica) {
					if (isset($_POST["paragrafo"])) {
						foreach($_POST["paragrafo"] AS $paragrafo) {
							$operazione = "UPDATE";
							$codice_paragrafo = $paragrafo["codice"];
							if (!is_numeric($paragrafo["codice"])) {
								$operazione = "INSERT";
								$codice_paragrafo = 0;
								if ($paragrafo["ordinamento"] == "") {
									$sql = "SELECT MAX(ordinamento) AS ordinamento FROM b_paragrafi WHERE codice_modello = :codice";
									$ris = $pdo->bindAndExec($sql,array(":codice"=>$codice));
									if ($ris->rowCount()>0) {
										$ordinamento = $ris->fetch(PDO::FETCH_ASSOC);
										$paragrafo["ordinamento"] = $ordinamento["ordinamento"]++;
									} else {
										$paragrafo["ordinamento"] = 0;
									}
								}
							}
							$paragrafo["codice_modello"] = $codice;
							if (isset($paragrafo["codice_opzione"])) $paragrafo["codice_opzione"] = implode(",",$paragrafo["codice_opzione"]);

							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_paragrafi";
							$salva->operazione = $operazione;
							$salva->oggetto = $paragrafo;
							$codice_paragrafo = $salva->save();

						}
					}
				} else {

					$sql = "SELECT * FROM b_paragrafi WHERE codice_modello = :codice_origine AND eliminato = 'N' ORDER BY ordinamento ";
					$ris_paragrafi = $pdo->bindAndExec($sql,array(":codice_origine"=>$codice_origine));
					if ($ris_paragrafi->rowCount()>0) {
						while($paragrafo = $ris_paragrafi->fetch(PDO::FETCH_ASSOC)) {
							$paragrafo["codice"] = 0;
							$paragrafo["codice_modello"] = $codice;

							$salva = new salva();
							$salva->debug = FALSE;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_paragrafi";
							$salva->operazione = "INSERT";
							$salva->oggetto = $paragrafo;
							$codice_paragrafo = $salva->save();

						}
					}
				}
				// $href = "/impostazioni/gruppi_modelli/";
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
				window.location.href = '/impostazioni/gruppi_modelli/edit.php?codice=<?= $codice ?>';
				<?
			}
		}
	}
?>
