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
		echo "<h1>GESTIONE GUUE</h1>";
		if ($_SESSION["gerarchia"] === "0") { 
			$strsql = "SELECT * FROM b_gestione_guue ORDER BY ordinamento,codice ";
			$risultato = $pdo->query($strsql);
			?>
				<style type="text/css">
					input[type="text"] {
						width: 100% !important;
						box-sizing : border-box !important;
						font-family: Tahoma, Geneva, sans-serif !important;
						font-size: 1.2em !important;
					}
				</style>
        <form name="box" method="post" action="save.php" rel="validate" >
        	<div class="comandi">
        		<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
					</div>
					<!--   <input type="hidden" id="codice" name="codice" value="<? echo $record_ente["codice"]; ?>"> -->
					<div class="box">
						<table width="100%" >
							<tbody id="gestione" class="sortable">
								<? 
								if (isset($risultato) && $risultato->rowCount() > 0) { 
						   			while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
										$id = $record["codice"];
										include("form.php");
									}
								} else {
									$id = "i_0";
									$record = get_campi("b_gestione_guue");
									include("form.php");
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="5">
										<button class="aggiungi" onClick="aggiungi('form.php','#gestione');return false;"><img src="/img/add.png" alt="Aggiungi opzione">Aggiungi opzione</button>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<input type="submit" class="submit_big" value="Salva">
				</form>
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