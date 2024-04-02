<?
	include_once("../../config.php");
	if (isset($_SESSION["codice_utente"]) && $_SESSION["gerarchia"] <= 2) {
		include_once($root."/layout/top.php");
		$edit = ($_SESSION["gerarchia"] == 0) ? true : false;
		?>
		<h1>Guida on-line</h1>
		<?
			$sql = "SELECT * FROM b_allegati WHERE sezione = 'guida' AND online = 'S' ORDER BY cartella, codice";
			$ris_allegati = $pdo->query($sql);
		?>
		<table width="100%" id="tab_allegati">
			<? if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
				$cartella_attuale = "";
					while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
						if ($allegato["cartella"]!=$cartella_attuale) {
							$cartella_attuale = $allegato["cartella"];
							$echo_cartella = strtoupper(str_replace("/"," ",$allegato["cartella"]));
							?>
								<tr><td><span class="fa fa-folder-open fa-2x"></span></td><td colspan="4"><strong><? echo $echo_cartella  ?></strong></td></tr>
																		<?
						}
							include($root."/allegati/tr_allegati.php");
					}
			} ?>
		</table>
		<?
		if ($edit) {
			?>
			<button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
				<span class="fa fa-paperclip"></span> Allega file
			</button>

			<?
			$form_upload["codice_gara"] = 0;
			$form_upload["sezione"] = "guida";
			$form_upload["online"] = "S";
			include($root."/allegati/form_allegati.php");
		} 
		include_once($root."/layout/bottom.php");
	} else {
		header("Location: /index.php");
	}
	
	?>
