<?
  include_once("../../config.php");
  if (isset($_SESSION["codice_utente"]) && $_SESSION["gerarchia"] <= 1 && check_permessi("albo",$_SESSION["codice_utente"])) {
    if (isset($_GET["codice_operatore"]) && isset($_SESSION["ente"]["codice"])) {
      $sql = "SELECT * FROM r_enti_operatori WHERE cod_ente = :cod_ente AND cod_utente = :cod_utente";
      $ris = $pdo->bindAndExec($sql,[":cod_ente"=>$_SESSION["ente"]["codice"],":cod_utente"=>$_GET["codice_operatore"]]);
      if ($ris->rowCount() > 0) {
        $id_interno = $ris->fetch(PDO::FETCH_ASSOC)["id_interno"];
        ?>
        <form rel="validate" action="/albo/saveIdInterno.php">
          <strong>ID Interno</strong><br>
          <input type="hidden" name="codice_operatore" value="<?= $_GET["codice_operatore"] ?>">
          <input type="text" class="titolo_edit" style="text-align:center" name="id_interno" title="id interno" rel="N;0;11;A" value="<?= $id_interno ?>">
          <input type="submit" class="submit_big" value="salva">
        </form>
        <script>
          f_ready();
        </script>
        <?
      }
    }
  }
?>
