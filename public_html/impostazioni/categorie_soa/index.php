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
	echo "<h1>CATEGORIE SOA</h1>";
	if ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON") {
		$strsql = "SELECT * FROM b_categorie_soa WHERE attivo = 'S' ORDER BY codice ";
		$risultato = $pdo->bindAndExec($strsql);
	?>
  <form name="box" method="post" action="save.php" rel="validate" >
  <div class="comandi">
  	<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
	</div>
  <div class="box">
  	<table class="elenco" width="100%" >
    	<thead>
				<tr><td>ID</td><td>Descrizione</td><td>S.I.O.S.</td><td>Qualificazione Obbligatoria</td><td>Tutelate</td><td>Elimina</td></tr>
			</thead>
			<tbody id="categorie_tabs">
				<? if (isset($risultato) && $risultato->rowCount() > 0) {
						while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
							$id = $record["codice"];
							include("form.php");
						}
					} else {
						$id = "i_0";
						$record = get_campi("b_categorie_soa");
						include("form.php");
					}?>
				</tbody>
				<tfoot>
					<tr><td colspan="6">
						<button class="aggiungi" onClick="aggiungi('form.php','#categorie_tabs');return false;"><img src="/img/add.png" alt="Aggiungi categoria">Aggiungi categoria</button>
					</td></tr>
				</tfoot>
			</table>
			<div class="clear"></div>
		</div>
		<input type="submit" class="submit_big" value="Salva">
	</form>
	<?
		} else {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	include_once($root."/layout/bottom.php");
	?>
