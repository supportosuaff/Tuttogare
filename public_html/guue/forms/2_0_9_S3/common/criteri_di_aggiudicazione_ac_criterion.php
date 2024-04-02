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
<table class="bordered" id="criterio_<?= str_replace(array('[',']'), '', $id) ?>">
	<tr>
		<td class="etichetta" style="width: 20px !important;">
			<label style="font-size: 1em;">Criterio:</label>
		</td>
		<td>
			<input name="guue[OBJECT_CONTRACT][OBJECT_DESCR]<?= $chiave ?>[AC][AC_CRITERION]<?= $id ?>" value="<?= !empty($criterio) && !empty($criterio) ? $criterio : null  ?>" type="text" style="font-size: 1.3em" title="Nome" rel="S;1;200;A">
		</td>
		<td style="width: 20px !important;">
			<input type="image" src="/img/del.png" onClick="elimina('criterio_<?= str_replace(array('[',']'), '', $id) ?>','guue/forms/2_0_9_S3/common/criterio');return false;">
		</td>
	</tr>
</table>