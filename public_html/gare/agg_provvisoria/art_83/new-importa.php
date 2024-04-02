<?
  if (isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] <= 2 && isset($numero_partecipanti)) {
    $sql_criteri = "SELECT b_punteggi_criteri.punteggio,
                           b_punteggi_criteri.codice_partecipante,
                           b_punteggi_criteri.codice_criterio
                    FROM b_punteggi_criteri
                    JOIN b_valutazione_tecnica ON b_punteggi_criteri.codice_criterio = b_valutazione_tecnica.codice
                    JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
                    WHERE b_punteggi_criteri.codice_gara = :codice_gara
                    AND b_punteggi_criteri.codice_lotto = :codice_lotto ";
                    if (!isset($offerteTecniche)) {
                      $sql_criteri .= " AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
                    } else {
                      $sql_criteri .= " AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
                    }
    $riparametra = true;
    if (empty($_POST["riparametrazione_semplice"]) || (!empty($_POST["riparametrazione_semplice"]) && $_POST["riparametrazione_semplice"] == "N")) $riparametra = false;
    $sql_all = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND (codice_lotto = :codice_lotto OR codice_lotto = 0)";
    $ris_punteggi = $pdo->bindAndExec($sql_criteri,[":codice_gara"=>$_POST["codice_gara"],":codice_lotto"=>$_POST["codice_lotto"]]);
    $ris_all = $pdo->bindAndExec($sql_all,[":codice_gara"=>$_POST["codice_gara"],":codice_lotto"=>$_POST["codice_lotto"]]);
    if ($ris_punteggi->rowCount() > 0 && $ris_all->rowCount() > 0) {
      $criteri = [];
      $totali = [];
      $punteggi = [];
      while ($criterio = $ris_all->fetch(PDO::FETCH_ASSOC)) {
        $criterio["punteggi"] = [];
        $criteri[$criterio["codice"]] = $criterio;
        if (empty($criterio["codice_padre"])) {
          $totali[$criterio["codice"]] = $criterio;
        }
      }
      while ($punteggio = $ris_punteggi->fetch(PDO::FETCH_ASSOC)) {
        if ($riparametra) {
          if (!isset($criteri[$punteggio["codice_criterio"]]["punteggi"][$punteggio["codice_partecipante"]])) $criteri[$punteggio["codice_criterio"]]["punteggi"][$punteggio["codice_partecipante"]] = 0;
          $criteri[$punteggio["codice_criterio"]]["punteggi"][$punteggio["codice_partecipante"]] += $punteggio["punteggio"];
        } else {
          $criterio = $criteri[$punteggio["codice_criterio"]];
          if (!isset($punteggi[$criterio["punteggio_riferimento"]])) $punteggi[$criterio["punteggio_riferimento"]] = [];
          if (!isset($punteggi[$criterio["punteggio_riferimento"]][$punteggio["codice_partecipante"]])) $punteggi[$criterio["punteggio_riferimento"]][$punteggio["codice_partecipante"]] = 0;
          $punteggi[$criterio["punteggio_riferimento"]][$punteggio["codice_partecipante"]] += $punteggio["punteggio"];
        }
      }
      if ($riparametra) {
        foreach($criteri AS $codice_criterio => $criterio) {
          if (count($criterio["punteggi"]) > 0) {
            $criterio["punteggi"] = normalizza($criterio["punteggi"],$criterio["punteggio"],$criterio["decimali"]);
            $id_totale = $criterio["codice_padre"];
            if (empty($id_totale)) $id_totale = $criterio["codice"];
            foreach($criterio["punteggi"] AS $cod_part => $punt) {
              if (!isset($totali[$id_totale]["punteggi"][$cod_part])) $totali[$id_totale]["punteggi"][$cod_part] = 0;
              $totali[$id_totale]["punteggi"][$cod_part] += $punt;
            }
          }
        }
        foreach($totali AS $criterio) {
          if (count($criterio["punteggi"]) > 0) {
            $criterio["punteggi"] = normalizza($criterio["punteggi"],$criterio["punteggio"],$criterio["decimali"]);
            foreach($criterio["punteggi"] AS $cod_part => $punt) {
              if (!isset($punteggi[$criterio["punteggio_riferimento"]])) $punteggi[$criterio["punteggio_riferimento"]] = [];
              if (!isset($punteggi[$criterio["punteggio_riferimento"]][$cod_part])) $punteggi[$criterio["punteggio_riferimento"]][$cod_part] = 0;
              $punteggi[$criterio["punteggio_riferimento"]][$cod_part] += $punt;
            }
          }
        }
      }
      if (count($punteggi) > 0) {
        foreach($punteggi AS $punteggio_riferimento => $part) {
          foreach ($part as $codice_part => $valore) {
            ?>
              $('#punteggio_<? echo $codice_part ?>_<? echo $punteggio_riferimento ?>').val('<?= $valore; ?>');
            <?
          }
        }
      }
    } else {
      ?>
      alert('Punteggi non attribuiti');
      <?
    }
  }
?>
