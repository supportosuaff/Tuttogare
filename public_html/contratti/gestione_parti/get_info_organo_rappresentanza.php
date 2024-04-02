<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	if (empty($_SESSION["codice_utente"]) || !check_permessi("contratti",$_SESSION["codice_utente"]) || empty($_POST["codice"])) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
		die();
	} else {
    $codice = $_POST["codice"];
    $sql = "SELECT * FROM b_contraenti WHERE codice = :codice AND tipologia = 'ore'";
    $ris = $pdo->bindAndExec($sql, array(':codice' => $codice));
    if($ris->rowCount() > 0) {
      $rec = $ris->fetch(PDO::FETCH_ASSOC);
      $rec['codice_organo'] = $rec["codice"];
      $rec['ruolo_ore'] = strtolower(html_entity_decode($rec["ruolo"], ENT_QUOTES, 'UTF-8'));
      $rec['titolo_ore'] = strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8'));
      $rec["data_nascita"] = mysql2date($rec["data_nascita"]);
      echo json_encode($rec);
    } else {
      header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
  		die();
    }
  }
?>
