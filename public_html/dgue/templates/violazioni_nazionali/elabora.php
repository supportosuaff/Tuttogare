<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  include($root."/dgue/config.php");
  if (is_operatore() && isset($_POST["espd"])) {
    $infrazione = false;
    $_SESSION["testo_violazioni_nazionali"] = "";
    $json = json_decode(file_get_contents($root."/dgue/templates/integrazioni_art80/definition.json"),true);
    foreach($_POST["espd"]["ccv:Criterion"] AS $uuid => $singleCriteria) {
      $values = findValues($singleCriteria,$json);
      if (!empty($values["_0"][0])) {
        if ($values["_0"][0] == "true" || $values["_0"][0] == "na") {
          $infrazione = true;
        }
        $sql = "SELECT * FROM b_dgue_settings WHERE uuid = :uuid AND version = '2016-50' AND attivo = 'S'";
        $ris = $pdo->bindAndExec($sql,array(":uuid"=>$uuid));
        if ($ris->rowCount() > 0) {
          $criterio = $ris->fetch(PDO::FETCH_ASSOC);
          $_SESSION["testo_violazioni_nazionali"].="\n\r ------------------------------ \n\r";
          $_SESSION["testo_violazioni_nazionali"].=$criterio["nome"];
          $_SESSION["testo_violazioni_nazionali"].=$criterio["descrizione"];
          $_SESSION["testo_violazioni_nazionali"].="\n\r";
          $_SESSION["testo_violazioni_nazionali"].="Risposta: ";
          if ($values["_0"][0] == "true") {
             $_SESSION["testo_violazioni_nazionali"].="SI";
          } else if ($values["_0"][0] == "na") {
            $_SESSION["testo_violazioni_nazionali"].="Non Applicabile";
          } else {
            $_SESSION["testo_violazioni_nazionali"].="NO";
          }
          if ($values["_0"][0] == "true" || $values["_0"][0] == "na") {
            if (!empty($values["_0"][2])) {
              $_SESSION["testo_violazioni_nazionali"].="\n\r";
              $_SESSION["testo_violazioni_nazionali"].="Data dell'accertamento definitivo e autorit&agrave;/organismo di emanazione: ";
              $_SESSION["testo_violazioni_nazionali"].=$values["_0"][2];
            }
            if (!empty($values["_0"][3])) {
              $_SESSION["testo_violazioni_nazionali"].="\n\r";
              $_SESSION["testo_violazioni_nazionali"].="Violazione Rimossa: ";
              if ($values["_0"][3]) {
                $_SESSION["testo_violazioni_nazionali"].="Si";
              } else {
                $_SESSION["testo_violazioni_nazionali"].="No";
              }
            }
            $_SESSION["testo_violazioni_nazionali"].="\n\r";
            $_SESSION["testo_violazioni_nazionali"].="Descrizione delle misure: ";
            $_SESSION["testo_violazioni_nazionali"].=$values["_0"][1];
          }
        }
      }
    }
    if ($infrazione == true) {
      $label = "Si";
      $value = "true";
      ?>
      $("#criteria_national").slideDown('fast');
      <?
    } else {
      $label = "No";
      $value = "false";
      ?>
      $("#criteria_national").slideUp('fast');
      <?
    }
    ?>
      $("#label_national").html('<?= $label ?>');
      $("#indicator_national").val('<?= $value ?>');
    <?
  }
