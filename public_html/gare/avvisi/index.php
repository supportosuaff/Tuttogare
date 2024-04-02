<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
						$edit = $esito["permesso"];
						$lock = $esito["lock"];
					}
					if (!$edit) {
						echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
						die();
					}
				} else {
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
				}
?>
	<h1>Avvisi di gara</h1>

        <hr>
        <a href="/gare/avvisi/edit.php?codice=0&codice_gara=<? echo $_GET["codice"] ?>" title="Inserisci nuova notizia"><div class="add_new">
        <img src="/img/add.png" alt="Inserisci nuova notizia"><br>
        Aggiungi nuovo avviso
        </div></a>
        <hr>
        <?
		$bind = array();
		$bind[":codice_gara"] = $_GET["codice"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

		$strsql  = "SELECT b_avvisi.* ";
		$strsql .= "FROM b_avvisi ";
		$strsql .= "WHERE codice_ente = :codice_ente AND codice_gara = :codice_gara ";
		$strsql .= "ORDER BY data DESC,  timestamp DESC " ;


	$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

	if ($risultato->rowCount()>0) {
		?>
        <table class="elenco" style="width:100%">
        <thead style="display:none;"><tr><td></td><td></td><td></td><td></td></tr>
        <tbody>
        <?
		while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
			$codice			= $record["codice"];
			$titolo			= $record["titolo"];
			$data			= mysql2date($record["data"]);
			$testo			= strip_tags($record["testo"]);
			$href = "/gare/avvisi/dettaglio.php?cod=".$codice;
					?>
                    <tr id="<? echo $codice ?>"><td width="10"><strong><? echo $data ?></strong></td><td><strong style="text-transform:uppercase"><a href="<? echo $href ?>" title="<? echo $titolo ?>"><? echo $titolo; ?></a></strong><br>
          	          <? echo substr($testo,0,255); ?>...
                      </td>
                       <td width="10"><input type="image" onClick="window.location.href='/gare/avvisi/edit.php?codice=<? echo $codice ?>&codice_gara=<? echo $record["codice_gara"] ?>'" src="/img/edit.png" title="Modifica"></td>
                        <td width="10"><input type="image" onClick="elimina('<? echo $codice ?>','gare/avvisi');" src="/img/del.png" title="Elimina"></td>
                        </tr>


<?php
	}
	?></tbody></table>
    <div class="clear"></div>

            <?
        } else {
        ?><h2 style="text-align:center">
        <span class="fa fa-exclamation-circle fa-3x"></span><br><? echo "Nessun risultato" ?>!</h2>	<?
        }

    include($root."/gare/ritorna.php");
	}
	include_once($root."/layout/bottom.php");
	?>
