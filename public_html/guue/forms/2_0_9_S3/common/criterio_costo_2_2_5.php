<?
$chiave = "";
if(!empty($_POST["chiavi"]))
{
	foreach ($_POST["chiavi"] as $key) {
		$chiave .= '['.$key.']';
	}
} elseif (!empty($item)) {
	$chiave = "[ITEM_".$item."]";
}
$id = empty($id) ? "" : "[".$id."]";
if(!empty($_POST["data"])) {
	$id = '['.$_POST["data"]["id"].']';
} 
?>
<table style="width: 100%">
	<tr class="noBorder">
		<td style="width: 80%">
			<input name="guue[OBJECT_CONTRACT][OBJECT_DESCR]<?= $chiave ?>[AC][AC_COST]<?= $id ?>[AC_CRITERION]" value="<?= !empty($criterio) && !empty($criterio["AC_CRITERION"]) ? $criterio["AC_CRITERION"] : null  ?>" type="text" style="font-size: 1.3em" title="Nome" rel="S;1;200;A">
		</td>
		<td style="width: 20%">
			<input name="guue[OBJECT_CONTRACT][OBJECT_DESCR]<?= $chiave ?>[AC][AC_COST]<?= $id ?>[AC_WEIGHTING]" value="<?= !empty($criterio) && !empty($criterio["AC_WEIGHTING"]) ? $criterio["AC_WEIGHTING"] : null  ?>" type="text" style="font-size: 1.3em" title="Ponderazione" rel="S;1;20;A">
		</td>
	</tr>
</table>
<?
$id = str_replace(array('[',']'), "",$id);
?>