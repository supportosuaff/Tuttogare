<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit) {
	echo "<h1>GESTIONE TABELLE PROGRAMMAZIONE</h1>";
	?>
  <form name="box" method="post" action="save.php" rel="validate" >
  <div class="comandi">
  	<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
	</div>
  <div class="box" id ="tabs">
		<ul>
			<li><a href="#tipologia">Tipologia</a></li>
			<li><a href="#categorie">Categorie</a></li>
			<li><a href="#privato">Capitali privati</a></li>
			<li><a href="#progettazione">Stato Progettazione</a></li>
			<li><a href="#finalita">Finalit&agrave;</a></li>
			<li><a href="#finanziarie">Risorse finanziarie</a></li>
		</ul>
		<div id="tipologia">
			<h2>Tipologia</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td width="5%">Valore</td><td>Etichetta</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="tipologia_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_programmazione_tipologia WHERE attivo = 'S' ORDER BY valore";
						$risultato_tipologia = $pdo->query($strsql);
						if (isset($risultato_tipologia) && $risultato_tipologia->rowCount() > 0) {
							while ($record = $risultato_tipologia->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("tipologia/form.php");
							}
						} ?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('tipologia/form.php','#tipologia_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
		<div id="categorie">
			<h2>Categorie</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td width="5%">Valore</td><td width="5%">Valore 1</td><td>Etichetta</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="categorie_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_programmazione_categorie WHERE attivo = 'S' ORDER BY valore";
						$risultato_categorie = $pdo->query($strsql);
						if (isset($risultato_categorie) && $risultato_categorie->rowCount() > 0) {
							while ($record = $risultato_categorie->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("categorie/form.php");
							}
						} ?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('categorie/form.php','#categorie_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
		<div id="privato">
			<h2>Capitali privati</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td width="5%">Valore</td><td>Etichetta</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="privato_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_programmazione_capitale_privato WHERE attivo = 'S' ORDER BY valore";
						$risultato_privato = $pdo->query($strsql);
						if (isset($risultato_privato) && $risultato_privato->rowCount() > 0) {
							while ($record = $risultato_privato->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("privato/form.php");
							}
						} ?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('privato/form.php','#privato_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
		<div id="progettazione">
			<h2>Stato di progettazione</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td width="5%">Valore</td><td>Etichetta</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="progettazione_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_programmazione_stato_progettazione WHERE attivo = 'S' ORDER BY valore";
						$risultato_progettazione = $pdo->query($strsql);
						if (isset($risultato_progettazione) && $risultato_progettazione->rowCount() > 0) {
							while ($record = $risultato_progettazione->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("progettazione/form.php");
							}
						} ?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('progettazione/form.php','#progettazione_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
		<div id="finalita">
			<h2>Finalit&agrave;</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td width="5%">Valore</td><td>Etichetta</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="finalita_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_programmazione_finalita WHERE attivo = 'S' ORDER BY valore";
						$risultato_finalita = $pdo->query($strsql);
						if (isset($risultato_finalita) && $risultato_finalita->rowCount() > 0) {
							while ($record = $risultato_finalita->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("finalita/form.php");
							}
						} ?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('finalita/form.php','#finalita_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
		<div id="finanziarie">
			<h2>Risorse finanziarie</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td width="5%">Valore</td><td>Etichetta</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="finanziarie_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_programmazione_risorse_finanziarie WHERE attivo = 'S' ORDER BY valore";
						$risultato_finanziarie = $pdo->query($strsql);
						if (isset($risultato_finanziarie) && $risultato_finanziarie->rowCount() > 0) {
							while ($record = $risultato_finanziarie->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("finanziarie/form.php");
							}
						} ?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('finanziarie/form.php','#finanziarie_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
	</div>
	<script>
		$("#tabs").tabs();
	</script>
	<input type="submit" class="submit_big" value="Salva">
	</form>
	<?
		} else {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}

	include_once($root."/layout/bottom.php");
	?>
