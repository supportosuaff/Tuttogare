<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
	if ((isset($_GET["codice"]) || isset($_GET["cod"])))
	{
		if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
		$codice_gara = $_GET["codice"];
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]))
		{
			$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
			if ($codice_fase!==false) {
				$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}

			if (!$edit)
			{
				echo '<meta http-equiv="refresh" content="0;URL=/gare/pannello.php?codice='.$codice_gara.'">';
				die();
			}
			else
			{
				if(!empty($codice_gara)) {
					$bind = array();
					$bind[":codice"] = $codice_gara;
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$strsql = "SELECT * FROM b_gare WHERE codice = :codice AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
					if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
						$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
					}
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if($risultato->rowCount() < 1) {
						echo '<meta http-equiv="refresh" content="0;URL=/gare/">';
						die();
					}
				}
				?>
				<h1>PUBBLICAZIONI SULLA GAZZETTA UFFICIALE EUROPEA</h1>
				<?
				include $root . '/guue/check_stato.php';
				include $root . '/guue/table.php';
				include $root . '/gare/ritorna.php';
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0;URL=/gare/pannello.php?codice='.$codice_gara.'">';
			die();
		}
	}
	else
	{
		echo '<meta http-equiv="refresh" content="0;URL=/gare/>';
		die();
	}
	include_once($root."/layout/bottom.php");
?>