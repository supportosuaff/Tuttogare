<?
include_once "../../../config.php";
$disable_alert_sessione = true;
include_once $root . "/layout/top.php";
include_once $root . "/inc/p7m.class.php" ;
if (!is_operatore()) {
  echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
  die();
} else {
  $ris_operatore = $pdo->bindAndExec("SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente", array(':codice_utente' => $_SESSION["codice_utente"]));
  $operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);
  if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && isset($_GET["codice"])) {
    $href_contratto = null;
    $codice_contratto = $_GET["codice"];
    $sql = "SELECT b_contratti.*, b_conf_modalita_stipula.invio_remoto, b_conf_modalita_stipula.etichetta as modalita_di_stipula
            FROM b_contratti
            JOIN b_conf_modalita_stipula ON b_conf_modalita_stipula.codice = b_contratti.modalita_stipula
            JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contratto = b_contratti.codice
            JOIN b_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente
            WHERE b_contraenti.codice_utente = :codice_utente
            AND b_contratti.codice = :codice_contratto
            AND r_contratti_contraenti.codice_capogruppo = 0";
    $ris = $pdo->bindAndExec($sql, array(':codice_utente' => $_SESSION["record_utente"]["codice"], ':codice_contratto' => $codice_contratto));
    if($ris->rowCount() > 0) {
      $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
      $href_contratto = "?codice={$rec_contratto["codice"]}";
      ?>
      <style media="screen">
        .btn {
          -webkit-border-radius: 100;
          -moz-border-radius: 100;
          border: none;
          border-radius: 100%;
          color: #ffffff;
          background: #3498db;
          padding: 5px 11px 5px 11px;
          text-decoration: none;
          display: inline-block;
          appearance: none;
          box-shadow: none;
          text-decoration: none;
          cursor: pointer;
        }
        .btn-circle {
          width: 35px;
          height: 35px;
        }
        .btn:hover {
          background: #3cb0fd;
          text-decoration: none;
          color: #FFF;
          cursor: pointer;
        }
      </style>
      <h1>PANNELLO DI GESTIONE - CONTRATTO #<?= $rec_contratto["codice"] ?></h1>
      <h2>Oggetto: <small><?= $rec_contratto["oggetto"] ?></small></h2>
      <h2 style="text-align:right; border-bottom:10px solid #009688; margin-bottom:20px;">
        Tipologia: <small><strong><?= $rec_contratto["modalita_di_stipula"] ?></strong></small>
        &nbsp;|&nbsp;Importo: <small><strong>&euro; <?= $rec_contratto["importo_totale"] ?></strong></small>
        <? if(!empty($rec_contratto["cig"])) echo "&nbsp;|&nbsp;CIG: <small><strong>{$rec_contratto["cig"]}</strong></small>" ?>
        <? if(!empty($rec_contratto["cup"])) echo "&nbsp;|&nbsp;CUP: <small><strong>{$rec_contratto["cup"]}</strong></small>" ?>
      </h2>
      <?
      $bind = array(":codice"=>$rec_contratto["codice"]);
      $sql_modulistica = "SELECT * FROM b_modulistica_contratto WHERE codice_contratto = :codice ";
      $ris_modulistica = $pdo->bindAndExec($sql_modulistica,$bind);
      if($ris_modulistica->rowCount() > 0) {
        ?>
        <script type="text/javascript" src="/js/resumable.js"></script>
        <script type="text/javascript" src="resumable-uploader.js"></script>
        <script type="text/javascript" src="/js/spark-md5.min.js"></script>
        <script>
          var uploader = new Array();
        </script>
        <form id="modulo" rel="validate" method="post" target="_self" action="save.php">
          <input type="hidden" name="codice_contratto" value="<?= $rec_contratto["codice"] ?>">
          <table id="tab_moduli" width="100%">
            <thead>
              <tr>
                <td>Documento</td>
                <td colspan="2">Allegato</td>
              </tr>
              <tr>
                <td colspan="3">
                  N.B. Tutti i documenti devono essere in formato PDF, firmati digitalmente (CADES/PADES);
                </td>
              </tr>
            </thead>
            <tbody>
              <?
              $sth = $pdo->prepare("SELECT * FROM b_allegati_contratto WHERE codice_modulo = :codice_modulo AND codice_operatore = :codice_operatore");
              $sth->bindValue(':codice_operatore', $operatore["codice"]);
              while ($record_modulo = $ris_modulistica->fetch(PDO::FETCH_ASSOC)) {
                $sth->bindValue(':codice_modulo', $record_modulo["codice"]);
                $sth->execute();
                $allegato = get_campi('b_allegati_contratto');
                $allegato["codice"] = 0;
                if($sth->rowCount() > 0) {
                  $allegato = $sth->fetch(PDO::FETCH_ASSOC);
                }
                ?>
                <tr>
                  <td width="70%"><?= $record_modulo["titolo"]; ?>: <? if($record_modulo["obbligatorio"] == "S") echo '*';
                  if(!empty($record_modulo["riferimento"]) && file_exists($config["pub_doc_folder"]."/allegati/contratti/{$record_modulo["codice_contratto"]}/{$record_modulo["riferimento"]}")) {
                      ?><br><a href="/documenti/allegati/contratti/<?= $record_modulo["codice_contratto"] . "/" . $record_modulo["nome_file"] ?>">Modello</a> <?
                    }
                  ?></td>
                  <td width="25%" colspan="2">
                    <input type="hidden" name="modulo[<?= $record_modulo["codice"] ?>][codice]" value="<?= $allegato["codice"] ?>">
                    <input type="hidden" name="modulo[<?= $record_modulo["codice"] ?>][codice_modulo]" value="<?= $record_modulo["codice"] ?>">
                    <input type="hidden" class="md5" name="modulo[<?= $record_modulo["codice"] ?>][md5_file]" id="md5_file_<?= $record_modulo["codice"] ?>" title="File">
                    <input type="hidden" class="filechunk <? if ($record_modulo["obbligatorio"] == "S") echo "obbligatorio" ?>" id="filechunk_<?= $record_modulo["codice"] ?>" name="modulo[<?= $record_modulo["codice"] ?>][filechunk]" title="Allegato">
                    <input type="hidden" class="terminato" id="terminato_<?= $record_modulo["codice"] ?>" title="Termine upload">
                    <div id="nome_file_<?= $record_modulo["codice"] ?>" style="float:left;">
                      <?
                      if(!empty($allegato["nome_file"])) {
                        ?><strong><?= $allegato["nome_file"] ?></strong> <a class="btn" href="#" onclick="$('#note_<?= $allegato["codice"] ?>').dialog({title: 'Informazioni firma', modal: true});"><i class="fa fa-info"></i></a><br> <?
                        $file = "{$config["arch_folder"]}/allegati_contratto/{$allegato["codice_contratto"]}/{$allegato["riferimento"]}";
                        $p7m = new P7Manager($file);
                        $certificati = $p7m->extractSignatures();
                        ?>
                        <div id="note_<?= $allegato["codice"] ?>" style="display:none;">
                          <ul>
                            <?
                            foreach ($certificati as $esito) {
                              ?>
                              <li>
                                <?
                                if (isset($data["subject"]["commonName"])) echo "<h1>" . $data["subject"]["commonName"] . "</h1>";
                                if (isset($data["subject"]["organizationName"])) echo "<strong>" . $data["subject"]["organizationName"] . "</strong><br>";
                                if (isset($data["subject"]["title"])) echo  $data["subject"]["title"] . "<br>";
                                if (isset($data["issuer"]["organizationName"])) echo  "<br>Emesso da:<strong> " . $data["issuer"]["organizationName"] . "</strong>";
                                $data = openssl_x509_parse($esito,false);
                                $validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
                                $validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
                                echo "<br><br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
                                ?>
                              </li>
                              <?
                            }
                            ?>
                          </ul>
                        </div>
                        <?
                        $file_info = new finfo(FILEINFO_MIME_TYPE);
                        $mime_type = $file_info->buffer(file_get_contents($file));
                        ?><a href="download_allegato.php?codice=<?= $allegato["codice"] ?>" title="Scarica Allegato" target="_blank"><img src="/img/p7m.png" alt="allegato p7m" width="25" /></a><?
                        if (strpos($mime_type,"pdf") === false) {
                          ?><a href="open_p7m.php?codice=<?= $allegato["codice"] ?>" title="Estrai Contenuto" target="_blank"><img src="/img/download.png" alt="Estrai Contenuto" width="25" /></a><?
                        }
                      }
                      ?>
                    </div>
                    <div id="modulistica_<?= $record_modulo["codice"] ?>" rel="<?= $record_modulo["codice"] ?>" class="scegli_file" style="float:right"><img src="/img/folder.png" height="30" style="vertical-align:middle"></div>
                    <div class="clear"></div>
                    <div id="progress_bar_<?= $record_modulo["codice"] ?>" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
                    <script>
                      tmp = (function($){
                        return (new ResumableUploader($("#modulistica_<?= $record_modulo["codice"] ?>")));
                      })(jQuery);
                      uploader.push(tmp);
                    </script>
                  </td>
                </tr>
                <?
              }
              $ris_altri_allegati = $pdo->bindAndExec("SELECT * FROM b_allegati_contratto WHERE codice_modulo = 0 AND tipologia <> 'contratto' AND codice_operatore = :codice_operatore AND codice_contratto = :codice_contratto", array(':codice_operatore' => $operatore["codice"], ':codice_contratto' => $rec_contratto["codice"]));
              if($ris_altri_allegati->rowCount() > 0) {
                while ($modulo = $ris_altri_allegati->fetch(PDO::FETCH_ASSOC)) {
                  $id = $modulo["codice"];
  								include("tr_modulo.php");
                }
              }
              ?>
            </tbody>
          </table>
          <button class="submit_big" style="background-color:rgb(238, 163, 33); border:none;" onClick="aggiungi('tr_modulo.php','#tab_moduli');return false;">Aggiungi File</button>
  				<input type="submit" class="submit_big" value="Salva">
        </form>
        <?
      } else {
        ?><h3 class="ui-state-error">Nessun modulo richiesto.</h3><?
      }
    }
  }
}
include_once $root . "/contratti_operatore/ritorna_pannello_contratto.php";
include_once $root . "/layout/bottom.php";
?>
