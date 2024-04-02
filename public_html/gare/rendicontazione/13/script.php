<?
if (!isset($ribasso)) {
  if ($contributo > 1000000) {
    $esito["contributo_sua"] = ((($contributo - 1000000) * 0.5/100) + 10000);
    $procedura_standard = false;
  }
}
?>
