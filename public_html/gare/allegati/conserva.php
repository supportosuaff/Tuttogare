<?
  use Dompdf\Dompdf;
  use Dompdf\Options;

  session_start();
  include_once("../../../config.php");
  include_once($root."/inc/funzioni.php");
  ini_set('max_execution_time', 600);
  ini_set('memory_limit', '-1');

  $edit = false;
  $lock = false;

  if (!empty($_SESSION["codice_utente"]) && !empty($_SESSION["ente"]) && !empty($_POST["codice"])) {
    $esito = check_permessi_gara(52,$_POST["codice"],$_SESSION["codice_utente"]);
    $edit = $esito["permesso"];
    if (!$edit) die('alert("Non si dispone dei permessi necessari per eseguire l\'operazione");');
  } else {
    die('alert("Non si dispone dei permessi necessari per eseguire l\'operazione");');
  }

  $bind = array();
  $bind[":codice_gara"] = $_POST["codice"];
  $esclusioni = explode(",", trim($_POST["esclusioni"],","));
  $esclusioni_allegati = [];
  $esclusioni_comunicazioni = [];
  if (count($esclusioni) > 0) {
    foreach($esclusioni AS $esclusione) {
      $esclusione = explode("_",$esclusione);
      if (count($esclusione) == 2) {
        if ($esclusione[0] == "msg") {
          $esclusioni_comunicazioni[] = $esclusione[1];
        } else {
          $esclusioni_allegati[] = $esclusione[1];
        }
      }
    }
  }
  $sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice_gara AND sezione = 'gara' AND codice NOT IN (SELECT codice_file FROM r_conservazione_file WHERE tabella = 'allegati') ";
  if (count($esclusioni_allegati) > 0) {
    $i = 0;
    $sql.= "AND codice NOT IN (";
    foreach($esclusioni_allegati AS $esclusione) {
      $i++;
      $bind[":esclusione_".$i] = $esclusione;
      $sql .= ":esclusione_".$i.",";
    }
    $sql = substr($sql,0,-1);
    $sql.= ")";
  }
  $ris_allegati = $pdo->bindAndExec($sql,$bind);
  $gara = $pdo->bindAndExec("SELECT * FROM b_gare WHERE codice = :codice", array(':codice' => $_POST["codice"]))->fetch(PDO::FETCH_ASSOC);
  $ris_ricevute = [];
  $ris_ricevute = getRicevuteNonConservate("gara",$gara["codice"]);
  if(($ris_allegati->rowCount()>0 || count($ris_ricevute)) && ! empty($gara["codice_ente"])) {
    $codice_pacchetto =  1;
    $sql = "SELECT MAX(codice_pacchetto) AS codice_pacchetto FROM b_conservazione WHERE (codice_ente = :codice_ente OR codice_gestore = :codice_ente) AND anno = :year";
    $ris = $pdo->bindAndExec($sql, array(':codice_ente' => $_SESSION["record_utente"]["codice_ente"], ':year' => date('Y')));
    if($ris->rowCount() > 0) $codice_pacchetto = $ris->fetch(PDO::FETCH_ASSOC)["codice_pacchetto"] + 1;
    $salva = new salva();
    $salva->debug = FALSE;
    $salva->codop = $_SESSION["codice_utente"];
    $salva->nome_tabella = "b_conservazione";
    $salva->operazione = "INSERT";
    $salva->oggetto = array(
      'codice_ente' => $gara["codice_ente"],
      'codice_gestore' => $gara["codice_gestore"],
      'codice_pacchetto' => $codice_pacchetto,
      'utente_creazione' => $_SESSION["codice_utente"],
      'anno' => date('Y'),
      'sezione' => 'gara',
      'codice_oggetto' => $gara["codice"],
      'stato' => 0,
      'denominazione' => $_POST["denominazione"],
      'descrizione' => $_POST["descrizione"]
    );
    $codice_pacchetto_conservazione = $salva->save();
    if ($codice_pacchetto_conservazione !== false) {

      $documento = [];
      $check = $pdo->go("SELECT codice FROM b_conservazione_documento WHERE codice_riferimento = :codice_gara AND modulo = 'gare'", [":codice_gara" => $gara["codice"]]);
      if($check->rowCount() > 0) $documento = $check->fetch(PDO::FETCH_ASSOC);

      if(empty($documento["documento"])) {
        $errore = TRUE;

        $documento['codice_ente'] = $gara["codice_ente"];
        $documento['codice_gestore'] = $gara["codice_gestore"];
        $documento['codice_riferimento'] = $gara["codice"];
        $documento['modulo'] = 'gare';
        $documento['utente_modifica'] = $_SESSION["record_utente"]["codice"];

        $salva->nome_tabella = "b_conservazione_documento";
        $salva->operazione = empty($documento["codice"]) ? 'INSERT' : 'UPDATE';

        $tipologia = $pdo->go("SELECT tipologia FROM b_tipologie WHERE codice = :tipologia", array(":tipologia" => $gara["tipologia"]))->fetch(PDO::FETCH_COLUMN, 0);
        $procedura = $pdo->go("SELECT nome FROM b_procedure WHERE codice = :procedura", array(":procedura" => $gara["procedura"]))->fetch(PDO::FETCH_COLUMN, 0);

        ob_start();
        ?>
        <html>
          <style>
            body { font-size:10px; }
            td, th { padding:5px; }
            tr.odd { background-color: #DDD; }
            table { width:100% }
            .etichetta {
              background-color: #DCDCDC;
              color: #000000;
              font-weight: bold;
            }
          </style>
          <body>
            <div style="text-align:center; width: 100%">
              <!-- <img src="<?= $config["link_sito"] ?>/documenti/enti/<?= $_SESSION["ente"]["logo"] ?>" style="width:150px"><br><br> -->
              <h1><?= $_SESSION["ente"]["denominazione"] ?></h1>
              <strong><?= $_SESSION["ente"]["indirizzo"] . " - " . $_SESSION["ente"]["citta"] . " (" . $_SESSION["ente"]["provincia"] . ")" ?></strong><br>
            </div>
            <div style="clear:both"></div>
            <table>
              <tr>
                <td style="width:25%" class="etichetta">ANNO GARA</td>
                <td style="width:25%"><?= $gara["anno"] ?></td>
                <td style="width:25%" class="etichetta">NUMERO GARA</td>
                <td style="width:25%"><?= $gara["id"] ?></td>
              </tr>
              <tr>
                <td colspan="4" class="etichetta">OGGETTO</td>
              </tr>
              <tr>
                <td colspan="4"><?= $gara["oggetto"] ?></td>
              </tr>
              <tr>
                <td class="etichetta">CIG</td>
                <td><?= $gara["cig"] ?></td>
                <td class="etichetta">CUP</td>
                <td><?= $gara["cup"] ?></td>
              </tr>
              <tr>
                <td class="etichetta">STRUTTURA PROPONENTE</td>
                <td><?= $gara["struttura_proponente"] ?></td>
                <td class="etichetta">RESPONSABILE</td>
                <td><?= $gara["responsabile_struttura"] ?></td>
              </tr>
              <tr>
                <td class="etichetta">TIPOLOGIA</td>
                <td><?= $tipologia ?></td>
                <td class="etichetta">PROCEDURA</td>
                <td><?= $procedura ?></td>
              </tr>
              <tr>
                <td class="etichetta">DATA PUBBLICAZIONE</td>
                <td><?= mysql2datetime($gara["data_pubblicazione"]) ?></td>
                <td class="etichetta">DATA SCADENZA</td>
                <td><?= mysql2datetime($gara["data_scadenza"]) ?></td>
              </tr>
            </table>
          </body>
        </html>
        <?
        $documento["documento"] = ob_get_clean();

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($documento["documento"]);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $documento["documento"] = $dompdf->output();

        $documento["size"] = strlen($documento["documento"]);
        $documento["documento"] = base64_encode($documento["documento"]);

        $documento["metadata"] = json_encode([
          "codice" => $gara["codice"],
          "anno" => ! empty($gara["anno"]) ? $gara["anno"] : (! empty($gara["data_pubblicazione"]) ? date('Y', strtotime($gara["data_pubblicazione"])) : date('Y')),
          "numero" => $gara["id"],
          "cig" => $gara["cig"],
          "cup" => $gara["cup"],
          "struttura_proponente" => $gara["struttura_proponente"],
          "oggetto" => strip_tags(html_entity_decode($gara["oggetto"])),
          "tipologia" => $tipologia,
          "procedura" => $procedura,
          "data_pubblicazione" => ! empty($gara["data_pubblicazione"]) ? $gara["data_pubblicazione"] : null,
          "data_scadenza" => ! empty($gara["data_scadenza"]) ? $gara["data_scadenza"] : null
        ]);

        $salva->oggetto = $documento;
        $documento["codice"] = $salva->save();

        if(! empty($documento["codice"])) {

          if($documento["size"] > 0) $errore = FALSE;

          $salva->nome_tabella = "b_conservazione";
          $salva->operazione = "UPDATE";
          $salva->oggetto = array(
            'codice' => $codice_pacchetto_conservazione,
            'codice_documento' => $documento["codice"]
          );
           $salva->save();
        }
      }

      if(! $errore) {
        $sth_save = $pdo->prepare("INSERT INTO `r_conservazione_file`(`codice_ente`,`codice_pacchetto`,`codice_file`,`tabella`,`file_path`,`nome_file`,`hash_md5`,`hash_sha1`,`hash_sha256`,`utente_modifica`) VALUES (:codice_ente,:codice_pacchetto,:codice_file,:tabella,:file_path,:nome_file,:hash_md5,:hash_sha1,:hash_sha256,:utente_modifica)");
        if ($ris_allegati->rowCount()>0) {
          while($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
            $cartella = "";
            $percorso_fisico = "";
            if ($allegato["cartella"]!="") $cartella = $allegato["cartella"] . DIRECTORY_SEPARATOR;
            $percorso_fisico = $config["pub_doc_folder"]."/allegati/" . $allegato["codice_gara"] . DIRECTORY_SEPARATOR . $cartella. $allegato["riferimento"];
            if($allegato["online"]=="N") $percorso_fisico = $config["arch_folder"].DIRECTORY_SEPARATOR.$allegato["codice_gara"].DIRECTORY_SEPARATOR.$cartella.$allegato["riferimento"];

            if (file_exists($percorso_fisico)) {
              $file_content = file_get_contents($percorso_fisico);

              $sth_save->bindValue(":codice_ente", !empty($_SESSION["record_utente"]["codice_ente"]) ? $_SESSION["record_utente"]["codice_ente"] : $_SESSION["ente"]["codice"]);
              $sth_save->bindValue(":codice_pacchetto", $codice_pacchetto_conservazione);
              $sth_save->bindValue(":codice_file", $allegato["codice"]);
              $sth_save->bindValue(":tabella", "allegati");
              $sth_save->bindValue(":file_path", $percorso_fisico);
              $sth_save->bindValue(":nome_file", $allegato["nome_file"]);
              $sth_save->bindValue(":hash_md5", hash("md5",$file_content));
              $sth_save->bindValue(":hash_sha1", hash("sha1",$file_content));
              $sth_save->bindValue(":hash_sha256", hash("sha256",$file_content));
              $sth_save->bindValue(":utente_modifica", $_SESSION["codice_utente"]);
              $sth_save->execute();
              $codice_file = $pdo->lastInsertId();
            }
          }
        }
        if (count($ris_ricevute) > 0) insertRicevuteinPacchetto("gara",$_POST["codice"],$codice_pacchetto_conservazione,$esclusioni_comunicazioni);
        die("alert('Pacchetto creato con successo!');window.location.href = window.location.href;");
      }

      if(! empty($codice_pacchetto_conservazione)) {
        $pdo->go("DELETE FROM b_conservazione WHERE codice = :codice", [":codice" => $codice_pacchetto_conservazione]);
      }

    } else {
      ?>alert('Si è verificato un errore durante la creazione del pacchetto')<?
    }
  }
?>
