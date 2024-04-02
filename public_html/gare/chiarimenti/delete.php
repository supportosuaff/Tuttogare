<?
	session_start();
	include_once "../../../config.php";
	include_once "{$root}/inc/funzioni.php";
	if(is_operatore() && isset($_SESSION["ente"]["codice"]) && isset($_SESSION["codice_utente"])) {
		if(! empty($_POST)) {
			$codici = explode(':', $_POST["codice"]);
			if(count($codici) == 2) {
				$allegato = $pdo->bindAndExec(
					"DELETE FROM b_allegati WHERE codice = :codice AND codice_gara = :codice_gara AND sezione = 'faq-gara' AND codice_ente = :codice_ente",
					array(':codice' => $codici[0], ':codice_gara' => $codici[1], ':codice_ente' => $_SESSION["ente"]["codice"])
				);
				if($allegato->rowCount() == 1) {
					?>
					var codici = $("#cod_allegati").val();
					if (codici != undefined) {
						codici = codici.replace('<?= $codici[0] ?>', '');
						$("#cod_allegati").val(codici);
					}
					$('#allegato_chiarimenti_<?= $codici[0] ?>').remove();
					<?
				}
			}
		}
	}
?>