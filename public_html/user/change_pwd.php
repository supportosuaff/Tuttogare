<?
	include_once("../../config.php");
	$change_pwd = true;
	$pagina_reset = true;
	include_once($root."/layout/top.php");
	if (
			(isset($_SESSION["expired_pass"]) && isset($_SESSION["codice_utente"])) ||
			(isset($_SESSION["record_utente"]) && $_SESSION["record_utente"]["force_reset"] == "S") ||
			((!empty($_GET["email"]) || !empty($_POST["email"])) && (!empty($_GET["token"]) || !empty($_POST["token"])))
		) {
		unset($record);
		if (!$echo_layout) {
			?>
			<style>
					body {
						background-color:#666;
					}
				</style>
				<div id="div_login">
					<div style="padding:50px">
						<div style="text-align:center">
							<?
							if (isset($_SESSION["ente"])) {
								?>
								<img src="/documenti/enti/<? echo $ente["logo"] ?>" width="30%" alt="<? echo $ente["denominazione"] ?>"><br>
								<strong><? echo $ente["denominazione"]; ?></strong><br><br>
								<?
							} else {
								?><img alt="TUTTOGARE" src="/img/logo-tuttogare-pa-big.png"><?
							}
							?>
							<div class="clear"></div>
						</div>
						<br>
			<?
			}

		if (isset($_SESSION["expired_pass"]) || (isset($_SESSION["record_utente"]) && $_SESSION["record_utente"]["force_reset"] == "S")) {
			$bind=array(":codice"=>$_SESSION["codice_utente"]);
			$sql = "SELECT * FROM b_utenti
							WHERE codice = :codice ";
			$ris = $pdo->bindAndExec($sql,$bind);
			if ($ris->rowCount() === 1) {
				$record = $ris->fetch(PDO::FETCH_ASSOC);
			}
			?>
			<h1>Password scaduta</h1>
			<strong>Ai sensi del Decreto Legislativo n. 196/2003, Allegato B c. 5, la password di accesso deve essere modificata almeno ogni 3 mesi</strong>
			<?
		} else if (!isset($_SESSION["codice_utente"])) {
			$email = empty($_GET["email"]) ? $_POST["email"] : $_GET["email"];
			$email = base64_decode($email);
			$token = empty($_GET["token"]) ? $_POST["token"] : $_GET["token"];
			$token = base64_decode($token);
			$bind=array(":token"=>$token,":email"=>$email);
			$sql = "SELECT * FROM b_utenti
							WHERE email = :email AND password_token = :token ";
			$sql .= " AND DATE_ADD(password_request, INTERVAL 2 DAY) > curdate()";
			$ris = $pdo->bindAndExec($sql,$bind);
			if ($ris->rowCount() === 1) {
				$record = $ris->fetch(PDO::FETCH_ASSOC);
			}
			?>
			<h1>Cambio Password</h1>
			<strong>E' stata richiesta la generazione di una nuova password</strong>
			<?
		}
		if (!empty($record)) {
			if (!empty($_POST["password"])) {
				$error = true;
				$msg = "";
				$cryptpassw = md5($_POST["password"]);
				if (password_verify($cryptpassw,$record["password"])) {
					$msg = "La password &egrave; gi&agrave; stata utilizzata.";
				}
				if (empty($msg)) {
					$password = password_hash(md5($_POST["password"]), PASSWORD_BCRYPT);
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $record["codice"];
					$salva->nome_tabella = "b_utenti";
					$salva->operazione = "UPDATE";
					$tmp = array();
					$tmp["codice"] = $record["codice"];
					$tmp["password"] = $password;
					$tmp["force_reset"] = "N";
					$tmp["tentativi"] = 0;
					$tmp["scaduto"] = "N";
					$tmp["bot_verify"] = "N";
					$salva->oggetto = $tmp;
					$codice = $salva->save();
					if ($codice != false) {
						$salva->debug = false;
						$salva->codop = $record["codice"];
						$salva->nome_tabella = "b_password_log";
						$salva->operazione = "INSERT";
						$salva->oggetto = array("codice_utente"=>$record["codice"]);
						if ($salva->save() != false) $error = false;
					}
				}
			}
			if (!isset($error) || (isset($error) && $error)) {
				if (isset($error)) {
					?>
					<div class="ui-state-error padding">
						<strong>Si è verificato un errore nel salvataggio della password, si prega di riprovare</strong>
						<?= $msg ?>
					</div>
					<?
				}
				?>
				<br><br>
				<form name="box" method="post" action="change_pwd.php" target="_self" rel="validate" autocomplete="off">
					<input type="hidden" id="codice" name="codice" value="<? echo $record["codice"]; ?>">
					<input type="hidden" id="email" name="email" value="<? echo base64_encode($record["email"]); ?>">
					<? if (!empty($token)) { ?><input type="hidden" id="token" name="token" value="<? echo base64_encode($token); ?>"><? } ?>
					<div style="text-align:center; width:50%; min-width:400px; margin:auto">
						<strong>Password</strong>
						<input type="password" name="password" id="password" title="Password" rel="S;8;16;P;check_password;=" autocomplete="off" class="titolo_edit">
						<div id="password_strenght"></div>
						<strong>Ripeti Password</strong><br>
						<input class="titolo_edit" type="password" id="check_password" title="Controllo Password" rel="S;8;16;P" onChange="valida($('#password'));" autocomplete="off">
						<input type="submit" class="submit_big" value="Cambia password">
					</div>
				</form>
			  <?
			} else {
				?>
				<h2 style="color:#0C3; text-align:center">PASSWORD MODIFICATA CON SUCCESSO</h2>
				<?
				if (isset($_SESSION["expired_pass"])) {
					unset($_SESSION["expired_pass"]);
				} else {
					?>
						<h3 style="text-align:center">Puoi procedere nell'utilizzo del portale effettuando il login</h3>
					<?
				}
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
	if ($echo_layout) {
		include_once($root."/layout/bottom.php");
	}

	?>
