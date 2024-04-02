<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if (isset($_SESSION["codice_utente"])) {
    if (check_permessi("operatori_economici",$_SESSION["codice_utente"])) {
      $iFilteredTotal = 0;

      $bind =array();
      $strsql  = "SELECT b_utenti.*, b_gruppi.gruppo AS tipo, b_operatori_economici.ragione_sociale, b_operatori_economici.partita_iva, b_operatori_economici.codice_fiscale_impresa
                  FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice LEFT JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente ";
      if (isset($_SESSION["ente"])) $strsql.="JOIN r_enti_operatori ON r_enti_operatori.cod_utente = b_utenti.codice ";
      $strsql.= "WHERE (b_gruppi.gerarchia = 3 OR b_gruppi.gerarchia = 4) ";
      if (isset($_SESSION["ente"])) {
         $bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
         $strsql.=" AND r_enti_operatori.cod_ente = :codice_ente";
      }

      $where = array();
      if (!empty($_POST["search"]["value"])) {
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_operatori_economici.ragione_sociale");
  			$bind = array_merge($bind,$condizione["bind"]);
  			$sql[] = $condizione["sql"];
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_utenti.cognome");
        $sql[] = $condizione["sql"];
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_utenti.nome");
        $sql[] = $condizione["sql"];
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_operatori_economici.codice_fiscale_impresa");
        $sql[] = $condizione["sql"];
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_operatori_economici.partita_iva");
        $sql[] = $condizione["sql"];
        $condizione = suddivisione_pdo($_POST["search"]["value"],"b_utenti.email");
        $sql[] = $condizione["sql"];
        $sql = implode(" OR ", $sql);
        $strsql.= " AND (" .$sql . ")";
      }

      $strsql .= " GROUP BY b_operatori_economici.codice ";

      $order = "ragione_sociale";
      $dir = (strtoupper($_POST["order"][0]["dir"]) == "ASC") ? "ASC" : "DESC";

      switch($_POST["order"][0]["column"]) {
        case 3:
          $order = "cognome " . $dir . ", nome";
          break;
        case 4:
          $order = "tipo";
          break;
        case 5:
          $order = "partita_iva";
          break;
        case 6:
          $order = "codice_fiscale_impresa";
          break;
        case 7:
          $order = "b_utenti.timestamp";
          break;
      }

      $strsql .= " ORDER BY " . $order . " " . $dir;

      $risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
      $iTotal = 0;
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
            $codice			= $record["codice"];
      			$nominativo		= $record["cognome"] . " " . $record["nome"];
      			$attivo			= $record["attivo"];

      			$colore = "#3C0";
      			if ($attivo == "N") { $colore = "#C00"; }

      			$colore_scaduto = "#3C0";
      			if ($record["scaduto"] == "S") { $colore_scaduto = "#C00"; }

            $columns[] = '<div class="status_indicator" id="flag_' . $codice  .'" style="background-color: ' .$colore  .'"></div>';
            $columns[] = '<div class="status_indicator" id="flag_scaduto_' . $codice  .'" style="background-color: ' .$colore_scaduto  .'"></div>';
            $columns[] = $record["ragione_sociale"];
            $columns[] = $nominativo;
            $columns[] = $record["tipo"];
            $columns[] = $record["partita_iva"];
            $columns[] = $record["codice_fiscale_impresa"];
            $columns[] = $record["timestamp"];;
            if (!isset($_SESSION["ente"])) {
              $bind = array(":codice_utente"=>$record["codice"]);
              $sql = "SELECT b_enti.denominazione FROM b_enti JOIN r_enti_operatori ON r_enti_operatori.cod_ente = b_enti.codice ";
              $sql .= " WHERE r_enti_operatori.cod_utente = :codice_utente";
              $ris = $pdo->bindAndExec($sql,$bind);
              if ($ris->rowCount()>0) {
                $enti = "";
                while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
                  $enti .= $rec["denominazione"] . "<br>";
                }
                $columns[] = $enti;
              }
            }
            $columns[] = '<button class="btn-round btn-primary" onClick="window.location.href=\'/operatori_economici/id'. $codice .'-edit\'" title="Modifica"><span class="fa fa-pencil"></span></button>';
            if (isset($_SESSION["ente"])) {
              $columns[] = '<button class="btn-round button-action" type="image" onClick="rigenera(\'' .$codice .'\')" title="Reinvia conferma"><span class="fa fa-reply"></span></button>';
              $columns[] = '<button class="btn-round btn-warning" onClick="reinvia(\''. $codice .'\')" title="Reinvia conferma"><span class="fa fa-envelope"></span></button>';
            }
            $columns[] = '<button class="btn-round btn-default" type="image" onClick="disabilita(\''. $codice .'\',\'user\');" title="Abilita/Disabilita"><span class="fa fa-refresh"></span>';
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
