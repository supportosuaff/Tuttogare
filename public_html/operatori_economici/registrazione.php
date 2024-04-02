<?
	include_once("../../config.php");
	$pagina_registrazione = true;
	include_once($root."/layout/top.php");
	if (registrazione_abilitata())	{
		$_SESSION["registrazione"] = true;
		?>
		<script>
			var submit_form = "";
			function submitForm() {
				if ($("#"+submit_form).length == 1) {
					$("#"+submit_form).submit();
				}
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
		<div id="reinvia_conferma" title="Reinvia conferma" style="display:none; text-align:center; margin-top:10px;">
			<strong>Inserisci l'indirizzo PEC indicato durante la registrazione.</strong><br>Riceverai un nuovo link con validit&agrave; di 48 ore<br><br>
			<form method="post" action="/operatori_economici/reinvia_conferma.php" rel="validate" id="conferma">
				<input type="text" title="Indirizzo pec" style="width:90%" name="pec_recupero" id="pec_recupero" rel="S;3;0;E">
			</form>
		</div>
		<h1><?= traduci("registrazione-oe") ?></h1>
		<div class="box">
			<?= traduci("avviso-registrazione") ?><br>
			<div class="ui-state-error" style="padding:10px; text-align: justify;">
				<strong><?= strtoupper(traduci('attenzione')) ?>:</strong>
				<?= traduci("avviso-registrazione-2") ?>
			</div>
			<div style="text-align:right"><a href="#" onClick="reinvia_conferma()"><?= traduci("Reinvia link conferma") ?></a></div>
		</div>
		<form name="box" id="registrazione_form" autocomplete="off" class="registrazione" method="post" action="preiscrizione.php" rel="validate">
			<h1 style="margin-top: 20px"><?= strtoupper(traduci('iscrizione')) ?></h1>
			<div class="box" style="padding:10px; text-align: justify; color: #666666">
				<strong><?= traduci('alert-dati-obbligatori') ?></strong>
			</div>
			<div class="box padding">
				<h2><strong><?= traduci("credenziali") ?></strong></h2>
				<table width="100%" id="credenziali">
					<tr>
						<td style="width: 50%">
							<strong><?= traduci('e-mail') ?> (*)</strong><br>
							<input class="titolo_edit" type="text" name="utenti[email]" id="email" title="<?= traduci('e-mail') ?>" rel="S;2;0;E;/user/check_email.php" autocomplete="off">
							<div id="email_check" style="display:none;"></div>
						</td>
						<td style="width: 50%">
							<strong><?= traduci('ripeti') ?> <?= traduci('e-mail') ?> (*)</strong><br>
							<input class="titolo_edit" type="text" id="email-ripeti" title="<?= traduci('e-mail') ?>" rel="S;2;0;E;email;=" autocomplete="off">
							<div id="email_check" style="display:none;"></div>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?= traduci('password') ?> (*)<strong><br>
								<input class="titolo_edit" type="password" name="utenti[password]" id="password" title="<?= traduci('password') ?>" rel="S;8;16;P;check_password;=" autocomplete="off">
								<div id="password_strenght"></div>
						</td>
						<td>
							<strong><?= traduci('ripeti') ?> <?= traduci('password') ?> (*)</strong><br>
							<input class="titolo_edit" type="password" id="check_password" title="Controllo <?= traduci('password') ?>" rel="S;8;16;P" onChange="valida($('#password'));" autocomplete="off">
						</td>
					</tr>
					<tr>
						<td>
							<input type="button" value="Random password" onClick="suggest_password('#suggest')">
						</td>
						<td><strong id="suggest"></strong></td>
					</tr>
				</table>
			</div>
			<div class="box padding">
				<h2><strong><?= traduci("referente") ?></strong></h2>
				<table width="100%">
					<tr>
						<td style="width: 50%">
							<strong><?= traduci('nome') ?> (*)</strong><br>
							<input type="text" class="titolo_edit" name="utenti[nome]" id="nome_referente" title="<?= traduci('nome') ?>" rel="S;2;0;A">
						</td>
						<td style="width: 50%">
							<strong><?= traduci('Cognome') ?> (*)</strong><br>
							<input type="text" class="titolo_edit" name="utenti[cognome]" id="cognome_referente" title="<?= traduci('Cognome') ?>" rel="S;2;0;A">
						</td>
					</tr>
				</table>
			</div>
			<div class="box padding">
				<h2><strong><?= traduci('Altri dati') ?></strong></h2>
				<table width="100%">
					<tr>
						<td colspan="2">
							<strong><?= traduci('denominazione') ?></strong>
							<i>(<?= traduci("Azienda") . " " . traduci('obbligatorio') ?>)</i><br>
							<input type="text" class="titolo_edit" name="operatori[ragione_sociale]" id="ragione_sociale" title="<?= traduci('denominazione') ?>" rel="N;2;0;A">
						</td>
					</tr>
					<tr>
						<td style="width: 50%">
							<strong><?= traduci('Codice Fiscale') ?> <?= traduci('azienda') ?>/ <?= traduci('professionista') ?> (*)</strong><br>
							<input type="text" class="titolo_edit" name="operatori[codice_fiscale_impresa]" id="codice_fiscale_impresa" title="<?= traduci('Codice Fiscale') ?>" rel="S;8;0;PICF">
						</td>
						<td style="width: 50%">
							<strong><?= traduci('partita iva') ?></strong> <i>(<?= traduci('facoltativo') ?>)</i><br>
							<input type="text" class="titolo_edit" name="operatori[partita_iva]" id="partita_iva" title="Partita IVA" rel="N;8;0;PICF">
						</td>
					</tr>
					<tr>
						<td>
							<strong><?= traduci('pec') ?> (*)</strong><br>
							<input class="titolo_edit" type="text" name="utenti[pec]" id="pec_referente" title="<?= traduci('pec') ?>" rel="S;2;0;E;/user/check_pec.php">
						</td>
						<td colspan="2">
							<strong><?= traduci('ripeti') ?> <?= traduci('pec') ?> (*)</strong><br>
							<input class="titolo_edit" type="text" id="PEC-ripeti" title="<?= traduci('pec') ?>" rel="S;2;0;E;pec_referente;=" autocomplete="off">
							<div id="email_check" style="display:none;"></div>
						</td>
					</tr>
				</table>
			</div>
			<div class="box padding">
				<h2><strong><?= traduci('categorie merceologiche') ?> CPV (*)</strong></h2>
				<? $string_cpv = ""; include("categorie/form.php"); ?>
			</div>
			<div class="box padding valida" title="Tipo utente" rel="S;0;0;checked;group_validate">
				<h2><strong><?= traduci('tipo utente') ?> (*)</strong></h2>
				<table width="100%">
					<tr>
						<td width="50%" style="text-align:center">
							<label for="OPE">
								<span class="fa fa-building-o fa-4x"></span><br>
								<strong><?= traduci('azienda') ?></strong><br>
								<input type="radio" id="OPE" name="tipo" value="OPE">
							</label>
						</td>
						<td width="50%" style="text-align:center">
							<label for="PRO">
								<span class="fa fa-user fa-4x"></span><br>
								<strong><?= traduci('professionista') ?></strong><br>
								<input type="radio" id="PRO" name="tipo" value="PRO">
							</label>
						</td>
					</tr>
				</table>
			</div>
			<script type="text/javascript">
				$(function() {
					$("input[name='tipo'][type='radio']").on('change', function(event) {
						event.preventDefault();
						$('#ragione_sociale').attr('rel', 'N;2;0;A');
						if($(this).val() == 'OPE') $('#ragione_sociale').attr('rel', 'S;2;0;A');
					});
				});
			</script>
			<div class="box">
				<table width="100%">
					<tr>
						<td colspan="2">
							<strong><?= traduci("Informativa sul trattamento dei dati personali") ?></strong><br>
							<? if (empty($_SESSION["ente"]["informativa_reg_oe"])) {
								$path = $config["path_vocabolario"]."/{$_SESSION["language"]}/registrazione.php";
							 	if (file_exists($path)) include($path);
							} else {
								echo $_SESSION["ente"]["informativa_reg_oe"];
							} ?>
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" name="privacy" id="Privacy" rel="S;0;0;Checked" title="Accettazione privacy">
						</td>
						<td>
							<?= traduci('dichiarazione-privacy') ?> <a href="/privacy.php" title="<?= traduci('policy privacy') ?>" target="_blank"><?= traduci('policy privacy') ?></a>
						</td>
					</tr>
					<tr>
						<td><input type="checkbox" name="norme_tecniche" id="norme_tecniche" rel="S;0;0;Checked" title="Accettazione norme tecniche"></td>
						<td><?= traduci('dichiarazione-norme-tecniche') ?><a href="/norme_tecniche.php" title="<?= traduci('norme tecniche di utilizzo') ?>" target="_blank"><?= traduci('norme tecniche di utilizzo') ?></a></td>
					</tr>
				</table>
			</div>
			<div class="box" style="padding:10px; text-align: justify; color: #666666">
				<strong><?= traduci('alert-dati-obbligatori') ?></strong>
			</div>
			<button class="submit_big" onClick="controlla('registrazione_form');return false"><?= traduci("SALVA ISCRIZIONE") ?></button>
		</form>
		<div class="clear"></div>
		<?
	} else {
		echo "<h1>".traduci("Impossibile accedere")."</h1>";
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	include_once($root."/layout/bottom.php");
?>
