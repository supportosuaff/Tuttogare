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

				if ($risultato->rowCount() > 0) {
					$gara = $risultato->fetch(PDO::FETCH_ASSOC);
					$operazione = "UPDATE";
?>
				<h1>PERMESSI</h1>
				<? if (!$lock) { ?>
					<form name="box" method="post" action="save.php" rel="validate">
						<input type="hidden" name="codice_gara" value="<? echo $_GET["codice"]; ?>">
						<div class="comandi">
							<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
						</div>
				<? }
				$bind = array();
				$bind[":codice_sua"] = $_SESSION["ente"]["codice"];
				$bind[":codice_ente"] = $gara["codice_ente"];
				$strsql  = "SELECT b_utenti.*, b_enti.denominazione
										FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
										JOIN b_enti ON b_utenti.codice_ente = b_enti.codice
										WHERE b_gruppi.gerarchia = 2
										AND (b_utenti.codice_ente = :codice_ente OR b_utenti.codice_ente = :codice_sua)
										ORDER BY cognome,nome,dnascita" ;
				$risultato_utenti  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

				$strsql  = "SELECT b_utenti.*, b_enti.denominazione
										FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
										JOIN b_enti ON b_utenti.codice_ente = b_enti.codice
										WHERE b_gruppi.gerarchia <= 2
										AND (b_utenti.codice_ente = :codice_ente OR b_utenti.codice_ente = :codice_sua)
										ORDER BY cognome,nome,dnascita" ;
				$risultato_all  = $pdo->bindAndExec($strsql,$bind)->fetchAll(PDO::FETCH_ASSOC); //invia la query contenuta in $strsql al database apero e connesso?>
				<div id="tabs">
					<ul>
						<? if ($risultato_utenti->rowCount() > 0) { ?><li><a href="#generali">Generali</a></li><? } ?>
						<li><a href="#buste">Buste</a></li>
					</ul>
					<?
					if ($risultato_utenti->rowCount() > 0) {
					?>
					<script>
						function filterUsersTable() {
							var text = $("#searchValue").val();
							if (text.length > 2) {
								$('.rowUser').hide();
								$('.searchTd').each(function() {
									content = $(this).html().toLowerCase();
									if (content.indexOf(text.toLowerCase()) > -1) {
										$(this).parent().show();
									}
								})
							} else {
								$('.rowUser').show();
							}
						}
					</script>
					<div id="generali">
						<div style="text-align:right; padding:10px;">
							<strong>Filtra: </strong><input type="text" onkeyup="filterUsersTable()" id="searchValue">
						</div>
						<table style="text-align:center; width:100%; font-size:0.8em" id="utenti" >
		        	<thead>
			        	<tr>
									<th width="5"></th>
									<th>Nominativo</th>
									<th width="80">Data di nascita</th>
									<th width="100">Codice Fiscale</th>
									<th width="250">Ente</th>
									<td></td>
									<th width="10">Attiva Disattiva</th>
								</tr>
		        	</thead>
		        	<tbody>
			       	<?

					while ($record = $risultato_utenti->fetch(PDO::FETCH_ASSOC)) {
						$codice			= $record["codice"];
						$nominativo		= $record["cognome"] . " " . $record["nome"];
						$data			= mysql2date($record["dnascita"]);
						$cf				= $record["cf"];
						$attivo			= false;
						$colore = "#C00";
						$bind = array();
						$bind[":codice"] = $codice;
						$bind[":codice_gara"] = $_GET["codice"];
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

						$sql = "SELECT * FROM b_permessi WHERE codice_utente = :codice AND codice_gara = :codice_gara AND codice_ente = :codice_ente";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount() >0) {
							$colore = "#3C0";
							$attivo = true;
						}

						?>
						<tr class="rowUser" id="<? echo $codice ?>">
            	<td  id="flag_<? echo $codice ?>" style="background-color: <? echo $colore ?>"></td>
             	<td class="searchTd" style="text-align:left"><strong><? echo $nominativo ?></strong></td>
              <td><? echo $data ?></td>
              <td class="searchTd"><? echo $cf ?></td>
							<td><? echo $record["denominazione"] ?></td>
							<td><? echo ($record["temporaneo"] == "C") ? "Commissario" : "" ?></td>
              <td>
								<button class="btn-round btn-primary" onClick="permessi('<? echo $codice ?>');return false;" title="Abilita/Disabilita">
									<span class="fa fa-refresh"></span>
								</button>
								<input <? if (!$attivo) echo "disabled='disabled'" ?> type="hidden" name="utenti[<? echo $codice ?>]" id="utente_<? echo $codice ?>" value="<? echo $codice ?>">
							</td>
            </tr>
        	<? } ?>
        	</tbody>
	      </table>
			</div>
			<? } ?>
			<div id="buste">
				<div class="box">
					<strong>Impostare, se necessario, gli utenti abilitati all'apertura delle buste</strong>
				</div>
				<table width="100%">
				<?
					$bind = array();
					$bind[":codice"]=$gara["procedura"];
					$filtro_mercato = "";
					$strsql = "SELECT * FROM b_procedure WHERE mercato_elettronico = 'S' AND codice = :codice";
					$ris_mercato = $pdo->bindAndExec($strsql,$bind);
					if ($ris_mercato->rowCount()>0) $filtro_mercato = " AND b_criteri_buste.mercato_elettronico = 'S' ";

					$bind = array();
					$bind[":codice"]=$gara["criterio"];
					$bind[":codice_gara"]=$gara["codice"];
					$sql = "SELECT b_criteri_buste.codice, b_criteri_buste.nome, r_permessi_apertura_buste.codice_utente
									FROM b_criteri_buste
									LEFT JOIN r_permessi_apertura_buste ON
														b_criteri_buste.codice = r_permessi_apertura_buste.codice_busta
														AND r_permessi_apertura_buste.codice_gara = :codice_gara
									WHERE b_criteri_buste.codice_criterio= :codice

									" . $filtro_mercato . "
									ORDER BY b_criteri_buste.ordinamento ";
					$ris_buste = $pdo->bindAndExec($sql,$bind);

					while($busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) { ?>
						<tr>
							<th><strong><?= $busta["nome"] ?></strong></th>
						</tr>
						<tr>
							<td>
								<select name="permesso_busta[<?= $busta["codice"] ?>]" title="<?= $busta["nome"] ?>" rel="N;0;0;N">
									<option value="">Tutti</option>
									<?
										foreach($risultato_all AS $utente) {
											?>
											<option <?= ($busta["codice_utente"] == $utente["codice"]) ? 'selected' : '' ?> value="<?= $utente["codice"] ?>"><?= $utente["cognome"] . " " . $utente["nome"] . " - " . $utente["denominazione"] ?></option>
											<?
										}
									?>
								</select>
							</td>
						</tr>
					<? } ?>
				</table>
			</div>
		</div>
      <script>
			$("#tabs").tabs();
		 	function permessi(codice) {
				if ($("#utente_"+codice+":disabled").length > 0) {
					$("#utente_"+codice).removeAttr('disabled');
					$("#flag_"+codice).css('background-color','#3C0');
				} else {
					$("#utente_"+codice).attr('disabled','disabled');
					$("#flag_"+codice).css('background-color','#C00');
				}
				return false;
			}
			</script>
      <?

				if (!$lock) { ?>
                <input type="submit" class="submit_big" value="Salva">
                </form>
                <?
                 } else {
					 ?>
						<script>
	                        $(":input").not('.espandi').prop("disabled", true);
                    	</script>
                     <?
				 }
				 include($root."/gare/ritorna.php");
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
