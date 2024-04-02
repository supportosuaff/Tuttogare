<?

if(!empty($root) && !empty($queryStart) && !empty($queryEnd) && (!empty($_SESSION["ente"]["codice"]) || (!isset($_SESSION["ente"]) && isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] < 1))) {

  if (!empty($_SESSION["ente"]["codice"])) {
    $bind = array(
      ':codice_sua' => $_SESSION["ente"]["codice"],
      ':codice_ente' => !empty($_SESSION["record_utente"]["codice_ente"]) ? $_SESSION["record_utente"]["codice_ente"] : 0,
      ':gerarchia' => !empty($_SESSION["gerarchia"]) ? $_SESSION["gerarchia"] : 0,
      ':codice_utente' => !empty($_SESSION["codice_utente"]) ? $_SESSION["codice_utente"] : 0
    );
    $logged_user = "";
    if(! empty($_SESSION["utente"]["codice"])) {
      $logged_user = "AND b_scadenze.codice_utente = :codice_utente";
    }
    $sql = "SELECT b_scadenze.codice, b_scadenze.oggetto, b_scadenze.descrizione, DAY(b_scadenze.data) as giorno, DATE(b_scadenze.data) as data, TIME(b_scadenze.data) as ora, b_scadenze.data
            FROM b_scadenze
            LEFT JOIN b_gruppi ON b_gruppi.codice = b_scadenze.codice_gerarchia
            WHERE b_scadenze.data BETWEEN '{$queryStart}' AND '{$queryEnd}'
            {$logged_user}
            AND (
              (
                b_scadenze.codice_ente = :codice_sua
                OR b_scadenze.codice_ente = 0
                OR b_scadenze.codice_ente IS NULL
              ) AND (
                b_scadenze.codice_ente_destinatario = :codice_ente
                OR b_scadenze.codice_ente_destinatario = 0
                OR b_scadenze.codice_ente_destinatario IS NULL
              ) AND (
                b_scadenze.codice_utente = :codice_utente
                OR b_scadenze.codice_utente = 0
                OR b_scadenze.codice_utente IS NULL
              ) AND (
                b_scadenze.codice_gerarchia = 0
                OR b_scadenze.codice_gerarchia IS NULL
                OR b_gruppi.gerarchia >= :gerarchia
              ) AND (
                b_scadenze.codice_modulo = 0
                OR b_scadenze.codice_modulo IS NULL
                OR b_scadenze.codice_modulo IN (SELECT cod_modulo FROM r_moduli_utente WHERE cod_utente = :codice_utente)
              )
            )
            ORDER BY b_scadenze.data ASC";
    $ris = $pdo->bindAndExec($sql, $bind);
    if($ris->rowCount() >  0) {
      while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
        if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
        $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
          'codice' => $rec["codice"],
          'oggetto' => $rec["oggetto"],
          'data' => $rec["data"],
          'tipologia' => "scadenze",
          'descrizione' => $rec["descrizione"]
        );
      }
    }
  }
  $bind = array();
  if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

  $sql  = "SELECT b_gare.codice, b_gare.oggetto, b_gare.descrizione, b_date_apertura.data_apertura, b_enti.denominazione AS ente, b_modalita.online FROM b_date_apertura
          JOIN b_gare ON b_date_apertura.codice_gara = b_gare.codice
          JOIN b_modalita ON b_gare.modalita = b_modalita.codice
          JOIN b_enti ON b_gare.codice_gestore = b_enti.codice ";
  if (!empty($_SESSION["codice_utente"]) && is_operatore()) { $sql .= "LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";}
  $sql .= "WHERE ";
  if (!isset($_SESSION["ente"])) $sql.=" b_enti.ambienteTest = 'N' AND ";
  $sql .= " (DATE(b_date_apertura.data_apertura) BETWEEN '{$queryStart}' AND '{$queryEnd}') ";
  $sql .= "AND annullata = 'N' ";
  if(!empty($_SESSION["ente"]["codice"])) $sql .= "AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
  if(empty($_SESSION["codice_utente"])) {
    $sql .= "AND b_gare.pubblica = '2' ";
  } else {
    if(is_operatore()) {
      $bind[":codice_utente"] = $_SESSION["codice_utente"];
      $sql .= "AND (b_gare.pubblica = '2' OR (b_gare.pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente)))) ";
    } else {
      $sql .= "AND  (b_gare.pubblica > 0) ";
    }
  }
  $ris  = $pdo->bindAndExec($sql,$bind);
  if($ris->rowCount() > 0 ) {
    while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
      if(strtotime($rec["data_apertura"]) >= strtotime($queryStart) && strtotime($rec["data_apertura"]) <= strtotime($queryEnd)) {
        $rec["giorno"] = (int) substr($rec["data_apertura"], 8, 2);
        $rec["ora"] = substr($rec["data_apertura"], 11, 5);
        if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
        $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
          'codice' => $rec["codice"],
          'oggetto' => $rec["oggetto"],
          'data' => $rec["data_apertura"],
          'tipologia' => "apertura_buste_gara",
          'descrizione' => $rec["descrizione"],
          'link' => "/gare/id{$rec["codice"]}-dettaglio",
          'online' => $rec["online"],
          'ente' => $rec["ente"]
        );
      }
    }
  }


  $bind = array();
  if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

  $sql  = "SELECT b_gare.*, b_enti.denominazione AS ente, b_modalita.online FROM b_gare
          JOIN b_modalita ON b_gare.modalita = b_modalita.codice
          JOIN b_enti ON b_gare.codice_gestore = b_enti.codice ";
  if (!empty($_SESSION["codice_utente"]) && is_operatore()) { $sql .= "LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";}
  $sql .= "WHERE ";
  if (!isset($_SESSION["ente"])) $sql.=" b_enti.ambienteTest = 'N' AND ";
  $sql .= " ((DATE(data_scadenza) BETWEEN '{$queryStart}' AND '{$queryEnd}') OR (DATE(data_accesso) BETWEEN '{$queryStart}' AND '{$queryEnd}') OR (DATE(data_apertura) BETWEEN '{$queryStart}' AND '{$queryEnd}')) ";
  $sql .= "AND annullata = 'N' ";
  if(!empty($_SESSION["ente"]["codice"])) $sql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
  if(empty($_SESSION["codice_utente"])) {
    $sql .= "AND pubblica = '2' ";
  } else {
    if(is_operatore()) {
      $bind[":codice_utente"] = $_SESSION["codice_utente"];
      $sql .= "AND (pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente)))) ";
    } else {
      $sql .= "AND  (pubblica > 0) ";
    }
  }
  $sql .= "GROUP BY b_gare.codice";
  $ris  = $pdo->bindAndExec($sql,$bind);
  if($ris->rowCount() > 0 ) {
    while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
      if(strtotime($rec["data_accesso"]) >= strtotime($queryStart) && strtotime($rec["data_accesso"]) <= strtotime($queryEnd)) {
        $rec["giorno"] = (int) substr($rec["data_accesso"], 8, 2);
        $rec["ora"] = substr($rec["data_accesso"], 11, 5);
        if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
        $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
          'codice' => $rec["codice"],
          'oggetto' => $rec["oggetto"],
          'data' => $rec["data_accesso"],
          'tipologia' => "richiesta_info_gara",
          'descrizione' => $rec["descrizione"],
          'link' => "/gare/id{$rec["codice"]}-dettaglio",
          'online' => $rec["online"],
          'ente' => $rec["ente"]
        );
      }
      if(strtotime($rec["data_scadenza"]) >= strtotime($queryStart) && strtotime($rec["data_scadenza"]) <= strtotime($queryEnd)) {
        $rec["giorno"] = (int) substr($rec["data_scadenza"], 8, 2);
        $rec["ora"] = substr($rec["data_scadenza"], 11, 5);
        if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
        $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
          'codice' => $rec["codice"],
          'oggetto' => $rec["oggetto"],
          'data' => $rec["data_scadenza"],
          'tipologia' => "gare",
          'descrizione' => $rec["descrizione"],
          'link' => "/gare/id{$rec["codice"]}-dettaglio",
          'online' => $rec["online"],
          'ente' => $rec["ente"]
        );
      }
      if(strtotime($rec["data_apertura"]) >= strtotime($queryStart) && strtotime($rec["data_apertura"]) <= strtotime($queryEnd)) {
        $rec["giorno"] = (int) substr($rec["data_apertura"], 8, 2);
        $rec["ora"] = substr($rec["data_apertura"], 11, 5);
        if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
        $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
          'codice' => $rec["codice"],
          'oggetto' => $rec["oggetto"],
          'data' => $rec["data_apertura"],
          'tipologia' => "apertura_buste_gara",
          'descrizione' => $rec["descrizione"],
          'link' => "/gare/id{$rec["codice"]}-dettaglio",
          'online' => $rec["online"],
          'ente' => $rec["ente"]
        );
      }
    }
  }

  if(!empty($_SESSION["codice_utente"])) {
    $bind = array();
    if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

    $sql = "SELECT b_gare.codice, b_gare.oggetto, b_gare.descrizione, b_2fase.data_inizio, b_2fase.data_fine, b_enti.denominazione AS ente, b_modalita.online FROM b_2fase ";
    if(is_operatore()) $sql .= "JOIN r_partecipanti ON r_partecipanti.codice_gara = b_2fase.codice_gara ";
    $sql .=" JOIN b_gare ON b_gare.codice = b_2fase.codice_gara
            JOIN b_modalita ON b_gare.modalita = b_modalita.codice
            JOIN b_enti ON b_gare.codice_gestore = b_enti.codice ";
    $sql .= "WHERE ";
    if (!isset($_SESSION["ente"])) $sql.=" b_enti.ambienteTest = 'N' AND ";
    $sql .= " ((DATE(b_2fase.data_fine) BETWEEN '{$queryStart}' AND '{$queryEnd}') OR (DATE(b_2fase.data_inizio) BETWEEN '{$queryStart}' AND '{$queryEnd}')) ";
    $sql .= "AND b_gare.annullata = 'N' ";
    if(!empty($_SESSION["ente"]["codice"])) $sql .= "AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
    if(is_operatore()) {
      $bind[":codice_utente"] = $_SESSION["codice_utente"];
      $sql .= "AND r_partecipanti.codice_lotto = b_2fase.codice_lotto AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N' AND r_partecipanti.codice_utente = :codice_utente ";
    } else {
      $sql .= "AND  (b_gare.pubblica > 0) ";
    }
    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount() >0) {
      while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
        if(strtotime($rec["data_inizio"]) >= strtotime($queryStart) && strtotime($rec["data_inizio"]) <= strtotime($queryEnd)) {
          $rec["giorno"] = (int) substr($rec["data_inizio"], 8, 2);
          $rec["ora"] = substr($rec["data_inizio"], 11, 5);
          if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
          $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
            'codice' => $rec["codice"],
            'oggetto' => $rec["oggetto"],
            'data' => $rec["data_inizio"],
            'tipologia' => "avvio_2fase",
            'descrizione' => $rec["descrizione"],
            'link' => "/gare/id{$rec["codice"]}-dettaglio",
            'online' => $rec["online"],
            'ente' => $rec["ente"]
          );
        }
        if(strtotime($rec["data_fine"]) >= strtotime($queryStart) && strtotime($rec["data_fine"]) <= strtotime($queryEnd)) {
          $rec["giorno"] = (int) substr($rec["data_fine"], 8, 2);
          $rec["ora"] = substr($rec["data_fine"], 11, 5);
          if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
          $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
            'codice' => $rec["codice"],
            'oggetto' => $rec["oggetto"],
            'data' => $rec["data_fine"],
            'tipologia' => "gare",
            'descrizione' => $rec["descrizione"],
            'link' => "/gare/id{$rec["codice"]}-dettaglio"
          );
        }
      }
    }

    $bind = array();
    if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

    $sql = "SELECT b_gare.codice, b_gare.oggetto, b_gare.descrizione, b_aste.data_inizio, b_aste.data_fine, b_enti.denominazione AS ente, b_modalita.online FROM b_aste ";
    if(is_operatore()) $sql .= "JOIN r_partecipanti ON r_partecipanti.codice_gara = b_aste.codice_gara ";
    $sql .=" JOIN b_gare ON b_gare.codice = b_aste.codice_gara
            JOIN b_modalita ON b_gare.modalita = b_modalita.codice
            JOIN b_enti ON b_gare.codice_gestore = b_enti.codice ";
    $sql .= "WHERE ";
    if (!isset($_SESSION["ente"])) $sql.=" b_enti.ambienteTest = 'N' AND ";
    $sql .= " ((DATE(b_aste.data_fine) BETWEEN '{$queryStart}' AND '{$queryEnd}') OR (DATE(b_aste.data_inizio) BETWEEN '{$queryStart}' AND '{$queryEnd}')) ";
    $sql .= "AND b_gare.annullata = 'N' ";
    if(!empty($_SESSION["ente"]["codice"])) $sql .= "AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
    if(is_operatore()) {
      $bind[":codice_utente"] = $_SESSION["codice_utente"];
      $sql .= "AND r_partecipanti.codice_lotto = b_aste.codice_lotto AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N' AND r_partecipanti.codice_utente = :codice_utente ";
    } else {
      $sql .= "AND  (b_gare.pubblica > 0) ";
    }
    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount() >0) {
      while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
        if(strtotime($rec["data_inizio"]) >= strtotime($queryStart) && strtotime($rec["data_inizio"]) <= strtotime($queryEnd)) {
          $rec["giorno"] = (int) substr($rec["data_inizio"], 8, 2);
          $rec["ora"] = substr($rec["data_inizio"], 11, 5);
          if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
          $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
            'codice' => $rec["codice"],
            'oggetto' => $rec["oggetto"],
            'data' => $rec["data_inizio"],
            'tipologia' => "avvio_asta",
            'descrizione' => $rec["descrizione"],
            'link' => "/gare/id{$rec["codice"]}-dettaglio",
            'online' => $rec["online"],
            'ente' => $rec["ente"]
          );
        }
        if(strtotime($rec["data_fine"]) >= strtotime($queryStart) && strtotime($rec["data_fine"]) <= strtotime($queryEnd)) {
          $rec["giorno"] = (int) substr($rec["data_fine"], 8, 2);
          $rec["ora"] = substr($rec["data_fine"], 11, 5);
          if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
          $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
            'codice' => $rec["codice"],
            'oggetto' => $rec["oggetto"],
            'data' => $rec["data_fine"],
            'tipologia' => "scadenza_asta",
            'descrizione' => $rec["descrizione"],
            'link' => "/gare/id{$rec["codice"]}-dettaglio",
            'online' => $rec["online"],
            'ente' => $rec["ente"]
          );
        }
      }
    }

    $bind = array();
    if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

    $sql = "SELECT b_gare.codice, b_integrazioni.titolo, b_integrazioni.richiesta, b_integrazioni.data_scadenza, b_enti.denominazione AS ente, b_modalita.online FROM b_integrazioni ";
    if(is_operatore()) $sql .= "JOIN r_integrazioni ON r_integrazioni.codice_integrazione = b_integrazioni.codice ";
    $sql .=" JOIN b_gare ON b_gare.codice = b_integrazioni.codice_gara
            JOIN b_modalita ON b_gare.modalita = b_modalita.codice
            JOIN b_enti ON b_gare.codice_gestore = b_enti.codice ";
    $sql .= "WHERE ";
    if (!isset($_SESSION["ente"])) $sql.=" b_enti.ambienteTest = 'N' AND ";
    $sql .= " (DATE(b_integrazioni.data_scadenza) BETWEEN '{$queryStart}' AND '{$queryEnd}') ";
    $sql .= "AND b_gare.annullata = 'N' ";
    if(!empty($_SESSION["ente"]["codice"])) $sql .= "AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
    if(is_operatore()) {
      $bind[":codice_utente"] = $_SESSION["codice_utente"];
      $sql .= "AND (r_integrazioni.nome_file = '' || r_integrazioni.nome_file IS NULL) AND r_integrazioni.codice_utente = :codice_utente ";
    } else {
      $sql .= "AND  (b_gare.pubblica > 0) ";
    }
    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount() >0) {
      while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
        if(strtotime($rec["data_scadenza"]) >= strtotime($queryStart) && strtotime($rec["data_scadenza"]) <= strtotime($queryEnd)) {
          $rec["giorno"] = (int) substr($rec["data_scadenza"], 8, 2);
          $rec["ora"] = substr($rec["data_scadenza"], 11, 5);
          if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
          $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
            'codice' => $rec["codice"],
            'oggetto' => $rec["titolo"],
            'data' => $rec["data_scadenza"],
            'tipologia' => "scadenza_integrazioni",
            'descrizione' => $rec["richiesta"],
            'link' => "/gare/id{$rec["codice"]}-dettaglio",
            'online' => $rec["online"],
            'ente' => $rec["ente"]
          );
        }
      }
    }

    if(!is_operatore()) {
      if(check_permessi('fabbisogno', $_SESSION["codice_utente"])) {
        $bind = array();
        if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

        $edit_new = FALSE;
        if($_SESSION["ente"]["codice"] == $_SESSION["record_utente"]["codice_ente"]) $edit_new = check_permessi('new_fabbisogno', $_SESSION["codice_utente"]);

        $sql = " SELECT b_fabbisogno.*, b_enti.denominazione AS ente FROM b_fabbisogno JOIN b_enti ON b_fabbisogno.codice_gestore = b_enti.codice ";
        if (!$edit_new) $sql .= "JOIN r_enti_fabbisogno ON b_fabbisogno.codice = r_enti_fabbisogno.codice_fabbisogno ";
        $sql .= "WHERE ";
        if (!isset($_SESSION["ente"])) $sql.=" b_enti.ambienteTest = 'N' AND ";
        $sql .= " (DATE(b_fabbisogno.scadenza) BETWEEN '{$queryStart}' AND '{$queryEnd}') ";
        if(!empty($_SESSION["ente"]["codice"])) $sql.= "AND b_fabbisogno.codice_gestore = :codice_ente ";
        if (!$edit_new && !empty($_SESSION["ente"]["codice"])) {
          $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
          $sql.= " AND r_enti_fabbisogno.codice_ente = :codice_ente_utente ";
        }
        $sql.= " ORDER BY b_fabbisogno.scadenza DESC" ;
        $ris  = $pdo->bindAndExec($sql,$bind);
        if($ris->rowCount() > 0) {
          while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
            if(strtotime($rec["scadenza"]) >= strtotime($queryStart) && strtotime($rec["scadenza"]) <= strtotime($queryEnd)) {
              $rec["giorno"] = (int) substr($rec["scadenza"], 8, 2);
              $rec["ora"] = substr($rec["scadenza"], 11, 5);
              if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
              $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
                'codice' => $rec["codice"],
                'oggetto' => $rec["oggetto"],
                'data' => $rec["scadenza"],
                'tipologia' => "scadenza_fabbisogno",
                'descrizione' => $rec["descrizione"],
                'online' => 'S',
                'ente' => $rec["ente"]
              );
            }
          }
        }
      }

      $bind = array();
      if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

  		$sql  = "SELECT b_contratti.*, b_enti.denominazione FROM b_contratti JOIN b_enti ON b_contratti.codice_ente = b_enti.codice ";
      $sql .= "WHERE ";
      if (!isset($_SESSION["ente"])) $sql.=" b_enti.ambienteTest = 'N' AND ";
      $sql .=" ((DATE(b_contratti.data_fine) BETWEEN '{$queryStart}' AND '{$queryEnd}') OR (DATE(b_contratti.promemoria) BETWEEN '{$queryStart}' AND '{$queryEnd}')) ";
  		if(!empty($_SESSION["ente"]["codice"])) $sql .= "AND b_contratti.codice_gestore = :codice_ente ";
  		if ($_SESSION["gerarchia"] > 0 && !empty($_SESSION["ente"]["codice"])) {
  			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
  			$sql .= "AND (b_contratti.codice_ente = :codice_ente_utente OR b_contratti.codice_gestore = :codice_ente_utente) ";
  		}
      $ris = $pdo->bindAndExec($sql, $bind);
      if($ris->rowCount() > 0) {
        while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
          if(strtotime($rec["data_fine"]) >= strtotime($queryStart) && strtotime($rec["data_fine"]) <= strtotime($queryEnd)) {
            $rec["giorno"] = (int) substr($rec["data_fine"], 8, 2);
            $rec["ora"] = substr($rec["data_fine"], 11, 5);
            if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
            $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
              'codice' => $rec["codice"],
              'oggetto' => $rec["oggetto"],
              'data' => $rec["data_fine"],
              'tipologia' => "termine_contrato",
              'descrizione' => $rec["descrizione"],
              'ente' => $rec["denominazione"]
            );
          }
          if(strtotime($rec["promemoria"]) >= strtotime($queryStart) && strtotime($rec["promemoria"]) <= strtotime($queryEnd)) {
            $rec["giorno"] = (int) substr($rec["promemoria"], 8, 2);
            $rec["ora"] = substr($rec["promemoria"], 11, 5);
            if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
            $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
              'codice' => $rec["codice"],
              'oggetto' => $rec["oggetto"],
              'data' => $rec["promemoria"],
              'tipologia' => "promemoria_termine_contrato",
              'descrizione' => $rec["descrizione"],
              'ente' => $rec["denominazione"]
            );
          }
        }
      }
      if(check_permessi('esecuzione', $_SESSION["codice_utente"])) {
        $bind = array();
        if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
        $sql  = "SELECT b_consegne_esecuzione.*, b_enti.denominazione AS ente FROM b_consegne_esecuzione JOIN b_enti ON b_consegne_esecuzione.codice_gestore = b_enti.codice ";
        $sql .= "WHERE ";
        if (!isset($_SESSION["ente"])) $sql.=" b_enti.ambienteTest = 'N' AND ";
        $sql .= " b_consegne_esecuzione.tipo = 'invito' AND (DATE(data_ora) BETWEEN '{$queryStart}' AND '{$queryEnd}') AND b_consegne_esecuzione.attivo = 'S' ";
        if(!empty($_SESSION["ente"]["codice"])) $sql.= "AND b_consegne_esecuzione.codice_gestore = :codice_ente ";
        $ris  = $pdo->bindAndExec($sql,$bind);
        if($ris->rowCount() > 0) {
          while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
            if(strtotime($rec["data_ora"]) >= strtotime($queryStart) && strtotime($rec["data_ora"]) <= strtotime($queryEnd)) {
              $rec["giorno"] = (int) substr($rec["data_ora"], 8, 2);
              $rec["ora"] = substr($rec["data_ora"], 11, 5);
              if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
              $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
                'codice' => $rec["codice"],
                'oggetto' => $rec["luogo"],
                'data' => $rec["data_ora"],
                'tipologia' => "consegna_lavori",
                'descrizione' => $rec["note"],
                'ente' => $rec["ente"]
              );
            }
          }
        }
      }
    }
  }


  $bind = array();
  if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
  $sql = "SELECT b_bandi_sda.*, b_enti.denominazione AS ente FROM b_bandi_sda JOIN b_enti ON b_bandi_sda.codice_gestore = b_enti.codice ";
  if(!empty($_SESSION["codice_utente"])) { $sql .= "WHERE (pubblica = '2' OR pubblica = '1') "; } else { $sql .= "WHERE pubblica = '2' "; }
  if(!empty($_SESSION["ente"]["codice"])) $sql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
  if (!isset($_SESSION["ente"])) $sql.=" AND b_enti.ambienteTest = 'N' ";
  $sql .= " AND annullata = 'N' AND (DATE(data_scadenza) BETWEEN '{$queryStart}' AND '{$queryEnd}') ";
  $sql .= "ORDER BY id DESC, b_bandi_sda.codice DESC ";
	$ris = $pdo->bindAndExec($sql,$bind);
  if($ris->rowCount() > 0) {
    while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
      if(strtotime($rec["data_scadenza"]) >= strtotime($queryStart) && strtotime($rec["data_scadenza"]) <= strtotime($queryEnd)) {
        $rec["giorno"] = (int) substr(mysql2date($rec["data_scadenza"]), 0, 2);
        $rec["ora"] = substr($rec["data_scadenza"], 11, 5);
        if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
        $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
          'codice' => $rec["codice"],
          'oggetto' => $rec["oggetto"],
          'data' => $rec["data_scadenza"],
          'tipologia' => "bandi_sda",
          'descrizione' => $rec["descrizione"],
          'link' => "/sda/id{$rec["codice"]}-dettaglio",
          'ente' => $rec["ente"],
          'online' => 'S'
        );
      }
    }
  }

  $bind = array();
  if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
  $sql = "SELECT b_bandi_mercato.*, b_enti.denominazione AS ente FROM b_bandi_mercato JOIN b_enti ON b_bandi_mercato.codice_gestore = b_enti.codice ";
  if(!empty($_SESSION["codice_utente"])) { $sql .= "WHERE (pubblica = '2' OR pubblica = '1') "; } else { $sql .= "WHERE pubblica = '2' "; }
  if(!empty($_SESSION["ente"]["codice"])) $sql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
  if (!isset($_SESSION["ente"])) $sql.=" AND b_enti.ambienteTest = 'N' ";
  $sql .= " AND annullata = 'N' AND (DATE(data_scadenza) BETWEEN '{$queryStart}' AND '{$queryEnd}') ";
  $sql .= "ORDER BY id DESC, b_bandi_mercato.codice DESC ";
	$ris = $pdo->bindAndExec($sql,$bind);
  if($ris->rowCount() > 0) {
    while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
      if(strtotime($rec["data_scadenza"]) >= strtotime($queryStart) && strtotime($rec["data_scadenza"]) <= strtotime($queryEnd)) {
        $rec["giorno"] = (int) substr(mysql2date($rec["data_scadenza"]), 0, 2);
        $rec["ora"] = substr($rec["data_scadenza"], 11, 5);
        if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
        $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
          'codice' => $rec["codice"],
          'oggetto' => $rec["oggetto"],
          'data' => $rec["data_scadenza"],
          'tipologia' => "mercato_elettronico",
          'descrizione' => $rec["descrizione"],
          'link' => "/mercato_elettronico/id{$rec["codice"]}-dettaglio",
          'ente' => $rec["ente"],
          'online' => 'S'
        );
      }
    }
  }

  $bind = array();
  if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
  $sql = "SELECT b_bandi_albo.*, b_enti.denominazione AS ente FROM b_bandi_albo JOIN b_enti ON b_bandi_albo.codice_gestore = b_enti.codice ";
  if(!empty($_SESSION["codice_utente"])) { $sql .= "WHERE (pubblica = '2' OR pubblica = '1') "; } else { $sql .= "WHERE pubblica = '2' "; }
  if(!empty($_SESSION["ente"]["codice"])) $sql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
  if (!isset($_SESSION["ente"])) $sql.=" AND b_enti.ambienteTest = 'N' ";
  $sql .= " AND annullata = 'N' AND (DATE(data_scadenza) BETWEEN '{$queryStart}' AND '{$queryEnd}') ";
  $sql .= "ORDER BY id DESC, b_bandi_albo.codice DESC ";
	$ris = $pdo->bindAndExec($sql,$bind);
  if($ris->rowCount() > 0) {
    while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
      if(strtotime($rec["data_scadenza"]) >= strtotime($queryStart) && strtotime($rec["data_scadenza"]) <= strtotime($queryEnd)) {
        $rec["giorno"] = (int) substr(mysql2date($rec["data_scadenza"]), 0, 2);
        $rec["ora"] = substr($rec["data_scadenza"], 11, 5);
        if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
        $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
          'codice' => $rec["codice"],
          'oggetto' => $rec["oggetto"],
          'data' => $rec["data_scadenza"],
          'tipologia' => "bando_albo",
          'descrizione' => $rec["descrizione"],
          'link' => "/albo_fornitori/id{$rec["codice"]}-dettaglio",
          'ente' => $rec["ente"],
          'online' => 'S'
        );
      }
    }
  }

  $bind = array();
  if(!empty($_SESSION["ente"]["codice"])) $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
  $sql = "SELECT b_bandi_dialogo.*, b_enti.denominazione AS ente FROM b_bandi_dialogo JOIN b_enti ON b_bandi_dialogo.codice_gestore = b_enti.codice ";
  if(!empty($_SESSION["codice_utente"])) { $sql .= "WHERE (pubblica = '2' OR pubblica = '1') "; } else { $sql .= "WHERE pubblica = '2' "; }
  if(!empty($_SESSION["ente"]["codice"])) $sql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
  if (!isset($_SESSION["ente"])) $sql.=" AND b_enti.ambienteTest = 'N' ";
  $sql .= " AND annullata = 'N' AND (DATE(data_scadenza) BETWEEN '{$queryStart}' AND '{$queryEnd}') ";
  $sql .= "ORDER BY id DESC, b_bandi_dialogo.codice DESC ";
  $ris = $pdo->bindAndExec($sql,$bind);
  if($ris->rowCount() > 0) {
    while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
      if(strtotime($rec["data_scadenza"]) >= strtotime($queryStart) && strtotime($rec["data_scadenza"]) <= strtotime($queryEnd)) {
        $rec["giorno"] = (int) substr(mysql2date($rec["data_scadenza"]), 0, 2);
        $rec["ora"] = substr($rec["data_scadenza"], 11, 5);
        if(!isset($promemoria[$rec["giorno"]][$rec["ora"]])) $promemoria[$rec["giorno"]][$rec["ora"]] = array();
        $promemoria[$rec["giorno"]][$rec["ora"]][] = array(
          'codice' => $rec["codice"],
          'oggetto' => $rec["oggetto"],
          'data' => $rec["data_scadenza"],
          'tipologia' => "bando_dialogo",
          'descrizione' => $rec["descrizione"],
          'link' => "/dialogo_competitivo/id{$rec["codice"]}-dettaglio",
          'ente' => $rec["ente"],
          'online' => 'S'
        );
      }
    }
  }
}
?>
