<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}


	if (!$edit) {
		die();
	} else {
		if (isset($_POST) && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
			$tabella = "b_gruppi_opzioni";
			$codice = $_POST["codice"];
			$operazione = "INSERT";
			if (is_numeric($codice)) $operazione = "UPDATE";

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = $tabella;
			$salva->operazione = $operazione;
			$salva->oggetto = $_POST;
			$codice = $salva->save();

			if ($codice !== false) {
				if (isset($_POST["opzioni"])) {
					foreach($_POST["opzioni"] AS $opzione) {
						$operazione = "UPDATE";
						$codice_opzione = $opzione["codice"];
						if (!is_numeric($opzione["codice"])) {
							$operazione = "INSERT";
							$codice_opzione = 0;
						}
						$opzione["codice_gruppo"] = $codice;

						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "b_opzioni";
						$salva->operazione = $operazione;
						$salva->oggetto = $opzione;
						$codice_paragrafo = $salva->save();
					}
				}
				$html="<option value=\"0\">Sempre</option>";
				$sql = "SELECT * FROM b_opzioni WHERE eliminato = 'N' ORDER BY codice_gruppo";
				$ris_opzioni = $pdo->query($sql);
				if ($ris_opzioni->rowCount()>0) {
					$gruppo_attuale = "";
					$prima = true;
					while($opzione = $ris_opzioni->fetch(PDO::FETCH_ASSOC)) {
						if ($opzione["codice_gruppo"] != $gruppo_attuale) {
							$gruppo_attuale = $opzione["codice_gruppo"];
							$bind=array(":codice"=>$gruppo_attuale);
							$sql_gruppo = "SELECT * FROM b_gruppi_opzioni WHERE codice = :codice";
							$ris_gruppo = $pdo->bindAndExec($sql_gruppo,$bind);
							if ($ris_gruppo->rowCount()>0) {
								$gruppo = $ris_gruppo->fetch(PDO::FETCH_ASSOC);
								if (!$prima) {
									$html.="</optgrout>";
								} else {
									$prima = false;
								}
								$html.="<optgrout label=\"". $gruppo["titolo"] ."\">";
								}
							}
							$html.="<option value=\"".$opzione["codice"]."\">" . $opzione["titolo"] . "</option>";
						}
						$html.="</optgrout>";
					} ?>
					if ($(".select_opzione").length > 0) {
						$.ajax({
							type: "POST",
							url: "/impostazioni/gruppi_modelli/list_opzioni.php",
							dataType: "html",
							data: data,
							async:false,
							success: function(script) {
								$(".select_opzione").each(function() {
									valore = $(this).val();
									$(this).html(script);
									$(this).val(valore);
									$(this).trigger("chosen:updated");
								})
							}
						});
						$("#dialog").dialog('destroy');
						$("#dialog").hide();
				 	} else {
						window.location.href = '/impostazioni/opzioni/';
					}
				<?
			}
		}
	}
?>
