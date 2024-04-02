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
	echo "<h1>Classifiche SOA</h1>";
		if ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON") {
			$strsql = "SELECT * FROM b_classifiche_soa WHERE attivo = 'S' ORDER BY id ";
			$risultato = $pdo->query($strsql);
	?>
        <form name="box" method="post" action="save.php" rel="validate" >
        <div class="comandi">
                    <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
		</div>
                    <input type="hidden" id="codice" name="codice" value="<? echo $record_ente["codice"]; ?>">
        			<div class="box">
                        <table width="100%" >
                        <thead>
                        	<tr><td>ID</td><td>Importo minimo</td><td>Importo Massimo</td><td>Elimina</td></tr>
												</thead>
												<tbody id="classifiche_tabs">
												<? if (isset($risultato) && $risultato->rowCount() > 0) {
														while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
															$id = $record["codice"];
															include("form.php");
														}
													} else {
														$id = "i_0";
														$record = get_campi("b_classifiche_soa");
														include("form.php");
													}
													?>
                          </tbody>
                          <tfoot>
                          <tr><td colspan="4">
                       			<button class="aggiungi" onClick="aggiungi('form.php','#classifiche_tabs');return false;"><img src="/img/add.png" alt="Aggiungi classifica">Aggiungi classifica</button></td></tr>
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
