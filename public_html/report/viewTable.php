<?
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  $startDate = $_GET["startDate"];
  $endDate = $_GET["endDate"];
  $tipologia = $_GET["tipologia"];
  $stato = $_GET["stato"];
  switch($stato){
    case '1':
      $stato = " AND b_gare.stato >= 3 ";
      break;
    case '2':
      $stato = " AND b_gare.stato = 99 ";
      break;
    case '3':
      $stato = " AND b_gare.stato = 98 ";
      break;
    case '4':
      $stato = " AND b_gare.stato BETWEEN 7 and 98 ";
      break;
    case '5':
      $stato = " AND b_gare.stato = 4 ";
    break;
    default: $stato = ""; break;
  }
  $ente = $_GET["ente"];

  $bind = array();
  $bind[":startDate"] = date("Y-m-d H:i:s",strtotime($startDate));
  $bind[":endDate"] = date("Y-m-d H:i:s",strtotime($endDate));

  $sql = "SELECT b_gare.codice, b_gare.importoAggiudicazione, SUM(b_lotti.importoAggiudicazione) AS totaleLotti, 
            AVG(b_gare.ribasso) AS mediaRibassiLotti,
            b_gare.ribasso, b_gare.id, b_enti.denominazione, b_gare.cig, b_tipologie.tipologia, b_criteri.criterio,
            b_procedure.nome as procedura, b_gare.oggetto, b_gare.prezzoBase, b_gare.contributo_sua, SUM(b_incassi.importo) AS contributo_incassato, b_gare.data_scadenza,
            b_stati_gare.titolo AS fase, b_gare.flag_gestione_autonoma, b_gare.utente_creazione
          FROM b_gare
          JOIN b_enti on b_enti.codice = b_gare.codice_ente
          LEFT JOIN b_lotti ON b_gare.codice = b_lotti.codice_gara
          JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase
          JOIN b_tipologie on b_tipologie.codice = b_gare.tipologia
          JOIN b_criteri on b_criteri.codice = b_gare.criterio
          JOIN b_procedure on b_procedure.codice = b_gare.procedura
          LEFT JOIN b_incassi ON b_gare.codice = b_incassi.codice_gara
          WHERE ((data_pubblicazione BETWEEN :startDate AND :endDate) OR (data_pubblicazione IS NULL AND data_scadenza BETWEEN :startDate AND :endDate)) ";
          if($tipologia!="0") {
            $bind[":tipologia"] = $tipologia;
            $sql .= " AND tipologia = :tipologia";
          }
          $sql .= $stato;
          if(isset($ente) && $ente != "0") {
            $bind[":ente"] = $ente;
            $sql .= " AND b_gare.codice_ente = :ente";
          }
  $sql .=" AND codice_gestore = ".$_SESSION["ente"]["codice"];
  $sql .= " GROUP BY b_gare.codice ";
  $sql .= " ORDER BY data_scadenza";
  $risultato = $pdo->bindAndExec($sql,$bind);
  $userRIS = $pdo->prepare("SELECT cognome, nome FROM b_utenti WHERE codice = :codice");
  $token = rand();
  $export = [];
  $export[] = ["id","ente","cig","tipologia","criterio","procedura","oggetto","stato","importo","partecipanti","aggiudicato","ribasso","contributo","incassato","data_scadenza","flag_gestione_autonoma","utente"];
  $result ="<br/><div style='float: right;'><a href='/report/exportArray.php?token={$token}'><img src='/img/opendata.png' id='opendata' name='opendata'/></a></div>";
  $table = "<table id='tabellaTotale' width='100%'>";
  $table .="<thead><tr>
  <th>Id</th>
  <th>Ente</th>
  <th>Cig</th>
  <th>Tipologia</th>
  <th>Criterio</th>
  <th>Procedura</th>
  <th>Oggetto</th>
  <th>Stato</th>
  <th>Importo</th>
  <th>Partecipanti</th>
  <th>Importo Aggiudicazione</th>
  <th>Ribasso</th>
  <th>Data scadenza</th>
  <th>Gestione Autonoma</th>
  <th>Utente</th>
  </tr></thead><tbody>";

  if($risultato->rowCount()>0){
    $totale_partecipanti = 0;
    while($record = $risultato->fetch(PDO::FETCH_ASSOC)){
      $tmp = [];
      //echo $record["data_scadenza"];
      $totale_incassato += $record["contributo_incassato"];
      $record["data_scadenza"]=date_format(date_create($record["data_scadenza"]), 'd/m/Y H:i:s');
      $sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND (conferma <> 'N' OR (conferma IS NULL AND primo = 'S')) ";
      $ris_partecipanti = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));
      $totale_partecipanti += $ris_partecipanti->rowCount();
      $table .="<tr>";
      $tmp[] = $record["id"];
      $tmp[] = $record["denominazione"];
      $tmp[] = $record["cig"];
      $tmp[] = $record["tipologia"];
      $tmp[] = $record["criterio"];
      $tmp[] = $record["procedura"];
      $tmp[] = $record["oggetto"];
      $tmp[] = $record["fase"];
      $tmp[] = number_format($record["prezzoBase"], 2, ',','.');
      $tmp[] = number_format($ris_partecipanti->rowCount(), 0, '','.');
      if (empty($record["totaleLotti"])) {
        $tmp[] = number_format($record["importoAggiudicazione"], 2, ',','.');
      } else {
        $tmp[] = number_format($record["totaleLotti"], 2, ',','.');
      }
      if (empty($record["mediaRibassiLotti"])) {
        $tmp[] = number_format($record["ribasso"], 2, ',','.');
      } else {
        $tmp[] = number_format($record["mediaRibassiLotti"], 2, ',','.');
      }
      $tmp[] = $record["data_scadenza"];
      $tmp[] = $record["flag_gestione_autonoma"];
      $userRIS->bindValue(":codice",$record["utente_creazione"]);
      $userRIS->execute();
      if ($userRIS->rowCount() > 0) {
        $user = $userRIS->fetch(PDO::FETCH_ASSOC);
        $tmp[] = $user["cognome"] . " " . $user["nome"];
      } else {
        $tmp[] = "";
      }

      $export[] = $tmp;
      foreach($tmp AS $value) $table .= "<td>{$value}</td>";
      $table .= "</tr>";
    }

      $bind = array();
      $bind[":startDate"] = date("Y-m-d H:i:s",strtotime($startDate));
      $bind[":endDate"] = date("Y-m-d H:i:s",strtotime($endDate));

    $strsql = "SELECT sum(b_gare.prezzoBase) as totale, sum(b_gare.importoAggiudicazione) as totaleAggiudicazione FROM b_gare WHERE (data_pubblicazione BETWEEN :startDate AND :endDate) ";
    if($tipologia!="0") {
      $bind[":tipologia"] = $tipologia;
      $strsql .= " AND tipologia = :tipologia";
    }
    $strsql .= $stato;
    if(isset($ente) && $ente != "0") {
      $bind[":ente"] = $ente;
      $strsql .= " AND b_gare.codice_ente = :ente";
    }
    //else $strsql .= " AND codice_ente = ".$_SESSION["ente"]["codice"];
    $strsql .=" AND codice_gestore = ".$_SESSION["ente"]["codice"];

    //echo $strsql;die();
    $ris = $pdo->bindAndExec($strsql,$bind);
    // echo $pdo->getSQL();
    // die();
    if($ris->rowCount()>0){
        $rec = $ris->fetch(PDO::FETCH_ASSOC);
        $table .="</tbody><tfoot>
        <tr>
        <td colspan='8' style='text-align:right'><strong>Totale importi &euro;</strong></td>
        <td><strong>".number_format($rec["totale"], 2, ',','.')."</strong></td>
        <td><strong>".number_format($totale_partecipanti, 0, '','.')."</strong></td>
        <td><strong>".number_format($rec["totaleAggiudicazione"], 2, ',','.')."</strong></td>
        <td></td>
        </tr>
        </tfoot></table>";
    }
  }
  $_SESSION["reportExport{$token}"] = $export;
  echo $result . $table;

?>
