<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (! isset($_SESSION["ente"])) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
	} else {
		?>
		<h1><? if (! isset($_GET["esiti"])) { echo traduci("Gare") . " "; if (isset($_GET["scadute"])) { echo ($_GET["scadute"]) ? traduci("Scadute") : traduci("Attive");}} else { echo traduci("Esiti di gara"); } ?></h1>
		<a href="/archivio_gare/index.php">Tutte</a> |
		<a href="/archivio_gare/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>scadute=0"><?= traduci("Attive") ?></a> |
		<a href="/archivio_gare/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>scadute=1"><?= traduci("Scadute") ?></a> |
		<a href="/archivio_gare/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>esiti=1"><?= traduci("Esiti di gara") ?></a> |
		<a href="/archivio_gare/anac.php">Altre procedure non gestite tramite il portale</a>
		<? 
			$bind = array();
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$sql  = "SELECT codice,denominazione FROM b_enti WHERE ((codice = :codice_ente) OR (sua = :codice_ente)) ORDER BY denominazione ";
			$ris = $pdo->bindAndExec($sql, $bind);
			if ($ris->rowCount() > 1) {
				?>
				<div style="float:right; text-align:right; width:25%">
					<strong>Filtra Ente</strong><br><select onchange="window.location.href='<?= $_SERVER["PHP_SELF"] ?>?codice_ente='+$(this).val()">
						<option value="">Tutti</option>
						<?
							while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
								?><option <?= (!empty($_GET["codice_ente"]) && $rec["codice"] == $_GET["codice_ente"]) ? "selected" : "" ?> value="<? echo $rec["codice"] ?>"><? echo $rec["denominazione"] ?></option><?
							}
						?>
					</select>
				</div>
				<?
			}
		?>
		<div class="clear"><br>&nbsp;</div>
		<?
		if(file_exists("personal/custom_{$_SESSION["ente"]["codice"]}.php")) {
			include_once "personal/custom_{$_SESSION["ente"]["codice"]}.php";
		} else {
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
						<td>ID SUAFF</td>
						<td><?= traduci("Stato") ?></td>
						<td>CIG</td>
						<td><?= traduci("Tipologia") ?></td>
						<td><?= traduci("Criterio") ?></td>
						<td><?= traduci("Procedura") ?></td>
						<td><?= traduci("Oggetto") ?></td>
						<? if ($_SESSION["ente"]["tipo"] == "SUA") { ?><td width="200"><?= traduci("Ente") ?></td><? } ?>
						<td><?= traduci("Scadenza") ?></td>
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
						{data: 'id_suaff', "orderable": true},
						{data: 'stato', "orderable": true},
						{data: 'cig', "orderable": false},
						{data: 'tipo', "orderable": true},
						{data: 'criterio', "orderable": true},
						{data: 'procedura', "orderable": true},
						{data: 'oggetto', "orderable": true}<?= $_SESSION["ente"]["tipo"] == "SUA" ? ', {data: "denominazione_ente", "orderable": true}' : null ?>,
						{data: 'scadenza', "orderable": true}
					],
					"drawCallback": function( settings ) {
					}
				});
			</script>
			<?
		}
	}
	include_once($root."/layout/bottom.php");
?>



	<?
	die();


	if (isset($_SESSION["ente"])) {
		$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
		if (!isset($_SESSION["codice_utente"])) {
			$strsql  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_ente_gestore.dominio, b_enti.denominazione,  b_enti.provincia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_stati_gare.titolo AS fase, b_stati_gare.colore  ";
			$strsql .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
			$strsql .= "JOIN b_criteri ON b_gare.criterio = b_criteri.codice ";
			$strsql .= "JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice ";
			$strsql .= "JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase ";
			$strsql .= "JOIN b_enti ON b_gare.codice_ente = b_enti.codice ";
			$strsql .= "JOIN b_enti AS b_ente_gestore ON b_gare.codice_gestore = b_ente_gestore.codice ";
			$strsql .= "WHERE pubblica = '2' AND (codice_gestore = :codice_ente OR codice_ente = :codice_ente) ";
			if (isset($_GET["scadute"])) {
				if ($_GET["scadute"]) {
					$strsql .= " AND b_gare.data_scadenza < NOW() ";
				} else {
					$strsql .= " AND b_gare.data_scadenza >= NOW() ";
				}
			}
			if (isset($_GET["codice_ente"])) {
				$bind[":codice_ente_filtro"]=$_GET["codice_ente"];
				$strsql .= " AND codice_ente = :codice_ente_filtro ";
			}
			if (isset($_GET["esiti"])) {
				$strsql .= " AND (b_gare.stato = 4 OR b_gare.stato >= 7) ";
			}
			$strsql .= "GROUP BY b_gare.codice ";
			$strsql .= "ORDER BY codice DESC" ;
		} else {
			if (is_operatore()) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql  = "SELECT b_gare.*, b_enti.denominazione, b_enti.provincia, b_tipologie.tipologia AS tipologia, b_ente_gestore.dominio, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_stati_gare.titolo AS fase, b_stati_gare.colore ";
				$strsql .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
				$strsql .= "JOIN b_criteri ON b_gare.criterio = b_criteri.codice ";
				$strsql .= "JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice ";
				$strsql .= "JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase ";
				$strsql .= "JOIN b_enti ON b_gare.codice_ente = b_enti.codice ";
				$strsql .= "JOIN b_enti AS b_ente_gestore ON b_gare.codice_gestore = b_ente_gestore.codice ";
				$strsql .= "LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara ";
				$strsql .= "WHERE (codice_gestore = :codice_ente OR codice_ente = :codice_ente) ";
				$strsql .= "AND (pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente)))) ";
				if (isset($_GET["scadute"])) {
					if ($_GET["scadute"]) {
						$strsql .= " AND b_gare.data_scadenza < NOW() ";
					} else {
						$strsql .= " AND b_gare.data_scadenza >= NOW() ";
					}
				}
				if (isset($_GET["codice_ente"])) {
					$bind[":codice_ente_filtro"]=$_GET["codice_ente"];
					$strsql .= " AND codice_ente = :codice_ente_filtro ";
				}
				if (isset($_GET["esiti"])) {
					$strsql .= " AND (b_gare.stato = 4 OR b_gare.stato >= 7) ";
				}
				$strsql .= "GROUP BY b_gare.codice ";
				$strsql .= "ORDER BY codice DESC" ;
			} else {
				$strsql  = "SELECT b_gare.*, b_enti.denominazione, b_enti.provincia, b_tipologie.tipologia AS tipologia, b_ente_gestore.dominio, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_stati_gare.titolo AS fase, b_stati_gare.colore ";
				$strsql .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
				$strsql .= "JOIN b_criteri ON b_gare.criterio = b_criteri.codice ";
				$strsql .= "JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice ";
				$strsql .= "JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase ";
				$strsql .= "JOIN b_enti ON b_gare.codice_ente = b_enti.codice ";
				$strsql .= "JOIN b_enti AS b_ente_gestore ON b_gare.codice_gestore = b_ente_gestore.codice ";
				$strsql .= "WHERE (pubblica > 0) AND (codice_gestore = :codice_ente OR codice_ente = :codice_ente) ";
				if (isset($_GET["scadute"])) {
					if ($_GET["scadute"]) {
						$strsql .= " AND b_gare.data_scadenza < NOW() ";
					} else {
						$strsql .= " AND b_gare.data_scadenza >= NOW() ";
					}
				}
				if (isset($_GET["codice_ente"])) {
					$bind[":codice_ente_filtro"]=$_GET["codice_ente"];
					$strsql .= " AND codice_ente = :codice_ente_filtro ";
				}
				if (isset($_GET["esiti"])) {
					$strsql .= " AND (b_gare.stato = 4 OR b_gare.stato >= 7) ";
				}
				$strsql .= "GROUP BY b_gare.codice ";
				$strsql .= "ORDER BY codice DESC" ;
			}
		}
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso


?><h1><? if (!isset($_GET["esiti"])) { ?><?= traduci("Gare") ?> <? if (isset($_GET["scadute"])) { echo ($_GET["scadute"]) ? traduci("Scadute") : traduci("Attive"); } } else { echo traduci("Esiti di gara"); } ?></h1>
<a href="/archivio_gare/index.php">Tutte</a> |
<a href="/archivio_gare/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>scadute=0"><?= traduci("Attive") ?></a> |
<a href="/archivio_gare/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>scadute=1"><?= traduci("Scadute") ?></a> |
<a href="/archivio_gare/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>esiti=1"><?= traduci("Esiti di gara") ?></a> |
<a href="/archivio_gare/anac.php">Altre procedure non gestite tramite il portale</a>
<br><br>
<?
	if ($risultato->rowCount() > 0) {
		$edited_output_path = "personal/table_".$_SESSION["ente"]["codice"].".php";
		if (file_exists($edited_output_path)) {
			include($edited_output_path);
		} else {
			include("standard_table.php");
		}
	?>
	<div class="clear"></div>
<?php
		} else { ?>
			<h1 style="text-align:center">Nessuna gara disponibile</h1>
        <? }
	}
	include_once($root."/layout/bottom.php");
	?>
