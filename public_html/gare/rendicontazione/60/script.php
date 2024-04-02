<?
if (!isset($ribasso)) {
  if (($contributo <= 500000)||($contributo > 2000000)) {
    $esito["contributo_sua"] = ((($contributo - 500000) * 0.3/100) + 2000);
    $procedura_standard = false;
  }else if(($contributo <= 2000000)||($contributo > 5000000)) {
    $esito["contributo_sua"] = ((($contributo - 2000000) * 0.2/100) + 8000);
    $procedura_standard = false;
  }else if ($contributo > 5000000) {
    $esito["contributo_sua"] = ((($contributo - 5000000) * 0.1/100) + 18000);
    $procedura_standard = false;
  }
}
?>
