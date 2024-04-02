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
  // $asse_x = array();
  // $asse_y = array();
  $output = array();


  $bind = array();
  $bind[":startDate"] = date("Y-m-d H:i:s",strtotime($startDate));
  $bind[":endDate"] = date("Y-m-d H:i:s",strtotime($endDate));

  $sql = "SELECT b_tipologie.tipologia as tipologia, sum(prezzoBase) as importo, count(b_gare.codice) as value FROM `b_gare` JOIN b_tipologie on b_tipologie.codice = b_gare.tipologia WHERE (data_pubblicazione BETWEEN :startDate AND :endDate) ";
  if($tipologia!="0") {
    $bind[":tipologia"] = $tipologia;
    $sql .= " AND tipologia = :tipologia";
  }
  $sql .= $stato;
  if(isset($ente) && $ente != "0") {
    $bind[":ente"] = $ente;
    $sql .= " AND codice_ente = :ente";
  }
  // else $sql .= " AND codice_ente = ".$_SESSION["ente"]["codice"];
  $sql .=" AND codice_gestore = ".$_SESSION["ente"]["codice"];
  $sql .= " group by tipologia";
  // echo $sql;die();
  $risultato = $pdo->bindAndExec($sql,$bind);

  if($risultato->rowCount()>0){
    while($record = $risultato->fetch(PDO::FETCH_ASSOC)){
      // $asse_x[] = $record["tipologia"];
      // $asse_y[] = floatval($record["importo"]);
      $output[]=array($record["tipologia"],"&euro; " . number_format($record["importo"],2,",","."), intval($record["value"]));
    }
  }
  $_SESSION["reportExport"] = $output;
  echo json_encode($output);
?>
