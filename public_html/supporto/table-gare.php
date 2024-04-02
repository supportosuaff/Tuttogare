<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if (isset($_SESSION["codice_utente"])) {
    if (check_permessi("supporto",$_SESSION["codice_utente"]) && in_array($_SESSION["tipo_utente"], array('SAD', 'SUP')) && empty($_SESSION["ente"])) {
      $bind = array();
      $strsql  = "SELECT b_gare.*, b_procedure.nome AS procedura, b_enti.denominazione, b_gestore.denominazione AS gestore, b_gestore.dominio, GROUP_CONCAT(b_lotti.cig) AS cig_lotti,
                  b_stati_gare.titolo AS fase, b_stati_gare.colore
                  FROM b_gare JOIN b_enti ON b_gare.codice_ente = b_enti.codice
                  JOIN b_enti AS b_gestore ON b_gare.codice_gestore = b_gestore.codice
                  JOIN b_procedure ON b_gare.procedura = b_procedure.codice
                  LEFT JOIN b_lotti ON b_gare.codice = b_lotti.codice_gara
                  JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase
                  WHERE b_gestore.ambienteTest = 'N' ";

      $where = array();

      if (!empty($_POST["search"]["value"])) {
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_gare.cig");
  			$bind = array_merge($bind,$condizione["bind"]);
  			$sql[] = $condizione["sql"];
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_gare.oggetto");
        $sql[] = $condizione["sql"];
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_gare.id");
        $sql[] = $condizione["sql"];
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_lotti.cig");
        $sql[] = $condizione["sql"];
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_enti.denominazione");
        $sql[] = $condizione["sql"];
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_gestore.denominazione");
        $sql[] = $condizione["sql"];
        $sql = implode(" OR ", $sql);
        $strsql.= " AND (" .$sql . ")";
      }
      $strsql .= " GROUP BY b_gare.codice ORDER BY b_gare.codice DESC  ";

      $risultato  = $pdo->bindAndExec($strsql,$bind);
      $iFilteredTotal = $iTotal = 0;
      $data = array();
      if ($risultato->rowCount() > 0) {
        $iTotal = $risultato->rowCount();
        $iFilteredTotal = $iTotal;
        if ( isset( $_POST['start'] ) && $_POST['length'] != '-1' && is_numeric( $_POST['start'] ) && is_numeric( $_POST['length'] ))
        {
          $start = (int)$_POST['start'];
          $lenght = (int)$_POST['length'];
          $strsql .= " LIMIT " . $start . ", " . $lenght;
        }
        $risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
        if ($risultato->rowCount() > 0) {
          // $iFilteredTotal = $risultato->rowCount();
          while($record=$risultato->fetch(PDO::FETCH_ASSOC)) {
            $columns = array();

            if (($record["stato"]==3) && (strtotime($record["data_scadenza"])<time())) {
							$record["colore"] = $config["colore_scaduta"];
							$record["fase"] = "Scaduta";
						}

            $cig = $record["cig"];
            if (!empty($record["cig_lotti"])) $cig = str_replace(",","<br>",$record["cig_lotti"]);

            $columns[] = '<div class="status_indicator" style="background-color: #' .$record["colore"]  .'"></div>';
            $columns[] = $record["fase"];
            $columns[] = $record["id"];
            $columns[] = $cig;
            ob_start();
            ?>
            <a target="_blank" href="https://<?= $record["dominio"] ?>/gare/id<?= $record["codice"] ?>-dettaglio"><?= $record["oggetto"] ?></a><br>
            <br>
            <small>URL DGUE: <strong>https://<?= $record["dominio"] ?>/dgue/edit.php?sezione=gare&codice_riferimento=<?= $record["codice"] ?></strong></small>
            <?
            $columns[] = ob_get_clean();
            $columns[] = $record["procedura"];
            $columns[] = $record["denominazione"];
            $columns[] = $record["gestore"];
            $data[] = $columns;
          }
        }
      }
      $output = array(
				"sEcho" => intval($_POST['draw']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $data
			);
      echo json_encode( $output );

    }
  }
?>
