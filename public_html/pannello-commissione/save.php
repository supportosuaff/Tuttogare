<?
	include_once("../../config.php");
	$error_salvataggio = true;
	if (!empty($_SESSION["codice_commissario"])
			&& !empty($_POST["codice_gara"])
			&& isset($_POST["codice_lotto"])
			&& !empty($_POST["codice_criterio"]))
	{
		if (checkCommissario($_POST["codice_gara"])) {
			$checkPunteggio = $pdo->go("SELECT punteggio FROM b_punteggi_criteri WHERE codice_criterio = :criterio 
																										AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto ",
																										[":criterio"=>$_POST["codice_criterio"],":codice_gara"=>$_POST["codice_gara"],":codice_lotto"=>$_POST["codice_lotto"]]);
			if ($checkPunteggio->rowCount() == 0) {
				$codice_gara = $_POST["codice_gara"];
				$codice_lotto = $_POST["codice_lotto"];
				$hide_layout = true;
				include("layout/intestazione.php");
				if (isset($gara)) {
					if (isset($codice_lotto) && in_array($_POST["codice_criterio"],$criteri_valutazione) !== false) {
						$criterio = $criteri[$_POST["codice_criterio"]];
						if ($coppie) {
							include("coppie/save.php");
						} else {
							include("diretto/save.php");
						}
						if (!$error_salvataggio) {
							?>
							alert("Salvataggio avvenuto con successo!");
							window.location.reload();
							<?
						}
					}
				}
			}
		}
	}
	if ($error_salvataggio) {
		?>
		alert("Si è verificato un errore");
		<?
	}
	?>
