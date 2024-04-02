<?
  session_start();
  if (!empty($_SESSION["amministratore"])) {
    $service_url = 'https://www.indicepa.gov.it:443/public-ws/WS05_AMM.php';
    $curl = curl_init($service_url);
    $curl_post_data = array(
          'AUTH_ID' => 'RBEFPDSW',
          'COD_AMM' => $_POST["codice"]
    );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
    $curl = addCurlAuth($curl);
    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
      ?>
      alert("Ente non trovato!");
      <?
    }
    curl_close($curl);
    $decoded = json_decode($curl_response,true);
    if (isset($decoded["result"]["cod_err"]) && $decoded["result"]["cod_err"] === 0) {
      ?>
      $("#denominazione").val("<?= $decoded["data"]["des_amm"] ?>");
      $("#cf").val("<?= $decoded["data"]["cf"] ?>");
      $("#url").val("http://<?= $decoded["data"]["sito_istituzionale"] ?>");
      $("#indirizzo").val("<?= $decoded["data"]["indirizzo"] ?>");
      $("#citta").val("<?= $decoded["data"]["comune"] ?>");
      $("#cap").val("<?= $decoded["data"]["cap"] ?>");
      $("#provincia").val("<?= $decoded["data"]["provincia"] ?>");
      $("#pec").val("<?= $decoded["data"]["mail1"] ?>");
      $("#email").val("<?= $decoded["data"]["mail2"] ?>");
      $("#stato").val("Italia");
      <?
    } else {
      ?>alert("Errore nella richiesta")<?
    }
  }
?>
