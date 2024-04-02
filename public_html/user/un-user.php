<?
	include_once("../../config.php");
	$pagina_reset = true;
	include_once($root."/layout/top.php");
	if (!isset($_SESSION["codice_utente"]) && !empty($_GET["email"]) && !empty($_GET["token"])) {
			$email = $_GET["email"];
			$email = base64_decode($email);
			$token = $_GET["token"];
			$token = base64_decode($token);
			$bind=array(":token"=>$token,":email"=>$email);
			$sql = "SELECT * FROM b_utenti
							WHERE attivo = 'S' AND email = :email AND unlock_token = :token AND (tentativi >= 5 OR scaduto = 'S' OR bot_verify = 'S') AND DATE_ADD(unlock_request, INTERVAL 2 DAY) > curdate()";
			$ris = $pdo->bindAndExec($sql,$bind);
			if ($ris->rowCount() === 1) {
				$record = $ris->fetch(PDO::FETCH_ASSOC);
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $record["codice"];
				$salva->nome_tabella = "b_utenti";
				$salva->operazione = "UPDATE";
				$salva->oggetto = array("codice"=>$record["codice"],"tentativi"=>0,"scaduto"=>"N","bot_verify"=>"N");
				$codice = $salva->save();
				if ($codice != false) {
					?>
					<h2 style="color:#0C3; text-align:center">SBLOCCO AVVENUTO CON SUCCESSO</h2>
					<h3 style="text-align:center">Puoi procedere nell'utilizzo del portale effettuando il login</h3>
					<?
				}
			} else {
				echo "<h1>Impossibile accedere!</h1>";
				echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
				die();
			}
		} else {
			echo "<h1>Impossibile accedere!</h1>";
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	include_once($root."/layout/bottom.php");
	?>
