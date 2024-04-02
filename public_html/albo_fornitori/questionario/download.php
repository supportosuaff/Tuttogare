<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$content = false;
	if (is_operatore() && isset($_GET["partecipante"]) && isset($_GET["codice_bando"])) {
		$ris = $pdo->go("SELECT codice,codice_bando,codice_operatore FROM r_partecipanti_albo WHERE codice_utente = :utente AND codice = :codice AND codice_bando = :bando",[":utente"=>$_SESSION["codice_utente"],":codice"=>$_GET["partecipante"],":bando"=>$_GET["codice_bando"]]);
		if ($ris->rowCount()) {
			$record = $ris->fetch(PDO::FETCH_ASSOC);
			$fileName = "{$config["arch_folder"]}/allegati_albo/{$record["codice_operatore"]}/questionario-{$record["codice_bando"]}-{$record["codice"]}.pdf";
			if (file_exists($fileName)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.$record["codice"].'_questionario.pdf');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				readfile($fileName);
			} else {
				echo "<h1>". traduci("Impossibile accedere") . "</h1>";
			}
		} else {
			echo "<h1>". traduci("Impossibile accedere") . "</h1>";
		}
	} else {
		echo "<h1>". traduci("Impossibile accedere") . "</h1>";
	}
	?>
