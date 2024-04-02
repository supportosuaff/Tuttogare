<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_concorso($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
				$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$operazione = "UPDATE";
?>
				<h1>UTENTI</h1>
				<? if (!$lock) { ?>
                	    <form name="box" method="post" action="save.php" rel="validate">
                    	<input type="hidden" name="codice_gara" value="<? echo $_GET["codice"]; ?>">
						<div class="comandi">
							<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
						</div>
                <? }
				$bind = array();
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql  = "SELECT b_utenti.*, b_enti.denominazione, b_gruppi.gruppo as ruolo ";
				$strsql .= "FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice ";
				$strsql .= "LEFT JOIN b_enti ON b_utenti.codice_ente = b_enti.codice ";
				$strsql .= "WHERE b_gruppi.gerarchia = 2 ";
				$strsql .= " AND (b_enti.codice = :codice_ente";
				$strsql .= " OR b_enti.sua = :codice_ente)";
				$strsql .= " ORDER BY cognome,nome,dnascita" ;
				$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
				if ($risultato->rowCount() > 0) {
				?>
				<table style="text-align:center; width:100%; font-size:0.8em" id="utenti" >
        <thead>
        <tr><th width="5"></th><th>Nominativo</th><th width="80">Data di nascita</th><th width="100">Codice Fiscale</th><th width="250">Ente</th><th width="10">Attiva Disattiva</th></tr>
        </thead>
        <tbody>
       	<?

		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
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

			$sql = "SELECT * FROM b_permessi_concorsi WHERE codice_utente = :codice AND codice_gara = :codice_gara AND codice_ente = :codice_ente";
			$ris = $pdo->bindAndExec($sql,$bind);
			if ($ris->rowCount() >0) {
				$colore = "#3C0";
				$attivo = true;
			}

			?>
			<tr id="<? echo $codice ?>">
            	<td  id="flag_<? echo $codice ?>" style="background-color: <? echo $colore ?>"></td>
               	<td style="text-align:left"><strong><? echo $nominativo ?></strong></td>
                <td><? echo $data ?></td>
                <td><? echo $cf ?></td>
                <td><? echo $record["denominazione"] ?></td>
                <td><input type="image" onClick="permessi('<? echo $codice ?>');return false;" src="/img/switch.png" title="Abilita/Disabilita">
                <input <? if (!$attivo) echo "disabled='disabled'" ?> type="hidden" name="utenti[<? echo $codice ?>]" id="utente_<? echo $codice ?>" value="<? echo $codice ?>">
                    </td>
            </tr>

        <?

		}

		?>
        </tbody>
         </table>
         <script>
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
				}
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
				 include($root."/concorsi/ritorna.php");
			} else {

				echo "<h1>Concorso non trovato</h1>";

				}
			} else {

				echo "<h1>Concorso non trovato</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
