<form id="invio" rel="validate" action="/moduli/invia_comunicazione.php">
<div class="padding">
	<?
	 	$bind = array();
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$sql_pec = "SELECT * FROM b_pec WHERE codice_ente = :codice_ente AND attivo = 'S'";
		$ris_pec = $pdo->bindAndExec($sql_pec,$bind);
		if ($ris_pec->rowCount() > 0) {
			?>
            <div style="float:right; text-align:right; width:33%">
	            <strong>Indirizzo PEC da utilizzare</strong>
                <select name="codice_pec" id="codice_pec">
                	<option value="0"><? echo $_SESSION["ente"]["pec"] ?> - Predefinito</option>
                    <? while ($indirizzo_pec = $ris_pec->fetch(PDO::FETCH_ASSOC)) {
						?>
                        <option value="<? echo $indirizzo_pec["codice"] ?>"><? echo $indirizzo_pec["pec"] ?></option>
                        <?
					}
					?>
                 </select>
            </div>
            <?
		}
		?>
        <div class="clear"></div>

                 <br>
	<div style="float:left; width:65%">
		<h1>COMUNICAZIONE</h1>
        	<? include($root."/moduli/protocollo.php") ?>
			<input type="text" id="oggetto_comunicazione" name="oggetto" title="Oggetto" rel="S;0;0;A" class="titolo_edit" style="width:99%">
		    <textarea title="corpo" id="corpo_comunicazione" name="corpo" class="ckeditor_simple" rel="S;0;0;A"></textarea>
		    <input type="hidden" value="" name="cod_allegati" id="cod_allegati">
				<input type="hidden" value="<? if (isset($codice_gara)) echo $codice_gara ?>" name="codice_gara" id="codice_gara_comunicazione">
		    <table width="100%" id="tab_allegati"></table>
    		<button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
			    <img src="/allegati/icon.png" alt="Allega" width="15" style="vertical-align:middle"> Allega file
		    </button>
	</div>
<div style="float:right; width:34%">
	<h1>Destinatari</h1>
	<input type="hidden" id="indirizzi" name="indirizzi" title="Destinatari" rel="S;0;0;A">
	<ul id="destinatari">
	</ul>
</div>
<div class="clear"></div>
</div>
<div style="background-color:#DCDCDC; padding:10px;">
<input type="submit" class="submit_big" value="Invia" style="width:90%;float:left;">
<input type="button" class="submit_big" onClick="annulla_comunicazione();return false;" value="Annulla" style="width:9%; float:right; background-color:#F33">
<div class="clear"></div>
</div>
</form>
	<? include($root."/allegati/form_allegati.php");?>
