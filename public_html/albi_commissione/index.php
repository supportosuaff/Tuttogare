<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("albi_commissione",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	echo "<h1>GESTIONE ALBI COMMISSIONI</h1>";


	if ($edit) {
		?>
			<a href="/albi_commissione/id0-edit" title="Inserisci nuovo bando"><div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Aggiungi nuovo albo
        </div>
			</a>
      <?
			$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
			$strsql  = " SELECT * ";
			$strsql .= " FROM b_albi_commissione ";
			$strsql .= " WHERE codice_gestore = :codice_ente AND codice_gara IS NULL ";
			$strsql .= " ORDER BY codice DESC" ;
			$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

			if ($risultato->rowCount()>0) {
	?>
    <table id="pagine" width="100%" id="gare" class="elenco">
    	<thead>
        	<tr>
						<td>ID</td><td>Oggetto</td><td>Interni</td><td>Esterni</td><td>Iscritti</td>
            </tr>
            </thead>
            <tbody>
					    <?
							while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
								$presidenti = 0;
								$commissari = 0;
								$disattivi = 0;
								$bind = array(":codice"=>$record["codice"]);
								$sql = "SELECT * FROM b_commissari_albo WHERE attivo = 'S' AND interno = 'S' AND codice_albo = :codice ";
								$ris_array = $pdo->bindAndExec($sql,$bind);
								$interni = $ris_array->rowCount();
								$sql = "SELECT * FROM b_commissari_albo WHERE attivo = 'S' AND interno = 'N' AND codice_albo = :codice ";
								$ris_array = $pdo->bindAndExec($sql,$bind);
								$esterni = $ris_array->rowCount();

						?>
    <tr id="<? echo $record["codice"] ?>">
    	<td width="5%"><? echo $record["id"] ?></td>
		<td width="75%"><a href="/albi_commissione/edit.php?cod=<? echo $record["codice"] ?>" title="Pannello gara"><? echo $record["oggetto"] ?></a><br>
		<?= $record["descrizione"] ?>
		</td>
		<td><h2 style="text-align:center"><?= $interni ?></h2></td>
		<td><h2 style="text-align:center"><?= $esterni ?></h2></td>
		<td style="text-align:center"><a href="/albi_commissione/iscritti/index.php?codice=<?= $record["codice"] ?>" class="button-primary btn-round"><span class="fa fa-address-book"></span></a></td>
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

		?><br>
        <a href="/albi_commissione/id0-edit" title="Inserisci nuovo bando"><div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
				Aggiungi nuovo albo
        </div></a>
        <? } ?>

<?

	include_once($root."/layout/bottom.php");
	?>
