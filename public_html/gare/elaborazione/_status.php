<?
  if (isset($record) && isset($st_index)) {
    if ($rec["link"] == "/gare/elaborazione/edit.php") {
      $st_color = $st_index ["danger"];
      if ($record["stato"] > 1) $st_color = $st_index ["ok"];
    }
  }

?>
