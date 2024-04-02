<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	include_once($root."/dgue/config.php");
	if (isset($_POST["espd"]) && isset($_POST["codice_riferimento"]) && isset($_POST["sezione"]) && is_operatore()) {

		$operazione = "INSERT";
		$dgue = array();
		$dgue["codice_riferimento"] = $_POST["codice_riferimento"];
		$dgue["sezione"] = $_POST["sezione"];
		$dgue["codice_utente"] = $_SESSION["codice_utente"];

		$bind = array();
		$bind[":codice_riferimento"] = $_POST["codice_riferimento"];
		$bind[":sezione"] = $_POST["sezione"];
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$sql = "SELECT codice FROM b_dgue_compilati WHERE codice_riferimento = :codice_riferimento AND sezione = :sezione AND
						codice_utente = :codice_utente";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount() > 0) {
			$operazione = "UPDATE";
			$dgue["codice"] = $ris->fetchAll(PDO::FETCH_ASSOC)[0]["codice"];
		}

		$array = $_POST["espd"];
		$criterion = array();

		if (!empty($array["ccv:Criterion"]["9b19e869-6c89-4cc4-bd6c-ac9ca8602165"]["ccv:RequirementGroup"][0]["ccv:Requirement"]) && empty($array["ccv:Criterion"]["9b19e869-6c89-4cc4-bd6c-ac9ca8602165"]["ccv:RequirementGroup"][0]["ccv:Requirement"][1]["ccv:Response"]["ccv-cbc:Indicator"])) $array["ccv:Criterion"]["9b19e869-6c89-4cc4-bd6c-ac9ca8602165"]["ccv:RequirementGroup"][0]["ccv:Requirement"][1]["ccv:Response"]["ccv-cbc:Indicator"] = "false";
		foreach($array["ccv:Criterion"] AS $id => $criteria) {
			if (isset($criteria[0])) {
				foreach($criteria AS $elemento) {
					$elemento["cbc:ID"]['$'] = $id;
					$criterion[] = $elemento;
				}
			} else {
				$criteria["cbc:ID"]['$'] = $id;
				$criterion[] = $criteria;
			}
		}
		$array["ccv:Criterion"] = $criterion;
		$array = convertDate($array);

		$dgue["json"] = json_encode($array);
		$echo_xml = true;
		$percentuale = 50;
		
		if (isset($_POST["soa"]["tipo"]) && $_POST["soa"]["tipo"] != "non_applicabile") {
			$dgue["soa"] = json_encode($_POST["soa"]);
			if ($echo_xml) {
				$percentuale = 33.3;
			} else {
				$percentuale = 50;
			}
		}
		if (isset($_POST["nazionali"])) {
			$dgue["nazionali"] = json_encode($_POST["nazionali"]);
		}
		if (isset($_POST["subappalto"])) {
			$dgue["subappalto"] = json_encode($_POST["subappalto"]);
		}
		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_dgue_compilati";
		$salva->operazione = $operazione;
		$salva->oggetto = $dgue;
		$codice = $salva->save();

		if ($codice > 0) {
			?>
				<h1>SALVATAGGIO DGUE</h1>
				<h2>Salvataggio effettuato con successo</h2>
				<div style="float:left; width:<?= $percentuale ?>%">
					<a href="/dgue/getPDF.php?codice_dgue=<?= $codice ?>" class="submit_big" style="background-color:#900"><h2 style="text-align:center"><span class="fa fa-file-pdf-o fa-4x"></span><br><br>Download PDF</h2></a>
				</div>
				<? if ($echo_xml) { ?><div style="float:left; width:<?= $percentuale ?>%">
					<a href="/dgue/getXML.php?codice_dgue=<?= $codice ?>" class="submit_big" style="background-color:#066"><h2 style="text-align:center"><span class="fa fa-code fa-4x"></span><br><br>Download XML</h2></a>
				</div>
				<? } ?>
				<?
					if (isset($_POST["soa"]["tipo"]) && $_POST["soa"]["tipo"] != "non_applicabile") {
						?>
						<div style="float:left; width:<?= $percentuale ?>%">
							<a href="/dgue/getSOA.php?codice_dgue=<?= $codice ?>" class="submit_big" style="background-color:#FC0"><h2 style="text-align:center"><span class="fa fa-file-pdf-o fa-4x"></span><br><br>Download dichiarazione S.O.A.</h2></a>
						</div>
						<?
					}
				?>
				<div class="clear"></div><br>
				<a href="/dgue/edit.php?sezione=<?= $_POST["sezione"] ?>&codice_riferimento=<?= $_POST["codice_riferimento"] ?>" class=" ritorna_button submit_big">Ritorna alla compilazione</a>
				<?
				$href = $_POST["sezione"];
				switch($_POST["sezione"]) {
					case "dialogo": $href = "dialogo_competitivo"; break;
					case "albo": $href = "albo_fornitori"; break;
					case "mercato": $href = "mercato_elettronico"; break;
				}
				?>
				<a href="/<?= $href ?>/id<?= $_POST["codice_riferimento"] ?>-dettaglio" class="ritorna_button submit_big" style="background-color:#999;">Ritorna al bando</a>
			<?
		} else {
			?>
				<h1>SALVATAGGIO DGUE</h1>
				<h2 class="errore">Errore nel salvataggio si prega di riprovare</h2>
			<?
		}
	} else {
		echo "<h1>Errore</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
