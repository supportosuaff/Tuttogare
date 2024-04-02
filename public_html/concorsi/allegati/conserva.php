<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
$lock = false;
  if (!empty($_SESSION["codice_utente"]) && !empty($_SESSION["ente"]) && !empty($_POST["codice"])) {
    $esito = check_permessi_concorso(1,$_POST["codice"],$_SESSION["codice_utente"]);
    $edit = $esito["permesso"];
    if (!$edit) {
      ?>alert("Non si dispone dei permessi necessari per eseguire l'operazione");<?
      die();
    }
  } else {
    ?>alert("Non si dispone dei permessi necessari per eseguire l'operazione");<?
    die();
  }

  $bind = array();
  $bind[":codice_gara"] = $_POST["codice"];
  $esclusioni = explode(",", trim($_POST["esclusioni"],","));
  $sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice_gara AND sezione = 'concorsi' AND codice NOT IN (SELECT codice_file FROM r_conservazione_file WHERE tabella = 'allegati') ";
  if (count($esclusioni) > 0) {
    $i = 0;
    $sql.= "AND codice NOT IN (";
    foreach($esclusioni AS $esclusione) {
      $i++;
      $bind[":esclusione_".$i] = $esclusione;
      $sql .= ":esclusione_".$i.",";
    }
    $sql = substr($sql,0,-1);
    $sql.= ")";
  }
  $ris_allegati = $pdo->bindAndExec($sql,$bind);
  $concorso = $pdo->bindAndExec("SELECT codice_ente, codice_gestore FROM b_concorsi WHERE codice = :codice", array(':codice' => $_POST["codice"]))->fetch(PDO::FETCH_ASSOC);
  if($ris_allegati->rowCount()>0 && !empty($concorso["codice_gestore"]) && !empty($concorso["codice_ente"])){

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
      'codice_ente' => $concorso["codice_ente"],
      'codice_gestore' => $concorso["codice_gestore"],
      'codice_pacchetto' => $codice_pacchetto,
      'utente_creazione' => $_SESSION["codice_utente"],
      'anno' => date('Y'),
      'sezione' => 'concorsi',
      'codice_oggetto' => $_POST["codice"],
      'stato' => 0,
      'denominazione' => $_POST["denominazione"],
      'descrizione' => $_POST["descrizione"]
    );
    $codice_pacchetto_conservazione = $salva->save();
    if ($codice_pacchetto_conservazione !== false) {

      $errore = FALSE;
      $sth_save = $pdo->prepare("INSERT INTO `r_conservazione_file`(`codice_ente`,`codice_pacchetto`,`codice_file`,`tabella`,`file_path`,`nome_file`,`hash_md5`,`hash_sha1`,`hash_sha256`,`utente_modifica`) VALUES (:codice_ente,:codice_pacchetto,:codice_file,:tabella,:file_path,:nome_file,:hash_md5,:hash_sha1,:hash_sha256,:utente_modifica)");

      while($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
        $cartella = "";
        $percorso_fisico = "";
        if ($allegato["cartella"]!="") $cartella = $allegato["cartella"] . DIRECTORY_SEPARATOR;
        if ($allegato["online"]=="S") {
         $percorso_fisico = $config["pub_doc_folder"]."/allegati/concorsi/" . $allegato["codice_gara"] . DIRECTORY_SEPARATOR . $cartella. $allegato["riferimento"];
        } else if($allegato["online"]=="N") {
         $percorso_fisico = $config["arch_folder"].DIRECTORY_SEPARATOR."concorsi".DIRECTORY_SEPARATOR.$allegato["codice_gara"].DIRECTORY_SEPARATOR.$cartella.$allegato["riferimento"];
        }
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
      ?>alert('Pacchetto creato con successo!');
      window.location.href = window.location.href;<?
    } else {
      ?>alert('Si è verificato un errore durante la creazione del pacchetto')<?
    }
  }
?>
