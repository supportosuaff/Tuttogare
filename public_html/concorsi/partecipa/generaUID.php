<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$public = true;
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

		$accedi = false;

		if ($risultato->rowCount() > 0) {
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$i = 0;
			$open = false;
			$last = array();
			$fase_attiva = array();

			$sql_fasi = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara  ";
			$ris_fasi = $pdo->bindAndExec($sql_fasi,array(":codice_gara"=>$record_gara["codice"]));
			if ($ris_fasi->rowCount() > 0) {
				$open = true;
				while($fase = $ris_fasi->fetch(PDO::FETCH_ASSOC)) {
					if ($fase["attiva"]=="S") {
						if ($i > 0) $open = false;
						$last = $fase_attiva;
						$fase_attiva = $fase;
					}
					$i++;
				}
			}

			if ($open && !empty($fase_attiva)) {
				$accedi = true;
			} else if (!empty($last["codice"])) {
				$sql_check = "SELECT * FROM r_partecipanti_concorsi JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
								WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase AND r_partecipanti_concorsi.conferma = 1 AND r_partecipanti_concorsi.ammesso = 'S'
								AND r_partecipanti_concorsi.escluso = 'N' AND r_partecipanti_utenti_concorsi.codice_utente = :codice_utente ";
				$ris_check = $pdo->bindAndExec($sql_check,array(":codice_gara"=>$record_gara["codice"],":codice_fase"=>$last["codice"],":codice_utente"=>$_SESSION["codice_utente"]));
				if ($ris_check->rowCount() > 0) $accedi = true;
			}

		}
		if ($accedi && empty($_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["salt"])) {
			?>
			<h1>GENERAZIONE CODICE UNIVOCO IDENTIFICATIVO - CONCORSO ID <? echo $record_gara["id"] ?></h1>
			<h2><? echo $record_gara["oggetto"] ?></h2>
			<?
				$error = true;
				if ($_POST["salt"] === $_POST["salt_repeat"]) {
					$public = openssl_pkey_get_public(trim($record_gara["public_key"]));
					if (openssl_public_encrypt($_POST["salt"],$cryptedSalt,$public)) {

						$salt = $_POST["salt"];
						$partecipante = array();
						$partecipante["codice_gara"] = $record_gara["codice"];
						$partecipante["codice_fase"] = $fase_attiva["codice"];
						$partecipante["salt"] = base64_encode($cryptedSalt);
						$partecipante["conferma"] = 0;
						$partecipante["ammesso"] = 'N';


						$salva = new salva();
						$salva->debug = false;
						$salva->codop = -1;
						$salva->nome_tabella = "r_partecipanti_concorsi";
						$salva->operazione = "INSERT";
						$salva->oggetto = $partecipante;
						$codice_partecipante = $salva->save();
						if ($codice_partecipante != false) {
							$rand = rand(3000,9999);
							$uid = "ID".$record_gara["codice"]."-".$fase_attiva["codice"]."-".$codice_partecipante.$rand;
							$tmp = array();
							$tmp["codice"]  = $codice_partecipante;
							$tmp["identificativo"] = $uid;
							$salva->debug = false;
							$salva->codop = -1;
							$salva->nome_tabella = "r_partecipanti_concorsi";
							$salva->operazione = "UPDATE";
							$salva->oggetto = $tmp;
							$codice_partecipante = $salva->save();
							if ($codice_partecipante != false) {
								$partecipante["codice"] = $codice_partecipante;
								$partecipante["identificativo"] = $uid;
								$cryptedPartecipante = array();

								$sql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice WHERE b_operatori_economici.codice_utente = :codice_utente ";
								$ris_operatori_economici = $pdo->bindAndExec($sql,array(":codice_utente"=>$_SESSION["codice_utente"]));
								$operatore_economico = $ris_operatori_economici->fetch(PDO::FETCH_ASSOC);

								$cryptedPartecipante["codice_partecipante"] = $codice_partecipante;
								$cryptedPartecipante["codice_operatore"] = openssl_encrypt($operatore_economico["codice"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
								$cryptedPartecipante["codice_utente"] = openssl_encrypt($_SESSION["codice_utente"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
								$cryptedPartecipante["partita_iva"] = openssl_encrypt($operatore_economico["codice_fiscale_impresa"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
								$cryptedPartecipante["ragione_sociale"] = openssl_encrypt($operatore_economico["ragione_sociale"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
								$cryptedPartecipante["pec"] = openssl_encrypt($operatore_economico["pec"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
								$cryptedPartecipante["identificativoEstero"] = openssl_encrypt($operatore_economico["identificativoEstero"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
								$salva->debug = false;
								$salva->codop = -1;
								$salva->nome_tabella = "r_partecipanti_utenti_concorsi";
								$salva->operazione = "INSERT";
								$salva->oggetto = $cryptedPartecipante;
								if ($salva->save() != false) {
									$error = false;
									$_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]] = $partecipante;
									$_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["salt"] = $salt;
								}
							}
						}
					}
				}

				if (!$error) {
						?>
						<ul class="success">
							<li>Salvataggio riuscito con successo</li>
						</ul>
						<h1>CONCORSO ID <? echo $record_gara["id"] ?></h1>
						<h2><? echo $record_gara["oggetto"] ?></h2>
						<br>
						<h1 style="text-align:center">CODICE UNIVOCO IDENTIFICATIVO<br><br><?= $uid ?></h1><br><BR><br>
						<h1 style="text-align:center; text-transform:none">PASSWORD<br><br><?= $salt ?></h1>
						<a target="_blank" class="submit_big" href="/concorsi/partecipa/downloadUID.php?codice_concorso=<?= $record_gara["codice"] ?>&codice_fase=<?= $fase_attiva["codice"] ?>">Scarica PDF ricevuta</a>
						<?
					} else {
						?>
						<h3 class="ui-state-error">Errore durante il salvataggio</h3>
						<?
					}
			} else {
				echo "<h1>Impossibile continuare: Partecipazione gi&agrave inviata</h1>";
			}
		} else {
			echo "<h1>Concorso inesistente o privilegi insufficienti</h1>";
		}
	?>
	<a class="submit_big" style="background-color:#444" href="/concorsi/partecipa/modulo.php?cod=<?= $codice_gara ?>">Ritorna al pannello</a>
	<?
	include_once($root."/layout/bottom.php");
	?>
