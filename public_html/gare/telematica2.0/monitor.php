<?
  if (!isset($config) && isset($_GET["cod"]) && isset($_GET["codice_lotto"])) {
    session_start();
    include("../../../config.php");
    include_once($root."/inc/funzioni.php");
;
    if (is_operatore()) {
      $codice = $_GET["cod"];
      $codice_lotto = $_GET["codice_lotto"];
      $bind = array();
      $bind[":codice"] = $codice;
      $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
      $strsql  = "SELECT b_gare.*, b_procedure.invito, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
                  WHERE b_gare.codice = :codice AND b_gare.annullata = 'N' AND b_modalita.online = 'S'
                  AND codice_gestore = :codice_ente
                  AND (pubblica = '2' OR pubblica = '1') ";
      $risultato = $pdo->bindAndExec($strsql,$bind);
      if ($risultato->rowCount() > 0) {
        $record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
      }
    }
  }

  if (!empty($record_gara) && $record_gara["seduta_pubblica"] == "S" && isset($codice_lotto) && is_operatore()) {
    if (($record_gara["annullata"] == "N") && (strtotime($record_gara["data_apertura"]) < time())) {

      $filtro_mercato = "";

      $bind = array();
      $bind[":codice"]=$record_gara["procedura"];

      $strsql = "SELECT * FROM b_procedure WHERE mercato_elettronico = 'S' AND codice = :codice";
      $ris_mercato = $pdo->bindAndExec($strsql,$bind);
      if ($ris_mercato->rowCount()>0) $filtro_mercato = " AND mercato_elettronico = 'S' ";

      $bind = array();
      $bind[":codice"]=$record_gara["criterio"];

      $sql = "SELECT * FROM b_criteri_buste WHERE codice_criterio= :codice " . $filtro_mercato . " ORDER BY ordinamento ";
      $buste_monitor = $pdo->bindAndExec($sql,$bind);
      $buste_monitor = $buste_monitor->fetchAll(PDO::FETCH_ASSOC);
      $bind = array(':codice' => $record_gara["codice"], ":codice_utente" => $_SESSION["codice_utente"],":codice_lotto"=>$codice_lotto);
      $sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_utente = :codice_utente AND (conferma = TRUE OR conferma IS NULL)";
      $ris_partecipazione_confermata = $pdo->bindAndExec($sql,$bind);
      if ($ris_partecipazione_confermata->rowCount() > 0) {
        $bind = array();
        $bind[":codice_lotto"] = $codice_lotto;
        $bind[":codice_gara"] = $record_gara["codice"];
        $sql  = "SELECT * FROM b_buste WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND aperto = 'S'";
        $ris_exist = $pdo->bindAndExec($sql,$bind);
        if ($ris_exist->rowCount()>0) {
          $bind = array();
          $bind[":codice"]=$record_gara["codice"];
          $bind[":codice_lotto"]=$codice_lotto;
          $sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0  AND (conferma = TRUE OR conferma IS NULL)";
          $ris_partecipanti = $pdo->bindAndExec($sql,$bind);
          if ($ris_partecipanti->rowCount()>0) {
            $bind = array();
						$bind[":codice_gara"] = $record_gara["codice"];
						$bind[":codice_lotto"] = $codice_lotto;
						$sql_punteggi  = "SELECT codice_punteggio FROM r_punteggi_gare WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto GROUP BY codice_punteggio";
						$ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
						if ($ris_punteggio->rowCount()>0) {
							$punteggiAssegnati = $ris_punteggio->fetchAll(PDO::FETCH_COLUMN);
						}
          ?><br>
          <h1><?= traduci("Apertura buste") ?></h1>
          <table  width="100%">
            <thead>
              <tr>
                <td><?= traduci("Partita IVA") ?></td>
                <td><?= traduci("Ragione Sociale") ?></td>
                <?
                if (count($buste_monitor)>0) {
                  foreach($buste_monitor AS $busta) {
                  ?>
                    <td width="10"><? echo traduci($busta["nome"]) ?></td>
                  <?
                  }
                }	?>
                <? if (!empty($punteggiAssegnati)) { 
                  foreach($punteggiAssegnati AS $codice_punteggio) {
                    ?>
                    <td width="10">
                      <?= $pdo->go("SELECT nome FROM b_criteri_punteggi WHERE codice = :codice",[":codice"=>$codice_punteggio])->fetch(PDO::FETCH_COLUMN); ?>
                    </td>
                    <?
                  }
                }?>
              </tr>
            </thead>
            <tbody>
            <?
            while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
              ?>
              <tr>
              			<td width="10">
              				<strong><? echo $record_partecipante["partita_iva"] ?></strong></td>
              				<td><? if ($record_partecipante["tipo"] != "") echo "<strong>" . traduci("RAGGRUPPAMENTO") . "</strong> - " ?><? echo $record_partecipante["ragione_sociale"] ?>    </td>
              				<?
              					if (count($buste_monitor)>0) {
              					foreach($buste_monitor AS $busta) {
              								$bind = array();
              								$bind[":codice_partecipante"] = $record_partecipante["codice"];
              								$bind[":codice_busta"] = $busta["codice"];
              								$bind[":codice_gara"] = $record_gara["codice"];
              								$sql  = "SELECT * FROM b_buste WHERE codice_partecipante = :codice_partecipante AND codice_busta  = :codice_busta AND codice_gara = :codice_gara AND aperto = 'S'";
              								$ris_exist = $pdo->bindAndExec($sql,$bind);
              								?>
              								<td width="150" id="<? echo $record_partecipante["codice"] . "_" . $busta["codice"] ?>" style="text-align:center">
              								<?
              								if ($ris_exist->rowCount()>0) {
                                $rec_busta = $ris_exist->fetch(PDO::FETCH_ASSOC);
              									?>
              									<strong style="color:#0C0"><?= traduci("Aperta") ?></strong><br>
                                <?= mysql2datetime($rec_busta["timestamp"]) ?>
              									<? } else {
                                  if ($record_partecipante["ammesso"] == "S" && $record_partecipante["escluso"] == "N") { ?>
                                  <strong style="color:#C00"><?= traduci("Non aperta") ?></strong>
              									<? } else {
                                  if ($record_partecipante["ammesso"] == "N") {?>
                                    <strong style="color:#333"><?= traduci("Non ammesso alle fasi successive") ?></strong>
                                  <? } else { ?>
                                    <strong style="color:#333"><?= traduci("Escluso") ?></strong>
                                <? 
                                  }
                                }
                              } ?>
              									</td>
              							<?	}
              						}
                          if (!empty($punteggiAssegnati)) { 
                            foreach($punteggiAssegnati AS $codice_punteggio) {
                              ?>
                              <td style="text-align:center">
                              <?
                                $bind = array();
                                $bind[":codice_partecipante"] = $record_partecipante["codice"];
                                $bind[":codice_punteggio"] = $codice_punteggio;
                                $sql_punteggi  = "SELECT punteggio FROM r_punteggi_gare WHERE codice_partecipante = :codice_partecipante AND codice_punteggio = :codice_punteggio ";
                                $ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
                                if ($ris_punteggio->rowCount()>0) {
                                  echo number_format($ris_punteggio->fetch(PDO::FETCH_COLUMN),3,",","");
                                }
                              ?>  
                              </td>
                              <?
                            }
                          }
                ?>
              </tr>
              <?
              }
          ?></tbody>
          </table>
          <?
          }
        }
      }
    }
  }
?>
