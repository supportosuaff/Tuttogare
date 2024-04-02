<?
	include_once("codfisc_class.php");
	
	if (isset($_GET["nome"])) {
		$cognome = $_GET["cognome"];
		$nome = $_GET["nome"];
		$data = str_replace("/","",$_GET["dnascita"]);
		$sesso = $_GET["sesso"];
		$comune = $_GET["luogo"];
		$prov = $_GET["prov"];
		$codicefiscale = AlberT_CodFis($cognome, $nome, $data, $sesso, $comune, $prov);
		if(strlen($codicefiscale)==16) {
			echo $codicefiscale;
		} else {
			echo "Errore";
		}
	}
?>
