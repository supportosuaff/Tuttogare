<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	if(is_operatore() && isset($_SESSION["ente"]["codice"]) && isset($_SESSION["codice_utente"])) {
		if(! empty($_POST["gara"]) && ! empty($_POST["filename"])) {
			$gara = $pdo->bindAndExec(
				"SELECT b_gare.codice FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara WHERE b_gare.codice = :codice AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) AND (pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente))))",
				array(':codice' => $_POST["gara"], ':codice_utente' => $_SESSION["codice_utente"], ':codice_ente' => $_SESSION["ente"]["codice"])
			)->fetch(PDO::FETCH_ASSOC);
			if(! empty($gara)) {
				$allegato = array();
				$percorso =  $config["pub_doc_folder"] . "/allegati/".$gara["codice"];
				if (!is_dir($percorso)) mkdir($percorso,0777,true);
				$copy = @copiafile_chunck($_POST["filename"], $percorso."/", $config["chunk_folder"]."/".$_SESSION["codice_utente"]);
				if (file_exists($percorso."/".$copy["nome_fisico"])) {
					$allegato["titolo"] = explode(".", $copy["nome_file"]);
					$salt_index = count($allegato["titolo"]) - 2;
					unset($allegato["titolo"][$salt_index]);
					$allegato["titolo"] = implode(".", $allegato["titolo"]);
					$allegato["nome_file"] = $copy["nome_file"];
					$allegato["riferimento"] = $copy["nome_fisico"];
					$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
					// $allegato["titolo"] = @str_replace('-', ' ', pathinfo($allegato["nome_file"], PATHINFO_FILENAME));
					$allegato["codice_gara"] = $gara["codice"];
					$allegato["sezione"] = 'faq-gara';
					$allegato["online"] = 'S';
					$allegato["hidden"] = 'S';

					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_allegati";
					$salva->operazione = "INSERT";
					$salva->oggetto = $allegato;
					$allegato["codice"] = $salva->save();
					if ($allegato["codice"] > 0) {
						ob_start();
						?>
						<tr id="allegato_chiarimenti_<?= $allegato["codice"] ?>">
							<td style="vertical-align: middle;" width="10"><img src="/img/<?= pathinfo($allegato["nome_file"], PATHINFO_EXTENSION) ?>.png"></td>
							<td style="vertical-align: middle;"><a href="/documenti/allegati/<?= $gara["codice"] ?>/<?= $allegato["nome_file"] ?>" target="_blank"><?= $allegato["titolo"] ?></a></td>
							<td width="20" style="text-align: right; vertical-align: middle;"><button type="button" class='btn-round btn-danger' onClick="elimina('<?= "{$allegato["codice"]}:{$gara["codice"]}" ?>','gare/chiarimenti'); return false" title="Elimina"><i class="fa fa-trash"></i></button></td>
						</tr>
						<?
						$response["html"] = ob_get_clean();
						$response["codice"] = $allegato["codice"];
						echo json_encode($response, JSON_FORCE_OBJECT);
						exit();
					}
				}
			}
		}
	}

	http_response_code(400);
?>
