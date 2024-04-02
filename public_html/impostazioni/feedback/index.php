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
		echo "<h1>CONFIGURAZIONE FEEDBACK OE</h1>";
		if (!empty($_SESSION["ente"]["codice"])) {
			$strsql = "SELECT * FROM b_set_feedback WHERE codice_ente = :codice AND eliminato = 'N'";
			$risultato = $pdo->bindAndExec($strsql,array(":codice"=>$_SESSION["ente"]["codice"]));
		} else {
			$strsql = "SELECT * FROM b_set_feedback WHERE codice_ente = 0 AND eliminato = 'N'";
			$risultato = $pdo->query($strsql);
		}
	?>
  <form name="box" method="post" action="save.php" rel="validate" >
		<input type="hidden" id="codice" name="codice" value="<? echo $record_ente["codice"]; ?>">
		<div class="comandi">
			<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
		</div>
		<div class="box">
			<table width="100%" >
				<tr>
					<th>Valutazioni richieste per effettuare il calcolo del punteggio</th>
					<td width="200">
						<input type="text" class="titolo_edit" style="text-align:center" name="required_feedback" value="<?= $_SESSION["ente"]["required_feedback"] ?>">
					</td>
				</tr>
			</table>
			<table width="100%" >
				<thead>
					<tr>
						<th>Nome</th>
						<th>Descrizione</th>
						<th>Ponderazione</th>
						<th></th>
					</tr>
				</thead>
				<tbody id="rows">
					<? if (isset($risultato) && $risultato->rowCount() > 0) {
							while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
								$id = $record["codice"];
								include("form.php");
							}
						} else {
							$id = "i_0";
							$record = get_campi("b_set_feedback");
							include("form.php");
						}
					?>
				</tbody>
				<tfoot>
					<tr><td colspan="8">
						<button class="aggiungi" onClick="aggiungi('form.php','#rows');return false;"><img src="/img/add.png" alt="Aggiungi campo">Aggiungi campo</button></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<input type="submit" class="submit_big" value="Salva">
	</form>
        <?
		} 

	include_once($root."/layout/bottom.php");
	?>
