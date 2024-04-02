<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	echo "<h1>RECAPITI</h1>";
	$bind = array(":codice"=>$_SESSION["ente"]["codice"]);
	$strsql = "SELECT b_enti.* FROM b_enti WHERE codice = :codice";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount() > 0) {
		$record_ente = $risultato->fetch(PDO::FETCH_ASSOC);
		?>
        <form name="box" method="post" action="save.php" rel="validate" >
        <div class="comandi">
                    <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
                    </div>
                    <input type="hidden" id="codice" name="codice" value="<? echo $record_ente["codice"]; ?>">
                   		 <div class="box">
	    		                <h2>Dati anagrafici</h2>
			                        <table width="100%">
            			            <tr><td class="etichetta" width="10%">Denominazione</td><td colspan="3"><h2><? echo $record_ente["denominazione"] ?></h2></td></tr>
			                        <tr><td class="etichetta">Dominio</td><td><strong><? echo $record_ente["dominio"] ?></strong></td>
                    				    <td class="etichetta">Codice Fiscale</td><td><strong><? echo $record_ente["cf"] ?></strong></td>
			                        </tr>
	                                </table>
    			                </div>
								<div class="box">
		                    	<h2>Recapiti</h2>
                        		 <table width="100%" id="recapiti">
			                         <tr>
                                     <td class="etichetta">Sito istituzionale</td>
                                     <td colspan="3"><input style="width:95%" type="text" name="ente[url]" id="url" title="Sito istituzionale" value="<? echo $record_ente["url"] ?>" rel="S;5;0;L"></td>
                                     </tr>
                                     <tr><td class="etichetta">Indirizzo</td><td><input type="text" name="ente[indirizzo]" id="indirizzo" title="Indirizzo" value="<? echo $record_ente["indirizzo"] ?>" rel="N;5;0;A"></td>
                                     <td class="etichetta">Citta</td><td><input type="text" name="ente[citta]" id="citta" title="Citta" value="<? echo $record_ente["citta"] ?>" rel="N;3;0;A"></td></tr>
                       				<tr>
                       					<td class="etichetta">Provincia</td><td><input type="text" name="ente[provincia]" id="provincia" title="Provincia" value="<? echo $record_ente["provincia"] ?>" rel="N;2;2;A" size="2" maxlength="2"></td>
                                        <td class="etichetta">Stato</td><td><input type="text" name="ente[stato]" id="stato" title="Stato" value="<? echo $record_ente["stato"] ?>" rel="N;2;0;A"></td></tr>
                        			<tr>
                                    	<td class="etichetta">Telefono</td><td><input type="text" name="ente[telefono]" id="telefono" title="Telefono" value="<? echo $record_ente["telefono"] ?>" rel="N;0;0;A"></td>
                        				<td class="etichetta">Fax</td><td><input type="text" name="ente[fax]" id="fax" title="fax" value="<? echo $record_ente["fax"] ?>" rel="N;0;0;A"></td></tr>
                         			<tr>
                                    	<td class="etichetta">E-mail*</td><td colspan="3"><input type="text" name="ente[email]" id="email" title="email" value="<? echo $record_ente["email"] ?>" rel="S;0;0;E"></td></tr>
                        			</table>
			                    </div>
               <input type="submit" class="submit_big" value="Salva">
	</form>
        <?
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	include_once($root."/layout/bottom.php");
	?>
