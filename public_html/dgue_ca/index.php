<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("dgue_ca",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit) {
?>
<h1>DGUE - DOCUMENTO DI GARA UNICO EUROPEO</h1>
<?
		$bind = array();
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql = "SELECT * FROM b_dgue_free WHERE ";
		$strsql .= " (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		if ($_SESSION["gerarchia"] > 0) {
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
		}

		$strsql.= " ORDER BY codice DESC ";
		$ris_model = $pdo->bindAndExec($strsql,$bind);
		?>
		<div style="float:left; width:49%">
			<a href="/dgue_ca/edit.php?codice=0" title="Crea nuovo modello"><div class="scegli_file">
			<span class="fa fa-plus-circle fa-3x"></span><br>
			Crea nuovo modello
			</div></a>
		</div>
		<script type="text/javascript" src="/js/resumable.js"></script>
		<script type="text/javascript" src="resumable-uploader.js"></script>
		<div style="float:right; width:49%">
			<form id="import_file" action="/dgue/getPDF.php" method="post" target="_self">
				<input type="hidden" id="filechunk" name="filechunk">
				<div class="scegli_file" id="scegli_file"><span class="fa fa-code fa-3x"></span><br>Visualizza DGUE da XML</div>
				<script>
					var uploader = (function($){
					return (new ResumableUploader($('#scegli_file')));
					})(jQuery);
				</script>
				<div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
			</form>
		</div>
		<div class="clear"></div><br>
		<?
		if ($ris_model->rowCount() > 0) {
	?>
		<table width="100%" class="elenco">
			<thead>
				<tr>
					<td width="10%">Identificativo</td>
					<td width="50%">Oggetto</td>
					<td width="20%">Denominazione</td>
					<td width="15%">Data</td>
					<td width="10"></td>
					<td width="10">XML</td>
					<td width="10">PDF</td>
					<td></td>
				</tr>
			</thead>
			<tbody>
    <?
		while($modello = $ris_model->fetch(PDO::FETCH_ASSOC)) {
			?>
			<tr id="<?= $modello["codice"] ?>">
				<td><?= $modello["identificativo"] ?></td>
				<td><?= $modello["procedura"] ?></td>
				<td><?= $modello["denominazione"] ?></td>
				<td><?= mysql2datetime($modello["timestamp"]) ?></td>
				<td><a href="edit.php?codice=<?= $modello["codice"] ?>" title="Modifica"><span class='fa fa-pencil fa-2x'></span></a></td>
				<td><a href="/dgue/getRequestXML.php?codice_riferimento=<?= $modello["codice"] ?>&sezione=free" title="Download XML"><span style="color:#066" class='fa fa-code fa-2x'></span></a></td>
				<td><a href="/dgue/getRequestPDF.php?codice_riferimento=<?= $modello["codice"] ?>&sezione=free" title="Download PDF"><span style="color:#900" class='fa fa-file-pdf-o fa-2x'></span></a></td>
				<td style"text-align:center"><input type="image" onClick="elimina('<? echo $modello["codice"] ?>','dgue_ca');" src="/img/del.png" title="Elimina"></td>
			</tr>
			<?
		}
	?>
    	</tbody>
    </table>
    <div class="clear"></div>
<?php
		} else { ?>
			<h1 style="text-align:center">Nessun documento in archivio</h1>
  <? }
	}
	include_once($root."/layout/bottom.php");
	?>
