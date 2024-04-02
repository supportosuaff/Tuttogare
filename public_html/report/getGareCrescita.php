<?
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  $startDate = $_GET["startDate"];
  $endDate = $_GET["endDate"];
  $tipologia = $_GET["tipologia"];
  $stato = $_GET["stato"];
  switch($stato){
    case '1':
      $stato = " AND b_gare.stato BETWEEN 3 and 7 ";
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

  // $asse_x = array();
  // $asse_y = array();
  $output = array();
  $somma_gare =0;
  $somma_prezzi =0;
  $bind = array();
  $bind[":startDate"] = date("Y-m-d H:i:s",strtotime($startDate));
  $bind[":endDate"] = date("Y-m-d H:i:s",strtotime($endDate));
  $sql = "SELECT DATE_FORMAT(DATE(e.data_pubblicazione),'%m/%Y') AS e_date FROM b_gare AS e WHERE data_pubblicazione BETWEEN :startDate AND :endDate";
  if($tipologia!="0") {
    $bind[":tipologia"] = $tipologia;
    $sql .= " AND tipologia = :tipologia";
  }
  $sql .= $stato;
  if(isset($ente) && $ente != "0") $sql .= " AND codice_ente = ".$ente;
  // else $sql .= " AND codice_ente = ".$_SESSION["ente"]["codice"];
  $sql .=" AND codice_gestore = ".$_SESSION["ente"]["codice"];
  $sql.=" GROUP BY DATE_FORMAT(DATE(e.data_pubblicazione),'%Y-%m')";

  //echo $sql; die();
  $risultato = $pdo->bindAndExec($sql,$bind);
  $bind = array();
  $bind[":startDate"] = date("Y-m-d H:i:s",strtotime($startDate));
  $bind[":endDate"] = date("Y-m-d H:i:s",strtotime($endDate));
  if($risultato->rowCount()>0){
    while($record = $risultato->fetch(PDO::FETCH_ASSOC)){
      $strsql= "SELECT count(codice) as somma, sum(prezzoBase) as value FROM b_gare WHERE DATE_FORMAT(DATE(data_pubblicazione),'%m/%Y') LIKE '".$record["e_date"]."'";
      $strsql .=" AND data_pubblicazione BETWEEN :startDate AND :endDate";
      $strsql .=" AND codice_gestore = ".$_SESSION["ente"]["codice"];
      // echo $strsql;die();
      $ris_somma = $pdo->bindAndExec($strsql,$bind);
      if($ris_somma->rowCount()>0){
        while($record_somma = $ris_somma->fetch(PDO::FETCH_ASSOC)){
          // $asse_x[] = $record["e_date"];
          $somma_gare+=$record_somma["somma"];
          $somma_prezzi+=$record_somma["value"];
          // $asse_y[] = intval($somma);
          $output[]=array($record["e_date"],intval($somma_gare),"&euro; ".number_format($somma_prezzi,2,",","."));
        }
      }
    }
  }
  $_SESSION["reportExport"] = $output;
  echo json_encode($output);
?>
