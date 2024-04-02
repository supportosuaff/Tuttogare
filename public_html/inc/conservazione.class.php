<?
class Conservazione {
  public static function setup()
  {
    global $root;
    require_once "{$root}/inc/conservazione.default.class.php";
    if(!empty($_SESSION["ente"]["codice"]) && file_exists("{$root}/inc/integrazioni/{$_SESSION["ente"]["codice"]}/conservazione.bridge.class.php")) {
      require_once "{$root}/inc/integrazioni/{$_SESSION["ente"]["codice"]}/conservazione.bridge.class.php";
      $class_name = "Conservazione".$_SESSION["ente"]["codice"];
      return new $class_name();
    } else {
      return new ConservazioneDefault();
    }
  }
}
?>
