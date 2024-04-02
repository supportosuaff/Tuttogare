<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = true;
	if ((isset($_SESSION["codice_utente"])) && ($_SESSION["codice_utente"] != $_POST["codice"])) {
		$edit = check_permessi($_POST["modulo"],$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["operazione"])) {

			$salva = new salva();
			if (isset($_POST["password"]) || (!isset($_POST["password"]) && $_POST["operazione"] == "INSERT")) {
				if (!isset($_POST["password"]) && $_POST["operazione"] == "INSERT") $_POST["password"] = genpwd(8);
				$_POST["password"] =password_hash(md5($_POST["password"]), PASSWORD_BCRYPT);
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_password_log";
				$salva->operazione = "INSERT";
				$salva->oggetto = array("codice_utente"=>$_POST["codice"]);
				$salva->save();
			}

			if (isset($_POST["procedureAttive"])) {
				if (count($_POST["procedureAttive"]) > 0) {
					$_POST["procedureAttive"] = implode(",",$_POST["procedureAttive"]);
				} else {
					$_POST["procedureAttive"] = "0";
				}
			}

			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_utenti";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST;
			$codice = $salva->save();

			$cod_moduli = $_POST["cod_moduli"];
			$bind = array(":codice"=>$codice);
			$strsql = "DELETE FROM r_moduli_utente WHERE cod_utente = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);

			$moduli = explode(";",$cod_moduli);
			$moduli = array_unique($moduli);
			if ($_POST["temporaneo"] == "C") {
				$moduli = [37,38,42];
			}
			foreach($moduli as $modulo) {
				if ($modulo != "") {
					$bind = array(":codice"=>$codice,":modulo"=>$modulo);
					$strsql = "INSERT INTO r_moduli_utente (cod_utente,cod_modulo) VALUES (:codice,:modulo)";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					scrivilog("r_moduli_utente","INSERT",$pdo->getSQL(),$_SESSION["codice_utente"]);
				}
			}
			$href = "/user/id".$codice."-edit";
			if ($_POST["operazione"]=="UPDATE") {
				?>
				alert('Modifica effettuata con successo');
				<?
			} elseif ($_POST["operazione"]=="INSERT") {
				?>
				alert('Inserimento effettuato con successo');
      	<?
			} ?>
			window.location.href = '<? echo $href ?>';
			<?
		}
	}



?>
