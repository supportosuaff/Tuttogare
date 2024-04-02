<?
	session_start();
	include_once('../../../config.php');
	include_once($root.'/inc/funzioni.php');
  include_once($root."/inc/oeManager.class.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("gare/elaborazione",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
	if ($edit) {

			$oeManager = new oeManager();
			foreach($_POST["oeManager"] AS $input) {
				$key = $input["name"];
				$key = str_replace("oeManager[", "", $key);
				$key = str_replace("]", "", $key);
				$value = $input["value"];
				if (property_exists("oeManager",$key)) $oeManager->$key = $value;
				if ($key == "classifica_only_selected") $oeManager->$key = false;
				if ($key == "elenco" && !empty($value)) {
					$value = explode("-",$value);
					$oeManager->tipo_elenco = $value[0];
					$oeManager->codice_elenco = $value[1];
				}
			}

			$oeManager->showInviti = true;
			$oeManager->showDataRichiesta = true;
			$oeManager->showDataAbilitazione = true;

			$order = array();

			$sort_array_key = "";
			$sort_array_dir = "";

			switch($_POST["order"][0]["column"]) {
				case "0":
					$order["id_interno"] = strtoupper($_POST["order"][0]["dir"]);
					$order["codice"] = strtoupper($_POST["order"][0]["dir"]);
					break;
				case "1":
					$order["ragione_sociale"] = strtoupper($_POST["order"][0]["dir"]);
					break;
				case "2":
					$order["tipo"] = strtoupper($_POST["order"][0]["dir"]);
					break;
				case "3":
					$order["codice_fiscale_impresa"] = strtoupper($_POST["order"][0]["dir"]);
					break;
				case "4":
					if (!empty($oeManager->codice_elenco)) {
						$order["timestamp_richiesta"] = strtoupper($_POST["order"][0]["dir"]);
					}
					break;
				case "5":
					if (!empty($oeManager->codice_elenco)) {
						$order["timestamp_abilitazione"] = strtoupper($_POST["order"][0]["dir"]);
					}
					break;
				case "6":
					$order["inviti"] = strtoupper($_POST["order"][0]["dir"]);
					if (!empty($oeManager->codice_elenco)) {
						$order["timestamp_richiesta"] = "ASC";
					}
					$order["ragione_sociale"] = "ASC";
					break;
				case "7":
					$order["inviti_anno"] = strtoupper($_POST["order"][0]["dir"]);
					if (!empty($oeManager->codice_elenco)) {
						$order["timestamp_richiesta"] = "ASC";
					}
					$order["ragione_sociale"] = "ASC";
					break;
				case "8":
					$order["feedback"] = strtoupper($_POST["order"][0]["dir"]);
					$order["ragione_sociale"] = "ASC";
					break;
					
			}

			if (count($order) > 0) {
				$oeManager->order = $order;
			}

      $alloperatori = $oeManager->getList();

			$totale = 0;
			$iTotal = 0;
			$iFilteredTotal = 0;
			if (count($alloperatori) && $alloperatori !== false) {
	      $totale = count($alloperatori);
	      $start = 0;
	      $lenght = $totale;
	  		if ( isset( $_POST['start'] ) && $_POST['length'] != '-1' && is_numeric( $_POST['start'] ) && is_numeric( $_POST['length'] ))
	  		{
	  			$start = (int)$_POST['start'];
	        $lenght = (int)$_POST['length'];
	  		}
				$rResultNoFilterTotal = $totale;
	    	$iTotal = $totale;

				if (!empty($_POST["search"]["value"])) {
					$search = $_POST["search"]["value"];
					$operatori = array();
					foreach($alloperatori AS $operatore) {
						foreach($operatore AS $value) {
							if (stripos($value, $search) !== false) {
								$operatori[] = $operatore;
								break;
							}
						}
					}
				} else {
					$operatori = $alloperatori;
				}
				$iFilteredTotal = count($operatori);
			}

			$output = array(
				"sEcho" => intval($_POST['draw']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
      if ($totale > 0) {

        for ($i=$start;$i<($start+$lenght);$i++) {
          if (!empty($operatori[$i])) {

						$operatore = $operatori[$i];

						$bind=array();
						$bind[":codice_utente"] = $operatore["codice"];
						$bind[":codice_gara"] = $_POST["codice_gara"];
						$sql = "SELECT * FROM r_inviti_gare WHERE codice_utente = :codice_utente AND codice_gara = :codice_gara";
						$ris_inviti  = $pdo->bindAndExec($sql,$bind);
						$classe = "";
						$invitato = false;
						if ($ris_inviti->rowCount()>0) $invitato = true;

    				$columns = array();
						$columns[] = (!empty($operatore["id_interno"])) ? $operatore["id_interno"] : "[" . $operatore["codice"] . "]";
            $columns[] = $operatore["ragione_sociale"];
            $columns[] = $operatore["tipo"];
            $columns[] = $operatore["codice_fiscale_impresa"];
						$columns[] = (!empty($operatore["timestamp_richiesta"])) ? "<div style='text-align:center'>" . mysql2datetime($operatore["timestamp_richiesta"]) . "</div>" : "";
						$columns[] = (!empty($operatore["timestamp_abilitazione"])) ? "<div style='text-align:center'>" . mysql2datetime($operatore["timestamp_abilitazione"]) . "</div>" : "";
						$columns[] = (isset($operatore["inviti"])) ? "<div style='text-align:center'>" . $operatore["inviti"] . "</div>" : "";
						$columns[] = (isset($operatore["inviti_anno"])) ? "<div style='text-align:center'>" . $operatore["inviti_anno"] . "</div>" : "";
						if (!empty($operatore["feedback"])) {
							$pnt = $operatore["feedback"];
							$columns[] = "<img src=\"/img/". number_format($pnt,0) .".png\" alt=\"Ranking\" height=\"12\" valign=\"middle\" style=\"margin-top:-3px\"> <b>" . number_format($pnt, 1) . "</b>";
						} else {
							$columns[] = "";
						}
						if (!$invitato) {
							$columns[] = "<div style='text-align:center'><button id=\"invia_" . $operatore["codice"] . "\" class=\"invita btn btn-warning\" onClick='invitato(\"" . $operatore["codice"] . "\");return false;'> <span class='fa fa-plus'></span> Invita</div>";
						} else {
							$columns[] = "<div style='text-align:center'>Gi&agrave; invitato</div>";
						}
    				$output["aaData"][] = $columns;
          } else {
            break;
          }
  			}
		  }
  		echo json_encode( $output );
	}

?>
