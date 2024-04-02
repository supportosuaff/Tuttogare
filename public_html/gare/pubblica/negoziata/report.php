<?
  if (isset($codice_gara)) {
    $bind=array();
    $bind[":codice_gara"]=$codice_gara;
    $gara = $pdo->bindAndExec("SELECT oggetto FROM b_gare WHERE codice = :codice_gara LIMIT 0,1", $bind)->fetchAll(PDO::FETCH_ASSOC);
    $sql_estrazione = "SELECT * FROM b_estrazioni WHERE codice_gara = :codice_gara";
    $ris_estrazione = $pdo->bindAndExec($sql_estrazione,$bind);
    if ($ris_estrazione->rowCount()>0) {
      $estrazione = $ris_estrazione->fetch(PDO::FETCH_ASSOC);
      ?>
      <h2>Verbale Estrazione - <?= mysql2datetime($estrazione["timestamp"]) ?></h2>
      <p>Gara: <strong><?= !empty($gara[0]["oggetto"]) ? $gara[0]["oggetto"] : null ?></strong></p>
      <table width="100%">
      <?
      if ($estrazione["codice_bando"] != 0) {
        $bind = array();
        $bind[":codice"] = $estrazione["codice_bando"];
        $sql_bando = "SELECT * FROM b_bandi_albo WHERE codice = :codice";
        $ris_bando = $pdo->bindAndExec($sql_bando,$bind);
        if ($ris_bando->rowCount()>0) {
          $bando = $ris_bando->fetch(PDO::FETCH_ASSOC);
          ?>
          <tr><td class="etichetta">Albo di riferimento</td><td colspan="3"><?= $bando["oggetto"] ?></td></tr>
          <?
        }
      }
      ?>
      <tr>
        <td class="etichetta">Partecipanti richiesti</td><td><?= $estrazione["numero_partecipanti"] ?></td>
        <td class="etichetta">Esclusioni</td>
        <td>
          <?
            switch ($estrazione["esclusioni"]) {
              case "A": echo "Aggiudicatari"; break;
              case "I": echo "Invitati"; break;
              default: echo "Nessuno";
            }
          ?>
        </td>
      </tr>
      <tr>
        <td class="etichetta">Filtro CPV</td>
        <td>
          <?
            if ($estrazione["filtro_cpv"] == "S") {
              echo "Attivo" . "<br>";
              $string_cpv = "";
              $strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice_gara ORDER BY b_cpv.codice";
              $risultato_cpv = $pdo->bindAndExec($strsql,array(":codice_gara"=>$codice_gara));
              if ($risultato_cpv->rowCount()>0) {
                $string_cpv = "<ul>";
                while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
                  $string_cpv .= "<li><strong>" . $rec_cpv["codice"] . "</strong> - " . $rec_cpv["descrizione"] . "</li>";
                }
                $string_cpv .= "</ul>";
              }
              echo $string_cpv;
            } else {
              echo "Disattivo";              
            }
          ?>
        </td>
        <? if ($estrazione["filtro_soa"] != "N") { ?>
        <td class="etichetta">Filtro SOA</td>
        <td>
          <?
            ob_start();
            switch ($estrazione["filtro_soa"]) {
              case "S": echo "Solo Categoria"; break;
              case "C": echo "Categoria e classifica"; break;
              case "F": echo "Fatturato su categoria"; echo " / " . $estrazione["anni"] . " anni"; break;
              default: echo "Disattivo";
            }
            $stato = ob_get_clean();
            echo $stato;
            if ($stato != "Disattivo") {
              $strsql = "SELECT b_categorie_soa.*, b_qualificazione_lavori.tipo, SUM(b_qualificazione_lavori.importo_base) AS importo_base
              FROM b_qualificazione_lavori JOIN b_categorie_soa ON b_qualificazione_lavori.codice_categoria = b_categorie_soa.codice
              WHERE codice_gara = :codice_gara GROUP BY codice_gara, tipo, id ORDER BY b_qualificazione_lavori.tipo ";
              $ris_qualificazione_gara = $pdo->bindAndExec($strsql,[":codice_gara"=>$codice_gara]);
              if ($ris_qualificazione_gara->rowCount() > 0) {
                ?>
                <ul>
                <?
                  while($categoria = $ris_qualificazione_gara->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <li>
                      <?= $categoria["id"] ?> - <?= $categoria["descrizione"] ?>
                      <?
                        $bind = array();
                        $bind[":importo_base"] = $categoria["importo_base"];
                        $sql_classifica = "SELECT * FROM b_classifiche_soa WHERE attivo = 'S' AND minimo <= :importo_base AND (massimo >= :importo_base OR massimo = 0)";
                        $ris_classifica = $pdo->bindAndExec($sql_classifica,$bind);
                        if ($ris_classifica->rowCount() > 0) {
                          $classifica = $ris_classifica->fetch(PDO::FETCH_ASSOC);
                          ?>
                          - <?= $classifica["id"] ?>
                          <?
                        }
                      ?>
                    </li>
                    <?
                  }
                ?>
                </ul>
                <?
              }
            }
          ?>
        </td>
        <? } else if ($estrazione["filtro_progettazione"] != "N") { ?>
          <td class="etichetta">Filtro progettazione</td>
          <td>
            <?  
              ob_start();
              switch ($estrazione["filtro_progettazione"]) {
                case "S": echo "Attivo" . " / " . $estrazione["anni"] . " anni"; break;
                default: echo "Disattivo";
              }
              $stato = ob_get_clean();
              echo $stato;
              if ($stato != "Disattivo") {
                $bind = array();
                $bind[":codice_gara"] = $codice_gara;
                $sql_progettazione = "SELECT b_qualificazione_progettazione.importo, b_categorie_progettazione.id, b_categorie_progettazione.destinazione
                                      FROM b_qualificazione_progettazione JOIN b_categorie_progettazione ON b_qualificazione_progettazione.codice_categoria = b_categorie_progettazione.codice
                                      WHERE b_qualificazione_progettazione.codice_gara = :codice_gara ORDER BY b_qualificazione_progettazione.importo, b_qualificazione_progettazione.codice DESC LIMIT 0,1";
                $risultato_progettazione = $pdo->bindAndExec($sql_progettazione,$bind);
                if ($risultato_progettazione->rowCount() > 0) {
                  $risultato_progettazione = $risultato_progettazione->fetchAll(PDO::FETCH_ASSOC);
                  ?>
                  <ul>
                    <? 
                      foreach ($risultato_progettazione AS $categoria) {
                        ?>
                        <li><?= $categoria["id"] ?> - <?= $categoria["destinazione"] ?> - <?= $categoria["importo"] ?></li>
                        <?
                      }
                    ?>
                  </ul>
                  <?
                }
              }
            ?>
          </td>
          <?
        } else {
          ?>
          <td></td>
          <td></td>
          <?
        }
        ?>
      </tr>
      <tr>
        <td class="etichetta">Conteggio rotazione</td>
        <td colspan="3">
            <?
              switch ($estrazione["conteggio_rotazione"]) {
                case "cpv_2" : echo "CPV 2 cifre";  break;
                case "cpv_3" : echo "CPV 3 cifre"; break;
                case "cpv_4" : echo "CPV 4 cifre"; break;
                case "soa" : echo "SOA"; break;
                case "soa_classifica" : echo "SOA e classifica"; break;
                case "progettazione" : echo "Progettazione"; break;
                default: echo "Generale";
              }
          ?>
        </td>
      </tr>
      <tr><td class="etichetta">Sequenza</td><td colspan="3"><strong><?= $estrazione["sequenza"] ?></strong></td></tr>
      </table>
      <?
        $bind = array();
        $bind[":codice"] = $estrazione["codice"];
        $sql_estratti = "SELECT b_operatori_economici.ragione_sociale, r_estrazioni.* FROM
        b_operatori_economici JOIN r_estrazioni ON b_operatori_economici.codice = r_estrazioni.codice_operatore
        WHERE r_estrazioni.codice_estrazione = :codice ORDER BY identificativo = 0, identificativo ";
        $ris_estratti = $pdo->bindAndExec($sql_estratti,$bind);
        if ($ris_estratti->rowCount()>0) {
          ?>
          <h2>Risultati sorteggio</h2>
          <table width="100%">
            <thead>
              <tr>
                <th width="10%">#</th>
                <th width="70%">Ragione sociale</th>
                <th width="15%">Esito</th>
                <th width="5%">Cont. Rotazione</th>
              </tr>
            </thead>
            <?
              while($estratto = $ris_estratti->fetch(PDO::FETCH_ASSOC)) {
                $style="";
                $stato_estrazione = "";
                if ($estratto["selezionato"] == "S") {
                  if ($estratto["identificativo"]==0) {
                    $style="color:#FFF; font-weight:bold; background-color:#2fccc7";
                    $stato_estrazione = "Selezionato automaticamente";
                  } else {
                    $style="color:#FFF; font-weight:bold; background-color:#74c27a";
                    $stato_estrazione = "Selezionato";
                  }
                }
                if ($estratto["escluso"] == "S") {
                  $style="color:#fff; background-color:#be6b70";
                  $stato_estrazione = "Escluso";
                }
                if (!isset($html)) $style .= " !important";
                $style="style=\"" . $style . ";\"";
                ?>
                <tr <?= $style ?>>
                  <td width="10%"><?= $estratto["identificativo"] ?></td>
                  <td width="70%"><?= $estratto["ragione_sociale"] ?></td>
                  <td width="15%"><?= $stato_estrazione ?></td>
                  <td width="5%"><?= $estratto["conteggio"] ?></td>
                </tr>
                <?
              }
            ?>
          </table>
          <?
        }
    }
  }
?>
