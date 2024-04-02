<?
	if (!empty($edit) && empty($lock)) {
		$sequenza_anomalia = (!empty($record_lotto["sequenza_anomalia"])) ? $record_lotto["sequenza_anomalia"] : $record_gara["sequenza_anomalia"];
		if (empty($sequenza_anomalia)) {
			$array_update = array();

			if (!empty($record_lotto["codice"])) {
				$array_update["codice"] =	$record_lotto["codice"];
				$tabella =	"b_lotti";
			} else {
				$array_update["codice"] =	$record_gara["codice"];
				$tabella =	"b_gare";
			}
			if ($_POST["scelta_anomalia"] == "S" && strtotime($record_gara["data_scadenza"]) < time()) {
				$algoritmi = array();
				$algoritmi[] = "A";
				$algoritmi[] = "B";
				shuffle($algoritmi);
				$selezione = rand(0,1);
				$array_update["algoritmo_anomalia"] = $algoritmi[$selezione];
				$array_update["sequenza_anomalia"] = json_encode($algoritmi);
			} else {
				$array_update["algoritmo_anomalia"] = $_POST["scelta_anomalia"];
			}
			if ($array_update["algoritmo_anomalia"] == "C") {
				if (!empty($record_gara["coef_e"])){
					$coef_e = $record_gara["coef_e"];
				} else if (!empty($record_lotto["coef_e"])) {
					$coef_e = $record_lotto["coef_e"];
				} else {
					$coef_e = (!empty($_POST["percentile"])) ? $_POST["percentile"] : null;
					if (empty($coef_e)) {
						$error = "Scegliere un ribasso di riferimento";		
					}
				}
				$array_update["coef_e"] = $coef_e;
			} else {
				$array_update["coef_e"] = "";
			}
		} else {
			$error = "Algoritmo anomalia già scelto";
		}
	
	}
