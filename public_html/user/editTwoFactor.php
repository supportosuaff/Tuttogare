<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  $error = true;
  if (isset($_SESSION["confirmTwoFactorAuth"])) unset($_SESSION["confirmTwoFactorAuth"]);
  if (isset($_SESSION["codice_utente"])) {
    if (!empty($_POST["codeVerify"]) && !empty($_SESSION["twoFactor_token"])) {
      $ga = new PHPGangsta_GoogleAuthenticator();
      $checkResult = $ga->verifyCode($_SESSION["twoFactor_token"], $_POST["codeVerify"], 2);    // 2 = 2*30sec clock tolerance
      if ($checkResult) {
        $_SESSION["confirmTwoFactorAuth"] = true;
        $tmp = array();
        $tmp["codice"] = $_SESSION["codice_utente"];
        $tmp["twoFactor_token"] = simple_encrypt($_SESSION["twoFactor_token"],$config["enc_key"]);

        $salva = new salva();
        $salva->debug = false;
        $salva->codop = 0;
        $salva->nome_tabella = "b_utenti";
        $salva->operazione = "UPDATE";
        $salva->oggetto = $tmp;
				if ($salva->save() > 0) {
          $error = false;
          $_SESSION["confirmTwoFactorAuth"] = true;
          ?>
          $("#enableTwoFactor_content").html('<h2 style="color:#0C0; text-align:center">ATTIVA</h2>');
          <?
        }
      }
    } else if (isset($_POST["eliminaAutenticazione"])) {
      $tmp = array();
      $tmp["codice"] = $_SESSION["codice_utente"];
      $tmp["twoFactor_token"] = "";

      $salva = new salva();
      $salva->debug = false;
      $salva->codop = 0;
      $salva->nome_tabella = "b_utenti";
      $salva->operazione = "UPDATE";
      $salva->oggetto = $tmp;
      if ($salva->save() > 0) {
        $error = false;
      ?>
      window.location.reload();
      <?
      }
    }
  }
  if ($error) header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
?>
