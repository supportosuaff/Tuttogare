<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$ccnl = get_campi("b_ccnl");
		$id = $_POST["id"];
	}
?>
<tr id="ccnl_<? echo $id ?>"><td><input type="hidden" name="ccnl[<? echo $id ?>][codice]"id="codice_ccnl_<? echo $id ?>" value="<? echo $ccnl["codice"] ?>">
<input type="text" style="width:95%" name="ccnl[<? echo $id ?>][nome]"  title="<?= traduci("oggetto") ?>" rel="N;3;255;A" id="nome_ccnl_<? echo $id ?>" value="<? echo $ccnl["nome"] ?>">
</td><td width="10"><? if ($id!="i_0") { ?><input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','operatori_economici/ccnl');return false;"><? } ?></td></tr>
