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
		  if (!isset($_SESSION["ente"])) {
			?>
      <div style="float:left; width:75%">
				<a href="/impostazioni/compilatore/edit.php?codice=0" title="Inserisci nuovo"><div class="add_new"><span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi nuovo modello</div></a>
			</div>
			<div style="float:right; width:24%">
				<a href="/impostazioni/compilatore/ricorsivi/index.php" title="Paragrafi ricorsivi"><div class="add_new"><span class="fa fa-book fa-3x"></span><br>Paragrafi ricorsivi</div></a>
			</div>
			<div class="clear"></div>
			<?
		  }
				$strsql = "SELECT b_modelli_new.attivo, b_modelli_new.codice,b_modelli_new.titolo, b_modelli_new.tipo, b_procedure.nome AS procedura, b_criteri.criterio, b_tipologie.tipologia, b_modelli_new.importo_minimo, b_modelli_new.importo_massimo ";
				$strsql.= "FROM b_modelli_new LEFT JOIN b_criteri ON b_modelli_new.criterio = b_criteri.codice ";
				$strsql.= "LEFT JOIN b_tipologie ON b_modelli_new.tipologia = b_tipologie.codice ";
				$strsql.= "LEFT JOIN b_procedure ON b_modelli_new.procedura = b_procedure.codice ";
				if (empty($_GET["archivio"])) $strsql.= " WHERE b_modelli_new.attivo = 'S'";
				$strsql.= " ORDER BY b_modelli_new.attivo DESC ,b_modelli_new.importo_minimo, b_procedure.nome, b_tipologie.tipologia, b_criteri.criterio, b_modelli_new.tipo";
				$risultato = $pdo->query($strsql);
				if ($risultato->rowCount()>0) {
					?>
							<table width="100%" class="elenco">
								<thead>
									<tr><th></th><th>Titolo</th><th>Documento</th><th>Procedura</th><th>Tipologia</th><th>Criterio</th><th>Da</th><th>A</th><th>Modifica</th><th></th></tr>
								</thead>
								<tbody>
									<? while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
											$colore = "#C00";
											if ($record["attivo"] == "S") $colore = "#3C0";
										?>
	                  <tr>
											<td id="flag_<?= $record["codice"] ?>" style="background-color:<?= $colore ?>" width="10"></td>
											<td><?= $record["titolo"] ?></td>
											<td><strong><?= $record["tipo"] ?></strong></td>
											<td><?= $record["procedura"] ?></td>
	                  	<td><? echo $record["tipologia"] ?></td>
											<td><? echo $record["criterio"] ?></td>
											<td><? echo number_format($record["importo_minimo"],2,",",".") ?></td>
											<td><? echo number_format($record["importo_massimo"],2,",",".") ?></td>
	                  	<td style="text-align: center" width="10"><input type="image" onClick="window.location.href='/impostazioni/compilatore/edit.php?codice=<? echo $record["codice"] ?>';return false" src="/img/edit.png" title="Modifica"></td>
											<td style"text-align:center"><input type="image" onClick="disabilita('<? echo $record["codice"] ?>','impostazioni/compilatore');" src="/img/switch.png" title="Abilita/Disabilita"></td>
	                  </tr>
									<? } ?>
								</tbody>
              </table>
							<div class="clear"></div>
						<?
				} else { ?>
					<h2>Nessun modulo presente</h2>
					<?
				}
				if (empty($_GET["archivio"])) { ?>
					<a class="submit_big" href="/impostazioni/compilatore/index.php?archivio=1">Visualizza Archivio</a>
				<? } else { ?>
					<a class="submit_big" href="/impostazioni/compilatore/index.php">Nascondi Archivio</a>
				<? }

				if (!isset($_SESSION["ente"])) {
					?>
					<hr><a href="/impostazioni/compilatore/edit.php?codice=0" title="Inserisci nuovo gruppo"><div class="add_new"><span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi nuovo modello</div></a><hr>
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
