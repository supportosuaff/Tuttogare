<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
		if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
			if ($codice_fase!==false) {
				$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
				die();
			}
		} else {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
		$codice = $_GET["codice"];
		$bind = array();
		$bind[":codice"]=$codice;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
		$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		if ($_SESSION["gerarchia"] > 0) {
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
		}
		$risultato = $pdo->bindAndExec($strsql,$bind);
		?>
		<h1>Contratto</h1>
		<?
		if ($risultato->rowCount() > 0) {
				$bind = array();
				$bind[":codice"]=$codice;
				$sql_lotti = "SELECT b_lotti.* FROM b_lotti WHERE b_lotti.codice_gara = :codice";
				$sql_lotti.= " GROUP BY b_lotti.codice ORDER BY codice";
				$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				$print_form = false;
				if ($ris_lotti->rowCount()>0) {
					if (isset($_GET["lotto"])) {
						$codice_lotto = $_GET["lotto"];
						$bind = array();
						$bind[":codice_lotto"]=$codice_lotto;
						$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice_lotto ORDER BY codice";
						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
						if ($ris_lotti->rowCount()>0) {
							$print_form = true;
							$risultato = $pdo->go("SELECT * FROM b_contratti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_gestore = :codice_gestore ",
															[":codice_gara"=>$codice,":codice_lotto"=>$codice_lotto,":codice_gestore"=>$_SESSION["ente"]["codice"]]);
						}
					} else {
						while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
						?>
							<a class="submit_big" href ="index.php?codice=<? echo $_GET["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
								<? echo $lotto["oggetto"]; ?>
							</a>
						<?
						}
					}
					include($root."/gare/ritorna.php");
				} else {
					$print_form = true;
					$codice_lotto = 0;
					$risultato = $pdo->go("SELECT * FROM b_contratti WHERE codice_gara = :codice_gara AND codice_lotto IS NULL AND codice_gestore = :codice_gestore ",
					[":codice_gara"=>$codice,":codice_gestore"=>$_SESSION["ente"]["codice"]]);
				}
			if ($print_form) {
				$path="/contratti/edit.php?codice=0&codice_gara={$codice}&codice_lotto={$codice_lotto}";
				if ($risultato->rowCount() > 0) {
					$path = "/contratti/pannello.php?codice={$risultato->fetch(PDO::FETCH_ASSOC)["codice"]}&codice_gara={$codice}";
				} 
				echo "<meta http-equiv=\"refresh\" content=\"0;URL={$path}\">";
			}
		} else {
			echo "<h1>Gara non trovata</h1>";
		}
	} else {
		echo "<h1>Gara non trovata</h1>";
	}
	include_once($root."/layout/bottom.php");
?>
