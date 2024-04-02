<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if (isset($_SESSION["codice_utente"])) {
    if (check_permessi("supporto",$_SESSION["codice_utente"]) && in_array($_SESSION["tipo_utente"], array('SAD', 'SUP')) && empty($_SESSION["ente"])) {
      $bind = array();
      $strsql  = "SELECT b_utenti.*, b_gruppi.gruppo AS tipo, b_operatori_economici.ragione_sociale, b_operatori_economici.partita_iva, b_operatori_economici.codice_fiscale_impresa
                  FROM b_utenti
                  JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
                  JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente
                  WHERE b_gruppi.gerarchia > 2";

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
        $sql = implode(" OR ", $sql);
        $strsql.= " AND (" .$sql . ")";
      }

      if(! empty($_POST["p_iva"])) {
        $strsql .= " AND (b_operatori_economici.partita_iva LIKE :partita_iva_oe OR b_operatori_economici.codice_fiscale_impresa LIKE :partita_iva_oe) ";
        $bind[":partita_iva_oe"] = "{$_POST["p_iva"]}%";
      }

      if(! empty($_POST["ragione_sociale"])) {
        $strsql .= " AND b_operatori_economici.ragione_sociale LIKE :ragione_sociale_oe ";
        $bind[":ragione_sociale_oe"] = "{$_POST["ragione_sociale"]}%";
      }

      if(! empty($_POST["email"])) {
        $strsql .= " AND b_utenti.email LIKE :email_oe ";
        $bind[":email_oe"] = "%{$_POST["email"]}%";
      }

      if(! empty($_POST["pec"])) {
        $strsql .= " AND b_utenti.pec LIKE :pec_oe ";
        $bind[":pec_oe"] = "%{$_POST["pec"]}%";
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

            $bind = array(":codice_utente"=>$record["codice"]);
            $sql = "SELECT b_enti.denominazione, b_enti.dominio FROM b_enti JOIN r_enti_operatori ON r_enti_operatori.cod_ente = b_enti.codice ";
            $sql .= " WHERE r_enti_operatori.cod_utente = :codice_utente";
            $ris = $pdo->bindAndExec($sql,$bind);
            if ($ris->rowCount()>0) {
              $enti = "";
              while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
                $enti .= "<a target='_blank' href='https://{$rec["dominio"]}'>{$rec["denominazione"]}</a><br>";
              }
              $columns[] = $enti;
            }

            $columns[] = '<button class="btn-round btn-primary" onClick="window.location.href=\'/albo/id'. $codice .'-dettaglio\'" title="Visualizza"><span class="fa fa-search"></span></button>';
            $columns[] = '<button class="btn-round button-plain" type="image" onClick="get_list(\'' .$codice .'\', \'random_password\')" title="Invia una password provvisoria"><span class="fa fa-asterisk" style="font-size: 14px;"></span></button>';
            $columns[] = '<button class="btn-round button-action" type="image" onClick="get_list(\'' .$codice .'\', \'rigenera_password\')" title="Rigenera password"><span class="fa fa-unlock-alt" style="font-size: 14px;"></span></button>';
            $columns[] = '<button class="btn-round btn-warning" onClick="get_list(\''. $codice .'\', \'reinvia_conferma\')" title="Reinvia conferma"><span class="fa fa-envelope"></span></button>';
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
