<?
  @session_start();
  if(empty($root)) {
    include_once '../../config.php';
    include_once $root . '/inc/funzioni.php';
  }
  $access = FALSE;
  if(!empty($_SESSION["codice_utente"]) && !empty($_SESSION["ente"])) {
    $access = check_permessi("guue",$_SESSION["codice_utente"]);
    if (!$access) {
      echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
      die();
    }
  } else {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  }

  if($access) {
    ?>
    <a href="/guue/edit.php?cod=0<?= !empty($codice_gara) ? "&codice_gara=".$codice_gara : null ?>" title="Inserisci nuova pubblicazione">
      <div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Nuova Pubblicazione
      </div>
    </a>
    <?
    $bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
    $sql = "SELECT * FROM b_pubb_guue WHERE codice_ente = :codice_ente AND soft_delete = FALSE ";

    $sql_guue = "SELECT * FROM b_gestione_guue WHERE form = :form AND attivo = 'S'";
    $sth_guue = $pdo->prepare($sql_guue);

    if(!empty($codice_gara)) {
      $sql .= "AND codice_gara = :codice_gara ";
      $bind[":codice_gara"] = $codice_gara;
    }
    $sql .= "ORDER BY `b_pubb_guue`.`timestamp` DESC";
    $ris = $pdo->bindAndExec($sql, $bind);
    if($ris->rowCount() > 0) {
      ?>
      <table class="elenco" id="pubblicazioni_guue" width="100%" style="padding-top: 10px; padding-bottom: 10px;">
        <thead>
          <tr>
            <th></th>
            <th>ID</th>
            <th>ID Gara</th>
            <th>Tipologia</th>
            <th>Oggeto</th>
            <th width="120">Stato</th>
            <th width="150">Data di trasmissione</th>
            <th width="220">Info pubblicazione</th>
            <th width="30"></th>
            <th width="30">PDF GUUE</th>
            <? if($_SESSION["gerarchia"] === "0") {?><th width="30"></th><th width="30"></th><th width="30"></th><?} ?>
          </tr>
        </thead>
        <tbody>
          <?
          while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
            $numero_form = "f".(strlen($rec["numero_form"]) > 1 ? $rec["numero_form"] : "0".$rec["numero_form"]);
            // $tipologia = $pdo->getSQL($sql_guue, array(':form' => $numero_form));
            $tipologia = '';

            $sth_guue->bindValue(':form', $numero_form);
            $sth_guue->execute();
            if ($sth_guue->rowCount() > 0) {
              $info = $sth_guue->fetch(PDO::FETCH_ASSOC);
              $tipologia = $info["titolo"];
            }
            $echo = TRUE;
            if(!empty($rec["codice_gara"])) {
              $echo = check_permessi_gara(1,$rec["codice_gara"],$_SESSION["codice_utente"]);
            }
            if($echo) {
              ?>
              <tr id="guue_<?= $rec["codice"] ?>">
                <?
                $color = "#F7CA18";
                if($rec["stato"] == "PUBBLICATO") $color = "#26A65B";
                if($rec["stato"] == "PRONTO PER LA TRASMISSIONE") $color = "#4183D7";
                if($rec["stato"] == "TRASMESSO") $color = "#4183D7";
                if($rec["stato"] == "RIFIUTATO") $color = "#CF000F";
                ?>
                <td style="background:<?= $color ?>" width="1px"></td>
                <td><?= $rec["codice"] ?></td>
                <td style="text-align: center;"><?= !empty($rec["codice_gara"]) ? '<a href="/gare/pannello.php?codice='.$rec["codice_gara"].'">'.$rec["codice_gara"].'</a>' : '/' ?></td>
                <td><?= $tipologia ?></td>
                <td>
                  <?
                  if(in_array($rec["stato"], array('TRASMESSO', 'PUBBLICATO'))) {
                    echo $rec["titolo_pubblicazione"];
                  } else {
                    ?><a href="/guue/id<?= $rec["codice"] ?>-edit"><?= $rec["titolo_pubblicazione"] ?></a><?
                  }
                  ?>
                </td>
                <td>
                  <?
                  $titolo_stato = $rec["stato"];
                  if($rec["stato"] === "PUBBLICATO" && empty($rec["no_doc_ojs"])) $titolo_stato = "IN PUBBLICAZIONE";
                  echo "<b>{$titolo_stato}</b>";
                  ?>
                </td>
                <td style="text-align: center;"><?= !empty($rec["data_trasmissione"]) && $rec["stato"] != 'BOZZA' ? mysql2datetime($rec["data_trasmissione"]) : '/' ?></td>
                <td style="text-align: center;">
                 <?
                    if($rec["stato"] == "PUBBLICATO") {
                      $data_pubblicazione = mysql2datetime($rec["data_pubblicazione"]);
                      if(!empty($rec["no_doc_ojs"])) echo '<small><b>ID: '.$rec["no_doc_ojs"].'</b></small><br>';
                      if(!empty($rec["id_pubblicazione"])) echo '<small><b>TECH-ID: '.$rec["id_pubblicazione"].'</b></small><br>';
                      if(!empty($data_pubblicazione)) echo '('.mysql2datetime($rec["data_pubblicazione"]).')';
                    } else {
                      echo '/';
                    }
                  ?>
                </td>
                <td>
                  <?
                  if($rec["stato"] == "PRONTO PER LA TRASMISSIONE") {
                    ?><a href="/guue/send_xml.php?codice=<?= $rec["codice"] ?>" style="color: #3399FF; cursor: pointer;"><i class="fa fa-cloud-upload fa-2x"></i></a><?
                  } elseif ($rec["stato"] == "PUBBLICATO") {
                    ?>
                    <a href="/guue/pdfrender.php?codice=<?= $rec["codice"] ?>" style="color: #666666; cursor: pointer;"><i class="fa fa-file-pdf-o fa-2x"></i></a>
                    <?
                  }
                  ?>
                </td>
                <td>
                  <?
                    if ($rec["stato"] == "PUBBLICATO" && (!empty($rec["no_doc_ojs"]))) {
                      $anno_guue = explode("/",$rec["no_doc_ojs"]);
                      $anno_guue = $anno_guue[0];
                      $id_guue = explode("-",$rec["no_doc_ojs"]);
                      $id_guue = end($id_guue);

                    ?>
                    <a href="http://ted.europa.eu/udl?uri=TED:NOTICE:<?= $id_guue ?>-<?= $anno_guue ?>:PDF:IT:HTML" style="color: #666666; cursor: pointer;"><i class="fa fa-file-pdf-o fa-2x"></i></a>
                    <?
                    }
                  ?>
                </td>
                <?
                if($_SESSION["gerarchia"] === "0") {
                  ?>
                  <td width="30">
                    <a style="color: #FF6600" href="/guue/reset.php?codice=<?= $rec["codice"] ?>">
                      <i class="fa fa-repeat fa-2x"></i>
                    </a>
                  </td>
                  <td width="30">
                    <a style="color: #333333" href="/guue/show_xml.php?codice=<?= $rec["codice"] ?>">
                      <i class="fa fa-code fa-2x"></i>
                    </a>
                  </td>
                  <td width="30">
                    <i style="color: #C00" class="fa fa-times fa-2x" onclick="elimina('<?= $rec["codice"] ?>','guue');"></i>
                  </td>
                  <?
                }
                ?>
              </tr>
              <?
            }
          }
          ?>
        </tbody>
      </table>
      <?
    } else {
      ?>
      <div class="box">
        <div class="padding">
          <br><br>
          <h2 style="text-align: center;"><b>NESSUNA PUBBLICAZIONE PRESENTE</b></h2>
          <br><br>
        </div>
      </div>
      <?
    }
    ?>
    <a href="/guue/edit.php?cod=0<?= !empty($codice_gara) ? "&codice_gara=".$codice_gara : null ?>" title="Inserisci nuova pubblicazione">
      <div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Nuova Pubblicazione
      </div>
    </a>
    <?
  }
?>
