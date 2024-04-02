<?
	$switch_area = false;
	if (isset($_POST["codice"])) {
		session_start();
		include("../../config.php");
		include_once($root."/inc/funzioni.php");
		$bind = array(":codice"=>$_POST["codice"]);
		$sql = "SELECT * FROM b_allegati WHERE codice = :codice ";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount()>0) {
			$allegato = $ris->fetch(PDO::FETCH_ASSOC);
		}
		$edit = true;
		if (strpos($_SERVER["HTTP_REFERER"],"allegati/")!==false) $switch_area = true;
	} else {
		if (strpos($_SERVER["REQUEST_URI"],"allegati/")!==false) $switch_area = true;
	}
	if (isset($allegato)) {
		$percorso_html = "/allegati/";
		if ($allegato["sezione"]=="mercato") $percorso_html .= "mercato_elettronico/";
		if ($allegato["sezione"]=="sda") $percorso_html .= "sda/";
		if ($allegato["sezione"]=="albo") $percorso_html .= "albo/";
		if ($allegato["sezione"]=="concorsi") $percorso_html .= "concorsi/";
		if ($allegato["sezione"]=="dialogo") $percorso_html .= "dialogo/";
		if ($allegato["sezione"]=="esecuzione") $percorso_html .= "esecuzione/";
		if ($allegato["sezione"]=="guida") $percorso_html .= "guida/";
		if ($allegato["sezione"]=="nso") $percorso_html .= "nso/";
		if ($allegato["sezione"]=="fabbisogno") $percorso_html .= "fabbisogno/";
		if ($allegato["sezione"]=="cpn") $percorso_html .= "cpn/{$allegato["codice_ente"]}/";
		if ($allegato["sezione"]=="progetti") $percorso_html .= "progetti/";
		if ($allegato["sezione"]=="documentale") $percorso_html .= "documentale/";
		$cartella = "";
		$percorso_fisico = "";
		$size = "";
		$file_exist = true;
		if (!empty($allegato["cartella"])) $cartella = $allegato["cartella"] . "/";
		if ($allegato["online"] == "N") {
			$sub_folder = "";
			if ($allegato["sezione"] == "mercato") $sub_folder .= "/mercato_elettronico";
			if ($allegato["sezione"] == "sda") $sub_folder .= "/sda";
			if ($allegato["sezione"] == "albo") $sub_folder .= "/albo";
			if ($allegato["sezione"] == "dialogo") $sub_folder .= "/dialogo";
			if ($allegato["sezione"] == "concorsi") $sub_folder .= "/concorsi";
			if ($allegato["sezione"] == "esecuzione") $sub_folder .= "/esecuzione";
			if ($allegato["sezione"] == "guida") $sub_folder .= "/guida";
			if ($allegato["sezione"] == "nso") $sub_folder .= "/nso";
			if ($allegato["sezione"] == "fabbisogno") $sub_folder .= "/fabbisogno";
			if ($allegato["sezione"] == "progetti") $sub_folder .= "/progetti";
			if ($allegato["sezione"] == "documentale") $sub_folder .= "/documentale";
			$percorso_html = "/allegati/download_allegato.php?codice=" . $allegato["codice"];
			$percorso_fisico = $config["arch_folder"].$sub_folder."/".$allegato["codice_gara"]."/".$cartella.$allegato["riferimento"];
		} else {
			if ($allegato["codice_gara"] != 0) {
				$percorso_html .= $allegato["codice_gara"] . "/";
				if (!empty($record["data_pubblicazione"])) {
					if (strtotime($record["data_pubblicazione"]) > strtotime($allegato["timestamp"])) $allegato["timestamp"] = $record["data_pubblicazione"];
				}
			}
			$percorso_html .= $cartella;
			$percorso_fisico = $config["pub_doc_folder"].$percorso_html.$allegato["riferimento"];
			$percorso_html = "/documenti".$percorso_html.$allegato["nome_file"];
		}
		if (file_exists($percorso_fisico) && !is_dir($percorso_fisico)) {
			$size = human_filesize(filesize($percorso_fisico));
		} else {
			$file_exist = false;
		}
		$note = "";
		$estensione = explode(".",$allegato["nome_file"]);
		$estensione = end($estensione);
		/*
		if (($estensione == "pdf" || $estensione == "p7m") && $file_exist && $allegato["online"] != "N") {
			include_once($root."/inc/p7m.class.php");
			$p7m = new P7Manager($percorso_fisico);
			$certificati = $p7m->extractSignatures();
			if (is_array($certificati)) {
				foreach ($certificati AS $esito) {
					$data = openssl_x509_parse($esito,false);
					$validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
					$validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
					$note .=  "<li>";
					if (isset($data["subject"]["commonName"])) $note .= "<h1>" . $data["subject"]["commonName"] . "</h1>";
					if (isset($data["subject"]["organizationName"]) && $data["subject"]["organizationName"] != "NON PRESENTE") $note .= "<strong>" . $data["subject"]["organizationName"] . "</strong><br>";
					if (isset($data["subject"]["title"])) $note .=  $data["subject"]["title"] . "<br>";
					if (isset($data["issuer"]["organizationName"])) $note .=  "<br>Emesso da:<strong> " . $data["issuer"]["organizationName"] . "</strong>";
					$note .=  "<br><br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
					$note .=  "</li>";
				}
				if ($note != "") $note = "<ul class='firme'>" . $note . "</ul>";
			}
		}
		*/
	?>
	<tr id="allegato_<? echo $allegato["codice"] ?>">
		<td width="10" style="text-align:center; vertical-align: middle;">
			<? if (file_exists($root."/img/".$estensione.".png")) { ?>
				<img src="/img/<? echo $estensione ?>.png" alt="File <? echo $estensione ?>" style="vertical-align:middle"></td>
			<? } ?>
		<td style="vertical-align: middle;">
			<strong>
				<? if ($file_exist) { ?><a href="<? echo $percorso_html ?>" target="_blank"><? } ?>
					<? echo $allegato["titolo"] ?>
				<? if ($file_exist) { ?></a><? } ?>
				<? if ($estensione =="p7m" && $allegato["online"] == "N" && $file_exist) { ?>
					<a href="/allegati/open_p7m.php?codice=<? echo $allegato["codice"] ?>" title="Estrai Contenuto"><img src="/img/download.png" alt="Estrai contenuto" width="15"></a>
				<? } ?>
				<? if ($note != "") {?>
					<input type="image" src="/img/info.png" style="vertical-align:middle; cursor:pointer;" onClick="$('#note_<? echo $allegato["codice"] ?>').dialog({title:'Informazioni di Firma',modal:'true'}); return false;">
				<? }
				if (($allegato["online"] == "N" || $allegato["hidden"] == "S") && $file_exist) {?><img width="20" src="/img/lock.png" alt="File riservato" style="vertical-align:middle"><? } ?>
			</strong>
				- <?= ($file_exist) ? $size : "<small><span class='fa fa-warning'></span> Il file non &egrave; presente sulla piattaforma</small>" ?>
			<div style="display:none;" id="note_<? echo $allegato["codice"] ?>"><? echo $note ?></div>
			<div style="text-align:right; float:right; display: inline;"><small><?= mysql2date($allegato["timestamp"]) ?></small></div>
		</td>
		<? if (!isset($public) && (isset($edit) && $edit) && ((isset($lock) && !$lock) || !isset($lock))) { ?>
			<? if (check_permessi("conservazione",$_SESSION["codice_utente"]) && $file_exist) { ?>
				<td width="10"><button class="btn-round btn-warning" onclick="setMetadati('<?= $allegato["codice"] ?>', 'allegati'); return false"  title="Metadati"><i class="fa fa-pencil"></i></button></td>
			<? } ?>
			<?
				if ($_SESSION["gerarchia"]==="0" || $_SESSION["ente"]["codice"] == $_SESSION["record_utente"]["codice_ente"] || $_SESSION["ente"]["permit_cross"] == "S" || ($allegato["utente_modifica"] == $_SESSION["codice_utente"])) {
					if ($switch_area) {
				?><td width="10"><?
					if (empty($cartella) && $file_exist) { ?>
					<button class='btn-round btn-primary' onClick="switchAllegato('<?= $allegato["codice"] ?>'); return false" title="Cambia area"><span class="fa fa-<?= ($allegato["online"]=="S") ? "lock" : "unlock" ?>"></span></button>
					<?
					}
				?></td>
				<? } ?>
				<td width="10"><button class='btn-round btn-danger' onClick="elimina('<?= $allegato["codice"] ?>','allegati'); return false" title="Elimina">
					<span class="fa fa-remove"></span></button>
				</td>
			<? }
				if (empty($status_conservazione)) $status_conservazione = array();
				if (empty($stati_conservazione)) $stati_conservazione = json_decode(file_get_contents($root."/conservazione/stati.json"),true);

				$bind = array(":codice_allegato"=>$allegato["codice"]);
				$sql = "SELECT b_conservazione.stato FROM b_conservazione JOIN r_conservazione_file ON b_conservazione.codice = r_conservazione_file.codice_pacchetto WHERE r_conservazione_file.codice_file = :codice_allegato AND tabella = 'allegati'";
				if($_SESSION["gerarchia"] > 0) {$sql .= " AND (b_conservazione.codice_ente = :codice_ente OR b_conservazione.codice_gestore = :codice_ente)"; $bind[':codice_ente'] = $_SESSION["record_utente"]["codice_ente"];}
				$ris_conservazione = $pdo->bindAndExec($sql,$bind);
				if ($ris_conservazione->rowCount() > 0) {
					$stato = $ris_conservazione->fetch(PDO::FETCH_ASSOC)["stato"];
					if (empty($status_conservazione[$stato])) $status_conservazione[$stato] = 0;
					$status_conservazione[$stato]++;
					if (!empty($stati_conservazione[$stato]["colore"])) {
						?>
						<td width="2" style="background-color:<?= $stati_conservazione[$stato]["colore"] ?>"></td>
						<?
					}
				} else if ($file_exist) {
					if (empty($status_conservazione[-1])) $status_conservazione[-1] = array();
					$status_conservazione[-1][] = $allegato;
				}
			?>
		<? } ?>
	</tr>
	<?
}
?>
