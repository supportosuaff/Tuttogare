<?
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");

  $edit = false;
  if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]["codice"])) {
    $edit = check_permessi("report", $_SESSION["codice_utente"]);
    if (!$edit) {
      echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
      die();
    }
  } else {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  }

  if($edit) {
    $sql = "SELECT g.codice, g.id, g.cig, g.prezzoBase AS importo, g.oggetto, g.data_scadenza, g.ribasso, i.importo_base, i.importo_oneri_ribasso, i.importo_personale, i.importo_oneri_no_ribasso, t.tipologia, c.criterio, e.denominazione, p.nome AS procedura
            FROM b_gare AS g
            JOIN b_enti AS e ON e.codice = g.codice_ente
            JOIN b_importi_gara AS i ON i.codice_gara = g.codice
            JOIN b_tipologie AS t ON t.codice = g.tipologia
            JOIN b_criteri AS c ON c.codice = g.criterio
            JOIN b_procedure AS p ON p.codice = g.procedura
            WHERE codice_gestore = {$_SESSION["ente"]["codice"]}
            GROUP BY g.codice
            ORDER BY g.data_scadenza
            ";

    $ris = $pdo->bindAndExec($sql);
    if($ris->rowCount() > 0) {
      ?>
      <table border="1" cellpadding="0" cellspacing="0" style="margin-bottom: 10px; width: 100%;">
        <thead>
          <tr>
            <td><strong>ID</strong></td>
            <td><strong>ENTE</strong></td>
            <td><strong>CIG</strong></td>
            <td><strong>OGGETTO</strong></td>
            <td><strong>TIPOLOGIA</strong></td>
            <td><strong>CRITERIO</strong></td>
            <td><strong>PROCEDURA</strong></td>
            <td><strong>IMPORTO</strong></td>
            <td><strong>IMPORTO AGGIUDICAZIONE</strong></td>
            <td><strong>PARTECIPANTI</strong></td>
            <td><strong>COMMISSIONE</strong></td>
            <td><strong>AGGIUDICATARIO</strong></td>
            <td><strong>DATA SCADENZA</strong></td>
          </tr>
        </thead>
        <tbody>
        <?
        $count = $pdo->prepare("SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND conferma <> 'N'");
        $aggiudicatario = $pdo->prepare("SELECT p.ragione_sociale, p.partita_iva, oe.citta_legale, oe.provincia_legale FROM r_partecipanti AS p JOIN b_operatori_economici AS oe ON p.codice_operatore = oe.codice WHERE codice_gara = :codice_gara AND p.primo = 'S'");
        $commissione = $pdo->prepare("SELECT c.* FROM b_commissioni AS c WHERE codice_gara = :codice_gara");
        while ($gara = $ris->fetch(PDO::FETCH_ASSOC)) {
          ?>
          <tr>
            <td><?= $gara["id"] ?></td>
            <td><?= $gara["denominazione"] ?></td>
            <td><?= $gara["cig"] ?></td>
            <td><?= $gara["oggetto"] ?></td>
            <td><?= $gara["tipologia"] ?></td>
            <td><?= $gara["criterio"] ?></td>
            <td><?= $gara["procedura"] ?></td>
            <td><?= number_format($gara["importo"], 2, ',', '.') ?></td>
            <td>
              <?
              if(!empty($gara["ribasso"]) && $gara["ribasso"] > 0) {
                $somma = $gara["importo_base"]; // + $gara["importo_oneri_ribasso"] + $gara["importo_personale"]
                if ($gara["norma"] == "2023-36") {
                  $somma = $somma - (($somma * $gara["ribasso"]) / 100) + $gara["importo_oneri_no_ribasso"] + $gara["importo_personale"];
                } else {
                  $somma = $somma - (($somma * $gara["ribasso"]) / 100) + $gara["importo_oneri_no_ribasso"];
                }
                echo number_format($somma, 2, ',', '.');
              }
              ?>
            </td>
            <td>
              <?
                $count->bindValue(':codice_gara', $gara["codice"]);
                $count->execute();
                echo $count->rowCount();
              ?>
            </td>
            <td>
              <?
              $commissione->bindValue(':codice_gara', $gara["codice"]);
              $commissione->execute();
              if($commissione->rowCount() > 0) {
                while ($commissario = $commissione->fetch(PDO::FETCH_ASSOC)) {
                  echo "<strong>{$commissario["ruolo"]}</strong>: {$commissario["titolo"]} {$commissario["cognome"]} {$commissario["nome"]} <br>";
                }
              }
              ?>
            </td>
            <td>
              <?
                $aggiudicatario->bindValue(':codice_gara', $gara["codice"]);
                $aggiudicatario->execute();
                if($aggiudicatario->rowCount() > 0) {
                  $agg = $aggiudicatario->fetch(PDO::FETCH_ASSOC);
                  echo "{$agg["ragione_sociale"]}, {$agg["citta_legale"]} ({$agg["provincia_legale"]})";
                }
              ?>
            </td>
            <td><?= $gara["data_scadenza"] ?></td>
          </tr>
          <?
        }
        ?>
        </tbody>
      </table>
      <?
    }
  }


?>
