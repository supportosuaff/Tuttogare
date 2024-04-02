<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	if (empty($_SESSION["codice_utente"]) || !check_permessi("contratti",$_SESSION["codice_utente"]) || empty($_POST["codice_ufficiale"])) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
		die();
	} else {
    $codice_ufficiale = $_POST["codice_ufficiale"];
    $sql = "SELECT * FROM b_ufficiale_rogante WHERE codice = :codice_ufficiale";
    $ris = $pdo->bindAndExec($sql, array(':codice_ufficiale' => $codice_ufficiale));
    if($ris->rowCount() > 0) {
      $rec = $ris->fetch(PDO::FETCH_ASSOC);
      $rec["codice_ufficiale"] = $rec["codice"];
      $rec["data_nascita"] = mysql2date($rec["data_nascita"]);
      $rec["titolo"] = strtolower(html_entity_decode($rec["titolo"], ENT_QUOTES, 'UTF-8'));
      $rec["ruolo"] = strtolower(html_entity_decode($rec["ruolo"], ENT_QUOTES, 'UTF-8'));
      echo json_encode($rec);
    } else {
      header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
  		die();
    }
  }
?>
