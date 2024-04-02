<?
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if (isset($_SESSION["codice_utente"])) {
    ?>
    <script>
      function enableTwoFactor() {
        $("#enableTwoFactor_div").dialog({
          modal: true,
          width: "500px",
          title: "Abilita autenticazione a due fattori"
        });
        $("#enableTwoFactor_div").show();
        f_ready();
      }
    </script>
  	<div id="enableTwoFactor_div" style="display:none;">
      <?
        $ga = new PHPGangsta_GoogleAuthenticator();
        if (empty($_SESSION["record_utente"]["twoFactor_token"])) {
          $secret = $ga->createSecret();
          $erroreQr = true;
          if (!empty($secret)) {
            $_SESSION["twoFactor_token"] = $secret;
            $codice_account = sanitize_string($config["nome_sito"]);
            $qrCodeUrl = $ga->getQRCodeGoogleUrl($codice_account, $secret);
            if (!empty($qrCodeUrl)) $erroreQr = false;
          }
          if (!$erroreQr) {
              ?>
              <div id="enableTwoFactor_content" style="text-align:center">
                <form name="box" method="post" action="/user/editTwoFactor.php" rel="validate" autocomplete="off">
                  <strong>Codice QR</strong><br>
                  <div style="text-align:center"><img src="<?= $qrCodeUrl ?>" alt="QR Code" width="200" height="200"></div>
                  Per attivare l'autenticazione a due fattori scarica sul tuo dispositivo l'app Google Authenticator, inquadra il QR Code e inserisci il codice di verifica,
                  In alternativa al QR Code inserire manualmente i seguenti dati:<br><br>
                  <table width="100%">
                    <tr>
                      <th class="etichetta" width="50%">Account</th>
                      <th class="etichetta" width="50%">Codice</th>
                    </tr>
                    <tr>
                      <td style="text-align:center"><strong><?= $codice_account ?></strong></td>
                      <td  style="text-align:center"><strong><?= $secret ?></strong></td>
                    </tr>
                  </table>
                  <br>
                  <table width="100%">
                    <tr>
                      <td width="50%" style="text-align:center">
                        <a target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=it">
                          <img src="/img/android.png" alt="Android" width="100">
                        </a><br>
                        Dispositivi Android
                      </td>
                      <td width="50%" style="text-align:center">
                        <a target="_blank" href="https://itunes.apple.com/it/app/google-authenticator/id388497605?mt=8">
                          <img src="/img/apple.png" alt="iOS" width="100">
                        </a><br>
                        Dispositivi iOS
                      </td>
                  </table>
                  <input type="text" name="codeVerify" id="codeVerify" title="Codice di verifica" rel="S;6;6;A" autocomplete="off" style="text-align:center" class="titolo_edit">
                  <input type="submit" class="submit_big" value="Attiva">
                </form>
              </div>
              <?
          } else {
            ?>
            <h1>Errore nella generazione del QR Code</h1>
            <?
          }
        } else {
          ?>
          <h2 style="color:#0C0; text-align:center">ATTIVA</h2>
          <form name="box" method="post" action="/user/editTwoFactor.php" rel="validate" autocomplete="off">
            <input type="hidden" name="eliminaAutenticazione">
            <input type="submit" class="submit_big" style="background-color:#C00" value="Disattiva">
          </form>
          <?
        }
      ?>
  	</div>
    <?
  }
?>
