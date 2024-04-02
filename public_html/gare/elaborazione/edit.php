<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
	$in_elaborazione = true;
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
				if ($risultato->rowCount() > 0) {
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
					$bind = array();
					$bind[":modalita"] = $record_gara["modalita"];
					$sql = "SELECT * FROM b_modalita WHERE codice = :modalita";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>0) {
							$rec = $ris->fetch(PDO::FETCH_ASSOC);
							$record_gara["online"] = $rec["online"];
						}
					$riepilogo = $record_gara;
					$_SESSION["gara"] = $record_gara;
					$bind = array();
					$bind[":codice"] = $record_gara["codice"];
					$sql = "SELECT * FROM b_importi_gara WHERE codice_gara = :codice";
					$ris_importi = $pdo->bindAndExec($sql,$bind);
?>
				<h1>ELABORAZIONE GARA</h1>
				<? if (!$lock) { ?>
					<div id="riepilogo" style="display:none;">
						<?
							$riepilogo["tipologie_gara"] = "";
							$bind = array();
							$bind[":codice"] = $riepilogo["tipologia"];
							$sql = "SELECT tipologia FROM b_tipologie WHERE codice = :codice";
							$ris_tipologie = $pdo->bindAndExec($sql,$bind);
							if ($ris_tipologie->rowCount()>0) {
									$rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC);
									$riepilogo["tipologie_gara"] .= $rec_tipologia["tipologia"];
							}
							$bind = array();
							$bind[":codice"] = $riepilogo["modalita"];
							$sql = "SELECT * FROM b_modalita WHERE codice= :codice";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount()>0) {
								$rec = $ris->fetch(PDO::FETCH_ASSOC);
								$riepilogo["nome_modalita"] = $rec["modalita"];
								$riepilogo["online"] = $rec["online"];
							}
							$bind = array();
							$bind[":codice"] = $riepilogo["criterio"];
							$sql = "SELECT * FROM b_criteri WHERE codice= :codice";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount()>0) {
								$rec = $ris->fetch(PDO::FETCH_ASSOC);
								$directory = $rec["directory"];
								$riepilogo["nome_criterio"] = $rec["criterio"];
								$riepilogo["riferimento_criterio"] = $rec["riferimento_normativo"];
							}
							$bind = array();
							$bind[":codice"] = $riepilogo["procedura"];
							$sql = "SELECT * FROM b_procedure WHERE codice = :codice";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount()>0) {
								$rec_procedura = $ris->fetch(PDO::FETCH_ASSOC);
								$directory = $rec_procedura["directory"];
								$riepilogo["nome_procedura"] = $rec_procedura["nome"];
								$riepilogo["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
							}

						$string_cpv = "";
						$cpv = array();
						$bind = array();
						$bind[":codice"] = $riepilogo["codice"];
						$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice ORDER BY codice";
						$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
						if ($risultato_cpv->rowCount()>0) {
							$string_cpv = "<ul>";
							while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
								$string_cpv .= "<li><strong>" . $rec_cpv["codice"] . "</strong> - " . $rec_cpv["descrizione"] . "</li>";
							}
							$string_cpv .= "</ul>";
						}
						?>
						<table>
							<tr>
						    	<td class="etichetta">Procedura</td><td><strong><? echo $riepilogo["nome_procedura"] ?></strong><br> <? echo $riepilogo["riferimento_procedura"] ?></td>
								<td class="etichetta">Criterio</td><td><strong><? echo $riepilogo["nome_criterio"] ?></strong><br> <? echo $riepilogo["riferimento_criterio"] ?></td>
							</tr>
						    <tr><td class="etichetta">Oggetto</td><td colspan="3"><strong><? echo $riepilogo["tipologie_gara"] ?></strong><br><? echo $riepilogo["oggetto"] ?></td></tr>
						    <tr><td class="etichetta">Totale appalto</td><td colspan="2"><strong>&euro; <? echo number_format($riepilogo["prezzoBase"],2,",","."); ?></strong></td></tr>
						   <? if ($string_cpv != "") { ?>
						<tr><td class="etichetta">Categorie</td><td colspan="3">
						<? echo $string_cpv; ?>
						</td></tr>
						<? } ?>

						<tr><td class="etichetta">Breve descrizione</td><td colspan="3"><? echo $riepilogo["descrizione"] ?></td></tr>
						<tr><td class="etichetta">Struttura proponente</td><td colspan="3"><? echo $riepilogo["struttura_proponente"] ?></td></tr>
						<tr><td class="etichetta">Responsabile del servizio</td><td><? echo $riepilogo["responsabile_struttura"] ?></td><td class="etichetta">Responsabile del procedimento</td><td><? echo $riepilogo["rup"] ?></td></tr>
						</table>
					</div>
                	    <form name="box" method="post" action="save.php" rel="validate">
                    	<input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"]; ?>">

						<div class="comandi">
							<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
							<button class='btn-round btn-warning'  onClick="$('#riepilogo').dialog({modal:true,width:'800',title:'Specchietto riepilogativo'}); return false;"title="Riepilogo Gara">
								<span class='fa fa-search'></span>
							</button>

						</div>
                 <? } ?>
				<div>
					<?
						$bind = array();
						$bind[":codice_gestore"] = $_SESSION["ente"]["codice"];
						$bind[":tipologia_a"] = $riepilogo["tipologia"];
						$bind[":tipologia_b"] = $riepilogo["tipologia"] . ";%";
						$bind[":tipologia_c"] = "%;" . $riepilogo["tipologia"] . ";%";
						$bind[":tipologia_d"] = "%;" . $riepilogo["tipologia"];
						$sql_minimi = "SELECT * FROM b_impostazioni_dati_minimi WHERE (tipologie = :tipologia_a OR tipologie LIKE :tipologia_b OR tipologie LIKE :tipologia_c OR tipologie LIKE :tipologia_d) AND
													 codice_gestore = :codice_gestore AND attivo = 'S' AND eliminato = 'N' ";
						$ris_minimi = $pdo->bindAndExec($sql_minimi,$bind);
						if ($ris_minimi->rowCount() > 0) { ?>
						<div id="tabs">
							<ul>
								<li><a href="#elaborazione">Elaborazione</a></li>
								<li><a href="#scheda">Altri dati</a></li>
							</ul>
							<div id="elaborazione">
					<? } ?>
						<? include("form.php"); ?>
					<? if ($ris_minimi->rowCount() > 0) {
						$print_form_minimi = true;
						?>
						</div>
						<div id="scheda"><? include("dati_minimi.php") ?></div>
					</div>
					<script>
						$("#tabs").tabs();
					</script>
					<? } ?>
			    <div class="clear"></div>
			    </div>
				<? if (!$lock) { ?>
                <input type="submit" class="submit_big" value="Salva">
                </form>
                <?
							} else {
								?>
									<script>
										$("#contenuto_top :input").not('.espandi').prop("disabled", true);
									</script>
								<?
							}
                ?>

			    <? include($root."/gare/ritorna.php"); ?>

    		<?
			} else {

				echo "<h1>Gara non trovata</h1>";

				}
			} else {

				echo "<h1>Gara non trovata</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
