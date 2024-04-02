<?
	session_start();
	include_once('../../config.php');
	include_once($root.'/inc/funzioni.php');
  include_once($root."/inc/oeManager.class.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("albo",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
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
					if ($value[1] == "0") $value[1] = null;
					$oeManager->tipo_elenco = $value[0];
					$oeManager->codice_elenco = $value[1];
				}
			}

			$order = array();
			switch($_POST["order"][0]["column"]) {
				case "0":
					$order["id_interno"] = strtoupper($_POST["order"][0]["dir"]);
					$order["codice"] = strtoupper($_POST["order"][0]["dir"]);
					break;
				case "2":
					$order["ragione_sociale"] = strtoupper($_POST["order"][0]["dir"]);
					break;
				case "3":
					$order["cognome"] = strtoupper($_POST["order"][0]["dir"]);
					$order["nome"] = "ASC";
					break;
				case "4":
					$order["tipo"] = strtoupper($_POST["order"][0]["dir"]);
					break;
				case "4":
					$order["partita_iva"] = strtoupper($_POST["order"][0]["dir"]);
					break;
				case "5":
						$order["codice_fiscale_impresa"] = strtoupper($_POST["order"][0]["dir"]);
						break;
				case "6":
					$order["timestamp_iscrizione"] = strtoupper($_POST["order"][0]["dir"]);
					break;
				case "8":
					if (!empty($oeManager->codice_elenco)) {
						$order["timestamp_richiesta"] = strtoupper($_POST["order"][0]["dir"]);
					}
					break;
				case "9":
						$order["inviti"] = strtoupper($_POST["order"][0]["dir"]);
					if (!empty($oeManager->codice_elenco)) {
						$order["timestamp_richiesta"] = "ASC";
					}
					$order["ragione_sociale"] = "ASC";
					break;
				case "10":
					$order["inviti_anno"] = strtoupper($_POST["order"][0]["dir"]);
					if (!empty($oeManager->codice_elenco)) {
						$order["timestamp_richiesta"] = "ASC";
					}
					$order["ragione_sociale"] = "ASC";
					break;
				case "11":
					$order["feedback"] = strtoupper($_POST["order"][0]["dir"]);
					break;
			}

			if (count($order) > 0) {
				$oeManager->order = $order;
			}
			$oeManager->showInviti = true;
			$oeManager->showDataRichiesta = true;
			$search = null;
			if (!empty($_POST["search"]["value"])) $search = $_POST["search"]["value"];
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


				$sql_albi = "SELECT r_partecipanti_albo.* FROM r_partecipanti_albo
										 JOIN b_bandi_albo ON r_partecipanti_albo.codice_bando = b_bandi_albo.codice
										 WHERE b_bandi_albo.codice_gestore = :codice_ente
										 AND r_partecipanti_albo.codice_operatore = :codice_operatore
										 AND r_partecipanti_albo.ammesso = 'S' ";
				$checks = [];
				$sth_albi = $pdo->prepare($sql_albi);
				$provider = getArt80Provider();
				if (!empty($provider)) {
					for ($i=$start;$i<($start+$lenght);$i++) {
						if (!empty($operatori[$i])) {
							$checks[] = $operatori[$i]["codice_fiscale_impresa"];
						}
					}
					$checks = checkStatoArt80($checks);
				}
				for ($i=$start;$i<($start+$lenght);$i++) {
          if (!empty($operatori[$i])) {
						$operatore = $operatori[$i];
    				$columns = array();
						ob_start();
						?>
						<a href="#" onclick="$('#modalIdInternoContent').load('editIdInterno.php?codice_operatore=<?= $operatore["codice"] ?>',function() { $('#modalIdInterno').dialog();});">
							<?= (!empty($operatore["id_interno"])) ? $operatore["id_interno"] : "[" . $operatore["codice"] . "]"; ?>
						</a>
						<?
						$cell = ob_get_clean();
						$columns[] = $cell;

						$td = "";
						if (!empty($checks[$operatore["codice_fiscale_impresa"]])) {
							$art80 = $checks[$operatore["codice_fiscale_impresa"]];
							if ($art80 != false) {
								$td = '<div class="status_indicator" style="background-color: ' .$art80["color"]  .'"></div>';
							}
						}
						$columns[] = $td;
            $columns[] = $operatore["ragione_sociale"];
            $columns[] = $operatore["cognome"] . " " . $operatore["nome"];
            $columns[] = $operatore["tipo"];
						$columns[] = $operatore["partita_iva"];
						$columns[] = $operatore["codice_fiscale_impresa"];
            $columns[] = mysql2datetime($operatore["timestamp_iscrizione"]);
						$bind_albi = array('codice_ente' => $_SESSION["ente"]["codice"], ':codice_operatore' => $operatore["codice_operatore"]);
						$sth_albi->execute($bind_albi);
						if ($sth_albi->rowCount() > 0) {
							$columns[] = "<span class=\"fa fa-check fa-2x\"></span>";
						} else {
            	$columns[] = "";
						}
						if (!empty($operatore["timestamp_richiesta"])) {
							$columns[] = mysql2datetime($operatore["timestamp_richiesta"]);
						} else {
							$columns[] = "";
						}
						$columns[] = (isset($operatore["inviti"])) ? $operatore["inviti"] : "";
						$columns[] = (isset($operatore["inviti_anno"])) ? $operatore["inviti_anno"] : "";
						if (!empty($operatore["feedback"])) {
							$pnt = $operatore["feedback"];
							$columns[] = "<img src=\"/img/". number_format($pnt,0) .".png\" alt=\"Ranking\" height=\"12\" valign=\"middle\" style=\"margin-top:-3px\"> <b>" . number_format($pnt, 1) . "</b>";
						} else {
							$columns[] = "";
						}
            $columns[] = "<a href=\"/albo/id{$operatore["codice"]}-dettaglio\" title=\"Apri dettaglio\" class=\"btn-round btn-warning\"><span class=\"fa fa-search\"></span></a>";
            $columns[] = "<input id=\"invia_{$operatore["codice"]}\" class=\"invia_comunicazione\" type=\"image\" src=\"/img/newsletter.png\" onClick='aggiungi_destinario(\"{$operatore["codice"]}\",\"".htmlentities(strtoupper(addslashes($operatore["ragione_sociale"])),ENT_QUOTES)."\");$(\"#comunicazione\").slideDown(\"slow\");' width=\"24\" title=\"Invia una comunicazione\">";
    				$output["aaData"][] = $columns;
          } else {
            break;
          }
  			}
		  }
  		echo json_encode( $output );
	}

?>
