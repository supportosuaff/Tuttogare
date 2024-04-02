<?
if (isset($_GET["codice"])) {
	session_start();
	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
;

	$bind = array(":codice"=>$_GET["codice"]);
	$strsql = "SELECT * FROM b_gruppi_opzioni WHERE codice = :codice";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$gruppo = $risultato->fetch(PDO::FETCH_ASSOC);
	} else {
		$gruppo = get_campi("b_gruppi_opzioni");
	}
}
?>
<form name="box" id="gruppo_form" method="post" action="/impostazioni/opzioni/save.php" rel="validate">
	<input type="submit" class="submit_big" value="Salva">
		<input type="hidden" id="gruppo_codice" name="codice" value="<? echo $gruppo["codice"]; ?>">
		<div class="box">
			<table width="100%">
				<tr>
					<td class="etichetta">Tipo</td>
					<td style="color:#000;">
						<select onChange="check_tipo(<?= $id ?>);" name="tipo" id="gruppo_tipo" title="Tipo" rel="S;0;0;A">
							<option value="radio">Scelta singola</option>
							<option value="checkbox">Scelta multipla</option>
						</select>
					</td>
					<td class="etichetta">Obbligatorio</td>
					<td>
						<select name="obbligatorio" title="Obbligatorio" rel="S;0;0;A" id="gruppo_obbligatorio">
							<option value="S">Si</option>
							<option value="N">No</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="etichetta">Titolo</td><td colspan="3">
						<input type="text" style="width:99%" value="<? echo $gruppo["titolo"] ?>" name="titolo" id="gruppo_titolo" title="Titolo" rel="S;0;0;A">
					</td>
				</tr>
				<tr>
					<td class="etichetta">GUUE</td><td colspan="3">
						<input type="text" style="width:99%" value="<? echo $gruppo["guue"] ?>" name="guue" id="gruppo_guue" title="GUUE" rel="N;0;0;A">
					</td>
				</tr>
				<tr>
					<td class="etichetta">Suggerimenti</td>
					<td colspan="3">
						<textarea class="ckeditor_simple" name="suggerimenti" id="gruppo_suggerimenti" title="Suggerimenti" rel="N;0;0;A">
							<? echo $gruppo["suggerimenti"] ?>
						</textarea>
					</td>
				</tr>
			</table>
		</div>
			<div class="box">
				<h2>Opzioni</h2>
				<button class="aggiungi" onClick="aggiungi('/impostazioni/opzioni/tr_opzione.php','#opzioni');return false;"><img src="/img/add.png" alt="Aggiungi opzione">Aggiungi opzione</button>
				<table id="opzioni" width="100%">
					<tr>
						<td class="etichetta"><strong>Nome</strong></td>
						<td class="etichetta"><strong>GUUE</strong></td>
						<td class="etichetta"></td>
					</tr>
					<?
						if ($gruppo["codice"] != "") {
							$bind = array(":codice_gruppo" => $gruppo["codice"]);
							$sql = "SELECT * FROM b_opzioni WHERE eliminato = 'N' AND codice_gruppo = :codice_gruppo";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount()>0) {
								while ($opzione = $ris->fetch(PDO::FETCH_ASSOC)) {
									$id = $opzione["codice"];
									include($root."/impostazioni/opzioni/tr_opzione.php");
								}
							}
						}
					?>
				</table>
			</div>
	</form>
				<script>
				$("#obbligatorio").val('<? echo $gruppo["obbligatorio"] ?>');
				$("#gruppo_tipo").val('<? echo $gruppo["tipo"] ?>');
			</script>
