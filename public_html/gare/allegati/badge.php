<?
  if (isset($record) && isset($st_index)) {
    $bind = array();
    $bind[":codice"]=$record["codice"];
    $sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice AND sezione = 'gara' AND online = 'S' ORDER BY cartella, codice";
    $ris_badge = $pdo->bindAndExec($sql,$bind);
    if ($ris_badge->rowCount()>0) {
			echo "<span class=\"badge\">" . $ris_badge->rowCount() . "</span>";
		}
  }

?>
