<?
	if (isset($_POST["codice"])) {
		@session_start();
		if(empty($pdo)) include '../../../../../config.php';

		$bind = array(":codice"=>$_POST["codice"]);
		$s = "SELECT * FROM b_cpv WHERE codice = :codice";
		$r = $pdo->bindAndExec($s,$bind);
		if ($r->rowCount()>0) {
			$rec_categorie = $r->fetch(PDO::FETCH_ASSOC);
			$lista = $_POST["lista"];
		}
	}
	if(empty($v_form)) $v_form = !empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : "2_0_9";
	if (isset($rec_categorie)) {
		$codice = $rec_categorie["codice"];
		$bind = array(":codice" => $codice."%", ":lunghezza" => strlen($codice) + 1);
		$s = "SELECT * FROM b_cpv WHERE LENGTH(codice)= :lunghezza AND codice like :codice ORDER BY codice";
		$r = $pdo->bindAndExec($s,$bind);
		$espandi = false;
		$image = "add.png";
		if ($lista == "in") {
			$image = "no.png";
		}
		if ($r->rowCount()>0) {
			$espandi = true;
		}
		?>
		<div id="<? echo $lista ?>_<? echo $rec_categorie["codice"] ?>" class="categoria">
			<table width="100%">
				<tr>
					<td width="15"><? if ($espandi) { ?><button onClick="carica_categorie('<? echo $rec_categorie["codice"] ?>','<? echo $lista ?>','forms/<?= $v_form ?>/categorie/albero.php');return false;"><img width="10" id="espandi_<? echo $rec_categorie["codice"] ?>" src="/img/espandi.png" alt="Espandi/Contrati"></button><? } ?></td>
					<td width="50"><? echo $rec_categorie["codice"] ?></td>
					<td><? echo $rec_categorie["descrizione"] ?></td>
					<? if (!isset($albero) || $lista == "all") { ?><? if ($lista == "in") { ?><td width="250"><label><input <?= (!empty($guue["main_cpv"]) && $guue["main_cpv"] == $rec_categorie["codice"]) ? 'checked="checked"' : null ?> type="radio" name="guue_main_cpv" onchange="set_main_cpv('<?= $rec_categorie["codice"] ?>')"> Imposta come principale</label></td><? } ?><td width="15"><input type="image" src="/img/<? echo $image ?>"  onClick="categoria('<? echo $rec_categorie["codice"] ?>','<? echo $lista ?>','forms/<?= $v_form ?>/categorie/categoria.php');return false;" width="16"></td><? } ?>
				</tr>
			</table>
			<div id="children_<? echo $rec_categorie["codice"]?>" class="children"></div>
		</div>
		<? 
	} 
?>
