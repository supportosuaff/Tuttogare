<?
  if (isset($record_gara)) {
    $procedura_standard = false;
    $prezzo = $record_gara["prezzoBase"];
    $esito["contributo_sua"] = 0;
    if ($prezzo <= 200000) {
      $scaglioni = ["0.5"=>$prezzo];
    } else if ($prezzo > 200000 && $prezzo <= 1000000) {
      $differenza = $prezzo - 200000;
      $scaglioni = ["0.5"=>200000,"0.4"=>$differenza];
    } else if ($prezzo > 1000000 && $prezzo <= 2000000) {
      $differenza = $prezzo - 1000000;
      $scaglioni = ["0.5"=>200000,"0.4"=>800000,"0.3"=>$differenza];
    } else if ($prezzo > 2000000 && $prezzo <= 5000000) {
      $differenza = $prezzo - 2000000;
      $scaglioni = ["0.5"=>200000,"0.4"=>800000,"0.3"=>1000000,"0.2"=>$differenza];
    } else if ($prezzo > 5000000) {
      $differenza = $prezzo - 5000000;
      $scaglioni = ["0.5"=>200000,"0.4"=>800000,"0.3"=>1000000,"0.2"=>3000000,"0.1"=>$differenza];
    }
    if (isset($scaglioni)) {
      foreach($scaglioni AS $perc => $importo) {
        $esito["contributo_sua"] += ($importo * $perc) / 100;
      }
    }
    if ($esito["contributo_sua"] < 500) {
      $esito["contributo_sua"] = 500;
    }
  }
?>
