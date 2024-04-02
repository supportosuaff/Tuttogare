<?
  if (!isset($config) && isset($_GET["cod"]) && isset($_GET["codice_lotto"])) {
    session_start();
    include("../../../config.php");
    include_once($root."/inc/funzioni.php");
    if (is_operatore()) {
      $codice = $_GET["cod"];
      $codice_lotto = $_GET["codice_lotto"];
      $bind = array();
      $bind[":codice"] = $codice;
      $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
      $strsql  = "SELECT b_gare.*, b_procedure.invito, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
                  WHERE b_gare.codice = :codice AND b_gare.annullata = 'N' AND b_modalita.online = 'S'
                  AND codice_gestore = :codice_ente
                  AND (pubblica = '2' OR pubblica = '1') ";
      $risultato = $pdo->bindAndExec($strsql,$bind);
      if ($risultato->rowCount() > 0) {
        $record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
      }
    }
  }

  if (!empty($record_gara) && $record_gara["seduta_pubblica"] == "S" && isset($codice_lotto) && is_operatore()) {
    if (($record_gara["annullata"] == "N") && (strtotime($record_gara["data_apertura"]) < time())) {

      $filtro_mercato = "";

      $bind = array();
      $bind[":codice"]=$record_gara["procedura"];

      $strsql = "SELECT * FROM b_procedure WHERE mercato_elettronico = 'S' AND codice = :codice";
      $ris_mercato = $pdo->bindAndExec($strsql,$bind);
      if ($ris_mercato->rowCount()>0) $filtro_mercato = " AND mercato_elettronico = 'S' ";

      $bind = array();
      $bind[":codice"]=$record_gara["criterio"];

      $sql = "SELECT * FROM b_criteri_buste WHERE codice_criterio= :codice " . $filtro_mercato . " ORDER BY ordinamento ";
      $buste_monitor = $pdo->bindAndExec($sql,$bind);
      $buste_monitor = $buste_monitor->fetchAll(PDO::FETCH_ASSOC);
      $bind = array(':codice' => $record_gara["codice"], ":codice_utente" => $_SESSION["codice_utente"],":codice_lotto"=>$codice_lotto);
      $sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_utente = :codice_utente AND (conferma = TRUE OR conferma IS NULL)";
      $ris_partecipazione_confermata = $pdo->bindAndExec($sql,$bind);
      if ($ris_partecipazione_confermata->rowCount() > 0) {
        $partecipante = $ris_partecipazione_confermata->fetch(PDO::FETCH_ASSOC);
        include_once($root."/inc/zoomMtg.class.php");
        $zoom = new zoomMtg;
        $meeting = $zoom->getMeetingFromDB("gare",$record_gara["codice"],$codice_lotto,"seduta pubblica");
        if (!empty($meeting)) {
          $meeting = json_decode($meeting["response"],true);
          $status = $zoom->getMeetingDetails($meeting["id"]);
          if (!empty($status["status"]) && $status["status"] != "finished") {
            ?>
            <!DOCTYPE html>
            <html lang="en">
              <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?= $meeting["topic"] ?></title>
                <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.9.1/css/bootstrap.css" />
                <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.9.1/css/react-select.css" />
              </head>
              <body>
                <!-- import ZoomMtg dependencies -->
                <script src="https://source.zoom.us/1.9.1/lib/vendor/react.min.js"></script>
                <script src="https://source.zoom.us/1.9.1/lib/vendor/react-dom.min.js"></script>
                <script src="https://source.zoom.us/1.9.1/lib/vendor/redux.min.js"></script>
                <script src="https://source.zoom.us/1.9.1/lib/vendor/redux-thunk.min.js"></script>
                <script src="https://source.zoom.us/1.9.1/lib/vendor/lodash.min.js"></script>

                <!-- import ZoomMtg -->
                <script src="https://source.zoom.us/zoom-meeting-1.9.1.min.js"></script>
                <script>
                  console.log('checkSystemRequirements');
                  console.log(JSON.stringify(ZoomMtg.checkSystemRequirements()));
                  ZoomMtg.setZoomJSLib('https://source.zoom.us/1.9.1/lib', '/av'); 
                  ZoomMtg.preLoadWasm();
                  ZoomMtg.prepareJssdk();
                  ZoomMtg.init({
                    leaveUrl: "/gare/telematica2.0/modulo.php?cod=<?= $_GET["cod"] ?>&codice_lotto=<?= $_GET["codice_lotto"] ?>",
                    isSupportAV: true,
                    disableInvite: true,
                    disableCallOut: true, 
                    disableRecord: true,
                    screenShare: false, //optional,
                    success: (success) => {
                      ZoomMtg.join({
                        signature: "<?= $zoom->generateJoinSignature($meeting["id"],0) ?>",
                        meetingNumber: <?= $meeting["id"] ?>,
                        userName: "<?= $partecipante["ragione_sociale"] ?>",
                        apiKey: "<?= $config["zoom-JWT-APIKEY"] ?>",
                        userEmail: "<?= $partecipante["pec"] ?>",
                        passWord: "<?= $meeting["password"] ?>",
                        success: (success) => {
                          ZoomMtg.showInviteFunction({
                            show: false
                          });
                        },
                        error: (error) => {
                          console.log(error)
                        }
                      })

                    },
                    error: (error) => {
                      console.log(error)
                    }
                  })
                </script>
              </body>
            </html>
            <?
            die();
          }
        }
      }
    }
  }
  echo "<h1>Non si dispone dei privilegi necessari</h1>";
?>
