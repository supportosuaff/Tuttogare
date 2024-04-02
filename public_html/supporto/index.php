<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && empty($_SESSION["ente"])) {
		$edit = check_permessi("supporto",$_SESSION["codice_utente"]);
		if (! $edit || ! in_array($_SESSION["tipo_utente"], array('SAD', 'SUP'))) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	if($edit) {
		?>
		<h1>SUPPORTO OPERATORI ECONOMICI</h1>

		<div id="chiudiCall-form" style="display:none">
			<form action="update_call.php" method="post" rel="validate">
				<input type="hidden" name="codice" id="chiudi-codice" value="" rel="S;0;0;N" title="codice">
				<input type="hidden" name="stato" id="chiudi-stato" value="" rel="S;0;0;N" title="stato">
				<textarea style="width:100%" id="chiudi-note" rows="10" title="note" name="note_interne" rel="S;3;0;A">
				</textarea>
				<div style="float:left; width:49%">
					<button class="submit_big" style="background-color:chartreuse" onClick="$('#chiudi-stato').val('20'); return true">
						<span class="fa fa-check"></span> Chiamata conclusa
					</button>
				</div>
				<div style="float:right; width:49%">
					<button class="submit_big" style="background-color:crimson" onClick="$('#chiudi-stato').val('30'); return true">
						<span class="fa fa-remove"></span> Chiamata annullata
					</button>
				</div>
				<div class="clearfix"></div>
			</form>
		</div>
		<div id="tabs">
			<ul>
				<li><a href="#call">Prenotazioni</a></li>
				<li><a href="#oe" onclick="elenco.draw();">OE</a></li>
				<li><a href="#gare" onclick="elenco_gare.draw();">Gare</a></li>
			</ul>
			<div id="call">
				<script>
					function toggleArchivio() {
						if ($("#archivio_call").val()=="") {
							$("#archivio_call").val('S');
							$("#archivio_toggle").attr('style','background-color:#FC0').html('Visualizza coda');
						} else {
							$("#archivio_call").val('');
							$("#archivio_toggle").removeAttr('style').html('Visualizza archivio');
						}
						elenco_call.draw();
					}
				</script>
				<input type="hidden" class="hidden" id="archivio_call" value="">
				<button class="submit_big" id="archivio_toggle" onClick="toggleArchivio()">Visualizza archivio</button>
				<table style="text-align:center; width:100%; font-size:0.8em; table-layout: fixed; margin-top: 20px;" id="call-table">
					<thead>
						<tr>
							<th width="10"></th>
							<th width="10">#</th>
							<th width="10%">Data</th>
							<th width="10%">Referente</th>
							<th width="10%">Telefono</th>
							<th width="25%">Operatore Economico</th>
							<th >Note</th>
							<th width="5%"></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div id="oe">
				<div class="box">
					<h2><i class="fa fa-search"></i> Ricerca</h2>
					<style type="text/css">input[type="text"] {width: 100%;}</style>
					<table width="100%" style="table-layout: fixed;">
						<tbody>
							<tr>
								<td class="etichetta" width="10%">Pec</td>
								<td><input class="titolo_edit extrasearch" type="text" name="pec" id="pec" title="Pec" value="" placeholder="Pec"></td>
								<td class="etichetta" width="10%">Email</td>
								<td><input class="titolo_edit extrasearch" type="text" name="email" id="email" title="Email" value="" placeholder="Email"></td>
							</tr>
							<tr>
								<td class="etichetta" width="10%">Ragione Sociale</td>
								<td><input class="titolo_edit extrasearch" type="text" name="Ragione Sociale" id="ragione_sociale" title="Ragione Sociale" value="" placeholder="Ragione Sociale"></td>
								<td class="etichetta" width="10%">Partita IVA</td>
								<td><input class="titolo_edit extrasearch" type="text" name="p_iva" id="p_iva" title="P. IVA / CF" value="" placeholder="P. IVA / CF"></td>
							</tr>
						</tbody>
					</table>
				</div>
				<table style="text-align:center; width:100%; font-size:0.8em; table-layout: fixed; margin-top: 20px;" id="utenti">
					<thead>
						<tr>
							<th width="5"></th>
							<th width="5"></th>
							<th>Ragione Sociale</th>
							<th>Nominativo</th>
							<th>Tipo</th>
							<th width="80">Partita IVA</th>
							<th width="120">Codice Fiscale</th>
							<th width="80">Timestamp</th>
							<th>Enti</th>
							<th width="30"></th>
							<th width="30"></th>
							<th width="30"></th>
							<th width="30"></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div id="gare">
				<table style="text-align:center; width:100%; font-size:0.8em; table-layout: fixed; margin-top: 20px;" id="gare-table">
					<thead>
						<tr>
							<th width="10"></th>
							<th width="120">Stato</th>
							<th width="70">ID</th>
							<th width="70">CIG</th>
							<th width="30%">Oggetto</th>
							<th>Procedura</th>
							<th>Beneficiario</th>
							<th>Gestore</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
				<script>
					function get_list(id, function_name){
						$.ajax({
							url: 'enti.list.php',
							type: 'POST',
							dataType: 'html',
							data: {user_id: id, function: function_name}
						})
						.done(function(html) {
							$('#enti-list').html(html);
							$("#enti-list").dialog({
								modal: true,
								width: "30%",
								title: "Seleziona l'ente:"
							});
							$("#enti-list").show();
						})
						.fail(function() {
							jalert('Attenzione, si è verificato un errore');
						});
					}

					function rigenera_password(id_utente,id_ente) {
						$.ajax({
							type: "GET",
							url: "/user/rigenera.php",
							data: {id: id_utente, id_ente: id_ente},
							success: function(e) {
								jalert(e);
							}
						});
					}

					function random_password(id_utente,id_ente) {
						if (confirm('Proseguendo sarà inviata una password provvisoria all\' operatore. Vuoi continuare?')) {
							$.ajax({
								type: "GET",
								url: "/user/random_password.php",
								data: {id: id_utente, id_ente: id_ente},
								success: function(e) {
									jalert(e);
								}
							});
						} else {
							return false
						}
					}

					function iniziaCall(codice) {
						if (confirm('Proseguendo la chiamata non sarà gestibile da altri operatori. Vuoi continuare?')) {
							$.ajax({
								type: "POST",
								url: "/supporto/update_call.php",
								data: {codice: codice },
								success: function(e) {
									jalert(e);
									elenco_call.draw();
								}
							});
						} else {
							return false
						}
					}
					
					function chiudiCall(codice) {
						$("#chiudi-codice").val(codice);
						$("#chiudi-stato").val('');
						$("#chiudi-note").val('');
						$("#chiudiCall-form").dialog({ modal: true, width:800, title: "Chiusura chiamata"});
						f_ready();
					}

					function reinvia_conferma(id_utente,id_ente) {
						$.ajax({
							type: "GET",
							url: "/operatori_economici/reinvia_conferma.php",
							data: {id: id_utente, id_ente: id_ente},
							success: function(e) {
								jalert(e);
							}
						});
					}
					var elenco_call = $("#call-table").DataTable({
						"processing": true,
						"serverSide": true,
						"language": {
								"url": "/js/dataTables.Italian.json"
						},
						"ajax": {
						  "type": "POST",
							"url": "table-call.php",
						  	"data": function ( data ) {
						    	data.archivio = $('#archivio_call').val();
						  	}
						},
						"drawCallback": function( settings ) {
							elenco_call.columns.adjust();
						},
						"pageLength": 50,
						"lengthMenu": [
								[5, 10, 25, 50, 100, 200, -1],
								[5, 10, 25, 50, 100, 200, "Tutti"]
						]
					});


					var elenco = $("#utenti").DataTable({
						"processing": true,
						"serverSide": true,
						"language": {
								"url": "/js/dataTables.Italian.json"
						},
						"ajax": {
						  "type": "POST",
							"url": "table.php",
						  	"data": function ( data ) {
						    	data.ragione_sociale = $('#ragione_sociale').val();
						    	data.p_iva = $('#p_iva').val();
						    	data.email = $('#email').val();
						    	data.pec = $('#pec').val();
						  	}
						},
						"drawCallback": function( settings ) {
							elenco.columns.adjust();
						},
						"pageLength": 50,
						"lengthMenu": [
								[5, 10, 25, 50, 100, 200, -1],
								[5, 10, 25, 50, 100, 200, "Tutti"]
						]
					});

					var elenco_gare = $("#gare-table").DataTable({
						"processing": true,
						"serverSide": true,
						"language": {
								"url": "/js/dataTables.Italian.json"
						},
						"ajax": {
						  "type": "POST",
							"url": "table-gare.php"
						},
						"drawCallback": function( settings ) {
							elenco_gare.columns.adjust();
						},
						"pageLength": 50,
						"lengthMenu": [
								[5, 10, 25, 50, 100, 200, -1],
								[5, 10, 25, 50, 100, 200, "Tutti"]
						]
					});

					$(function() {
					  $('.extrasearch').on('input', function(event) {
					    event.preventDefault();
					    elenco.ajax.reload();
					  });
					});
					$("#tabs").tabs();
				</script>
        <div class="clear"></div>
        <div id="enti-list" style="display: none;"></div>
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
