<?
  include_once("../config.php");
  include_once($root."/layout/top.php");
  ?>
  <h1>SUPPORTO</h1>
  <div class="box padding">
    <strong>Il servizio di Help Desk è attivo dal lunedì al venerdì dalle 09 alle 14 e dalle 14:30 alle 17:30</strong>
  </div>
  <?
    $email = "";
    $nome = "";

    if (!empty($_SESSION["record_utente"])) {
      $email = $_SESSION["record_utente"]["email"];
      $nome = $_SESSION["record_utente"]["cognome"] . " " . $_SESSION["record_utente"]["nome"];
    }
    ?>
    <div style="float:left; width:49%; margin-right:2%">
      <div class="box_iscrizione" style="float:none; width:100%">
        <h2><span class="fa fa-phone fa-5x"></span><br>Telefono</h2><br>
        <h1 style="font-size:3em; text-align:center; border:0;"><?= $_SESSION["numero_assistenza"] ?></h1><br>
      </div>
    </div>
    <div style="float:left; width:49%;">
      <div class="box_iscrizione" style="float:none; width:100%; padding: 0">
        <h2><span class="fa fa-envelope fa-5x"></span><br>e-Mail</h2><br>
        <h3 style="font-size:2em; text-align:center; border:0;">
          <a href="mailto:<?= $_SESSION["email_assistenza"] ?>" style="color:#FFF"><?= $_SESSION["email_assistenza"] ?></a>
        </h3><br>
      </div>
    </div>
    <div class="clear"></div>
  <?
  include_once($root."/layout/bottom.php");
?>
