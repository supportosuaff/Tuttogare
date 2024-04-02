<?
  if (isset($_SESSION["codice_commissario"]) && isset($codice_gara)) {
    $ris_gara = $pdo->bindAndExec("SELECT * FROM b_gare WHERE codice = :codice",[":codice"=>$codice_gara]);
    if ($ris_gara->rowCount() === 1) {
      $gara = $ris_gara->fetch(PDO::FETCH_ASSOC);
      if (!isset($codice_lotto) && isset($_GET["codice_lotto"])) $codice_lotto = $_GET["codice_lotto"];
      if (isset($codice_lotto)) {
        if ($codice_lotto != 0) {
          $ris_lotto = $pdo->bindAndExec("SELECT * FROM b_lotti WHERE codice = :codice",[":codice"=>$codice_lotto]);
          if ($ris_lotto->rowCount() === 1) {
            $lotto = $ris_lotto->fetch(PDO::FETCH_ASSOC);
            $gara["oggetto"] .= "<br>" . $lotto["oggetto"];
          }
        }
        $coppie = false;

        $bind = array();
        $bind[":codice_gara"] = $gara["codice"];
        $sql_opzione = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 124";
        $ris_opzione = $pdo->bindAndExec($sql_opzione,$bind);
        if ($ris_opzione->rowCount() > 0) $coppie = true;

        $sql_partecipanti = "SELECT * FROM r_partecipanti
                             WHERE ammesso = 'S' AND escluso = 'N' AND (conferma IS NULL or conferma = 1)
                             AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0;
                             ";
        $partecipanti = $pdo->bindAndExec($sql_partecipanti,[":codice_gara"=>$gara["codice"],":codice_lotto"=>$codice_lotto])->fetchAll(PDO::FETCH_ASSOC);

        $sql_criteri = "SELECT b_valutazione_tecnica.*, b_criteri_punteggi.nome
                        FROM b_valutazione_tecnica
                        JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
                        WHERE b_valutazione_tecnica.codice_gara = :codice_gara
                        AND (b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)";
        $tmp_criteri = $pdo->bindAndExec($sql_criteri,[":codice_gara"=>$gara["codice"],":codice_lotto"=>$codice_lotto])->fetchAll(PDO::FETCH_ASSOC);
        $criteri = [];
        $echo_criteri = [];
        $criteri_valutazione = [];
        foreach ($tmp_criteri as $criterio) {
          $criteri[$criterio["codice"]] = $criterio;
          if (empty($criterio["codice_padre"])) {
            $echo_criteri[$criterio["codice"]] = [];
          } else {
            $echo_criteri[$criterio["codice_padre"]][$criterio["codice"]] = [];
          }
          if ($criterio["tipo"] == "Q") {
            $criteri_valutazione[] = $criterio["codice"];
            if (!empty($criterio["codice_padre"])) {
              foreach($criteri_valutazione AS $sk => $sc) {
                if ($sc == $criterio["codice_padre"]) {
                  unset($criteri_valutazione[$sk]);
                }
              }
            }
          }
        }
      }
      if (!isset($hide_layout)) {
      ?>
      <style>
        body {
          background-color: #FFF !important;
        }
      </style>
      <div style="background-color:#ccc" class="padding">
        <div style="float:left">
          <img src="/documenti/enti/<?= $_SESSION["ente"]["logo"] ?>" height="80" alt="<?= $_SESSION["ente"]["denominazione"] ?>"><br>
        </div>
        <h2 style="float:left">PANNELLO COMMISSARIO - <?= $_SESSION["commissario"] ?><br>
          <small><strong><?= $gara["oggetto"] ?></strong></small>
        </h2>
        <div style="float:right">
          <a style="text-decoration:none" href="/pannello-commissione/pannello.php?codice=<?= $gara["codice"] ?>" title="Home"><span class="fa fa-home"></span> HOME</a>
          <?
        		include_once($root."/inc/zoomMtg.class.php");
            $contesto = "commissione valutatrice";
            $zoom = new zoomMtg;
            $meeting = $zoom->getMeetingFromDB("gare",$codice_gara,$codice_lotto,$contesto);
            if (!empty($meeting)) {
              $meeting = json_decode($meeting["response"],true);
              $status = $zoom->getMeetingDetails($meeting["id"]);
              if (!empty($status["status"]) && $status["status"] != "finished") {
                $join_url = $meeting["join_url"];
                ?>
                | <a target="_blank" style="text-decoration:none" href="<?= $join_url ?>" title="CONFERENCE ROOM"><span class="fa fa-video-camera"></span> CONFERENCE ROOM</a>
                <?
              }
            }
          ?>
          | <a style="text-decoration:none" href="/logout.php" title="Logout"><span class="fa fa-power-off"></span> LOGOUT</a>
        </div>
        <div class="clear"></div>

      </div>
      <div class="padding">
      <?
      }
    }
  }
?>
