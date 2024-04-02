<?
	if (isset($_POST["valore"]) && isset($_POST["codice"])) {
	session_start();
	include_once("../../config.php");
	$bind = array(":email"=>$_POST["valore"]);
	$sql = "SELECT * FROM b_utenti WHERE email = :email";
	if ($_POST["codice"] != "") {
		$bind[":codice"] = $_POST["codice"];
		 $sql.=" AND codice <> :codice ";
	 }
	$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount()>0) {
			$utente = $ris->fetch(PDO::FETCH_ASSOC);
			echo "Indirizzo gia presente!";
			if ($utente["gruppo"]=="3"||$utente["gruppo"]=="4") {
				if ($utente["attivo"]=="S") {
					$sql = "SELECT b_enti.* FROM b_enti JOIN r_enti_operatori ON b_enti.codice = r_enti_operatori.cod_ente WHERE cod_utente = :codice_utente";
					$ris_enti = $pdo->bindAndExec($sql,array(":codice_utente"=>$utente["codice"]));
					$ris_enti = $ris_enti->fetchAll(PDO::FETCH_ASSOC);
					echo " L'utenza risulta gi&agrave; ";
					if (isset($_SESSION["ente"])) {
						foreach($ris_enti AS $ente) {
							if ($ente["codice"] == $_SESSION["ente"]["codice"]) {
								$found = true;
								echo "attiva, si pu&ograve; procedere al Login";
								break;
							}
						}
						if (!isset($found)) {
							echo "registrata presso la piattaforma di " . $ris_enti[0]["denominazione"] . ",  è possibile effettuare direttamente il login inserendo le proprie credenziali";
						}
					}
				} else {
					echo " L'utente non ha confermato la registrazione, contattare Help desk per ricevere un nuovo link di conferma";
				}
			}
		}
	} else {
		echo "Errore di validazione!";
	}
?>
