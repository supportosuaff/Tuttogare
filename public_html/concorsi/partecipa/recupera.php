<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$error = true;
	if (isset($_POST["codice_gara"]) && is_operatore()) {
		$codice_gara = $_POST["codice_gara"];

		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_concorsi.* FROM b_concorsi WHERE b_concorsi.codice = :codice ";
		$strsql .= "AND b_concorsi.annullata = 'N' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
			$sql_fasi = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice AND attiva = 'S' ORDER BY codice DESC LIMIT 0,1 ";
			$ris_fasi = $pdo->bindAndExec($sql_fasi,array(":codice"=>$record_gara["codice"]));
			if ($ris_fasi->rowCount() === 1) {
				$fase_attiva = $ris_fasi->fetch(PDO::FETCH_ASSOC);
				if (!empty($_POST["key"]) && !empty($_POST["identificativo"]) && empty($_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["salt"])) {
					$sql = "SELECT * FROM r_partecipanti_concorsi WHERE codice_fase = :codice_fase AND identificativo = :identificativo";
					$ris = $pdo->bindAndExec($sql,array(":codice_fase"=>$fase_attiva["codice"],":identificativo"=>$_POST["identificativo"]));
					if ($ris->rowCount() === 1)	{
						$partecipante = $ris->fetch(PDO::FETCH_ASSOC);
						$sql = "SELECT codice_utente FROM r_partecipanti_utenti_concorsi WHERE codice_partecipante = :codice_partecipante";
						$ris = $pdo->bindAndExec($sql,array(":codice_partecipante"=>$partecipante["codice"]));
						if ($ris->rowCount() === 1) {
							$crypted = $ris->fetch(PDO::FETCH_ASSOC);
							$codice_utente = openssl_decrypt($crypted["codice_utente"],$config["crypt_alg"],$_POST["key"],OPENSSL_RAW_DATA,$config["enc_salt"]);
							if ($codice_utente == $_SESSION["codice_utente"]) {
								$error = false;
								$_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]] = $partecipante;
								$_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["salt"] = $_POST["key"];
								?>
								window.location.reload();
								<?
							}
						}
					}
				}
			}
		}
	}
	if ($error) {
		?>
		alert("Partecipazione non riconosciuta");
		<?
	}
