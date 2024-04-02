<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("controllo_accessi",$_SESSION["codice_utente"]);
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
		<style type="text/css">
			.dataTables_paginate , .paging_two_button
			{
				height: 40px !important;
			}
		</style>
		<h1>CONTROLLO ACCESSI</h1>
		<div id="tabs">
	    	<ul>

					<li><a href="#sessioni">Sessioni attive</a></li>
					<li><a href="#accessi">Log Accessi</a></li>
	      	<li><a href="#tentativi">Tentativi di Accesso</a></li>
        </ul>
				<div id="sessioni">
					<table width="100%" id="sessioni" class="elenco">
						<thead>
							<tr>
								<th width="43%">Utente</th>
								<th width="5%">ID</th>
								<th width="30%">Ente</th>
								<th width="20%">Timestamp</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?
								$sql = "SELECT b_login_hash.codice, b_utenti.cognome, b_login_hash.hash, b_utenti.nome, b_login_hash.timestamp, b_enti.denominazione
												FROM b_login_hash JOIN b_utenti ON b_login_hash.codice_utente = b_utenti.codice
												JOIN b_enti ON b_utenti.codice_ente = b_enti.codice ";
								if (isset($_SESSION["ente"]["codice"])) {
									$sql .= " WHERE b_enti.codice = " . $_SESSION["ente"]["codice"] . " OR b_enti.sua = " . $_SESSION["ente"]["codice"];
								}
								$sql .= " ORDER BY b_utenti.cognome, b_utenti.nome ";
								$ris_sessioni = $pdo->query($sql);
								if ($ris_sessioni->rowCount() > 0) {
									while($sessione = $ris_sessioni->fetch(PDO::FETCH_ASSOC)) {
										?>
										<tr>
											<td><?= $sessione["cognome"] . " " . $sessione["nome"] ?></td>
											<td><?= strtoupper(substr($sessione["hash"],0,4)) ?></td>
											<td><?= $sessione["denominazione"] ?></td>
											<td><?= mysql2datetime($sessione["timestamp"]) ?></td>
											<td><button class='submit' style="width:25px !important; height:25px !important; background-color:#c00; cursor:pointer; border-radius: 12px; " onClick="elimina('<?= $sessione["codice"] ?>','controllo_accessi');" title="Elimina">
											<span class="fa fa-remove"></span></button></td>
										</tr>
										<?
									}
								}
							?>
						</tbody>
					</table>
				</div>
	        <div id="tentativi">
	        	<table width="100%" id="tab_tentativi">
            		<thead>
            			<tr>
            				<th>Ente</th>
            				<th>Utente</th>
            				<th>IP</th>
            				<th>Data e ora</th>
            			</tr>
            		</thead>
            		<tbody>
            			<tr>
            				<td></td>
            			</tr>
            		</tbody>
            	</table>
	        </div>
            <div id="accessi">
            	<table width="100%" id="tab_accessi">
            		<thead>
            			<tr>
            				<th>Ente</th>
            				<th>Utente</th>
            				<th>IP</th>
            				<th>Data e ora</th>
            			</tr>
            		</thead>
            		<tbody>
            			<tr>
            				<td></td>
            			</tr>
            		</tbody>
            	</table>
            </div>

        </div>
        <script type="text/javascript">
	        $(document).ready(function() {
	        	$("#tabs").tabs();
	        	$("#tab_accessi").dataTable({
							"processing": true,
							"searching": false,
							"ordering": false,
							"serverSide": true,
							"autoWidth": false,
							"language": {
									"url": "/js/dataTables.Italian.json"
							},
							"ajax": {
								"url": "table_rows.php",
								"method": "POST",
								"data": function (d) {
									d.operazione = "accessi";
									return d
								}
							},
							"pageLength": 50,
							"lengthMenu": [
									[5, 10, 25, 50, 100, 200, -1],
									[5, 10, 25, 50, 100, 200, "Tutti"]
							]
			    	});
				    $("#tab_tentativi").dataTable({
							"processing": true,
							"searching": false,
							"ordering": false,
							"serverSide": true,
							"autoWidth": false,
							"language": {
									"url": "/js/dataTables.Italian.json"
							},
							"ajax": {
								"url": "table_rows.php",
								"method": "POST",
								"data": function (d) {
									d.operazione = "tentativi";
									return d
								}
							},
							"pageLength": 50,
							"lengthMenu": [
									[5, 10, 25, 50, 100, 200, -1],
									[5, 10, 25, 50, 100, 200, "Tutti"]
							]
				    });
	        });
        </script>
		<?
	}
	?>
	<?

	include_once($root."/layout/bottom.php");
?>
