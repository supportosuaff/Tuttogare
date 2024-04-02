<input type="hidden" name="cpv" id="cpv" value="<? echo $string_cpv ?>" title="Categorie merceologiche" rel="S;1;0;A">
<? if (isset($lock) && !$lock) { ?>
<input type="text" class="cerca_cpv" rel="all" url="categorie/categoria.php" title="Cerca..." style="width:70%">
<input type="button" class="submit" value="Scegli da lista" onClick="visualizza_cpv_disponibili();return false" style="width:26%"><br>
<? } ?>
<div id="list_all" style="display:none">
<?
			$sql_categorie = "SELECT * FROM b_cpv WHERE LENGTH(codice)=2 ORDER BY codice";
			$ris_categorie = $pdo->query($sql_categorie);
			if ($ris_categorie->rowCount()>0) {
				$lista = "all";
				while($rec_categorie=$ris_categorie->fetch(PDO::FETCH_ASSOC)) {
					include("categorie/categoria.php");
				}
			}
?>
</div>
<div class="box">
	<div id="list_in" style="height:auto !important;">
		<?
			if (isset($risultato_cpv) && count($risultato_cpv)>0) {
				foreach ($risultato_cpv as $rec_categorie) {
					$lista = "in";
					include("categorie/categoria.php");
				}
			}
		?>
  </div>
</div>
<script>
	check_categorie();
</script>
