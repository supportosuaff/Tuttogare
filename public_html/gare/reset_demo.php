<?
session_start();
include_once("../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
$errore = "";
if (isset($_SESSION["codice_utente"])) {
	$edit = check_permessi("gare",$_SESSION["codice_utente"]);
	if (!$edit) {
		die();
	}
} else {
	die();
}

if (!$edit) {
	die();
} else {
	/*
		Inserire un elemento nella variabile $demo_code contenente un array con
			Chiave = al codice gara da resettare
			Elementi = Codici delle buste da riaprire
	*/
	if ($_SESSION["ente"]["codice"] == "1") {
		$demo_code = array();
		$demo_code["1545"] = array("19375","19377");
		$demo_code["1155"] = array("15260","15259");
		$demo_code["3685"] = array("38281","38284");
		$demo_code["4575"] = array("46374","46377");
		$demo_code["10690"] = array("95436","95442");
	} else if ($_SESSION["ente"]["codice"] == "237") {
		$demo_code = array();
		$demo_code["1337"] = array("17628","17630");
	} else if ($_SESSION["ente"]["codice"] == "780") {
		$demo_code = array();
		$demo_code["3656"] = array("38217","38220");
		$demo_code["18556"] = array("161950","161952");
	} else if ($_SESSION["ente"]["codice"] == "257") {
		$demo_code = array();
		$demo_code["4712"] = array("46819","46823");
	}
	if (isset($demo_code) && isset($_GET["codice"]) && is_numeric($_GET["codice"])) {
		$codice_gara = $_GET["codice"];
		$codici_gare = array_keys($demo_code);
		if (in_array($codice_gara,$codici_gare) !== false) {
			$bind = array(":codice_gara"=>$codice_gara);
			$pdo->bindAndExec("UPDATE `b_gare` SET `stato` = '3', `soglia_anomalia` = NULL, `media` = NULL, `scarto_medio` = NULL,
									 `sequenza_anomalia` = '', `algoritmo_anomalia` = '', `sequenza_coef` = '', `coef_e` = NULL,
									 `messaggio_anomalia` = '', `seduta_pubblica` = 'N', `numero_atto_esito` = '', `data_atto_esito` = '', `ribasso` = 0 WHERE `codice` = :codice_gara",$bind);
			if (count($demo_code[$codice_gara]) > 0) {
				foreach($demo_code[$codice_gara] AS $busta) {
					$pdo->bindAndExec("UPDATE `b_buste` SET `codice_allegato` = '', `aperto` = 'N' WHERE `codice` = :codice_busta AND codice_gara = :codice_gara",array(":codice_busta"=>$busta,":codice_gara"=>$codice_gara));
				}
			}
			$pdo->bindAndExec("UPDATE `r_partecipanti` SET `primo` = 'N', `secondo` = 'N', `anomalia` = 'N', `escluso` = 'N', `motivazione_anomalia` = '' WHERE `codice_gara` = :codice_gara",$bind);
			$pdo->bindAndExec("DELETE FROM `r_punteggi_gare` WHERE `codice_gara` = :codice_gara",$bind);
			$pdo->bindAndExec("UPDATE b_documentale SET attivo = 'N' WHERE `codice_gara` = :codice_gara",$bind);
			?>
			window.location.reload();
			<?
		} else {
			die();
		}
	} else {
		die();
	}
}
?>
