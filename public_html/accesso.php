<?
	include("../config.php");
	$pagina_login = true;
	include($root."/layout/top.php");
	if (!isset($_SESSION["codice_utente"]) &&
			(
				(md5(session_id()) == $_SESSION["id_sessione"] && isset($_SESSION["ente"]))
			  ||
				(isset($_SESSION["id_sessione-admin"]) && sha1(session_id()) == $_SESSION["id_sessione-admin"] && !isset($_SESSION["ente"]))
			)
		) {
		?>
		<script>
		  function unlock(email) {
				$.ajax({
					url: '/user/unlock.php',
					type: 'POST',
					dataType: 'html',
					data: {"email": email},
					beforeSend: function(e) {
						$('#wait').fadeIn();
					}
				})
				.done(function(html_response) {
					$("#sblocca_utente").html("<strong>Procedura avviata</strong><br><br>La procedura per sbloccare l'utente è stata inviata alla tua casella e-mail e alla tua casella pec");
					$("#sblocca_utente").dialog({
						position:["center",50],
						modal:true
					});
					$("#sblocca_utente").show();
				})
				.fail(function() {
					jalert('Si è verificato un errore. Si prega di riprovare!<br>Se il problema persiste contattare l&#39;helpdesk tecnico.');
				})
				.always(function() {
					$('#wait').fadeOut();
				});
			}
			function reinvia_conferma() {
				$("#reinvia_conferma").dialog({
						position: ["center", 50],
						modal: true,
						show: {
								effect: 'drop',
								direction: "up"
						},
						buttons: [{
								text: "Invia link",
								click: function() {
										$("#conferma").submit();
								}
						}]
				});
				$("#reinvia_conferma").show();
			}
		</script>
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
						?><img alt="TUTTOGARE" style="width: 100%" src="/img/logo-tuttogare-pa-big.png"><?
					}
					?>
					<div class="clear"></div>
				</div>
				<br>
				<form id='login_form' name="login" method="post" rel="validate" action="/login.php">
					<label for="username"><?= traduci("e-mail") ?></label><br>
					<input type="text" name="username" title="Indirizzo e-mail" rel="S;3;0;E" class="titolo_edit">
					<label for="Password"><?= traduci("Password") ?></label><br>
					<input type="password" name="password" title="Password" rel="S;3;16;A" class="titolo_edit" maxlength="16"><br>
					<a href="#" onClick="recupera_password()"><?= traduci("Recupera Password") ?></a> | 
					<a href="#" onClick="reinvia_conferma()"><?= traduci("Reinvia link conferma") ?></a>
					<button class="submit_big">Login</button>
				</form>
				<div id="msg_login" style="display:none; color:#000; font-weight:bold;"></div>
				<div id="sblocca_utente" title="Sblocca utenza" style="display:none; text-align:center; margin-top:10px;">
					<div id="messaggio_sblocco" style="display:none; color:#000; font-weight:bold;"></div>
				</div>
				<div id="recupera_password" title="Recupera Password" style="display:none; text-align:center; margin-top:10px;">
					<strong>Inserisci il tuo indirizzo e-mail.</strong><br>Riceverai un link per scegliere una nuova password per<br> accedere ai servizi del portale<br><br>
					<form method="post" action="/user/recupera.php" rel="validate" id="recupero">
						<input type="text" title="Indirizzo e-mail" style="width:90%" name="email_recupero" id="email_recupero" rel="S;3;0;E">
					</form>
					<div id="messaggio_recupero_password"></div>
				</div>
				<div id="reinvia_conferma" title="Reinvia conferma" style="display:none; text-align:center; margin-top:10px;">
					<strong>Inserisci l'indirizzo PEC indicato durante la registrazione.</strong><br>Riceverai un nuovo link con validit&agrave; di 48 ore<br><br>
					<form method="post" action="/operatori_economici/reinvia_conferma.php" rel="validate" id="conferma">
						<input type="text" title="Indirizzo pec" style="width:90%" name="pec_recupero" id="pec_recupero" rel="S;3;0;E">
					</form>
				</div>
			</div>
			<? if (isset($_SESSION["ente"]) && !$hide_amica) { ?><br><img alt="TUTTOGARE" width="150" src="/img/tuttogarepa-logo-software-sx.png"><? } ?>
		</div>
		<?
	} else {
		echo "<h1>Accesso negato</h1>";
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	include($root."/layout/bottom.php");
?>
