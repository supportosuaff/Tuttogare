<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$edit = check_permessi("sda",$_SESSION["codice_utente"]);
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
				$bind = array(":codice"=>$_GET["codice"],":codice_ente"=>$_SESSION["ente"]["codice"]);
				$strsql = "SELECT * FROM b_bandi_sda WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$bind = array(":codice"=>$record["codice"]);
					$sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice AND sezione = 'sda' AND online = 'S' ORDER BY cartella, codice";
					$ris_allegati = $pdo->bindAndExec($sql,$bind);
					$sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice AND sezione = 'sda' AND online = 'N' ORDER BY cartella, codice";
					$ris_riservati = $pdo->bindAndExec($sql,$bind);
					$operazione = "UPDATE";
?>
				<h1>ALLEGATI</h1>
				<h3><?= $record["oggetto"] ?></h3><br>
				<div id="tabs">
                    <ul>
						<li><a href="#pubblici">Allegati pubblici</a></li>
						<li><a href="#riservati">Allegati riservati</a></li>
					</ul>
                    <div id="pubblici">
                    		<h2>Pubblici</h2>
                            <table width="100%" id="tab_allegati">
                                <? if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
                                    while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
                                        include($root."/allegati/tr_allegati.php");
                                    }
                                } ?>
                            </table>
                    </div>
                    <div id="riservati">
		                    <h2>Riservati</h2>
                     		<table width="100%" id="tab_riservati">
                                <? if (isset($ris_riservati) && ($ris_riservati->rowCount()>0)) {
									$cartella_attuale = "";
                                    while ($allegato = $ris_riservati->fetch(PDO::FETCH_ASSOC)) {
										if ($allegato["cartella"]!=$cartella_attuale) {
											$cartella_attuale = $allegato["cartella"];
											$echo_cartella = strtoupper(str_replace("/"," ",$allegato["cartella"]));
											?>
												<tr><td><span class="fa fa-folder-open fa-2x"></span></td><td colspan="4"><strong><? echo $echo_cartella  ?></strong></td></tr>
                                            <?
										}
                                        include($root."/allegati/tr_allegati.php");
                                    }
                                } ?>
                            </table>
                     </div>
			    <div class="clear"></div>
			    </div>

                </form>
                            <button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
                                       <img src="/allegati/icon.png" alt="Allega" width="15" style="vertical-align:middle"> Allega file
                            </button>

                <?
					$form_upload["codice_gara"] = $record["codice"];
					$form_upload["sezione"] = "sda";
					include($root."/allegati/form_allegati.php");
                ?>

			    <? include($root."/sda/ritorna.php"); ?>
				<script>
                    $("#tabs").tabs();
                    
                </script>
    		<?
			} else {

				echo "<h1>Bando non trovato</h1>";

				}

	include_once($root."/layout/bottom.php");
	?>
