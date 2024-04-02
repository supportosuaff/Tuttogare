<?
  if (!isset($_SESSION["codice_utente"])) {
    session_start();
    include("../../../config.php");
    include_once($root."/inc/funzioni.php");
;
    $edit = false;
    if (isset($_SESSION["codice_utente"]) && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
      $edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
      if (!$edit) {
        echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
        die();
      }
    } else {
      echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
      die();
    }
  }
?>
<option value="">Sempre</option>
<?
$sql = "SELECT b_opzioni.* FROM b_opzioni JOIN b_gruppi_opzioni ON b_opzioni.codice_gruppo = b_gruppi_opzioni.codice WHERE  b_gruppi_opzioni.eliminato = 'N' AND b_opzioni.eliminato = 'N' ORDER BY b_gruppi_opzioni.titolo";
$ris_opzioni = $pdo->query($sql);
if ($ris_opzioni->rowCount()>0) {
  $gruppo_attuale = "";
  $prima = true;
  while($opzione = $ris_opzioni->fetch(PDO::FETCH_ASSOC)) {
    if ($opzione["codice_gruppo"] != $gruppo_attuale) {
      $gruppo_attuale = $opzione["codice_gruppo"];
      $sql_gruppo = "SELECT * FROM b_gruppi_opzioni WHERE codice = :codice_gruppo";
      $ris_gruppo = $pdo->bindAndExec($sql_gruppo,array(":codice_gruppo"=>$gruppo_attuale));
      if ($ris_gruppo->rowCount()>0) {
        $gruppo = $ris_gruppo->fetch(PDO::FETCH_ASSOC);
        if (!$prima) { ?>
        </optgroup>
        <? } else { $prima = false; } ?>
        <optgroup label="<?= $gruppo["titolo"] ?>">
          <?
        }
      }
      ?>
      <option value="<?= $opzione["codice"] ?>"><?= $gruppo["titolo"] ?>: <?= $opzione["titolo"] ?></option>
      <?
    }
    ?>
  </optgroup>
  <?
}
?>
