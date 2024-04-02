<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;

	if (isset($_SESSION["codice_utente"])) {
		if(isset($_SESSION["ente"])) {
			$edit = check_permessi("user",$_SESSION["codice_utente"]);
		} else {
			if(check_permessi("supporto",$_SESSION["codice_utente"]) && in_array($_SESSION["tipo_utente"], array('SAD', 'SUP'))) {
			  $edit = true;
			}
		}
	}

	if($edit) {
		if(isset($_GET["id"])){

			$dominio = $_SERVER["SERVER_NAME"];
			$check_dominio = false;
			if(! empty($_GET["id_ente"])) {
				$check_dominio = true;
				$ente = $pdo->bindAndExec("SELECT b_enti.dominio FROM b_enti  WHERE codice = :codice_ente AND attivo = 'S'", array(':codice_ente' => $_GET["id_ente"]))->fetch(PDO::FETCH_ASSOC);
				if(! empty($ente["dominio"])) $dominio = $ente["dominio"];
			}

			$bind = array(':codice' => $_GET["id"]);
			$sql = "SELECT * FROM b_utenti WHERE codice = :codice";
			$ris = $pdo->bindAndExec($sql,$bind);

			if ($ris->rowCount()===1) {
				$record = $ris->fetch(PDO::FETCH_ASSOC);
				if (!empty($record["codice_ente"]) && !isset($_SESSION["ente"]["codice"]) && !$check_dominio) {
					$check_ente = $pdo->bindAndExec("SELECT * FROM b_enti WHERE codice = :codice",array(":codice"=>$record["codice_ente"]));
					if ($check_ente->rowCount()===1) {
						$ente = $check_ente->fetch(PDO::FETCH_ASSOC);
						if (empty($ente["dominio"]) && !empty($ente["sua"])) {
							$check_ente = $pdo->bindAndExec("SELECT * FROM b_enti WHERE codice = :codice",array(":codice"=>$ente["sua"]));
							if ($check_ente->rowCount() == 1) $ente = $check_ente->fetch(PDO::FETCH_ASSOC);
						}
						$dominio = (!empty($ente["dominio"])) ? $ente["dominio"] : $dominio;
					}
				}

				$password = genpwd(10);

				$data = array(
					'codice' => $record["codice"],
					'password' => password_hash(md5($password), PASSWORD_BCRYPT),
					'force_reset' => 'S'
				);

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = 0;
				$salva->nome_tabella = "b_utenti";
				$salva->operazione = "UPDATE";
				$salva->oggetto = $data;
				if ($salva->save() > 0) {
					$link =  "{$config["protocollo"]}{$dominio}/";
					$link = "<a href='{$link}' target=\"_blank\" title=\"Accedi\">{$link}</a>";
					$messaggio = "<h1>" . $_SESSION["config"]["nome_sito"]. "</h1>";
					$messaggio.= "<p>In data " . date("d/m/Y") . " alle ore " . date("H:i") . " &egrave; stata richiesta la generazione di una password provvisoria per l'accesso ai servizi del portale. La nuova password &egrave;:</p>";
					$messaggio.= "<h3 style=\"text-align: center\">{$password}</h3>";
					$messaggio.= "<p>Le ricordiamo che dopo il primo accesso sar&agrave; necessario impostare una nuova password.<br>{$link}<br></p>";
					$oggetto = "Password di accesso provvisoria";

					$mailer = new Communicator();
					$mailer->oggetto = $oggetto;
					$mailer->corpo = $messaggio;
					$mailer->codice_pec = -3;
					$mailer->comunicazione = false;
					$mailer->coda = false;
					$mailer->destinatari = $record["email"];
					$esito = $mailer->send();
					if ($esito !=true) {
						$errore = "Problema durante l'invio.\n";
						?>
						<?= $esito ?>
						<?
					} else {
					?><strong>Invio effettuato con successo</strong><br><br>
		      <? }
				} else {
					?>Errore nella generazione del token<?
				}
			} else {
				?>Nessun utente è associato a all'ID indicato<?
			}
		} else {
			?>ID è obbligatorio<?
		}
	}

?>
