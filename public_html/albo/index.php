<?
	include_once("../../config.php");
	$form_comunicazione = true;
	include_once($root."/layout/top.php");
	include_once($root."/inc/oeManager.class.php");


	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("albo",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
		?>
		<div id="modalIdInterno" style="display:none">
			<div style="text-align:center" id="modalIdInternoContent">
			</div>
		</div>
		<h1>INDIRIZZARIO OE</h1>
    <div style="text-align:right">
      <button onClick="$('#filtro').toggle('fast');" style="width:100%; padding:10px; background-color:#F60" class="submit">
      <span class="fa fa-filter"></span> Filtri
      </button>
    </div>
    <div class="box" id="filtro" <? if (!isset($_POST["oeManager"]["cpv"])) echo "style=\"display:none\""; ?>>
    	<? oeManager::printFilterForm() ?>
			<button class="submit_big" onClick="oeManagerFilters = $('.oeManagerInput').serializeArray(); $('.export-filter').val($('.oeManagerInput').serialize()); elenco.draw(); $('#filtro').slideUp('fast');"><span class="fa fa-filter"></span> Filtra</button>
      <br>
		</div>
	    <table style="text-align:center; width:100%; font-size:0.8em" id="oe">
      	<thead>
     			<tr>
						<th>#</th>
						<td width="10"></td>
     				<th>Ragione Sociale</th>
     				<th>Referente</th>
     				<th width="10">Tipo</th>
						<th width="100">Partita IVA</th>
						<th width="100">Codice Fiscale Impresa</th>
     				<th width="90">Data Iscrizione</th>
						<th width="10">Albi</th>
						<th width="150">Data richiesta</th>
						<th width="10">Inviti</th>
						<th width="10">Inviti <?= date('Y') ?></th>
     				<th width="10">FeedBack</th>
      			<th width="10">Dettagli</th>
						<th width="10">PEC <input id="invia_all" type="image" src="/img/newsletter.png" onClick="triggerClick = true; elenco.page.len(-1).draw();" width="24" title="Invia una comunicazione a tutti"></th>
					</tr>
	       </thead>
	       <tbody>
	       </tbody>
      	</table>
				<script>
					var oeManagerFilters = $(".oeManagerInput").serializeArray();
					var triggerClick = false
        	var elenco = $("#oe").DataTable({
            "processing": true,
            "serverSide": true,
						"language": {
		            "url": "/js/dataTables.Italian.json"
		        },
            "ajax": {
              "url": "table.php",
							"method": "POST",
              "data": function (d) {
                d.oeManager = oeManagerFilters;
								return d
              }
            },
						"drawCallback": function( settings ) {
							if (triggerClick) {
							 $('.invia_comunicazione').trigger('click');
							 triggerClick = false;
							}
							$.each(oeManagerFilters,function() {
								<?
									$firstCol = 8;
									$secondCol = 9;
								?>
								if (this.name == "oeManager[elenco]") {
									if (this.value != '' && this.value != 'albo-0') {
										elenco.column(<?= $firstCol ?>).visible(false);
										elenco.column(<?= $secondCol ?>).visible(true);
									} else {
										elenco.column(<?= $firstCol ?>).visible(true);
										elenco.column(<?= $secondCol ?>).visible(false);
									}
								}
							});
						},
						"pageLength": 20,
						"lengthMenu": [
								[5, 10, 25, 50, 100, 200, -1],
								[5, 10, 25, 50, 100, 200, "Tutti"]
						]
          });
		 		</script>
       	<div class="clear"></div>
      	<div style="text-align:right">
					<form target="_blank" action="pdf.php" method="POST">
						<input type="hidden" id="filters-pdf" class="export-filter" name="filters">
    				<button title="Esporta PDF"><img style="vertical-align:middle" src="/img/pdf.png" alt="Esporta">Esporta PDF</button>
					</form>
					<form target="_blank" action="excel<?= ($_SESSION["ente"]["codice"] == 940) ? "_eur" : "" ?>.php" method="POST">
						<input type="hidden" id="filters-excel" class="export-filter" name="filters">
    				<button title="Esporta CSV"><img style="vertical-align:middle" src="/img/xls.png" alt="Esporta">Esporta CSV</button>
					</form>
					<script>
						$(".export-filter").val($(".oeManagerInput").serialize());
					</script>
      	</div>
          <?

	include_once($root."/layout/bottom.php");
	?>
