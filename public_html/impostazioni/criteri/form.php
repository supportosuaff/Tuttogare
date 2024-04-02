<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_criteri");
		$id = $_POST["id"];
		$new = true;
	}

	$colore = "#3C0";
	if ($record["attivo"] == "N") { $colore = "#C00"; }
?>
<tr id="criterio_<? echo $id ?>">
<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
<td>
<input type="hidden" name="criterio[<? echo $id ?>][id]" id="id_criterio_<? echo $id ?>" value="<? echo $id ?>">
<input type="hidden" name="criterio[<? echo $id ?>][codice]"id="codice_criterio_<? echo $id ?>" value="<? echo $record["codice"] ?>">
<table width="100%">
<tr><td class="etichetta">Titolo</td><td>
<input type="text" class="titolo_edit" name="criterio[<? echo $id ?>][criterio]"  title="Criterio" rel="S;3;255;A" id="criterio_criterio_<? echo $id ?>" value="<? echo $record["criterio"] ?>"></td></tr>
<tr><td class="etichetta">Riferimento normativo</td><td>
<input type="text" style="width:98%" name="criterio[<? echo $id ?>][riferimento_normativo]"  title="Riferimento normativo" rel="S;3;255;A" id="riferimento_normativo_criterio_<? echo $id ?>" value="<? echo $record["riferimento_normativo"] ?>"></td></tr>
<tr><td class="etichetta">Directory</td><td>
<input type="text" name="criterio[<? echo $id ?>][directory]"  title="Directory elaborazione" rel="S;3;255;A" id="directory_criterio_<? echo $id ?>" value="<? echo $record["directory"] ?>">
</td></tr>
<tr><td class="etichetta">Opzioni necessarie</td><td>
	<select class="select_opzione" name="criterio[<?= $id ?>][opzioni][]" multiple title="Opzione" rel="N;0;0;A" id="opzioni_criterio_<?= $id ?>">
		<?
		$sql = "SELECT * FROM b_gruppi_opzioni WHERE  b_gruppi_opzioni.eliminato = 'N' ORDER BY b_gruppi_opzioni.titolo";
		$ris_opzioni = $pdo->query($sql);
		if ($ris_opzioni->rowCount()>0) {
			while($opzione = $ris_opzioni->fetch(PDO::FETCH_ASSOC)) {
					?>
					<option value="<?= $opzione["codice"] ?>"><?= $opzione["titolo"] ?></option>
					<?
				}
		}
		?>
	</select>
	<script>
		codici_opzione = '<? echo $record["opzioni"] ?>';
		$("#opzioni_criterio_<?= $id ?>").val(codici_opzione.split(','));
	</script>
</td></tr>
<tr><td class="etichetta">Moduli avanzati necessari</td><td>
	<select class="select_opzione" name="criterio[<?= $id ?>][script][]" multiple title="Moduli" rel="N;0;0;A" id="script_criterio_<?= $id ?>">
		<?
		$scripts = scandir($root."/gare/elaborazione/moduli_avanzati");
		foreach ($scripts AS $directory) {
			if ($directory != "." && $directory != ".." && file_exists($root."/gare/elaborazione/moduli_avanzati/".$directory."/form.php")) {
					?>
					<option><?= $directory ?></option>
					<?
				}
		}
		?>
	</select>
	<script>
		scripts = '<? echo $record["script"] ?>';
		$("#script_criterio_<?= $id ?>").val(scripts.split(','));
	</script>
</td></tr>
</table>

<div style="float:left; width:49%">
<table width="100%">
<thead>
<tr><td></td><td>Punteggio</td><td width="10">Economico</td><td width="10">Migliorativa</td><td width="10">Temporale</td><td width="10">Elimina</td></tr>
</thead>
<tbody id="punteggi_<? echo $id ?>" class="sortable">
<?
	if (!isset($new)) {
		$bind=array(":codice"=>$record["codice"]);
		$sql = "SELECT * FROM b_criteri_punteggi WHERE eliminato = 'N' AND codice_criterio = :codice ORDER BY ordinamento";
		$ris_punteggi = $pdo->bindAndExec($sql,$bind);
		if ($ris_punteggi->rowCount()>0) {
			while ($record_punteggio = $ris_punteggi->fetch(PDO::FETCH_ASSOC)) {
				$id_criterio = $id;
				$id_punteggio = $record_punteggio["codice"];
				include("tr_punteggi.php");
			}
		} else {
			$record_punteggio = get_campi("b_criteri_punteggi");
			$id_criterio = $id;
			$id_punteggio = "i_".rand();
			include("tr_punteggi.php");
		}
	} else {
		$record_punteggio = get_campi("b_criteri_punteggi");
		$id_criterio = $id;
		$id_punteggio = "i_".rand();
		include("tr_punteggi.php");
	}
?>
</tbody>
<tfoot>
<tr><td colspan="6">
  <button class="aggiungi" onClick="aggiungi('tr_punteggi.php?id_criterio=<? echo $id ?>','#punteggi_<? echo $id ?>');return false;"><img src="/img/add.png" alt="Aggiungi punteggio">Aggiungi punteggio</button></td></tr>
</tfoot>
</table>
</div>
<div style="float:right; width:49%">
<table width="100%">
<thead>
	<tr><td colspan="2" class="etichetta">Buste</td><td>Tecnica</td><td>Economica</td><td>Mercato Elettronico</td><td>II Fase</td><td>Elimina</tr>
</thead>
<tbody id="buste_<? echo $id ?>" class="sortable">
<?
	if (!isset($new)) {
		$bind=array(":codice"=>$record["codice"]);
		$sql = "SELECT * FROM b_criteri_buste WHERE eliminato = 'N' AND codice_criterio = :codice ORDER BY ordinamento";
		$ris_buste = $pdo->bindAndExec($sql,$bind);
		if ($ris_buste->rowCount()>0) {
			while ($record_busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
				$id_criterio = $id;
				$id_busta = $record_busta["codice"];
				include("tr_buste.php");
			}
		} else {
			$record_busta = get_campi("b_criteri_buste");
			$id_criterio = $id;
			$id_busta = "i_".rand();
			include("tr_buste.php");
		}
	} else {
		$record_busta = get_campi("b_criteri_buste");
		$id_criterio = $id;
		$id_busta = "i_".rand();
		include("tr_buste.php");
	}
?>
</tbody>
<tfoot>
<tr><td colspan="7">
  <button class="aggiungi" onClick="aggiungi('tr_buste.php?id_criterio=<? echo $id ?>','#buste_<? echo $id ?>');return false;"><img src="/img/add.png" alt="Aggiungi busta">Aggiungi busta</button></td></tr>
</tfoot>
</table>
</div>
</td>

 <td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/criteri');return false;" src="/img/switch.png" title="Abilita/Disabilita"></td>
 <td width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/criteri');return false;" src="/img/del.png" title="Elimina"></td></tr>
