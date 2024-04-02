<?
  include("../../config.php");
  $pagina_login = true;
  if (isset($_SESSION["confirmTwoFactorAuth"])) unset($_SESSION["confirmTwoFactorAuth"]);
  include($root."/layout/top.php");
  if (!isset($_SESSION["tentativi_twoFactor"])) $_SESSION["tentativi_twoFactor"] = 0;
  $tentativi_totali = 5;
  if (isset($_SESSION["codice_utente"])) {
    if (empty($_POST["codeVerify"])) {
      ?>
      <div id="div_login">
        <div style="padding:50px">
          <div style="text-align:center">
            <img src="/img/gA.png" alt="Google Authenticator" width="150">
            <form name="box" method="post" action="twoFactor.php" target="_self" rel="validate" autocomplete="off">
              <strong>Codice di verifica</strong><br>
              <small>Inserire il codice di verifica generato dall'app Google Authenticator</small>
              <input type="text" name="codeVerify" id="codeVerify" title="Codice di autenticazione" rel="S;6;6;A" autocomplete="off" style="text-align:center" class="titolo_edit">
              <?
                if ($_SESSION["tentativi_twoFactor"] > 0) {
                  ?>
                  <strong style="color:#C00">Codice errato - Tentativi residui: <?= $tentativi_totali - $_SESSION["tentativi_twoFactor"] ?></strong>
                  <?
                }
              ?>
              <input type="submit" class="submit_big" value="Accedi">
              <a href="#" style="background-color:#C00" onClick="logout()" class="submit_big"><span class='fa fa-power-off'></span>&nbsp;&nbsp;logout</a>
            </form>
          </div>
        </div>
      </div>
      <?
    } else {
      $ga = new PHPGangsta_GoogleAuthenticator();
      $twoFactor_token = simple_decrypt($_SESSION["record_utente"]["twoFactor_token"],$config["enc_key"]);
      $checkResult = $ga->verifyCode($twoFactor_token, $_POST["codeVerify"], 2);    // 2 = 2*30sec clock tolerance
      if ($checkResult) {
        $_SESSION["confirmTwoFactorAuth"] = true;
        echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
      } else {
        $_SESSION["tentativi_twoFactor"]++;
        ?>
        <h1>Errore nel riconoscimento</h1>
        <?
        if ($_SESSION["tentativi_twoFactor"] < $tentativi_totali) {
          echo '<meta http-equiv="refresh" content="0;URL=/user/twoFactor.php">';
        } else {
          session_destroy();
    			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
        }
      }
    }
  } else {
    ?>
    <h1>Errore nel riconoscimento</h1>
    <?
  }
  include($root."/layout/bottom.php");

?>
