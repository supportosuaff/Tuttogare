<?php
$now = new DateTime();
if (!isset($_GET["format"])) {
  echo $now->format("M j, Y H:i:s O")."\n";
} else {
  echo $now->format("d/m/Y H:i:s")."\n";
}
?>
