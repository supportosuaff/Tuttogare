<?
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  $startDate = $_GET["startDate"];
  $endDate = $_GET["endDate"];
  $ente = $_GET["ente"];

  $output = array();

  $bind = array();
  $bind[":startDate"] = date("Y-m-d H:i:s",strtotime($startDate));
  $bind[":endDate"] = date("Y-m-d H:i:s",strtotime($endDate));

  $sql = "SELECT count(codice) AS codice FROM `b_gare` WHERE (data_pubblicazione BETWEEN :startDate AND :endDate)";
  if(isset($ente) && $ente != "0") {
    $bind[":ente"] = $ente;
    $sql .= " AND codice_ente = :ente";
  }
  $sql .=" AND codice_gestore = ".$_SESSION["ente"]["codice"];

  $risultato = $pdo->bindAndExec($sql,$bind);
  $totale_gare = $risultato->fetch(PDO::FETCH_ASSOC);

  $sql = "SELECT codice, tipologia FROM b_tipologie WHERE attivo = 'S'";
  $risultato = $pdo->query($sql);
  if($risultato->rowCount()>0){
    $bind = array();
    $bind[":startDate"] = date("Y-m-d H:i:s",strtotime($startDate));
    $bind[":endDate"] = date("Y-m-d H:i:s",strtotime($endDate));
    while($tipologia = $risultato->fetch(PDO::FETCH_ASSOC)){

      $strsql = "SELECT count(codice) as value, sum(prezzoBase) as somma FROM `b_gare` WHERE (data_pubblicazione BETWEEN :startDate AND :endDate) AND tipologia = ".$tipologia["codice"];
      if(isset($ente) && $ente != "0") {
        $bind[":ente"] = $ente;
        $strsql .= " AND codice_ente = :ente";
      }

      // else $strsql .= " AND codice_ente = ".$_SESSION["ente"]["codice"];
      $strsql .=" AND codice_gestore = ".$_SESSION["ente"]["codice"];

      $ris = $pdo->bindAndExec($strsql,$bind);

      if($ris->rowCount()>0){
        $tmp= $ris->fetch(PDO::FETCH_ASSOC);
        if($totale_gare["codice"]!=0)
          $percentuale = (float)number_format((floatval($tmp["value"])/floatval($totale_gare["codice"]))*100,2,",",".");
        else
          $percentuale = 0;
        $output[] = array($tipologia["tipologia"],$percentuale,number_format($tmp["somma"],2,",","."));
      }else
        $output[] = array($tipologia["tipologia"],floatval(0),floatval(0));
    }
  }
  //print_r($serie);
  $_SESSION["reportExport"] = $output;
  echo json_encode($output);
?>
