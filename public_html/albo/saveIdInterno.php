<?
  include_once("../../config.php");
  if (isset($_SESSION["codice_utente"]) && $_SESSION["gerarchia"] <= 1 && check_permessi("albo",$_SESSION["codice_utente"])) {
    if (isset($_POST["codice_operatore"]) && isset($_SESSION["ente"]["codice"])) {
      $sql = "UPDATE r_enti_operatori SET id_interno = :id_interno WHERE cod_ente = :cod_ente AND cod_utente = :cod_utente";
      $ris = $pdo->bindAndExec($sql,[":id_interno"=>$_POST["id_interno"],":cod_ente"=>$_SESSION["ente"]["codice"],":cod_utente"=>$_POST["codice_operatore"]]);
      if ($ris->rowCount() > 0) {
        ?>
        alert('Salvataggio effettuato con successo');
        <?
      }
      ?>
      $('#modalIdInterno').dialog('close');
      elenco.draw();
      <?
    }
  }
?>
