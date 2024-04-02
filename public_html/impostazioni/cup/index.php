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
	echo "<h1>GESTIONE TABELLE DECODIFICA - CUP</h1>";
	?>
  <form name="box" method="post" action="save.php" rel="validate" >
  <div class="comandi">
  	<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
	</div>
  <div class="box" id ="tabs">
		<ul>
			<li><a href="#strumenti">Strumenti di programmazione</a></li>
			<li><a href="#finanziamenti">Tipologie Finanziamento</a></li>
			<li><a href="#natura">Natura</a></li>
			<li><a href="#tipologia">Tipologia</a></li>
			<li><a href="#settore">Settore</a></li>
			<li><a href="#sottosettore">Sotto-settore</a></li>
			<li><a href="#categoria">Categoria</a></li>
		</ul>
		<div id="strumenti">
			<h2>Strumenti di programmazione</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td>Valore</td><td>Etichetta</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="strumenti_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_cup_strumenti_programmazione WHERE attivo = 'S' ORDER BY valore";
						$risultato_strumenti = $pdo->query($strsql);
						if (isset($risultato_strumenti) && $risultato_strumenti->rowCount() > 0) {
							while ($record = $risultato_strumenti->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("strumenti/form.php");
							}
						} ?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('strumenti/form.php','#strumenti_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
		<div id="finanziamenti">
			<h2>Tipologie Finanziamento</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td>Valore</td><td>Etichetta</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="finanziamenti_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_cup_finanziamenti WHERE attivo = 'S' ORDER BY valore";
						$risultato_finanziamenti = $pdo->query($strsql);
						if (isset($risultato_finanziamenti) && $risultato_finanziamenti->rowCount() > 0) {
							while ($record = $risultato_finanziamenti->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("finanziamenti/form.php");
							}
						} ?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('finanziamenti/form.php','#finanziamenti_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
		<div id="natura">
			<h2>Natura</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td>Valore</td><td>Etichetta</td><td>Tipologia</td><td width="5">Elimina</td></tr>
				</thead>
				<tbody id="natura_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_cup_natura WHERE attivo = 'S' ORDER BY valore";
						$risultato_natura = $pdo->query($strsql);
						if (isset($risultato_natura) && $risultato_natura->rowCount() > 0) {
							while ($record = $risultato_natura->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("natura/form.php");
							}
						} ?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('natura/form.php','#natura_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
		<div id="tipologia">
			<h2>Tipologia</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td width="5%">Valore</td><td width="60%">Etichetta</td><td width="30%">Natura</td><td>Art.21</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="tipologia_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_cup_tipologia WHERE attivo = 'S' ORDER BY codice_derivazione, valore";
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
		<div id="settore">
			<h2>Settore</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td width="45%">Valore</td><td width="45%">Etichetta</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="settore_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_cup_settore WHERE attivo = 'S' ORDER BY valore";
						$risultato_settore = $pdo->query($strsql);
						if (isset($risultato_settore) && $risultato_settore->rowCount() > 0) {
							while ($record = $risultato_settore->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("settore/form.php");
							}
						}?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('settore/form.php','#settore_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
		<div id="sottosettore">
			<h2>Sotto-settore</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td width="10">Valore</td><td>Etichetta</td><td width="30%">Settore</td><td>Art.21</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="sottosettore_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_cup_sottosettore WHERE attivo = 'S' ORDER BY codice_derivazione, valore";
						$risultato_sottosettore = $pdo->query($strsql);
						if (isset($risultato_sottosettore) && $risultato_sottosettore->rowCount() > 0) {
							while ($record = $risultato_sottosettore->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("sottosettore/form.php");
							}
						}?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('sottosettore/form.php','#sottosettore_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
						</td></tr>
					</tfoot>
				</table>
			<div class="clear"></div>
		</div>
		<div id="categoria">
			<h2>Categoria</h2>
	  	<table width="100%" >
	    	<thead>
					<tr><td width="30%">Valore</td><td width="30%">Etichetta</td><td width="30%">Sotto-settore</td><td>Art. 21</td><td width="10">Elimina</td></tr>
				</thead>
				<tbody id="categoria_tabs">
					<?
						$strsql = "SELECT * FROM b_conf_cup_categoria WHERE attivo = 'S' ORDER BY codice_derivazione, valore";
						$risultato_categoria = $pdo->query($strsql);
						if (isset($risultato_categoria) && $risultato_categoria->rowCount() > 0) {
							while ($record = $risultato_categoria->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("categoria/form.php");
							}
						} ?>
					</tbody>
					<tfoot>
						<tr><td colspan="6">
							<button class="aggiungi" onClick="aggiungi('categoria/form.php','#categoria_tabs');return false;"><img src="/img/add.png" alt="Aggiungi elemento">Aggiungi elemento</button>
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
