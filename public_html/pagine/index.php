<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("pagine",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	echo "<h1>PAGINE</h1>";

	if ($edit) {
		?>
        <hr>
        <a href="/pagine/id0-edit" title="Inserisci nuova pagina"><div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Aggiungi nuova pagina
        </div></a>
        <hr>
        <?
					$codice_ente = 0;
					if (isset($_SESSION["ente"])) $codice_ente = $_SESSION["ente"]["codice"];
					$strsql  = "SELECT b_pagina.* FROM b_pagina WHERE codice_ente = :codice_ente ORDER BY ordinamento, titolo" ;
					$risultato = $pdo->bindAndExec($strsql,array(":codice_ente"=>$codice_ente));

	if ($risultato->rowCount()>0) {
	?>
    <table id="pagine" width="100%" class="elenco">
    	<thead>
        	<tr>
            	<th width="5">&nbsp;</th>
            	<th>Titolo</th>
                <th width="30">Sezione</th>
                <th width="10">Modifica</th>
                <th width="10">Attiva Disattiva</th>
                <th width="10">Elimina</th>
            </tr>
            </thead>
            <tbody>
    <?
		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$codice			= $record["codice"];
			$titolo			= $record["titolo"];
			$sezione		= $record["sezione"];
			$attivo			= $record["attivo"];
			$colore = "#3C0";
			if ($attivo == "N") { $colore = "#C00"; }
			$href = "/pagine/id".$codice."-".$titolo;
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);


	?>
    <tr id="<? echo $codice ?>">
    	<td  id="flag_<? echo $codice ?>" style="background-color: <? echo $colore ?>"></td>
        <td><a href="<? echo $href ?>"><? echo $titolo ?></a></td>
        <td><? echo $sezione ?></td>
        <td align="center"><input type="image" onClick="window.location.href='/pagine/id<? echo $codice ?>-edit'" src="/img/edit.png" title="Modifica"></td>
        <td align="center"><input type="image" onClick="disabilita('<? echo $codice ?>','pagine');" src="/img/switch.png" title="Abilita/Disabilita"></td>
		<td align="center"><input type="image" onClick="elimina('<? echo $codice ?>','pagine');" src="/img/del.png" title="Elimina"></td>
     </tr>

        <?
		}

	?>
    	</tbody>
    </table>
    <div class="clear"></div>

<?php

	}		else {
?><h1 style="text-align:center">
<span class="fa fa-exclamation-circle fa-3x"></span><br>Nessun risultato!</h1>	<?
}

		?>
        <hr>
        <a href="/pagine/id0-edit" title="Inserisci nuova pagina"><div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Aggiungi nuova pagina
        </div></a>
        <hr>
        <? } ?>

<?

	include_once($root."/layout/bottom.php");
	?>
