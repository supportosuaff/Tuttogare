<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (!is_operatore()) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
	} else {
		echo "<h1>COMUNICAZIONI</h1>";
		$bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ":codice_utente" => $_SESSION["codice_utente"]);
		$strsql = "SELECT b_comunicazioni.*, r_comunicazioni_utenti.letto, r_comunicazioni_utenti.protocollo, r_comunicazioni_utenti.data_protocollo AS data_protocollo_oe FROM b_comunicazioni JOIN r_comunicazioni_utenti ON b_comunicazioni.codice = r_comunicazioni_utenti.codice_comunicazione WHERE b_comunicazioni.codice_ente =:codice_ente AND codice_utente = :codice_utente ORDER BY b_comunicazioni.timestamp DESC ";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount()>0){
			?>
			<script>
				function all_read() {
					$.ajax('all_read.php', {
					    async: false
					});
					window.location.reload();
				}
			</script>
      <div style="text-align:right">
        <button onClick="all_read()"><?= traduci("Segnala tutti come letti") ?></button>
      </div>
      <table style="font-size:12px" width="100%" class="elenco">
      	<thead>
      		<tr>
      			<td><?= traduci("Data") ?></td>
      			<td><?= traduci("Protocollo") ?></td>
      			<td><?= traduci("Oggetto") ?></td>
      		</tr>
      	</thead>
      	<tbody>
      		<?
      			while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
							$protocollo = "n." . $record["codice"] . " del " . mysql2date($record["timestamp"]) . "<br>Assegnato dal sistema";
							if ($record["protocollo"] != "") {
								$protocollo = "n. " . $record["protocollo"] . " del " . mysql2date($record["data_protocollo_oe"]);
							}
							?>
			        <tr <? if ($record["letto"] == "N") echo "style=\"font-weight:bold\"" ?>>
			        	<td width="120"><? echo mysql2datetime($record["timestamp"]) ?></td>
		            <td><? echo $protocollo; ?></td>
		            <td><a href="/comunicazioni/id<? echo $record["codice"] ?>-comunicazione"><? echo substr($record["oggetto"],0,180) . "..." ?></a></td>
			        </tr>
							<?
						}
					?>
      	</tbody>
      </table>
      <div class="clear"></div>
			<?
			}
	}
	include_once($root."/layout/bottom.php");
?>
