<?
	if (isset($_POST["codice"])) {
		include("../../../config.php");
		$bind = array(":codice"=>$_POST["codice"]);
		$s = "SELECT * FROM b_cpv WHERE codice = :codice";
		$r = $pdo->bindAndExec($s,$bind);
		if ($r->rowCount()>0) {
			$rec_categorie = $r->fetch(PDO::FETCH_ASSOC);
			$lista = $_POST["lista"];
		}
	}
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
            <tr class="categoria_cpv">
            	<td width="15"><? if ($espandi) { ?><button class="espandi" onClick="carica_categorie('<? echo $rec_categorie["codice"] ?>','<? echo $lista ?>','categorie/albero.php');return false;"><img width="10" id="espandi_<? echo $rec_categorie["codice"] ?>" src="/img/espandi.png" alt="Espandi/Contrati"></button><? } ?></td>
                <td width="50" class="codice_cpv"><? echo $rec_categorie["codice"] ?></td>
                <td class="descrizione_cpv"><? echo $rec_categorie["descrizione"] ?></td>
                <? if ((!isset($albero) || $lista == "all") && (!isset($lock) || !$lock)) { ?><td width="15"><input type="image" src="/img/<? echo $image ?>"  onClick="categoria('<? echo $rec_categorie["codice"] ?>','<? echo $lista ?>','categorie/categoria.php');return false;" width="16"></td><? } ?>
             </tr>
        </table>
            <div id="children_<? echo $rec_categorie["codice"]?>" class="children"></div>
        </div>
<? } ?>
