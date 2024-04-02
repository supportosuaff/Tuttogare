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
	echo "<h1>CATEGORIE PROGETTAZIONE</h1>";
		if ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON") {
			$strsql = "SELECT * FROM b_categorie_progettazione WHERE attivo = 'S' ORDER BY codice ";
			$risultato = $pdo->query($strsql);
	?>
        <form name="box" method="post" action="save.php" rel="validate" >
        <div class="comandi">
                    <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
		</div>
                    <input type="hidden" id="codice" name="codice" value="<? echo $record_ente["codice"]; ?>">
        			<div class="box">
                        <table class="elenco" width="100%" >
                        <thead>
													<tr><td>ID</td><td>Gruppo</td><td>Destinazione</td><td>L. 143/49 Classi e categorie</td><td>D.M. 18/11/1971</td><td>D.M. 232/1991</td><td>Complessit&agrave;</td><td>Elimina</td></tr>
												</thead>
												<tbody id="categorie_tabs">
								<? if (isset($risultato) && $risultato->rowCount() > 0) {
									while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
										$id = $record["codice"];
										include("form.php");
									}
								} else {
									$id = "i_0";
									$record = get_campi("b_categorie_progettazione");
									include("form.php");
								}?>
                            </tbody>
                            <tfoot>
                            <tr><td colspan="8">
                       <button class="aggiungi" onClick="aggiungi('form.php','#categorie_tabs');return false;"><img src="/img/add.png" alt="Aggiungi categoria">Aggiungi categoria</button></td></tr>
                            </tfoot>
                           </table>
                           <div class="clear"></div>
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
