<?
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  $startDate = $_GET["startDate"];
  $endDate = $_GET["endDate"];
  $tipologia = $_GET["tipologia"];
  $stato = $_GET["stato"];
  $ente = $_GET["ente"];
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

  // $serie = array();
  $output = array();

  $bind = array();
  $bind[":startDate"] = date("Y-m-d H:i:s",strtotime($startDate));
  $bind[":endDate"] = date("Y-m-d H:i:s",strtotime($endDate));

  $sql_sua = "SELECT count(b_gare.codice) as value, sum(prezzoBase) as somma FROM `b_gare` WHERE (data_pubblicazione BETWEEN :startDate AND :endDate)";
  if($tipologia!="0") {
    $bind[":tipologia"] = $tipologia;
    $sql_sua .= " AND tipologia = :tipologia";
  }
  $sql_sua .= $stato;
  $sql_sua .=" AND codice_gestore = ".$_SESSION["ente"]["codice"];
  $sql_sua .=" AND codice_ente = ".$_SESSION["ente"]["codice"];

  //echo $strsql; die();
  $ris_sua = $pdo->bindAndExec($sql_sua,$bind);
  $record_sua = $ris_sua->fetch(PDO::FETCH_ASSOC);
  // $serie[$indice]["y"]=intval($tmp[0]);
  $output[] = array($_SESSION["ente"]["denominazione"],intval($record_sua["value"]),"&euro; " . number_format($record_sua["somma"],2,",","."));


  $sql = "SELECT b_enti.codice, b_enti.denominazione FROM b_enti WHERE sua= :codice_ente";
  //echo $sql; die();
  $risultato = $pdo->bindAndExec($sql,array(":codice_ente"=>$_SESSION["ente"]["codice"]));
  if($risultato->rowCount()>0){
    // $indice=0;
    while($record = $risultato->fetch(PDO::FETCH_ASSOC)){
      // $serie[$indice]["name"]=$record["denominazione"];
      $strsql = "SELECT count(b_gare.codice) AS numero, sum(prezzoBase) AS totale FROM `b_gare` WHERE (data_pubblicazione BETWEEN :startDate AND :endDate) AND codice_ente = ".$record["codice"];
      if($tipologia!="0") {
        $bind[":tipologia"] = $tipologia;
        $strsql .= " AND tipologia = :tipologia";
      }
      $strsql .= $stato;
      $strsql .=" AND codice_gestore = ".$_SESSION["ente"]["codice"];

      $ris = $pdo->bindAndExec($strsql,$bind);
      $tmp= $ris->fetch(PDO::FETCH_ASSOC);
      // $serie[$indice]["y"]=intval($tmp[0]);
      $output[] = array($record["denominazione"],intval($tmp["numero"]),"&euro; " . number_format($tmp["totale"],2,",","."));
      // $indice++;
    }
  }
  $_SESSION["reportExport"] = $output;
  echo json_encode($output);
?>
