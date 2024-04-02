<?
  session_start();
  include("../../../config.php");
  include_once($root."/inc/funzioni.php");
  if(empty($_SESSION["codice_utente"]) || !check_permessi("scadenzario/gestione",$_SESSION["codice_utente"])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
    die();
  } else {
    session_write_close();

    $result = array();
    $rec_scadenze = array();

    $limit = "";
    $start = 0;
    $end = -1;
    $bind = array();
    $search = array('oggetto', 'data', 'descrizione');

    if (isset($_GET['start']) && $_GET['length'] != '-1') {
      $start = $_GET["start"];
      $end = $_GET['length'];
      $limit = " LIMIT {$start}, {$end}";
    }
    if (!empty($_SESSION["record_utente"]["codice_ente"]) || !empty($_SESSION["ente"]["codice"])) {
      $bind[":codice_ente"] = !empty($_SESSION["record_utente"]["codice_ente"]) ? $_SESSION["record_utente"]["codice_ente"] : $_SESSION["ente"]["codice"];
      $bind[":codice_sua"] = $bind[":codice_ente"];
      if (!empty($_SESSION["ente"]["codice"])) {
        $bind[":codice_sua"] = $_SESSION["ente"]["codice"];
      }
    } else {
      $bind[":codice_ente"] = 0;
      $bind[":codice_sua"] = 0;
    }

    $old = (bool) $_GET["old"];

    $response = array();
    $response["draw"] = (int) $_GET["draw"];
    if ($_SESSION["gerarchia"] < 2) {
      $response["recordsTotal"] = $pdo->bindAndExec('SELECT COUNT(codice) AS recordsTotal FROM b_scadenze WHERE (codice_ente = :codice_ente  OR codice_ente = :codice_sua OR codice_ente IS NULL OR codice_ente = 0)', $bind)->fetch(PDO::FETCH_ASSOC)["recordsTotal"];
      $sql = "SELECT SQL_CALC_FOUND_ROWS b_scadenze.* FROM b_scadenze WHERE (codice_ente = :codice_ente  OR codice_ente = :codice_sua OR :codice_ente IS NULL OR codice_ente = 0) ";
    } else {
      $bind = array(":codice_utente"=> $_SESSION["record_utente"]["codice"]);
      $response["recordsTotal"] = $pdo->bindAndExec('SELECT COUNT(codice) AS recordsTotal FROM b_scadenze WHERE codice_utente = :codice_utente', $bind)->fetch(PDO::FETCH_ASSOC)["recordsTotal"];
      $sql = "SELECT SQL_CALC_FOUND_ROWS b_scadenze.* FROM b_scadenze WHERE codice_utente = :codice_utente ";
    }
    if(empty($old)) {
      $bind[":date_time"] = date('Y-m-d h:i:s');
      $sql .= "AND data > :date_time ";
    }
    if(!empty($_GET["search"]["value"])) {
      $sql .= " AND (";
      foreach ($search as $key) {
        $bind[":term"] = "%" . $_GET["search"]["value"] . "%";
        $sql .= $key . " LIKE :term OR ";
      }
      $sql = substr($sql,0,-3);
      $sql .= ")";
    }
    $sql .= "ORDER BY timestamp DESC ";
    $sql .= $limit;
    $ris = $pdo->bindAndExec($sql,$bind);
    // echo $pdo->getSQL();
    if($ris->rowCount() > 0) {
      $rec_scadenze = $ris->fetchAll(PDO::FETCH_ASSOC);
    }

    $response["recordsFiltered"] = $pdo->query('SELECT FOUND_ROWS() AS recordsFiltered')->fetch(PDO::FETCH_ASSOC)["recordsFiltered"];
    $response["data"] = array();

    if(count($rec_scadenze) > 0) {
      foreach ($rec_scadenze as $scadenza) {
        $row = array();
        $row[0] = $scadenza["codice"];
        $row[1] = mysql2datetime($scadenza["data"]);
        $row[2] = $scadenza["oggetto"];
        if ($_SESSION["gerarchia"]==="0" || ($scadenza["codice_ente"] == $_SESSION["record_utente"]["codice_ente"] && $_SESSION["gerarchia"]==="1") || $_SESSION["record_utente"]["codice"] == $scadenza["codice_utente"]) {
          $row[3] = '<a href="edit.php?codice='.$scadenza["codice"].'" class="button button-highlight button-circle button-small"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
          $row[4] = "<button class=\"button button-caution button-circle button-small\" onclick=\"elimina('".$scadenza["codice"]."', 'scadenzario/gestione')\"><i class=\"fa fa-times\"></i></button>";
        } else {
          $row[3] = "";
          $row[4] = "";
        }
        $response["data"][] = $row;
      }
    }

  }
  if(!empty($response) && count($response) > 2) { echo json_encode($response); } else { header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400); }
?>
