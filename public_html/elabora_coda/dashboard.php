<?
  if (isset($_SESSION["codice_utente"]) && $_SESSION["gerarchia"]==="0") {
    $qu_list = $pdo->query("SELECT MIN(b_coda.timestamp_creazione) AS firstOccurence, min(b_coda.codice) AS codice, count(b_coda.codice) AS comunicazioni,
                            b_enti.denominazione, b_enti.pec AS principale, b_pec.pec, b_coda.codice_ente
                            FROM b_coda
                            LEFT JOIN b_enti ON b_coda.codice_ente = b_enti.codice
                            LEFT JOIN b_pec ON b_coda.codice_pec = b_pec.codice
                            GROUP BY b_enti.codice, b_coda.codice_pec ORDER BY b_coda.timestamp");
    if ($qu_list->rowCount() > 0) {
    ?>
    <div style="display:none;" id="qu_list">
      <button class="submit_big" onClick="elimina('-1','elabora_coda');">Svuota coda da ambienti test</button>
      <table width="100%" class="elenco">
        <thead>
          <tr>
            <td width="50%">Info</td>
            <td>Timestamp</td>
            <td>Messaggi</td>
            <td>Segnala</td>
            <td>Smaltisci</td>
          </tr>
        </thead>
        <tbody>
          <?
            while($qu = $qu_list->fetch(PDO::FETCH_ASSOC)) {
              $integrazione = false;
              if (file_exists("{$root}/inc/integrazioni/{$qu["codice_ente"]}/communicator.bridge.class.php")) $integrazione = true;
              ?>
              <tr id="qu-<?= $qu["codice"] ?>">
                <td>
                  <?= ($integrazione) ? "<strong style='color:#090'>INTEGRAZIONE</strong><br>" : ""; ?>
                  Ente: <strong><?= $qu["denominazione"] ?></strong><br>
                  PEC Principale: <strong><?= $qu["principale"] ?></strong><br>
                  PEC Interessata : <strong><?= (empty($qu["pec"])) ? $qu["principale"] : $qu["pec"] ?></strong><br><br>
                </td>
                <td><?= mysql2datetime($qu["firstOccurence"]) ?></td>
                <td><?= $qu["comunicazioni"] ?></td>
                <td style="text-align:center">
                  <button class="btn-round" style="background-color:#00C" onClick="disabilita('<? echo $qu["codice"] ?>','elabora_coda');"><span class="fa fa-send"></span></button>
                </td>
                <td style="text-align:center; width:10">
                  <a href="/force_coda.php?codice_ente=<?= $qu["codice_ente"] ?>" style="background-color:#0AC" target="_blank" class="btn-round" ><span class="fa fa-refresh"></span></a>
                </td>
              </tr>
              <?
            }
          ?>
        </tbody>
      </table>
    </div>
    <button onClick='$("#qu_list").dialog({modal: true,width: "1024px",title: "Coda"});' class="submit_big">Visualizza coda</button>
    <?
    }
  }
?>
