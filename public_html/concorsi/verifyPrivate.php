<?
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$error = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && !empty($_POST["codice_concorso"]) && check_permessi("concorsi",$_SESSION["codice_utente"])) {
		$test_string = "test-verifica-stringa";

		$bind = array();
		$bind[":codice"] = $_POST["codice_concorso"];

		$strsql = "SELECT codice, public_key FROM b_concorsi WHERE codice = :codice";
		$concorso = $pdo->go($strsql,$bind);

		if($concorso->rowCount() > 0) {
			$record_concorso = $concorso->fetch(PDO::FETCH_ASSOC);
			$public = openssl_pkey_get_public(trim($record_concorso["public_key"]));
			if (openssl_public_encrypt($test_string, $encr, $public)) {
				$key = openssl_pkey_get_private($_POST["private_key"]);
				if (openssl_private_decrypt($encr, $decr, $key)) {
					if (strcmp($decr, $test_string) === 0) {
						$salva = new salva();
						$salva->debug = FALSE;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "b_concorsi";
						$salva->operazione = "UPDATE";
						$salva->oggetto = ["codice" => $record_concorso["codice"], "check_private" => "S"];
						if ($salva->save() != false) {
							$error = false;
							?>
								alert("Verifica effettuata con successo");
								window.location.reload();
							<?
						}
					}
				}
			}
		}
	}
	if ($error) {
		?>
		alert("Verifica fallita");
		window.location.reload();
		<?
	}
?>
