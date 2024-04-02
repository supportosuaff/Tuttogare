<?
	if (isset($soa_fatt)) {
?>
<tr><td>
<table width="100%">
	  <tr>
			<td class="etichetta">Categoria</td>
			<td colspan="9"><strong><?= $soa_fatt["descrizione"] ?></strong></td>
		<tr>
			<tr>
				<td class="etichetta" colspan="10">Fatturato</td>
			</tr>
			<tr>
			<?
				$anno_corrente = (int)date("Y");
				for($i = ($anno_corrente-5);$i<$anno_corrente;$i++){
					$fatturato = 0;
					$sql = "SELECT * FROM b_fatturato_soa WHERE codice_attestazione = :codice_attestazione AND anno = :anno ";
					$ris_fatturato = $pdo->bindAndExec($sql,array(":codice_attestazione"=>$soa_fatt["codice"],":anno"=>$i));
					if ($ris_fatturato->rowCount() === 1) {
						$fatturato = $ris_fatturato->fetch(PDO::FETCH_ASSOC)["fatturato"];
					}
					?>
					<td class="etichetta"><?= $i ?></td>
					<td>
						&euro; <?= number_format($fatturato,2,",",".") ?>
					</td>
					<?
				}
			?>
			</tr>
</table>
</td></tr>
<? } ?>
