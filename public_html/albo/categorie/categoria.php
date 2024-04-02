<?
	if (isset($rec_categorie)) {
		$codice = $rec_categorie["codice"];
		$bind = array(":codice" => $codice."%", ":lunghezza" => strlen($codice) + 1);
		$s = "SELECT * FROM b_cpv WHERE LENGTH(codice)= :lunghezza AND codice like :codice ORDER BY codice";
		$r = $pdo->bindAndExec($s,$bind);
		$espandi = false;
		if ($r->rowCount()>0) {
			$espandi = true;
		}
?>        <div id="in_<? echo $rec_categorie["codice"] ?>" class="categoria">
        <table width="100%">
            <tr>
            	<td width="15"><? if ($espandi) { ?><button onClick="carica_categorie('<? echo $rec_categorie["codice"] ?>','in','categorie/albero.php');return false;"><img width="10" id="espandi_<? echo $rec_categorie["codice"] ?>" src="/img/espandi.png" alt="Espandi/Contrati"></button><? } ?></td>
                <td width="50"><? echo $rec_categorie["codice"] ?></td>
                <td><? echo $rec_categorie["descrizione"] ?></td>
             </tr>
        </table>
            <div id="children_<? echo $rec_categorie["codice"]?>" class="children"></div>
        </div>
<? } ?>
