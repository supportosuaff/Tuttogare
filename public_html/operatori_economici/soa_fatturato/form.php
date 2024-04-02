<?
	if (isset($_POST["id"])) {
		session_start();
		include_once("../../../config.php");
		include_once($root."/inc/funzioni.php");
		$soa_fatt = get_campi("b_certificazioni_soa");
		$id = $_POST["id"];
	}
?>
<tr id="soa_<? echo $id ?>"><td>
<input type="hidden" name="soa[<? echo $id ?>][codice]"id="codice_soa_<? echo $id ?>" value="<? echo $soa_fatt["codice"] ?>">
<input type="hidden" name="soa[<? echo $id ?>][id]"id="codice_soa_<? echo $id ?>" value="<? echo $id ?>">
<table width="100%">
	  <tr>
			<td class="etichetta"><?= traduci("Categoria") ?>*</td>
			<td colspan="9">
			<select rel="S;0;0;N" title="<?= traduci("Categoria") ?> SOA" name="soa[<? echo $id ?>][codice_categoria]" id="codice_categoria_soa_<? echo $id ?>">
				<option value=""><?= traduci("Seleziona") ?>...</option>
				<?
					$sql_soa = "SELECT * FROM b_categorie_soa WHERE attivo = 'S' ORDER BY codice";
					$ris_elenco_soa = $pdo->query($sql_soa);
					if ($ris_elenco_soa->rowCount()>0) {
						while($oggetto_soa = $ris_elenco_soa->fetch(PDO::FETCH_ASSOC)) {
							?>
							<option value="<? echo $oggetto_soa["codice"] ?>"><?= $oggetto_soa["descrizione"] ?></option>
							<?
						}
					}
				?>
			</select>
			<script>
				$("#codice_categoria_soa_<? echo $id ?>").val('<? echo $soa_fatt["codice_categoria"] ?>');
			</script>
			<input type="hidden" name="soa[<? echo $id ?>][codice_classifica]" id="codice_classifica_soa_<? echo $id ?>" value="0">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="10"><?= traduci("Fatturato") ?></td>
	</tr>
	<tr>
	<?
		$anno_corrente = (int)date("Y");
		for($i = ($anno_corrente-5);$i<$anno_corrente;$i++){
			$fatturato = "";
			if (is_numeric($soa_fatt["codice"])){
				$sql = "SELECT * FROM b_fatturato_soa WHERE codice_attestazione = :codice_attestazione AND anno = :anno ";
				$ris_fatturato = $pdo->bindAndExec($sql,array(":codice_attestazione"=>$soa_fatt["codice"],":anno"=>$i));
				if ($ris_fatturato->rowCount() === 1) {
					$fatturato = $ris_fatturato->fetch(PDO::FETCH_ASSOC)["fatturato"];
				}
			}
			?>

				<td class="etichetta"><?= $i ?></td>
				<td>
					&euro; <input type="text" name="soa[<? echo $id ?>][fatturato][<?= $i ?>][fatturato]" id="fatturato_<?= $i ?>_soa_<? echo $id ?>" title="Fatturato <?= $i ?>" rel="S;0;0;2D;0;>=" value="<?= $fatturato ?>">
				</td>
			<?
		}
	?>
	</tr>
</table>
</td><td width="10"><input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','operatori_economici/soa_fatturato');return false;"></td></tr>
