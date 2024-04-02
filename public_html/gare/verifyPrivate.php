<?
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$error = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && !empty($_POST["codice_gara"])) {
		$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/pubblica/edit.php'";
		$risultato = $pdo->query($strsql);
		if ($risultato->rowCount()>0) {
			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
			$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			if ($edit) {
				$test_string = "test-verifica-stringa";
				$bind = array();
				$bind[":codice"] = $_POST["codice_gara"];
				$strsql = "SELECT codice,public_key FROM b_gare WHERE codice = :codice ";
				$gara = $pdo->go($strsql,$bind);
				if ($gara->rowCount() > 0) {
					$record_gara = $gara->fetch(PDO::FETCH_ASSOC);
					$public = openssl_pkey_get_public(trim($record_gara["public_key"]));
					if (openssl_public_encrypt($test_string,$encr,$public)) {
						$key = openssl_pkey_get_private($_POST["private_key"]);
						if (openssl_private_decrypt($encr,$decr,$key)) {
							if (strcmp($decr,$test_string)===0) {
								$salva = new salva();
								$salva->debug = FALSE;
								$salva->codop = $_SESSION["codice_utente"];
								$salva->nome_tabella = "b_gare";
								$salva->operazione = "UPDATE";
								$salva->oggetto = ["codice"=>$record_gara["codice"],"check_private"=>"S"];
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
		}
	}
	if ($error) {
		?>
		alert("Verifica fallita");
		window.location.reload();
		<?
	}
?>
