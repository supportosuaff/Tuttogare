<?
  if (isset($types)) { 
    $results = [];
    foreach($types AS $flag => $label) {
      $bind = array(':codice_ente' => $_SESSION["ente"]["codice"],":tipologia"=>$flag);
      $strsql  = "SELECT * FROM b_bandi_albo WHERE (codice_ente = :codice_ente OR codice_gestore = :codice_ente) AND pubblica > ";
      $strsql .= (!isset($_SESSION["codice_utente"])) ? "1" : "0";
      $strsql .= " AND tipologia = :tipologia ";
      if (isset($_GET["scadute"])) {
        if ($_GET["scadute"]) {
          $strsql .= " AND b_bandi_albo.data_scadenza < NOW() AND b_bandi_albo.data_scadenza > 0  ";
        } else {
          $strsql .= " AND (b_bandi_albo.data_scadenza >= NOW() OR b_bandi_albo.data_scadenza = 0) ";
        }
      }
      if (!empty($_GET["codice_ente"])) {
        $bind[":codice_beneficiario"] = $_GET["codice_ente"];
        $strsql .= " AND codice_ente = :codice_beneficiario ";
      }
      $strsql .= "ORDER BY id DESC, codice DESC" ;
      $risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
      if ($risultato->rowCount() > 0) $results[$flag] = $risultato;
    }
    if (count($results) > 0) {
      ?>
      <div id="tabs">
      <?
      if (count($results) > 1) {
        ?>
        <ul>
          <? foreach($results AS $flag => $risultato) { ?>
            <li><a href="#list-<?= $flag ?>"><?= traduci($types[$flag]) ?></a></li>
          <? } ?>
        </ul>
        <?
      }
      foreach($results AS $flag => $risultato){
    ?>
    <div id="list-<?= $flag ?>">
      <table width="100%" id="bandi" class="elenco">
        <thead>
          <tr>
            <td>ID</td>
            <td>Oggetto</td>
            <td>Scadenza</td>
            <? if ($_SESSION["ente"]["tipo"] == "SUA") { 
                $enteBeneficiario = $pdo->prepare("SELECT denominazione FROM b_enti WHERE codice = :codice");
                ?>
              <td width="200"><?= traduci("Ente") ?></td>
            <? } ?>
          </tr>
        </thead>
        <tbody>
          <?
          while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
          ?>
          <tr id="<? echo $record["codice"] ?>">
            <td width="5%"><? echo $record["id"] ?></td>
            <td width="75%">
              <? if ($record["annullata"] == "S") {
                echo "<strong>Annullata con atto n. " . $record["numero_annullamento"] . " del " . mysql2date($record["data_annullamento"]) . "</strong> - ";
              } ?>
              <a href="/albo_fornitori/id<? echo $record["codice"] ?>-dettaglio" title="Dettagli bando"><? echo $record["oggetto"] ?></a><br>
              <?= $record["descrizione"] ?>
            </td>
            <td>
              <? if ($record["data_scadenza"] > 0) {
                ?><span style="display:none"><? echo $record["data_scadenza"] ?></span><?
                echo mysql2datetime($record["data_scadenza"]);
              } ?>
            </td>
            <? if ($_SESSION["ente"]["tipo"] == "SUA") {
							$enteBeneficiario->bindValue(":codice",$record["codice_ente"]);
							$enteBeneficiario->execute(); ?>
							<td>
								<?= $enteBeneficiario->fetch(PDO::FETCH_ASSOC)["denominazione"]; ?>
							</td>
						<? } ?>
          </tr>
          <?
          }
          ?>
        </tbody>
      </table>
    </div>
  <? } ?>
    </div>
    <? if (count($results) > 1) { ?>
      <script>
        $("#tabs").tabs();
      </script>
    <? } ?>
  <?
  } else { ?>
    <h2 style="text-align:center">Nessun bando</h2>
  <? } ?>
  <div class="clear"></div>
  <? } ?>