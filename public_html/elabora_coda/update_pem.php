<?
  if (!empty($elabora_coda)) {
    $current_time = (int) date('Hi');
    if($current_time >= 300 &&  $current_time <= 305) {
      include_once($root."/inc/p7m.class.php");
      P7Manager::updatePEM(true);
    }
  }
?>
