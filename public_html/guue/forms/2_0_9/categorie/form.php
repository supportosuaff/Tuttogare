<h3 id="cpv_table"><b>Selezione delle Categorie Merceologiche</b></h3>
<input type="hidden" name="guue[supplementary_cpv]" id="cpv" value="<?= !empty($guue["supplementary_cpv"]) ? $guue["supplementary_cpv"] : null ?>" onchange="caricacpv($(this).val());maincpvexist($(this).val())" title="Categorie merceologiche" rel="N;1;0;A">
<input type="hidden" name="guue[main_cpv]" id="cpv_main" value="<?= !empty($guue["main_cpv"]) ? $guue["main_cpv"] : null ?>" title="Categorie merceologiche" rel="S;1;0;A">
<table class="bordered">
	<tr>
		<td colspan="2"><input type="button" class="submit" value="Scegli da lista" onClick="visualizza_cpv_disponibili();return false" style="width:100%; padding-top: 5px; padding-bottom: 5px;"></td>
	</tr>
	<tr>
		<td colspan="2">
			<div id="list_all" style="display:none">
			<?
				$sql_categorie = "SELECT * FROM b_cpv WHERE LENGTH(codice)=2 ORDER BY codice";
				$ris_categorie = $pdo->query($sql_categorie);
				if ($ris_categorie->rowCount()>0) {
					$lista = "all";
					foreach ($ris_categorie as $rec_categorie) {
						include $root . "/guue/forms/".$v_form."/categorie/categoria.php";
					}
				}
			?>
			</div>
			<div class="box">
				<div id="list_in" style="height:auto !important;">
					<?
					if(!empty($guue["supplementary_cpv"])) {
						$sql = "SELECT b_cpv.* FROM b_cpv WHERE codice IN ('".implode("','", array_filter(explode(";",$guue["supplementary_cpv"])))."')";
						$risultato_cpv = $pdo->bindAndExec($sql);
					}
					if (isset($risultato_cpv) && count($risultato_cpv)>0) {
					  foreach ($risultato_cpv as $rec_categorie) {
							$lista = "in";
							include $root . "/guue/forms/".$v_form."/categorie/categoria.php";
						}
					}
					?>
				</div>
			</div>
		</td>
	</tr>
</table>
<script>
	check_categorie();
</script>
