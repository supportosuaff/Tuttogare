<?
	include_once("../../config.php");
	$error_permessi = true;
	if (!empty($_SESSION["codice_commissario"]) && !empty($_GET["codice"]) && !empty($_GET["partecipante"]) && !empty($_GET["busta"]))
	{
		if ($_GET["busta"] == "tecnica" || $_GET["busta"] == "economica" || $_GET["busta"] == "amministrativa") {
			if (checkCommissario($_GET["codice"])) {
				$codice_gara = $_GET["codice"];
				$sql = "SELECT b_allegati.*
								FROM b_allegati
								JOIN b_buste ON b_allegati.codice = b_buste.codice_allegato
								JOIN b_criteri_buste ON b_buste.codice_busta = b_criteri_buste.codice
								WHERE b_buste.codice_partecipante = :codice_partecipante
								AND b_buste.codice_gara = :codice_gara AND ";
				if ($_GET["busta"] == "tecnica") {
					$sql .= " b_criteri_buste.tecnica = 'S' ";
				} else if ($_GET["busta"] == "tecnica") {
					$sql .= " b_criteri_buste.economica = 'S' ";
				} 
				$ris_allegato = $pdo->bindAndExec($sql,[":codice_gara"=>$codice_gara,":codice_partecipante"=>$_GET["partecipante"]]);
				if ($ris_allegato->rowCount() == 1) {
					ini_set('memory_limit', '1536M');
					ini_set('max_execution_time', 600);
					$record_allegato = $ris_allegato->fetch(PDO::FETCH_ASSOC);
					$type = getTypeAndExtension($config["arch_folder"] . "/" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["riferimento"]);
					if ($type != false) {
						$error_permessi = false;
						header('Content-Description: File Transfer');
						header('Content-Type: ' . $type["type"]);
						if (!empty($record_allegato["titolo"])) {
							header('Content-Disposition: attachment; filename=' . str_replace(" ","_",$record_allegato["titolo"] . $type["ext"]));
						} else {
							header('Content-Disposition: attachment; filename=' . str_replace(" ","_",$record_allegato["nomefile"]));
						}
						header('Content-Transfer-Encoding: binary');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Pragma: public');
						readfile($config["arch_folder"] . "/" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["riferimento"]);
					}
				}
			}
		}
	}
	if ($error_permessi) {
		?>
		<h1>Impossibile accedere: Non si dispone dei permessi necessari o la gara non è in uno stato compatibile</h1>
		<?
	}
	?>
