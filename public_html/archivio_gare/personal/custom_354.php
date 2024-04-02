<?
	if (isset($_SESSION["ente"])) {
	?>
			<style type="text/css">
				table tbody tr > td:first-child {
					padding: 0px;
					position: relative;
					width: 10px;
				}
			</style>
			<table width="100%" id="elenco_gare">
    		<thead>
					<tr>
						<td></td>
						<td>ID</td>
						<td>Tipologia</td>
						<td>Criterio</td>
						<td>Procedura</td>
						<td>Oggetto</td>
						<td>Importo</td>
						<? if ($_SESSION["ente"]["tipo"] == "SUA") echo "<td>Ente</td>"; ?>
						<td>Provincia</td>
						<td>Struttura Proponente</td>
						<td>Pubblicazione</td>
						<td>Scadenza</td>
          </tr>
        </thead>
				<tbody></tbody>
			</table>
			<script type="text/javascript">
				var table = $('#elenco_gare').DataTable({
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
							<? if (isset($_GET["scadute"])) { ?>data.scadute = '<?= ! empty($_GET["scadute"]) ? $_GET["scadute"] : '' ?>';<? } ?>
							data.esiti = '<?= ! empty($_GET["esiti"]) ? $_GET["esiti"] : '' ?>';
							data.codice_ente = '<?= ! empty($_GET["codice_ente"]) ? $_GET["codice_ente"] : '' ?>';
						}
					},
					"language": {
						"url": "/js/dataTables.Italian.json"
					},
					columns: [
						{data: 'colore', "orderable": false},
						{data: 'id', "orderable": true},
						{data: 'tipo', "orderable": false},
						{data: 'criterio', "orderable": false},
						{data: 'procedura', "orderable": false},
						{data: 'oggetto', "orderable": false},
						{data: 'importo', "orderable": false}<?= $_SESSION["ente"]["tipo"] == "SUA" ? ', {data: "denominazione_ente", "orderable": false}' : null ?>,
						{data: 'provincia', "orderable": false},
						{data: 'struttura', "orderable": false},
						{data: 'pubblicazione', "orderable": false},
						{data: 'scadenza', "orderable": false},
					],
					"drawCallback": function( settings ) {
					}
				});
			</script>
		<? } ?>
