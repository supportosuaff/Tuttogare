<?
  if (isset($record) && isset($st_index)) {
    if ($record["stato"] > 2) {
      $st_color = $st_index ["ok"];
    } else {
      $st_color = $st_index ["danger"];
    }
  }

?>
