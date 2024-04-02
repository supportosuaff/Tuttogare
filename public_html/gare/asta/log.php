<?
  if (!isset($pdo) && isset($_GET["codice_gara"]) && isset($_GET["codice_lotto"])) {
    session_start();
    include_once("../../../config.php");
    include_once($root."/inc/funzioni.php");
    $codice_gara = $_GET["codice_gara"];
    $codice_lotto = $_GET["codice_lotto"];
    $post = true;
  }
  if (isset($codice_gara) && isset($codice_lotto) && is_operatore()) {

    $bind = array();
    $bind[":codice_gara"] = $codice_gara;
    $bind[":codice_lotto"] = $codice_lotto;
    $bind[":codice_utente"] = $_SESSION["codice_utente"];
    $sql_log = "SELECT b_aste.* FROM b_aste JOIN r_partecipanti ON b_aste.codice_gara = r_partecipanti.codice_gara AND b_aste.codice_lotto = r_partecipanti.codice_lotto
                WHERE codice_utente = :codice_utente AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N'
                AND r_partecipanti.codice_gara = :codice_gara
                AND r_partecipanti.codice_lotto = :codice_lotto";
    $ris_log = $pdo->bindAndExec($sql_log,$bind);
    if ($ris_log->rowCount()>0) {
      $log_asta = $ris_log->fetch(PDO::FETCH_ASSOC);
      $minuti_residui = abs(strtotime($log_asta["data_fine"]) - time()) / 60;
      if ($minuti_residui > 0) { // if ($minuti_residui > 5) { CONTROLLO 5 MINUTI SCADENZA ELIMINATO A SEGUITO DI ABROGAZIONE DEL DPR.207/2010
        $bind = array();
        $bind[":codice_gara"] = $log_asta["codice_gara"];
        $bind[":codice_lotto"] = $log_asta["codice_lotto"];
        $sql_graduatoria = "SELECT r_partecipanti.*, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare
                            ON r_partecipanti.codice = r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara
                            AND r_punteggi_gare.codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N'
                            GROUP BY r_punteggi_gare.codice_gara,  r_punteggi_gare.codice_lotto, r_punteggi_gare.codice_partecipante
                            ORDER BY primo DESC, secondo DESC, ammesso DESC, escluso, totale_punteggio DESC, codice";
        $ris_graduatoria = $pdo->bindAndExec($sql_graduatoria,$bind);
        if ($ris_graduatoria->rowCount()>0) {
          $posizione = 0;
          echo "<h3>Graduatoria</h3>";
          echo "<table width='100%'><tr><th>Posizione</th><th>Punteggio</th></tr>";
          while ($log_partecipante = $ris_graduatoria->fetch(PDO::FETCH_ASSOC)) {
            $posizione++;
            $echo_tr = true;
            if (($log_partecipante["codice_utente"] != $_SESSION["codice_utente"]) && $log_asta["visualizzazione"] == "0") $echo_tr = false;
            if ($echo_tr) {
              $style="";
              if ($log_partecipante["codice_utente"] == $_SESSION["codice_utente"]) $style = "style=\"font-weight:bold; background-color:#0CF\"";
              echo "<tr " . $style . "><td><h2  style=\"text-align:center;\">" . $posizione . "</h2></td><td>" . number_format( $log_partecipante["totale_punteggio"] ,3,".","") . "</td></tr>";
            }
          }
          echo "</table>";
        }
        $bind = array();
        $bind[":codice_gara"] = $codice_gara;
        $bind[":codice_lotto"] = $codice_lotto;
        $sql_base = "SELECT MAX(timestamp) AS last_valida FROM b_offerte_economiche_asta WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND stato = 1
        GROUP BY codice_gara, codice_lotto ";
        $ris_base = $pdo->bindAndExec($sql_base,$bind);
        if ($ris_base->rowCount()>0) {
          $tempo_base = $ris_base->fetch(PDO::FETCH_ASSOC);
          $last_valida = strtotime($tempo_base["last_valida"]);
          if (!isset($_SESSION["last_valida"]) || ($_SESSION["last_valida"] != $last_valida)) {
      			$_SESSION["last_valida"] = $last_valida;
            $seconds_left =  $log_asta["tempo_base"]*60 - abs(time() - $last_valida);
            $scadenza_tempo_base = date('Y-m-d H:i:s', time() + $seconds_left);
            $legal = date("I");
    				$scadenza_tempo_base = explode(" ",$scadenza_tempo_base);
    				$scadenza_tempo_base[0] = explode("-",$scadenza_tempo_base[0]);
    				$scadenza_tempo_base[1] = explode(":",$scadenza_tempo_base[1]);
    		?>
              <script>
                $("#timing_base").countdown('destroy');
                $("#timing_base").countdown({
                  until: $.countdown.UTCDate(<? if ($legal == 0) { echo "+1,"; } else { echo "+2,"; } ?><?= $scadenza_tempo_base[0][0] ?>,<?= $scadenza_tempo_base[0][1] ?> - 1, <?= $scadenza_tempo_base[0][2] ?>,<?= $scadenza_tempo_base[1][0] ?>,<?= $scadenza_tempo_base[1][1] ?>,<?= $scadenza_tempo_base[1][2] ?>),
                  serverSync: serverTimeBase,
                  padZeroes: true
                });
              </script>
            <?
          }
          if (($log_asta["tempo_base"]*60 - (time() - $last_valida))>($minuti_residui*60)) {
          ?>
          <script>
            $("#timing_base").countdown('destroy');
          </script>
          <?
          }
        }
          ?>
        <script>
          $("#avviso_scadenza :visible").slideUp();
        </script>
        <?
      /*
      CONTROLLO 5 MINUTI SCADENZA ELIMINATO A SEGUITO DI ABROGAZIONE DEL DPR.207/2010
     } else if ($minuti_residui > 0) {
        ?>
        <script>
          $("#avviso_scadenza").slideDown();
        </script>
        Dati non visualizzabili negli ultimi 5 minuti
        ai sensi del DPR n. 207 art. 292 c. 3 del 05/10/2010.
        <? */
      } else {
        ?>
        <script>
          window.document.location = window.document.location;
        </script>
        <?
      }
    }
  }
?>
