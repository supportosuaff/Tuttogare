<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_tipologie");
		$id = $_POST["id"];
	}
	$colore = "#3C0";
	if ($record["attivo"] == "N") { $colore = "#C00"; }
?>
<div id="tipologia_<? echo $id ?>">
	<table width="100%">
		<tr>
<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
<td><input type="hidden" name="tipologia[<? echo $id ?>][codice]"id="codice_tipologia_<? echo $id ?>" value="<? echo $record["codice"] ?>">
<input type="text" class="titolo_edit" name="tipologia[<? echo $id ?>][tipologia]"  title="Tipologia" rel="S;3;255;A" id="tipologia_tipologia_<? echo $id ?>" value="<? echo $record["tipologia"] ?>">
</td>
<td width="10"><input type="image" onClick="aggiungi('tr_importo.php?id_tipologia=<?= $id ?>','#importi_<?= $id ?>');return false;" src="/img/add.png" title="Abilita/Disabilita"></td>
 <td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/tipologie');return false;" src="/img/switch.png" title="Abilita/Disabilita"></td>
 <td width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/tipologie');return false;" src="/img/del.png" title="Elimina"></td></tr>
</table>
	<div id="importi_<?= $id ?>">
		<?
			if ($record["codice"] != "") {
				$bind = array(":codice"=>$record["codice"]);
				$strsql = "SELECT * FROM b_tipologie_importi WHERE codice_tipologia = :codice";
				$ris_importi = $pdo->bindAndExec($strsql,$bind);
				if ($ris_importi->rowCount()>0) {
					while($importo = $ris_importi->fetch(PDO::FETCH_ASSOC)) {
						$id = $importo["codice"];
						include("tr_importo.php");
					}
				}
			}
		?>
	</div>
</div>
