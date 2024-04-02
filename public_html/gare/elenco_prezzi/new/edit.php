<?
	include_once("../../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		if (!empty($_GET["codice"])) {
			$ris = $pdo->bindAndExec("SELECT * FROM b_valutazione_tecnica WHERE codice = :codice",[":codice"=>$_GET["codice"]]);
			if ($ris->rowCount() > 0) {
				$criterio = $ris->fetch(PDO::FETCH_ASSOC);
				$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/elenco_prezzi/edit.php'";
				$risultato = $pdo->query($strsql);
				if ($risultato->rowCount()>0) {
					$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
					$esito = check_permessi_gara($gestione["codice"],$criterio["codice_gara"],$_SESSION["codice_utente"]);
					$edit = $esito["permesso"];
					$lock = $esito["lock"];
				}
			}
			if (!$edit) {
				echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
				die();
			}
		} else {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	}
	if ($edit) { ?>
		<h1><?= $criterio["descrizione"] ?></h1>
		<? if (!$lock) { ?>
			<script>
				function aggiungi_prezzo() {
					if ($(".prezzo").length < 50) {
						aggiungi('/gare/elenco_prezzi/record.php','#elenco_prezzi');
					} else {
						alert("Troppi prezzi in modifica, procedere al salvataggio e riprovare");
					}
					return false;
				}
				function edit_prezzo(id) {
					if ($(".prezzo").length < 50) {
						data = "id=" + id;
						$.ajax({
							type: "POST",
							url: "/gare/elenco_prezzi/record.php",
							dataType: "html",
							data: data,
							async:false,
							success: function(script) {
								$("#prezzo_"+id).replaceWith(script);
							}
						});
						f_ready();
						etichette_testo();
					} else {
						alert("Troppi prezzi in modifica, procedere al salvataggio e riprovare");
					}
					return false;
				}
			</script>
			<form name="box" method="post" action="/gare/elenco_prezzi/save.php" rel="validate">
				<input type="hidden" name="codice_gara" value="<? echo $criterio["codice_gara"]; ?>">
				<input type="hidden" name="codice_criterio" value="<? echo $criterio["codice"]; ?>">
				<div class="comandi">
					<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
				</div>
			<? } ?>
		<table width="100%">
			<thead>
				<tr>
					<th width="10">Tipo</th>
					<th>Descrizione</th>
					<th width="50">U.d.m.</th>
					<th width="50">Quantit&agrave;</th>
					<th width="10"></th>
				</tr>
			</thead>
			<tbody id="elenco_prezzi">
				<?
				$sql = "SELECT * FROM b_elenco_prezzi WHERE codice_criterio = :codice_criterio ";
				$ris = $pdo->bindAndExec($sql,array(":codice_criterio"=>$criterio["codice"]));
				if ($ris->rowCount() > 0) {
					while($prezzo = $ris->fetch(PDO::FETCH_ASSOC)) {
						$id = $prezzo["codice"];
						include($root."/gare/elenco_prezzi/view.php");
					}
				}
				?>
			</tbody>
		</table>
		<? if (!$lock) { ?>
			<button class="aggiungi" onClick="aggiungi_prezzo();return false;"><img src="/img/add.png" alt="Aggiungi voce">Aggiungi voce</button>
			<input type="submit" class="submit_big" value="Salva">
		</form>
		<? } ?>
		<a href="/gare/elenco_prezzi/edit.php?codice=<?= $criterio["codice_gara"] ?>" class="espandi ritorna_button submit_big" style="background-color:#999;">Ritorna all'elenco</a>
		<?
			$_GET["codice"] = $criterio["codice_gara"];
			include($root."/gare/ritorna.php");
		?>
		<script>
			<? if ($lock) { ?>
				$("#contenuto_top :input").not('.espandi').prop("disabled", true);
			<? } ?>
		</script>
		<?
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	include_once($root."/layout/bottom.php");
?>
