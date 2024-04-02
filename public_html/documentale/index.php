<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
  if(empty($_SESSION["codice_utente"]) || !check_permessi("documentale",$_SESSION["codice_utente"])) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  } else {
    ?><h1>Area documentale</h1>
		<? if (check_permessi("manage_documentale",$_SESSION["codice_utente"]) && isset($_SESSION["ente"])) { ?>
		<button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
			<span class="fa fa-paperclip"></span> Allega file
		</button>
		<? } ?>
		<div class="clear"><br></div>
    <table id="tabella_allegati" style="width:100%; margin-top:30px !important;" class="dataTables">
			<thead>
				<tr>
					<td width="20"></td>
					<td></td>
					<? if (!isset($_SESSION["ente"]["codice"])) echo "<td width=\"20%\">Ente</td>"; ?>
					<td width="100"></td>
					<td width="20"></td>
				</tr>
			</thead>
    </table><br>
		<script>
		$("#tabella_allegati").dataTable({
			"processing": true,
			"serverSide": true,
			"paging": true,
			"lengthChange": true,
			"searching": false,
			"ordering": false,
			"info": true,
			"autoWidth": false,
			"pageLength": 50,
			"lengthMenu": [[5,10,25,50,100,200,-1],[5, 10, 25, 50,100,200,"Tutte"]],
			"ajax": "table.php"
		});
		</script>
    <?
		if (check_permessi("manage_documentale",$_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			?>
			<button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
				<span class="fa fa-paperclip"></span> Allega file
			</button>
			<?
			$form_upload["sezione"] = "documentale";
			$form_upload["online"] = "S";
			include($root."/allegati/form_allegati.php");
		}
  }
  include_once($root."/layout/bottom.php");
?>
