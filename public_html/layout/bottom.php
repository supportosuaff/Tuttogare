<? if ($echo_layout) { ?>
</div>
<div id="bottom" style="padding-bottom:100px">
<? if (isset($ente)) { ?>
<div class="padding" style="float:right; text-align:right;">
	<img src="/img/sequenza_completa_loghi_web.jpg" alt="Logo Governance e capacita istituzionale 2014-2020" style="vertical-align:middle; max-width:100%">
<? if (!$hide_amica) { ?>
	<a href="https://app.tuttogare.it" title="Tutto Gare"><img src="/img/tuttogarepa-logo-software.png" style="vertical-align:middle;" height="60" alt="TuttoGare"></a><br><br>
<? } ?>
<br><a href="/norme_tecniche.php" title="<?= traduci("Norme tecniche di utilizzo"); ?>"><?= traduci("Norme tecniche di utilizzo") ?></a> |
<a href="/privacy.php" title="<?= traduci("Policy Privacy") ?>"><?= traduci("Policy Privacy") ?></a><br>
<?
echo traduci("Help Desk") . " ";
$email = "";
$nome = "";

if (!empty($_SESSION["record_utente"])) {
	$email = $_SESSION["record_utente"]["email"];
	$nome = $_SESSION["record_utente"]["cognome"] . " " . $_SESSION["record_utente"]["nome"];
}
echo "<strong>{$_SESSION["email_assistenza"]} - {$_SESSION["numero_assistenza"]}</strong>";
// echo " - <a href=\"#chatOperatore\" name='chatOperatore' onClick='launchLiveChat(\"{$config["rocketChat-url"]}\",\"{$nome}\",\"{$email}\")' title=\"Chat con operatore\">Chat con operatore</a>";
echo "<br>";
?>
<br> 
<? 
	 echo traduci('orari-helpdesk');
?>
</div>
<div class="padding" style="float:left; text-align:left">
  <?
  if(! empty($_SESSION["ente"]["custom_footer"])) {
    echo $_SESSION["ente"]["custom_footer"];
  } else {
    echo "<h3><a href='{$ente["url"]}' title='sito istituzionale'>{$ente["denominazione"]}</a></h3>";
    echo "<strong>{$ente["indirizzo"]} {$ente["citta"]} ({$ente["provincia"]})</strong><br>";
    if ($ente["telefono"]!="") echo "Tel. " . $ente["telefono"];
    if ($ente["fax"]!="") echo " - Fax. " . $ente["fax"];
    if ($ente["email"]!="") echo " - Email: <a href=\"mailto:" . $ente["email"] . "\">" . $ente["email"] . "</a>";
    echo ! empty($ente["pec_footer"]) ? " - PEC: <a href='mailto:{$ente["pec_footer"]}'>{$ente["pec_footer"]}</a>" : (! empty($ente["pec"]) ? " - PEC: <a href='mailto:{$ente["pec"]}'>{$ente["pec"]}</a>" : null);
  }
  ?>
</div>
<?
}
?>
<div class="clear"></div>
</div>
<div id="search-box" style="display: none;" title="Ricerca">
	<div class="content">
		<form id="formRicerca" action="/ricerca/index.php" rel="validate" method="post" target="_self">
			<input type="hidden" name="advGare" id="advGare" value="1">
			<input type="hidden" name="advCatalogo" id="advCatalogo" value="1">
			<table width="100%">
				<tbody>
					<tr>
						<td><b><?= traduci("Cerca") ?>: *</b></td>
					</tr>
					<tr>
						<td>
							<input type="text" rel="S;3;0;A" title="<?= traduci("Cerca") ?>..." name="query" id="query" style="width:98%; border:none; font-size:14px;" />
						</td>
					</tr>
					<tr>
						<td>
							<button class="submit_big" type="submit"><?= traduci("Cerca") ?></button>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<div class="footer">
		<a href="/ricerca/index.php?advanced=1"><?= traduci("avanzata") ?></a>
	</div>
</div>
<div id="segnalazioni" style="display:none;" title="Invia una segnalazione" style="background-color:#444;">
	<?
	$nome = "";
	$cognome = "";
	$email = "";
	$telefono = "";
	$cellulare = "";

	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["record_utente"]))
	{
		$nome = $_SESSION["record_utente"]["nome"];
		$cognome = $_SESSION["record_utente"]["cognome"];
		$email = $_SESSION["record_utente"]["email"];
		$telefono = $_SESSION["record_utente"]["telefono"];
		$cellulare = $_SESSION["record_utente"]["cellulare"];
	}
	?>
	<form method="post" action="/bugreport.php" rel="validate">
		<div id="bugreport">
			<ul>
				<li><a href="#descrizione_bug"><?= traduci("descrizione") ?></a></li>
				<li><a href="#contatti_bug"><?= traduci("contatti") ?></a></li>
			</ul>
			<div id="descrizione_bug">
		    <table width="100%">
		        <tbody>
		            <tr>
		                <td colspan="4" class="etichetta"><b><?= traduci("Oggetto") ?>*</b></td>
		            </tr>
		            <tr>
		                <td colspan="4">
		                    <input type="text" name="summary" value="" title="<?= traduci("Oggetto") ?>" style="width:98%" rel="S;3;0;A">
		                </td>
		            </tr>
		            <tr>
		                <td class="etichetta"><b><?= traduci("descrizione") ?></b></td>
		                <td class="etichetta"><b><?= traduci("step") ?></b></td>
		            </tr>
		            <tr>
		                <td>
		                    <textarea name="description" id="description" class="" rel="S;3;0;A" title="<?= traduci("descrizione") ?>"></textarea>
		                </td>
		                <td>
		                    <textarea name="steps_to_reproduce" id="steps_to_reproduce" class="" rel="N;3;0;A" title="<?= traduci("step") ?>"></textarea>
		                </td>
		            </tr>
		        </tbody>
		    </table>
			</div>
  		<div id="contatti_bug">
  			<input type="hidden" name="url" value="<?= purify($config["protocollo"].$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]) ?>">
  			<table width="100%">
  				<tbody>
  				    <tr>
  				        <td class="etichetta" width="20%"><b><?= traduci("nome") ?>*</b></td>
  				        <td>
  				            <input type="text" name="nome" value="<?= $nome ?>" title="<?= traduci("nome") ?>" rel="S;3;0;A">
  				        </td>
  				    </tr>
  				    <tr>
  				        <td class="etichetta"><b><?= traduci("cognome") ?>*</b></td>
  				        <td>
  				            <input type="text" name="cognome" value="<?= $cognome ?>" title="<?= traduci("cognome") ?>" rel="S;3;0;A">
  				        </td>
  				    </tr>
  				    <tr>
  				        <td class="etichetta"><b><?= traduci("email") ?>*</b></td>
  				        <td>
  				            <input type="text" name="email" value="<?= $email ?>" title="<?= traduci("email") ?>" style="width:98%" rel="S;3;0;E">
  				        </td>
  				    </tr>
  				    <tr>
  				        <td class="etichetta"><b><?= traduci("telefono") ?></b></td>
  				        <td>
  				            <input type="text" name="telefono" value="<?= $telefono ?>" title="<?= traduci("telefono") ?>" rel="N;3;0;N">
  				        </td>
  				    </tr>
  				    <tr>
  				        <td class="etichetta"><b><?= traduci("cellulare") ?></b></td>
  				        <td>
  				            <input type="text" name="cellulare" value="<?= $cellulare ?>" title="<?= traduci("cellulare") ?>" rel="N;3;0;N">
  				        </td>
  				    </tr>
  				</tbody>
  			</table>
  		</div>
		  <input type="submit" class="submit_big" value="INVIA">
    </div>
	</form>
</div>
<style type="text/css">
	.ui-widget-overlay {
   opacity: .8;
}
</style>
<script type="text/javascript">
	$("#bugreport").tabs();
	function show_bug_report() {
		event.preventDefault();
		$('#segnalazioni').dialog({
			modal: true,
			width: 900,
			open: function( event, ui ) {
				$("#description, #steps_to_reproduce").ckeditor(config_simple);
			}
		});
	}
	function show_search() {
		event.preventDefault();
		$('#search-box').dialog({
			modal: true,
			width: 500,
		});
		$("#query").blur();
	}
</script>
<? } ?>
	</div>
</div>
<? if (!isset($disable_alert_sessione) && (isset($_SESSION["codice_utente"]) || isset($_SESSION["tmp_codice_utente"]))) { ?>
	<script>
	alert_sessione = setTimeout(function(){
		jalert('<div style="text-align:center"><strong>Attenzione, la sessione sta per scadere.</strong><br>Se necessario, si consiglia di procedere ad un salvataggio dei dati.</div>');
	}, 1200000);
	</script>
<? }
?>
</body>
</html>