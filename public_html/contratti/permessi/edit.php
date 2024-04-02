<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	if(!empty($_GET["codice"]) && !isset($_GET["codice_gara"])) {
		if(!isset($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("user",$_SESSION["codice_utente"])) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	$codice_contratto = $_GET["codice"];
	$bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice_contratto' => $codice_contratto);
	$sql  = "SELECT b_contratti.*, b_enti.denominazione FROM b_contratti JOIN b_enti ON b_contratti.codice_ente = b_enti.codice ";
	$sql .= "WHERE b_contratti.codice = :codice_contratto AND b_contratti.codice_gestore = :codice_ente ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
		$sql .= "AND (b_contratti.codice_ente = :codice_ente_utente OR b_contratti.codice_gestore = :codice_ente_utente) ";
	}
	$ris  = $pdo->bindAndExec($sql,$bind);
	if($ris->rowCount() > 0) {
		?>
		<h1>UTENTI</h1>
		<form name="box" method="post" action="save.php" rel="validate">
			<input type="hidden" name="codice_contratto" value="<? echo $_GET["codice"]; ?>">
			<div class="comandi"><button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button></div>
			<?
			$bind = array(":codice_ente" => $_SESSION["ente"]["codice"]);
			$sql_utenti  = "SELECT b_utenti.*, b_enti.denominazione, b_gruppi.gruppo as ruolo FROM b_utenti ";
			$sql_utenti .= "JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice ";
			$sql_utenti .= "LEFT JOIN b_enti ON b_utenti.codice_ente = b_enti.codice ";
			$sql_utenti .= "WHERE b_gruppi.gerarchia = 2 ";
			$sql_utenti .= "AND (b_enti.codice = :codice_ente OR b_enti.sua = :codice_ente) ";
			$sql_utenti .= "ORDER BY cognome,nome,dnascita ";
			$ris_utenti  = $pdo->bindAndExec($sql_utenti,$bind);
			if($ris_utenti->rowCount() > 0) {
				?>
				<table style="text-align:center; width:100%; font-size:0.8em" id="utenti" class="elenco">
	        <thead>
	        <tr><th width="5"></th><th>Nominativo</th><th width="80">Data di nascita</th><th width="100">Codice Fiscale</th><th width="250">Ente</th><th width="10">Attiva Disattiva</th></tr>
	        </thead>
        	<tbody>
						<?
						$sth_check_permessi = $pdo->prepare("SELECT * FROM b_permessi_contratti WHERE codice_utente = :codice_utente AND codice_contratto = :codice_contratto AND codice_ente = :codice_ente");
						$sth_check_permessi->bindValue(':codice_contratto', $codice_contratto);
						$sth_check_permessi->bindValue(':codice_ente', $_SESSION["ente"]["codice"]);
						while ($rec_utente = $ris_utenti->fetch(PDO::FETCH_ASSOC)) {
							$sth_check_permessi->bindValue(':codice_utente', $rec_utente["codice"]);
							$sth_check_permessi->execute();
							$codice = $rec_utente["codice"];
							$nominativo	= ucwords(strtolower(html_entity_decode($rec_utente["cognome"] . " " . $rec_utente["nome"])));
							$data = mysql2date($rec_utente["dnascita"]);
							$cf = strtoupper($rec_utente["cf"]);
							$attivo = FALSE;
							$colore = "#C00";
							if($sth_check_permessi->rowCount() > 0) {
								$colore = "#3C0";
								$attivo = TRUE;
							}
							?>
							<tr id="<?= $codice ?>">
	            	<td  id="flag_<?= $codice ?>" style="background-color: <?= $colore ?>"></td>
               	<td style="text-align:left"><strong><?= $nominativo ?></strong></td>
                <td><?= $data ?></td>
                <td><?= $cf ?></td>
                <td><?= $rec_utente["denominazione"] ?></td>
                <td>
									<input type="image" onClick="permessi('<?= $codice ?>');return false;" src="/img/switch.png" title="Abilita/Disabilita">
	                <input <? if (!$attivo) echo "disabled='disabled'" ?> type="hidden" name="utenti[<?= $codice ?>]" id="utente_<?= $codice ?>" value="<?= $codice ?>">
								</td>
	            </tr>
							<?
						}
						?>
					</tbody>
				</table>
				<script type="text/javascript">
					function permessi(codice) {
						if ($("#utente_"+codice+":disabled").length > 0) {
							$("#utente_"+codice).removeAttr('disabled');
							$("#utente_"+codice).removeProp('disabled');
							$("#flag_"+codice).css('background-color','#3C0');
						} else {
							$("#utente_"+codice).attr('disabled','disabled');
							$("#utente_"+codice).prop('disabled','disabled');
							$("#flag_"+codice).css('background-color','#C00');
						}
						return false;
					}
				</script>
				<?
			}
			?>
			  <input type="submit" class="submit_big" value="Salva">
		</form>
		<?
	} else {
		?>
		<h2 class="ui-state-error">Contratto non trovato</h2>
		<?
	}
	include_once $root . '/contratti/ritorna_pannello_contratto.php';
	include_once $root."/layout/bottom.php";
?>
