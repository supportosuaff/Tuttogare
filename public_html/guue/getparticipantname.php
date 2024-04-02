<?
	session_start();
	include '../../config.php';
	include_once $root . '/inc/funzioni.php';

	if(!empty($_POST["param"]["item"])) $item_name = $_POST["param"]["item"];
?>
<tr id="item_name_<?= $item_name ?>">
	<td>
		<input style="font-size: 1.4em" type="text" name="guue[PROCEDURE][PARTICIPANT_NAME][ITEM_<?= $item_name ?>]" value="<?= !empty($item_name_value) ? $item_name_value : null ?>" rel="S;1;150;A" title="Nome Partecipante Selezionato">
	</td>
	<td width="15">
		<input type="image" src="/img/no.png" onclick="$('#item_name_<?= $item_name ?>').slideUp().remove();return false;" width="16">
	</td>
</tr>
