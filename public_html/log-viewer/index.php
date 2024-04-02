<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && empty($_SESSION["ente"])) {
		$edit = check_permessi("log-viewer",$_SESSION["codice_utente"]);
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	if($edit) {
		?>
		<h1>LOG</h1>
		<div class="box">
			<h2><i class="fa fa-search"></i> Ricerca</h2>
			<style type="text/css">input[type="text"] {width: 100%;}</style>
			<form target="_blank" action="table.php" rel="validate" method="POST">
				<table width="100%" style="table-layout: fixed;">
					<tbody>
						<tr>
							<td class="etichetta" width="10%">Data e ora Inizio</td>
							<td><input class="titolo_edit datetimepick extrasearch" type="text" name="data_inizio" id="data_inizio" title="Data inizio" rel="S;0;0;DT" value="" placeholder="Data inizio"></td>
							<td class="etichetta" width="10%">Data e ora fine</td>
							<td><input class="titolo_edit datetimepick extrasearch" type="text" name="data_fine" id="data_fine" title="Data fine" rel="S;0;0;DT" value="<?= date('d/m/Y H:i') ?>" placeholder="Data fine"></td>
						</tr>
					</tbody>
				</table>
				<input class="submit_big" value="Esporta" type="submit">
			</form>
		</div>
    <div class="clear"></div>
		<?
	} else {
		?>
		<h1 style="text-align:center">
			<span class="fa fa-exclamation-circle fa-3x"></span><br>Accesso negato!
		</h1>
		<?
	}
	include_once($root."/layout/bottom.php");
?>
