<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
		if (isset($_GET["codice"])) {

				$codice = $_GET["codice"];
				$bind = array();
				$bind[":codice_bando"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

				$strsql  = "SELECT * ";
				$strsql .= "FROM b_bandi_albo ";
				$strsql .= "WHERE codice = :codice_bando ";
				$strsql .= "AND codice_gestore = :codice_ente ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					?>
					<h1>PANNELLO DI GESTIONE - AVVISO #<? echo $record["id"] ?> <input type="image" onclick="$('#info').toggle()" src="/img/info.png" title="Ulteriori informazioni"></h1>
                    <div id="info" class="ui-state-error padding" style="display:none">
                    <h2>Ulteriori informazioni</h2>
                    	<table width="100%">
                    		<tr><td class="etichetta">Codice Bando Univoco</td><td><strong><? echo $record["codice"]; ?></strong></td></tr>
                    		<tr><td class="etichetta">Codice Bando Relativo</td><td><strong><? echo $record["id"]; ?></strong></td></tr>
                    		<tr><td class="etichetta">Codice Ente</td><td><strong><? echo $record["codice_ente"]; ?></strong></td></tr>
                    	</table>
                    </div>
                    <h2><? echo $record["oggetto"] ?></h2>
                    <a class="pannello" href="allegati/edit.php?codice=<? echo $record["codice"]?>" title="Allegati">Allegati</a>
                    <a class="pannello" href="edit.php?codice=<? echo $record["codice"]?>" title="Dati preliminari">Modifica dati preliminari</a>
										<a class="pannello" href="dgue/edit.php?codice=<? echo $record["codice"]?>" title="Configurazione DGUE">Configurazione DGUE</a>
										<a class="pannello" href="modulistica/index.php?codice=<? echo $record["codice"]?>" title="Modulistica">Modulistica</a>
                    <a class="pannello" href="pubblica/edit.php?codice=<? echo $record["codice"]?>" title="Pubblica">Pubblica</a>
                    <a class="pannello" href="partecipanti/index.php?codice=<? echo $record["codice"]?>" title="Partecipanti">Partecipanti
						<?
							$bind = array();
							$bind[":codice_bando"] = $record["codice"];
							$strsql = "SELECT * FROM r_partecipanti_albo WHERE codice_bando = :codice_bando AND valutato = 'N' AND conferma = 'S'";
							$ris_valutati = $pdo->bindAndExec($strsql,$bind);
							if ($ris_valutati->rowCount()>0) echo "<div class=\"badge\">" . $ris_valutati->rowCount() . "</div>";
						?>
					</a>
                    <?
				} else {
					echo "<h1>Non autorizzato - Contattare l'amministratore</h1>";
				}
		} else {
			echo "<h1>Gara non trovata</h1>";
		}

	include_once($root."/layout/bottom.php");
	?>
