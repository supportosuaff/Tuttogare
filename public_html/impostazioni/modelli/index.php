<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit) {
		echo "<h1>MODELLI</h1>";
		if ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON") {
		  if (!isset($_SESSION["ente"])) {
			?>
            <hr><a href="/impostazioni/modelli/edit.php?codice=0" title="Inserisci nuovo modello"><div class="add_new"><img src="/img/add.png" alt="Inserisci nuovo modello"><br>Aggiungi nuovo modello</div></a><hr>
            <?
		  }
				$strsql = "SELECT * FROM b_modelli_standard ORDER BY titolo ASC";
				$risultato = $pdo->query($strsql);
				if ($risultato->rowCount()>0) {
					?>
                    <table width="100%" id="elenco">
                    <? while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) { ?>
                    <thead style="display:none">
                    <tr><td></td><td></td>
                    <? if (isset($_SESSION["ente"])) { ?><td></td><td></td><? } ?></tr>
                    </thead>
                    <tbody>
                    <tr>
                    	<td><h2><? echo $record["titolo"] ?></h2></td>
                    	<td width="10"><input type="image" onClick="window.location.href='/impostazioni/modelli/edit.php?codice=<? echo $record["codice"] ?>';return false" src="/img/edit.png" title="Modifica"></td>
                        <? if (isset($_SESSION["ente"])) {
							$colore = "#333";
						?><td width="10">
                        	<?
													$bind = array();
													$bind[":codice"] = $record["codice"];
													$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
													$sql = "SELECT * FROM b_modelli_enti WHERE codice = :codice AND codice_ente = :codice_ente";
								$ris = $pdo->bindAndExec($sql,$bind);
								if ($ris->rowCount()>0) {
									$colore = "#C00";
									$modello_ente = $ris->fetch(PDO::FETCH_ASSOC);
									if ($modello_ente["attivo"] == "S") $colore = "#3C0";
									?><input type="image" onClick="disabilita('<? echo $modello_ente["codice"] ?>','impostazioni/modelli');" src="/img/switch.png" title="Abilita/Disabilita"><?
								}
								?>
                        </td><td id="flag_<? echo $record["codice"] ?>" style="background-color:<? echo $colore ?>"  width="5"><? } ?>
                    </tr>
					<? } ?>
                    </table>
                    <?
				} else { ?>
					<h2>Nessun modello presente</h2>
                    <?
				}
		} else {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	include_once($root."/layout/bottom.php");
	?>
