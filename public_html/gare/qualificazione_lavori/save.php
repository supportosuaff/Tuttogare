<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
$lock = true;
if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
	$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
	if ($codice_fase !== false) {
		$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
		$edit = $esito["permesso"];
		$lock = $esito["lock"];
	}
	if (!$edit) {
		die();
	}
} else {
	die();
}
if ($edit && !$lock) {
	log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Qualificazione lavori");
	$bind=array();
	$bind[":codice"] = $_POST["codice_gara"];
	$strsql = "DELETE FROM b_qualificazione_lavori WHERE codice_gara = :codice";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if (isset($_POST["qualificazione"])) {
		foreach ($_POST["qualificazione"] as $record) {
			$record["codice_gara"] = $_POST["codice_gara"];
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_qualificazione_lavori";
			$salva->operazione = "INSERT";
			$salva->oggetto = $record;
			$codice_qualificazione = $salva->save();
			if ($codice_qualificazione === false) $errore = true;
		}
	}
	if (!isset($errore)) {
		/*=======================================
		=            Simog Operation            =
		=======================================*/
		$ris_cat_prev = $pdo->bindAndExec("SELECT b_categorie_soa.id FROM b_categorie_soa JOIN b_qualificazione_lavori ON b_qualificazione_lavori.codice_categoria = b_categorie_soa.codice WHERE b_qualificazione_lavori.codice_gara = :codice_gara AND b_qualificazione_lavori.codice_lotto = :codice_lotto AND b_qualificazione_lavori.tipo = 'P'", array(':codice_gara' => $_POST["codice_gara"], ':codice_lotto' => $record["codice_lotto"]));
		if($ris_cat_prev->rowCount() > 0) {
			$rec_cat_prev = $ris_cat_prev->fetch(PDO::FETCH_ASSOC);
			$dati_lotto["id_categoria_prevalente"] = $rec_cat_prev["id"];
		} else {
			$ris_cat_prev = $pdo->bindAndExec("SELECT b_tipologie.tipologia FROM b_tipologie JOIN b_gare ON b_tipologie.codice = b_gare.tipologia WHERE b_gare.codice = :codice", array(':codice' => $_POST["codice_gara"]));
			if($ris_cat_prev->rowCount() > 0) {
				$rec_cat_prev = $ris_cat_prev->fetch(PDO::FETCH_ASSOC);
				if(strpos(strtolower($rec_cat_prev["tipologia"]), "forniture")) {
					$dati_lotto["id_categoria_prevalente"] = "FB";
				} elseif (strpos(strtolower($rec_cat_prev["tipologia"]), "servizi")) {
					$dati_lotto["id_categoria_prevalente"] = "FS";
				}
			}
		}
		/*=====  End of Simog Operation  ======*/

		$href = "/gare/pannello.php?codice=" . $_POST["codice_gara"];
		$href = str_replace('"',"",$href);
		$href = str_replace(' ',"-",$href);
		?>
		alert('Modifica effettuata con successo');
		window.location.href = '<? echo $href ?>';
		<?
	} else {
		alert('Errore nel salvataggio. Riprovare.');
	}
}
?>
