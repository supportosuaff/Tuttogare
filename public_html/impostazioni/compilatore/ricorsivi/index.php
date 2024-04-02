<?
	include_once("../../../../config.php");
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
		  if (!isset($_SESSION["ente"])) {
			?>
      <a href="/impostazioni/compilatore/ricorsivi/edit.php?codice=0" title="Inserisci nuovo"><div class="add_new"><span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi nuovo</div></a>
			<?
		  }
				$strsql = "SELECT * FROM b_paragrafi_ricorsivi WHERE eliminato = 'N'";
				$strsql.= " ORDER BY b_paragrafi_ricorsivi.titolo ASC ";
				$risultato = $pdo->query($strsql);
				if ($risultato->rowCount()>0) {
					?>
							<table width="100%" class="elenco">
								<thead>
									<tr><th>Titolo</th><th>Modifica</th><th>Elimina</th></tr>
								</thead>
								<tbody>
									<? while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) { ?>
	                  <tr>
											<td><?= $record["titolo"] ?></td>
											<td style="text-align: center" width="10"><button class="btn-round btn-warning" onClick="window.location.href='/impostazioni/compilatore/ricorsivi/edit.php?codice=<? echo $record["codice"] ?>';return false" title="Modifica"><span class="fa fa-pencil"></span></button></td>
											<td width="10"><button class="btn-round btn-danger" onClick="elimina('<? echo $record["codice"] ?>','impostazioni/compilatore/ricorsivi');return false" title="Elimina"><span class="fa fa-remove"></span></td>
	                  </tr>
									<? } ?>
								</tbody>
              </table>
							<div class="clear"></div>
						<?
				} else { ?>
					<h2>Nessun modello presente</h2>
					<?
				}
				if (!isset($_SESSION["ente"])) {
					?>
					<hr><a href="/impostazioni/compilatore/ricorsivi/edit.php?codice=0" title="Inserisci nuovo gruppo"><div class="add_new"><span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi nuovo modello</div></a><hr>
					<?
				}
				?>
				<a class="submit_big" style="background-color:#333" href="/impostazioni/compilatore/index.php">Ritorna al compilatore</a>
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
