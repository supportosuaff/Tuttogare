<?
	include_once("../../config.php");
	$error_permessi = true;
	if (!empty($_SESSION["codice_commissario"]) && !empty($_GET["codice"]) && isset($_GET["codice_lotto"]) && !empty($_GET["criterio"]))
	{
		if (checkCommissario($_GET["codice"])) {
			$codice_gara = $_GET["codice"];
			$pagina_login = true;
			include_once($root."/layout/top.php");
			include_once($root."/pannello-commissione/layout/intestazione.php");
			if (isset($gara)) {
				if (isset($codice_lotto) && in_array($_GET["criterio"],$criteri_valutazione) !== false) {
					$error_permessi = false;
					$criterio = $criteri[$_GET["criterio"]];
					?>
					<div class="box"><?= $criterio["descrizione"] ?></div>
					<?
					if ($coppie) {
						include("coppie/form.php");
					} else {
						include("diretto/form.php");
					}
				}
				include_once($root."/layout/bottom.php");
			}
		}
	}
	if ($error_permessi) {
		?>
		<h1>Impossibile accedere: Non si dispone dei permessi necessari o la gara non è in uno stato compatibile</h1>
		<?
	}
	?>
