<?
  $show=false;
  $scadenza = mysql2date($record["data_scadenza"]);
  if (!empty($scadenza)) {
    if (strtotime($record["data_scadenza"]) < time()) $show = true;
  }
?>
