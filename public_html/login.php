<?
	session_start();
	include_once("../config.php");
	include_once($root."/inc/funzioni.php");
	if (!isset($_SESSION["codice_utente"]) && md5(session_id()) == $_SESSION["id_sessione"] && (isset($_POST["username"])) && (isset($_POST["password"]))) {
		$username = $_POST["username"];
		$password = $_POST["password"];

		$login = accedi($username,$password,(isset($_POST["force"]))?true:false);
		if (!empty($login["tentativi"])) {
			?>
				$("#email_recupero").val("<? echo purify($username) ?>");
				$("#email_recupero").removeClass("label");
				$("#msg_login").html('Utente non riconosciuto<br>');
				<?
					$residui = 0;
					if (isset($login["tentativi"])) $residui = 5 - $login["tentativi"] ;
					if ($residui <= 0 || $login["scaduto"]=="S") {
						?>
						$("#msg_login").html($("#msg_login").html() + "<strong style=\"color:#900\">Utente Bloccato <?= (isset($login["scaduto"]) && $login["scaduto"] == "S") ? " - Nessuna attivit&agrave; da 6 mesi" : "" ?></strong><br><a href=\"#\" onClick=\"unlock('<?= purify($username) ?>');\">Sblocca</a>");
						<?
					} else {
						?>
						$("#msg_login").html($("#msg_login").html() + "Tentativi rimasti: <?= $residui ?>");
						<?
					}
				?>
				$("#msg_login").slideDown();
			<?
		} else {
			if ($login != 0) {
				if ($login != -1) {

					$_SESSION["codice_utente"] = $login["codice_utente"];
					$_SESSION["nome_utente"] = $login["nome_utente"];
					$_SESSION["amministratore"] = $login["admin"];
					$_SESSION["gerarchia"] = $login["gerarchia"];
					$_SESSION["tipo_utente"] = $login["tipo_utente"];
					$_SESSION["record_utente"] = $login["record_utente"];
					if(! empty($_SESSION["REDIRECT_BACK_ALBO"]) && is_numeric($_SESSION["REDIRECT_BACK_ALBO"])) {
						?>window.location.href = '/albo_fornitori/id<?= $_SESSION["REDIRECT_BACK_ALBO"] ?>-dettaglio';<?
					} else {
						?>window.location.href = '/index.php';<?
					}
				} else {
						?>
							$("#msg_login").html('Un altro utente sta usando queste credenziali.<br><br><span style="color:#C00">Ripeti il login per sbloccare la sessione, tutte le altre sessioni saranno disattivate</span>');
							$("#login_form").append('<input type="hidden" name="force" value="S">');
							$("#msg_login").slideDown();
						<?
				}
			} else {
				?>
					$("#msg_login").html('Utente non riconosciuto');
					$("#msg_login").slideDown();
				<?
			}
		}
	} else {
		?>
		$("#msg_login").html('Errore nella richiesta<br>');
		$("#msg_login").slideDown();
		<?
	}

?>
