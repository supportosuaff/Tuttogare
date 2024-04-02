<?
	if (!empty($edit) && empty(!$lock)) {
		if (empty($record_gara["sequenza_anomalia"]) && empty($record_lotto["sequenza_anomalia"])) {
			$array_update = array();

			if (!empty($record_lotto["codice"])) {
				$array_update["codice"] =	$record_lotto["codice"];
				$tabella =	"b_lotti";
			} else {
				$array_update["codice"] =	$record_gara["codice"];
				$tabella =	"b_gare";
			}
			if ($_POST["scelta_anomalia"] == "z") {
				$algoritmi = array();
				$algoritmi[] = "a";
				$algoritmi[] = "b";
				$algoritmi[] = "c";
				$algoritmi[] = "d";
				$algoritmi[] = "e";
				shuffle($algoritmi);
				$selezione = rand(0,4);
				$array_update["algoritmo_anomalia"] = $algoritmi[$selezione];
				$array_update["sequenza_anomalia"] = json_encode($algoritmi);
			} else {
				$array_update["algoritmo_anomalia"] = $_POST["scelta_anomalia"];
			}
			if ($array_update["algoritmo_anomalia"] == "e") {
				if (!empty($record_gara["coef_e"])){
					$coef_e = $record_gara["coef_e"];
				} else if (!empty($record_lotto["coef_e"])) {
					$coef_e = $record_lotto["coef_e"];
				} else {
					$coef_e = (!empty($_POST["coef_e"])) ? $_POST["coef_e"] : "z";
					if ($coef_e == "z") {
						if (strtotime($record_gara["data_pubblicazione"]) < strtotime('2017-05-20')) {
							$coefficenti = array("0.6","0.8","1","1.2","1.4");
							$selezione = rand(0,4);
						} else {
							$coefficenti = array("0.6","0.7","0.8","0.9");
							$selezione = rand(0,3);
						}
						shuffle($coefficenti);
						$array_update["coef_e"] = $coefficenti[$selezione];
						$array_update["sequenza_coef"] = json_encode($coefficenti);
					} else {
						$array_update["coef_e"] = $coef_e;
					}
				}
			} else {
				$array_update["coef_e"] = "";
			}
		} else {
			$msg = "Algoritmo anomalia già scelto";
		}
	
	}
