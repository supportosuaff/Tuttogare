<?
  session_start();
  include_once "../../../config.php";
  include_once $root . "/layout/top.php";

  if(empty($_GET["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"]) || !file_exists(__DIR__."/script/".$_SESSION["ente"]["codice"]."/form.php")) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  } else {
    $codice = $_GET["codice"];
    $codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;
    $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
    $sql  = "SELECT b_contratti.*, b_conf_modalita_stipula.invio_remoto FROM b_contratti JOIN b_conf_modalita_stipula ON b_contratti.modalita_stipula = b_conf_modalita_stipula.codice ";
    if(!empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
      $sql .= "JOIN b_permessi ON b_contratti.codice_gara = b_permessi.codice_gara ";
    } elseif (empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
      $sql .= "JOIN b_permessi_contratti ON b_contratti.codice = b_permessi_contratti.codice_contratto ";
    }
    $sql .= "WHERE b_contratti.codice = :codice ";
    $sql .= "AND b_contratti.codice_gestore = :codice_ente ";
    if ($_SESSION["gerarchia"] > 0) {
      $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
      $sql .= "AND (b_contratti.codice_ente = :codice_ente_utente OR b_contratti.codice_gestore = :codice_ente_utente) ";
    }
    if (!empty($codice_gara)) {
      $bind[":codice_gara"] = $codice_gara;
      $sql .= " AND b_contratti.codice_gara = :codice_gara";
      if($_SESSION["gerarchia"] > 1) {
        $bind[":codice_utente"] = $_SESSION["codice_utente"];
        $sql .= " AND (b_permessi.codice_utente = :codice_utente)";
      }
    } else {
      if($_SESSION["gerarchia"] > 1) {
        $bind[":codice_utente"] = $_SESSION["codice_utente"];
        $sql .= " AND (b_permessi_contratti.codice_utente = :codice_utente)";
      }
    }
    $ris = $pdo->bindAndExec($sql,$bind);
    $href_contratto = null;
    if($ris->rowCount() == 1) {
      $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
      $href_contratto = "?codice=".$rec_contratto["codice"] . (!empty($rec_contratto["codice_gara"]) ? "&codice_gara=".$rec_contratto["codice_gara"] : null);

      $ris_oe = $pdo->bindAndExec("SELECT b_contraenti.*, r_contratti_contraenti.codice_contratto FROM b_contraenti JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contraente = b_contraenti.codice WHERE b_contraenti.tipologia = :tipologia AND r_contratti_contraenti.codice_contratto = :codice_contratto AND (r_contratti_contraenti.codice_capogruppo = 0 OR r_contratti_contraenti.codice_capogruppo IS NULL) LIMIT 0,1", array(':tipologia' => "oe", ':codice_contratto' => $codice));
      if($ris_oe->rowCount() > 0) {
        $rec_oe = $ris_oe->fetch(PDO::FETCH_ASSOC);
      }
      /*
            'Nome' => ucwords(strtolower(html_entity_decode($rec_ore["nome"], ENT_QUOTES, 'UTF-8'))),
            'Cognome' => ucwords(strtolower(html_entity_decode($rec_ore["cognome"], ENT_QUOTES, 'UTF-8'))),
            'CodiceFiscale' => $rec_ore["cf"],
            'DataNascita' => mysql2date($rec_ore["data_nascita"]),
      */
      $locked = false;
      ?>
      <h1>TRASMETTI CONTRATTO AD ERP</h1><br>
      <form action="send.php" method="post" rel="validate">
        <input type="hidden" name="codice" value="<? echo $codice; ?>">
        <input type="hidden" name="codice_gara" value="<? echo $codice_gara; ?>">
    		<div class="comandi">
    			<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
    		</div>
        <? include(__DIR__."/script/".$_SESSION["ente"]["codice"]."/form.php") ?>
        <? if (!$locked) { ?>
          <input type="submit" class="submit_big" value="Invia">
        <? } ?>
      </form>
      <script type="text/javascript">
        $(document).ready(function() {
          $('select').trigger('chosen:updated');
        });
      </script>
      <?
    }
  }
  include_once $root . '/contratti/ritorna_pannello_contratto.php';
	include_once $root."/layout/bottom.php";
  die();
?>
