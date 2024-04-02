<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;

	if (! isset($_SESSION["codice_utente"]) || ! isset($_SESSION["ente"])) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
	} else {
		if(! check_permessi("gare",$_SESSION["codice_utente"])) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
		} else {
			$permessi_gare_preliminari = check_permessi("gare/preliminari",$_SESSION["codice_utente"]);
			if($permessi_gare_preliminari && time() < strtotime('2021-05-03 00:00:00')) {
				?>
				<a href="/gare/id0-edit" title="Inserisci nuova gara">
					<div class="add_new">
						<span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi nuova gara
					</div>
				</a>
				<?
			}
			?>
			<style type="text/css">
				table tbody tr > td:first-child {
					padding: 0px;
					position: relative;
					width: 10px;
				}
			</style>
			<h1>GESTIONE GARE</h1>
			<table width="100%" id="gare">
				<thead>
					<tr>
						<td width="1"></td>
						<td>ID</td>
						<td>ID SUAFF</td>
						<td style="width: 80px">CIG</td>
						<td style="width: 100px">Stato</td>
						<td style="width: 90px">Tipo</td>
						<td style="width: 100px">Criterio</td>
						<td style="width: 100px">Procedura</td>
						<td>Oggetto</td>
						<td>Termine DL Semplificazioni</td>
						<? if ($_SESSION["ente"]["tipo"] == "SUA") { ?><td style="width: 150px">Ente</td><? } ?>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			<script type="text/javascript">
				var table = $('#gare').DataTable({
					"processing": true,
					"serverSide": true,
					"searching": true,
					"bLengthChange": false,
					"pageLength": 50,
					"autoWidth": false,
					"order": [[ 1, "desc" ]],
					"ajax": {
						"type": "POST",
						"url": "table.php",
						"data": function ( data ) {
							data.subject = $('#search-subject').val();
							data.customer = $('#search-customer').val();
						}
					},
					"language": {
						"url": "/js/dataTables.Italian.json"
					},
					columns: [
						{data: 'colore', "orderable": false},
						{data: 'id', "orderable": true},
						{data: 'id_suaff', "orderable": true},
						{data: 'cig', "orderable": false},
						{data: 'stato', "orderable": true},
						{data: 'tipo', "orderable": true},
						{data: 'criterio', "orderable": true},
						{data: 'procedura', "orderable": true},
						{data: 'oggetto', "orderable": true},
						{data: 'termini', "orderable": false},
						<?= $_SESSION["ente"]["tipo"] == "SUA" ? '{data: "denominazione_ente", "orderable": true}' : null ?>
					],
					"drawCallback": function( settings ) {
					}
				});
			</script>
			<div class="clear"><br>&nbsp;</div>
			<?
			/*
			if($permessi_gare_preliminari) {
				?>
				<a href="/gare/id0-edit" title="Inserisci nuova gara">
					<div class="add_new">
						<span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi nuova gara
					</div>
				</a>
				<?
			}
			*/
		}
		include_once($root."/layout/bottom.php");
	}

	?>
