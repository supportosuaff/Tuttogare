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
		echo "<h1>Modelli</h1>";
		if ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON") {
		  ?>
			<script>
			function open_dialog(codice) {
				$("#dialog").load("edit.php?codice="+codice,function(){
					$(this).dialog({
						modal:true,
						width: '800px',
						position: 'top',
					});
					f_ready();
				});
			}
			</script>
			<div style="display:none" id="dialog"></div>
            <hr><a href="#" onclick="open_dialog(0)" title="Inserisci nuovo gruppo"><div class="add_new"><img src="/img/add.png" alt="Inserisci nuovo modulo"><br>Aggiungi nuovo gruppo</div></a><hr>

			      <?

				$strsql = "SELECT * FROM b_gruppi_opzioni WHERE eliminato = 'N' ORDER BY titolo";
				$risultato = $pdo->query($strsql);
				if ($risultato->rowCount()>0) {
					?>
            	<table width="100%" id="elenco">
            	 	<thead>
									<tr><th>Titolo</th><th><span class="fa fa-euro"></span></th><th><span class="fa fa-euro"></span><br><span class="fa fa-list"></span></th><th></th><th></tr>
                </thead>
                <tbody id="gruppi_opzioni">
                  <? while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) { ?>
                  <tr id="gruppo_<?= $record["codice"] ?>">
										<td><?= $record["titolo"] ?></td>
										<td style="background-color:<?= (empty($record['guue'])) ? "#C00" : "#3C0" ?>" width="10"></td>
										<?
											$color = "#C00";
											$bind = array(":codice"=>$record["codice"]);
											$sql = "SELECT * FROM b_opzioni WHERE codice_gruppo = :codice AND guue IS NULL";
											$empty = $pdo->bindAndExec($sql,$bind);
											$empty = $empty->rowCount();
											$sql = "SELECT * FROM b_opzioni WHERE codice_gruppo = :codice AND guue IS NOT NULL";
											$full = $pdo->bindAndExec($sql,$bind);
											$full = $full->rowCount();
											if ($full > 0) {
												$color = "#3C0";
												if ($empty > 0) $color = "#FC0";
											}
										?>
										<td style="background-color:<?= $color ?>" width="10"></td>
										<td style="text-align: center" width="10"><input type="image" onClick="open_dialog(<? echo $record["codice"] ?>);return false" src="/img/edit.png" title="Modifica"></td>
										<td width="10"><input type="image" onClick="elimina('<? echo $record["codice"] ?>','impostazioni/opzioni');return false" src="/img/del.png" title="Elimina"></td>
                  </tr>
									<? } ?>
								</tbody>
              </table>
						<?
				} else { ?>
					<h2>Nessun modulo presente</h2>
                    <?
				}

					?>
					<hr><a href="#" onclick="open_dialog(0)" title="Inserisci nuovo gruppo"><div class="add_new"><img src="/img/add.png" alt="Inserisci nuovo modulo"><br>Aggiungi nuovo gruppo</div></a><hr>
					<?
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
