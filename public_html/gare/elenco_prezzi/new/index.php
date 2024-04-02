<? if (isset($record)) { ?>
	<h1>ELENCO CRITERI</h1>
  <?
    $sql = "SELECT b_valutazione_tecnica.*, b_lotti.descrizione AS lotto
            FROM b_valutazione_tecnica LEFT JOIN b_lotti ON b_valutazione_tecnica.codice_lotto = b_lotti.codice
            WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.valutazione = 'E'";
    $ris = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));
    if ($ris->rowCount() > 0) {
      if (!$lock) {
      ?>
      <div class="box">
        <table width="100%">
          <tbody>
            <tr>
              <td style="text-align: center;vertical-align: middle;"><a href="#" onClick="$('#massive').slideToggle()">Caricamento massivo</strong></td>
            </tr>
          </tbody>
        </table>
        <form id="massive" action="massive.php" method="post" enctype="multipart/form-data" style="display:none">
          <input type="hidden" name="codice_gara" value="<? echo $_GET["codice"]; ?>">
          <table class="dettaglio" width="100%">
            <tbody>
            <tr>
              <td width="25%">
                <img target="_blank" src="../../img/xls.png" alt="Modello"/><a href="tracciato.csv" download style="vertical-align:super">Modello CSV</a>
              </td>
              <td width="50%">
                  <input type="file" name="tracciato" id="file">
              </td>
              <td width="5%">
                  <input type="submit" name="submit" value="Upload">
              </td>
            </tr>
            </tbody>
          </table>
          <h2 style="text-align:center">Guida alla compilazione del CSV</h2>
          Il file da caricare dovrà essere generato includendo ogni campo in doppi apici <strong>(")</strong> ed utilizzando il separatore punto e virgola <strong>(;)</strong>
          <table>
            <tr><td><strong>ID_ELENCO</strong></td><td>Riportare l'ID corrispondente alla prima colonna della tabella che segue</td></tr>
            <tr><td><strong>TIPO</strong></td><td>Corpo / Misura</td></tr>
            <tr><td><strong>DESCRIZIONE</strong></td><td></td></tr>
            <tr><td><strong>UNITA</strong></td><td>Unit&agrave; di misura della voce</td></tr>
            <tr><td><strong>QUANTITA</strong></td><td>Quantit&agrave; prevista</td></tr>
          </table>
          <strong>Tutti i campi sono obbligatori</strong>
      </div>
      <? } ?>
      <table width="100%">
        <thead>
          <tr>
            <th width="10"></th>
            <th width="10">ID</th>
            <th>Descrizione</th>
            <th width="100">Voci</th>
            <th width="10"></th>
          </tr>
        </thead>
        <tbody>
          <?
            while($crit = $ris->fetch(PDO::FETCH_ASSOC)) {
              $sql_check = "SELECT * FROM b_elenco_prezzi WHERE codice_criterio = :codice_criterio ";
              $ris_check = $pdo->bindAndExec($sql_check,array(":codice_criterio"=>$crit["codice"]));
              $voci = $ris_check->rowCount();
              $colore = "#C00";
              if ($voci > 0) $colore = "#3C0";
              ?>
              <tr>
                <td><div class="status_indicator" style="background-color: <?= $colore ?>"></div></td>
                <td><?= $crit["codice"] ?></td>
                <td>
                  <?= $crit["descrizione"] ?>
									<?= (!empty($crit["lotto"])) ? "<br><small><strong>Lotto: " . $crit["lotto"] . "</strong></small>" : "" ?>
                </td>
                <td style="text-align:center"><?= $voci ?></td>
                <td>
									<a class="btn-round btn-warning" href="new/edit.php?codice=<?= $crit["codice"] ?>">
					          <span class="fa fa-pencil"></span>
					        </a>
                </td>
              </tr>
              <?
            }
          ?>
        </tbody>
      </table>
			<? include($root."/gare/ritorna.php"); ?>
      <?
    } else {
      echo "<h2>Elenco prezzi non necessario</h2>";
    }
  } else {
  	echo "<h1>Gara non trovata</h1>";
  }
?>
