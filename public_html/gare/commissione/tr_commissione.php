<?
if (isset($_POST["id"])) {
	session_start();
	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$record_partecipante = get_campi("b_commissioni");
	$id = $_POST["id"];
	$lock = false;
	$send = (isset($send) ? $send : false);
}
if (!empty($record_partecipante)) {
?>

<tr id="partecipante_<? echo $id ?>">
	<td>
		<input type="hidden" name="partecipante[<? echo $id ?>][id]" id="id_partecipante_<? echo $id ?>" value="<? echo $id ?>">
		<input type="hidden" name="partecipante[<? echo $id ?>][codice]" id="codice_partecipante_<? echo $id ?>" value="<? echo $record_partecipante["codice"] ?>">
		<input type="text" name="partecipante[<? echo $id ?>][cognome]" style="width:98%"  title="Cognome" rel="S;2;50;A" id="cognome_partecipante_<? echo $id ?>" value="<? echo $record_partecipante["cognome"] ?>">
	</td>
	<td>
		<input type="text" name="partecipante[<? echo $id ?>][nome]" style="width:98%" title="Nome" rel="S;2;50;A" id="nome_partecipante_<? echo $id ?>" value="<? echo $record_partecipante["nome"] ?>">
	</td>
	<td>
		<input type="text" name="partecipante[<? echo $id ?>][ruolo]" style="width:98%" title="Ruolo" rel="S;2;50;A" id="ruolo_partecipante_<? echo $id ?>" value="<? echo $record_partecipante["ruolo"] ?>">
	</td>
	<td>
		<input type="text" name="partecipante[<? echo $id ?>][pec]" style="width:98%" title="E-mail" rel="S;3;255;E" id="pec_partecipante_<? echo $id ?>" value="<? echo $record_partecipante["pec"] ?>">
	</td>
	<td style="text-align:center">
		<input type="hidden" class="filechunk" id="filechunk_<?= $id ?>" name="partecipante[<? echo $id ?>][filechunk]" title="CV">
		<input type="hidden" name="partecipante[<? echo $id ?>][existing_cv]" value="<?= $record_partecipante["cv"] ?>">
		<input type="hidden" class="terminato" id="terminato_<?= $id ?>" title="Termine upload">
		<div id="nome_file_<?= $id ?>" ><? if (!empty($record_partecipante["cv"])) {
			$sql = "SELECT * FROM b_allegati WHERE codice = :codice_allegato";
			$ris_allegato = $pdo->bindAndExec($sql,array(":codice_allegato"=>$record_partecipante["cv"]));
			if ($ris_allegato->rowCount() > 0) {
				$allegato = $ris_allegato->fetch(PDO::FETCH_ASSOC);
				$percorso_html = "/documenti/allegati/". $allegato["codice_gara"] . "/" . $allegato["nome_file"];
				$percorso_fisico = $config["pub_doc_folder"]."/allegati/". $allegato["codice_gara"] . "/" . $allegato["riferimento"];

				if (file_exists($percorso_fisico)) {
					$estensione = explode(".",$allegato["nome_file"]);
					$estensione = end($estensione);
					?>
					<a href="<?= $percorso_html ?>" target="_blank" title="Allegat0">
					<?
						if (file_exists($root."/img/".$estensione.".png")) { ?>
							<img src="/img/<? echo $estensione ?>.png" alt="File <? echo $estensione ?>" style="vertical-align:middle">
						<? } else {
							echo $allegato["nome_file"];
						 }
					?>
					</a>
					<?
				}
			}
		}
		?></div>
		<div id="modulistica_<?= $id ?>" rel="<?= $id ?>" class="scegli_file"><span class="fa fa-folder-open" style="vertical-align:middle"></div>
		<div class="clear"></div>
		<div id="progress_bar_<?= $id ?>" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>

		 <script>
			tmp = (function($){
				return (new ResumableUploader($("#modulistica_<?= $id ?>")));
			})(jQuery);
			uploader.push(tmp);
		</script>
	</td>
	<? /* <td width="10" align="center">
		<? if ($record_partecipante["valutatore"] == "S") { ?>
			<button id="invia_<?= $id ?>" class="btn-round btn-primary invia_comunicazione" onclick="rigenera('<? echo $id ?>')" title="Rigenera Credenziali" placeholder="Rigenera Credenziali"><span class="fa fa-paper-plane"></span></button>
		<? } ?>
	</td> */ ?>
	<? if (!$lock && (!isset($estrazione) || (isset($estrazione) && $estrazione===false))) { ?>
	<td width="10" style="text-align:center">
		<span class="fa fa-remove fa-2x" style="color:#C00; cursor:pointer" onClick="elimina('<? echo $id ?>','gare/commissione');return false;" src="/img/del.png" title="Elimina"></span>
	</td>
	<? } ?>
</tr>
<? } ?>
