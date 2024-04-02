<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index ["danger"];
    if ($record["stato"] > 4) $st_color = $st_index ["ok"];
  }

?>
