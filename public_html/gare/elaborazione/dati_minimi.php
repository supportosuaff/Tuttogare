<?
	if ($ris_minimi->rowCount() > 0 && isset($record_gara)) {
		?>
		<table id="tab_minimi" width="100%">
			<script type="text/javascript" src="/js/resumable.js"></script>
			<script type="text/javascript" src="resumable-uploader.js"></script>
			<script>
				var uploader = new Array();
			</script>
		<?
		while ($campo = $ris_minimi->fetch(PDO::FETCH_ASSOC)) {
			$sql_dati = "SELECT * FROM b_dati_minimi WHERE codice_gara = :codice_gara AND codice_campo = :codice_campo ";
			$ris_dati = $pdo->bindAndExec($sql_dati,array(":codice_gara"=>$record_gara["codice"],":codice_campo"=>$campo["codice"]));
			$valore = "";
			$codice_valore = "";
			if($ris_dati->rowCount() > 0) {
				$valore = $ris_dati->fetch(PDO::FETCH_ASSOC);
				$codice_valore = $valore["codice"];
				$valore = $valore["valore"];
			}
			?>
			<tr>
				<td class="etichetta">
					<strong><?= $campo["titolo"] ?></strong><?= ($campo["obbligatorio"]=="S") ? "*" : "" ?>
					<input type="hidden" name="datiMinimi[<?= $campo["codice"] ?>][codice]" value="<?= $codice_valore ?>">
				</td>
				<td>
					<?
						if ($campo["tipo"] == "attach") {
							$nome_file = "";
							if (!empty($valore)) {
								$valore = json_decode($valore,true);
								$sql_allegato = "SELECT * FROM b_allegati WHERE codice = :codice ";
								$ris_allegato = $pdo->bindAndExec($sql_allegato,array(":codice"=>$valore["codice_allegato"]));
								if ($ris_allegato->rowCount() > 0) {
									$allegato = $ris_allegato->fetch(PDO::FETCH_ASSOC);
									$percorso_html = "/documenti/allegati/" . $allegato["codice_gara"] . "/" . $allegato["nome_file"];
									if ($allegato["online"] == "N") {
										$percorso_html = "/allegati/download_allegato.php?codice=" . $allegato["codice"];
									}
									$nome_file .= "<a href=\"".$percorso_html."\" target=\"_blank\">";
									$nome_file .= "<img src=\"/img/". substr($allegato["nome_file"],-3) .".png\" alt=\"File " . substr($allegato["nome_file"],0,-3) . "\" style=\"vertical-align:middle\">&nbsp;";
									$nome_file .= $allegato["nome_file"];
									$nome_file .= "</a>";
								}
							}
						}
						if (isset($print_form_minimi)) {
							$rel = "N;1;0;A";
							$name = "datiMinimi[".$campo["codice"]."][valore]";
							switch($campo["tipo"]) {
								case "input":
									?>
									<input type="text" style="width:100%" name="<?= $name ?>" title="<?= $campo["titolo"] ?>" rel="<?= $rel ?>" value="<?= $valore ?>">
									<?
									break;
								case "text":
									?>
									<textarea class="ckeditor_simple" name="<?= $name ?>" title="<?= $campo["titolo"] ?>" rel="<?= $rel ?>">
										<?= $valore ?>
									</textarea>
									<?
									break;
								case "attach":
									?>
										<?
											if (!empty($nome_file)) $rel="N;0;0;A";
										?>
										<input type="hidden" class="filechunk" id="filechunk_<? echo $campo["codice"] ?>" name="<?= $name ?>" title="<?= $campo["titolo"] ?>" rel="<?= $rel ?>">
										<input type="hidden" class="terminato" id="terminato_<? echo $campo["codice"] ?>" title="Termine upload">
										<div id="modulistica_<? echo $campo["codice"] ?>" rel="<? echo $campo["codice"] ?>" class="scegli_file" style="float:left"><img src="/img/folder.png" height="30" style="vertical-align:middle"></div>
										<div id="nome_file_<? echo $campo["codice"] ?>" style="margin-left:10px; float:left;"><? echo $nome_file ?></div>
										<div class="clear"></div>
										<div id="progress_bar_<? echo $campo["codice"] ?>" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
										<script>
										 tmp = (function($){
											 return (new ResumableUploader($("#modulistica_<? echo $campo["codice"] ?>")));
										 })(jQuery);
										 uploader.push(tmp);
									 </script>
									<?
									break;
							}
						} else {
							if ($campo["tipo"] != "attach") {
								echo $valore;
							} else {
							 echo $nome_file;
							}
						}
					?>
				</td>
			</tr><?
		}
		?>
		</table>
		<?
	}
?>
