<?
	include_once(__DIR__."/../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
	if (!isset($elaborazioneApi)) {
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
			if ($codice_fase !== false) {
				$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}
	}
	if (($edit && !$lock) || isset($elaborazioneApi)) {
		if (isset($elaborazioneApi)) ob_start();
		$_POST["gara"]["codice"] = $_POST["codice_gara"];
		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_gare";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $_POST["gara"];
		$codice_gara = $salva->save();

		if ($codice_gara != false) {
			$sql = "SELECT * FROM b_gare WHERE codice = :codice_gara ";
			$ris = $pdo->bindAndExec($sql,array(":codice_gara"=>$codice_gara));
			$gara = $ris->fetch(PDO::FETCH_ASSOC);

			log_gare($_SESSION["ente"]["codice"],$codice_gara,"UPDATE","Lotti");

			if (isset($_POST["lotti"])) {
				if (is_array($_POST["lotti"]))  {
					$codici_lotti = array();
					$errore = false;
					foreach($_POST["lotti"] as $lotto) {
						$lotto["codice_gara"] = $codice_gara;
						$operazione_lotto = "UPDATE";
						if (!is_numeric($lotto["codice"])) {
							$operazione_lotto = "INSERT";
							$lotto["codice"] = 0;
						}
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "b_lotti";
						$salva->operazione = $operazione_lotto;
						$salva->oggetto = $lotto;
						$codice_lotto = $salva->save();
						if ($codice_lotto != false) {
							$codici_lotti[]  = $codice_lotto;
						} else {
							$errore = true;
						}
					}
					if ($errore) {
						?>
						alert('Errore nel salvataggio dei lotti. Riprovare!');
						<?
					}
				}
			}
			$href = "/gare/lotti/edit.php?codice=" . $codice_gara;
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			?>
			alert('Modifica effettuata con successo');
			window.location.href = '<? echo $href ?>';
			<?
		} else {
			?>
			alert('Errore nel salvataggio. Riprovare!');
			<?
		}
		if (isset($elaborazioneApi)) $return = ob_get_clean();
	}
?>
