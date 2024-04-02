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
		<td>
			<input name="guue[OBJECT_CONTRACT][OBJECT_DESCR]<?= $chiave ?>[AC_PRICE]<?= $id ?>[AC_WEIGHTING]" value="<?= !empty($criterio) && !empty($criterio["AC_WEIGHTING"]) ? $criterio["AC_WEIGHTING"] : null  ?>" type="text" style="font-size: 1.3em" title="Ponderazione" rel="N;1;20;A">
		</td>
	</tr>
</table>
<?
$id = str_replace(array('[',']'), "",$id);
?>