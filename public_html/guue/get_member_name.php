<?
	@session_start();
	if(empty($root)) {
		include '../../config.php';
		include_once $root . '/inc/funzioni.php';
	}
	if(!empty($_POST["param"]["item"])) $item_member_name = $_POST["param"]["item"];
?>
<tr id="item_member_name_<?= $item_member_name ?>">
	<td>
		<input style="font-size: 1.4em" type="text" name="guue[PROCEDURE][MEMBER_NAME][ITEM_<?= $item_member_name ?>]" value="<?= !empty($item_member_name_value) ? $item_member_name_value : null ?>" rel="S;1;150;A" title="Nome membro della giuria">
	</td>
	<td width="15">
		<input type="image" src="/img/no.png" onclick="$('#item_member_name_<?= $item_member_name ?>').slideUp().remove();return false;" width="16">
	</td>
</tr>
