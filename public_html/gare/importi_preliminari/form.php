<?
	if (isset($_POST["codice_tipologia"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$bind=array();
		$bind[":codice_tipologia"] = $_POST["codice_tipologia"];
		$sql = "SELECT * FROM b_tipologie_importi WHERE codice_tipologia = :codice_tipologia";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount()>0) {
			$importi = array();
			while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
				$importi[$rec["codice"]] = get_campi("b_importi_gara");
				$importi[$rec["codice"]]["titolo"] = $rec["titolo"];
				$importi[$rec["codice"]]["codice_tipologia"] = $rec["codice"];
			}
		}
	}
	if (isset($importi)) {
		foreach($importi as $importo) {
			if ($importo["codice_tipologia"]!="") {
			?>
				<tr class="tr_importo" id="importo_<? echo $importo["codice_tipologia"] ?>">
					<td class="etichetta"><? echo $importo["titolo"] ?> (soggetti a ribasso)
						<input class="espandi" name="importi[<? echo $importo["codice_tipologia"] ?>][codice_tipologia]" type="hidden" id="codice_tipologia_<?= $importo["codice_tipologia"] ?>" value="<? echo $importo["codice_tipologia"] ?>" rel="S;1;0;N" >
					</td>
					<td>
						<input onChange="update_totale()" class="importi espandi" name="importi[<? echo $importo["codice_tipologia"] ?>][importo_base]" id="base_<?= $importo["codice_tipologia"] ?>" title="Importo base" value="<? echo $importo["importo_base"] ?>" rel="S;1;0;N" ><br>
					</td>
					<td>
						<input class="importi espandi" onChange="update_totale()" name="importi[<? echo $importo["codice_tipologia"] ?>][importo_personale]" id="personale_<?= $importo["codice_tipologia"] ?>" title="Costo della manodopera" value="<? echo $importo["importo_personale"] ?>" rel="S;1;0;N">
					</td>
					<td>
						<input onChange="update_totale()" class="importi espandi" name="importi[<? echo $importo["codice_tipologia"] ?>][importo_oneri_no_ribasso]" id="oneri_no_ribasso_<?= $importo["codice_tipologia"] ?>" title="Costi sicurezza non sogetti a ribasso" value="<? echo $importo["importo_oneri_no_ribasso"] ?>" rel="S;1;0;N">
					</td>
				</tr>
		<?
		}
	}
}
	?>
