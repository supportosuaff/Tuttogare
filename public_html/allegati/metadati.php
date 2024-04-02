<?
  session_start();
  include_once "../../config.php";
  include_once $root . "/inc/funzioni.php";

  $tabelle = array('b_allegati_albo','b_allegati_dialogo','b_allegati_contratto','b_allegati','b_allegati_me','b_allegati_sda',);
  if(empty($_POST["codice"]) || empty($_POST["tabella"]) || is_operatore() || empty($_SESSION["codice_utente"]) || !in_array("b_".$_POST["tabella"], $tabelle)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
    die();
  } else {
    $sql = "SELECT * FROM b_{$_POST["tabella"]} WHERE codice = :codice";
    $ris = $pdo->bindAndExec($sql,array(':codice'=>$_POST["codice"]));
    if($ris->rowCount() != 1) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
      die();
    } else {
      $allegato = $ris->fetch(PDO::FETCH_ASSOC);
      if(in_array("b_".$_POST["tabella"], array('b_allegati_albo','b_allegati_dialogo','b_allegati_me','b_allegati_sda')) && $_SESSION["gerarchia"] > 0) {
        if($_SESSION["record_utente"]["codice_ente"] != $_SESSION["ente"]["codice"]) {
          header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
          die();
        }
      }
      $sql_metadati = "SELECT * FROM b_schema_metadati WHERE soft_delete = 'N' AND codice_ente = :codice_ente";
      $bind_metadati = array(':codice_ente' => $_SESSION["record_utente"]["codice_ente"]);
      $ris_metadati = $pdo->bindAndExec($sql_metadati,$bind_metadati);
      if($ris_metadati->rowCount() < 1) {
        if(isset($_SESSION["ente"])) {
          $bind_metadati[':codice_ente'] = $_SESSION["ente"]["codice"];
          $ris_metadati = $pdo->bindAndExec($sql_metadati,$bind_metadati);
          if($ris_metadati->rowCount() < 1) {
            $bind_metadati[':codice_ente'] = 0;
            $ris_metadati = $pdo->bindAndExec($sql_metadati,$bind_metadati);
          }
        } else {
          $bind_metadati[':codice_ente'] = 0;
          $ris_metadati = $pdo->bindAndExec($sql_metadati,$bind_metadati);
        }
      }
      if($ris_metadati->rowCount() > 0) {
        $sth = $pdo->prepare("SELECT * FROM b_metadati WHERE codice_schema = :codice_schema AND codice_file = :codice_file AND tabella = :tabella");
        $sth->bindValue(':codice_file', $_POST["codice"]);
        $sth->bindValue(':tabella', $_POST["tabella"]);
        ?>
        <form action="/allegati/save_metadati.php" method="post" rel="validate">
          <input type="hidden" name="codice_allegato" value="<?= $_POST["codice"] ?>">
          <input type="hidden" name="tabella" value="<?= $_POST["tabella"] ?>">
          <table class="metadati-xml" style="width:100%">
            <?
            while($record_campo = $ris_metadati->fetch(PDO::FETCH_ASSOC)) {
              $valore = "";
              $sth->bindValue(':codice_schema', $record_campo["codice"]);
              $sth->execute();
              if($sth->rowCount() > 0) $valore = $sth->fetch(PDO::FETCH_ASSOC)["valore"];
              ?>
              <tr>
                <td style="width:30%"><?= $record_campo["etichetta"] ?>:</td>
                <td><input type="text" placeholder="<?= $record_campo["descrizione"] ?>" title="<?= $record_campo["etichetta"] ?>" name="meta[<?= $record_campo["codice"] ?>]" value="<?= $valore ?>" rel="<?= $record_campo["obbligatorio"] ?>;1;0;<?= $record_campo["tipologia"] ?>"></td>
              </tr>
              <?
            }
            ?>
          </table>
          <input type="submit" value="Salva" class="submit_big" style="cursor:pointer;">
        </form>
        <?
      } else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
        die();
      }
    }
  }
?>
