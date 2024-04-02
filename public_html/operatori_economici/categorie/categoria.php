<?
	if (isset($_POST["codice"])) {
		session_start();
		include("../../../config.php");

		$bind = array(":codice"=>$_POST["codice"]);

		if ($_SESSION["language"] == "IT") {
			$s = "SELECT * FROM b_cpv WHERE codice = :codice";
		} else {
			$s = "SELECT b_cpv.*, b_cpv_dict.{$_SESSION["language"]} AS descrizione
												FROM b_cpv JOIN b_cpv_dict ON b_cpv.codice_completo = b_cpv_dict.codice_completo
												WHERE b_cpv.codice = :codice ";
		}
		$r = $pdo->bindAndExec($s,$bind);
		if ($r->rowCount()>0) {
			$rec_categorie = $r->fetch(PDO::FETCH_ASSOC);
			$lista = $_POST["lista"];
		}
	}
	if (isset($rec_categorie)) {
		$codice = $rec_categorie["codice"];
		$bind = array(":codice" => $codice."%", ":lunghezza" => strlen($codice) + 1);
		if ($_SESSION["language"] == "IT") {
			$s = "SELECT b_cpv.*
												FROM b_cpv
												WHERE LENGTH(codice)= :lunghezza
												AND codice like :codice ORDER BY codice";
		} else {
			$s = "SELECT b_cpv.*, b_cpv_dict.{$_SESSION["language"]} AS descrizione
												FROM b_cpv JOIN b_cpv_dict ON b_cpv.codice_completo = b_cpv_dict.codice_completo
												WHERE LENGTH(b_cpv.codice)= :lunghezza
												AND b_cpv.codice like :codice ORDER BY b_cpv.codice";
		}
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
            	<td width="15"><? if ($espandi) { ?><button type="button" onClick="carica_categorie('<? echo $rec_categorie["codice"] ?>','<? echo $lista ?>','categorie/albero.php');return false;"><img width="10" id="espandi_<? echo $rec_categorie["codice"] ?>" src="/img/espandi.png" alt="Espandi/Contrati"></button><? } ?></td>
                <td width="50"><? echo $rec_categorie["codice"] ?></td>
                <td><? echo $rec_categorie["descrizione"] ?></td>
                <? if (!isset($albero) || $lista == "all") { ?><td width="15"><input type="image" src="/img/<? echo $image ?>"  onClick="categoria('<? echo $rec_categorie["codice"] ?>','<? echo $lista ?>','categorie/categoria.php');return false;" width="16"></td><? } ?>
             </tr>
        </table>
            <div id="children_<? echo $rec_categorie["codice"]?>" class="children"></div>
        </div>
<? } ?>
