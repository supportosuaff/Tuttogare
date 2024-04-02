<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	if (isset($_GET["cod"]) && isset($_SESSION["ente"]["codice"])) {

				$codice = $_GET["cod"];
				$bind = array(":codice"=>$codice,":codice_ente"=>$_SESSION["ente"]["codice"]);
				if (!isset($_SESSION["codice_utente"])) {
					$strsql  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id ";
					$strsql .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
					$strsql .= "WHERE b_avvisi.codice = :codice AND pubblica = '2' AND (codice_gestore = :codice_ente OR b_gare.codice_ente = :codice_ente) ";
					$strsql.= " AND b_avvisi.data <= now() ";
				} else {
					if (is_operatore()) {
						$bind[":codice_utente"] = $_SESSION["codice_utente"];
						$strsql  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id ";
						$strsql .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
						$strsql .= "LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara ";
						$strsql .= "JOIN b_procedure ON b_procedure.codice = b_gare.procedura ";
						$strsql .= "WHERE b_avvisi.codice = :codice AND  (codice_gestore = :codice_ente OR b_gare.codice_ente = :codice_ente) ";
						$strsql.= " AND b_avvisi.data <= now() ";
						$strsql .= "AND (pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente)))) ";
					} else {
						$strsql  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id ";
						$strsql .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
						$strsql .= "WHERE b_avvisi.codice = :codice AND  (pubblica > 0) AND (codice_gestore = :codice_ente OR b_gare.codice_ente = :codice_ente) ";
					}
				}
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					if ($record["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$record["cod_allegati"])) {
								$allegati = explode(";",$record["cod_allegati"]);
								$str_allegati = ltrim(implode(",",$allegati),",");
								$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ") AND online = 'S'";
								$ris_allegati = $pdo->query($sql);
					}
?>
        <div class="box">
        <?				echo echo_intestazione(mysql2date($record["data"]),$record["titolo"],"news",$record["codice"],FALSE);
					echo $record["testo"] . "<div class=\"clear\"></div></div>";
					if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
							$public = true;
							?>
                            <div class="box"><h2><?= traduci("Allegati") ?></h2>
                            <table width="100%" id="tab_allegati">
                            <?
                       			while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
									include($root."/allegati/tr_allegati.php");
								}
							?>
                        </table>
                        </div>
                        <?
						}
						?>
    <div class="clear"></div>
   	<?
   						$bind = array(":codice_utente"=>$_SESSION["ente"]["codice"],":codice_gara"=>$record["codice_gara"]);
						$sql  = "SELECT b_gare.* FROM b_gare ";
						$sql .= "WHERE (b_gare.codice_ente = :codice_utente OR b_gare.codice_gestore = :codice_utente) AND b_gare.codice = :codice_gara";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>0) {
							if ($rec_gara = $ris->fetch(PDO::FETCH_ASSOC)) {
								?><div class="box"><h2><?= traduci("riferimento") ?></h2>
	                            <a href="/gare/id<? echo $rec_gara["codice"] ?>-dettagli" title="Dettagli gara"><strong>ID: <? echo $rec_gara["id"] ?></strong> - <? echo $rec_gara["oggetto"] ?></a>
	                            </div>
	                            <?
                        	}
						} ?>
    <div class="note">
                    Ultimo aggiornamento il <? echo $record["timestamp"] ?>
                    </div>
                    <br>
    <?


			} else {

				echo "<h1>".traduci('impossibile accedere')."</h1>";

				}

	?>


<?
	$modulo_dx = array("allegati.php");
	include_once($root."/layout/bottom.php");
	?>
