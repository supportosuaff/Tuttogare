<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("enti",$_SESSION["codice_utente"]);
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
		if (isset($_POST["operazione"])) {

			$operazione = $_POST["operazione"];
			$cod_moduli = $_POST["cod_moduli"];

			if (!empty($_POST["filechunk"])) {
				$copy = copiafile_chunck($_POST["filechunk"],$config["pub_doc_folder"]."/enti/",$config["chunk_folder"]."/".$_SESSION["codice_utente"]);
				if ($copy != false)  {
					$path = $config["pub_doc_folder"].'/enti/'.$copy["nome_fisico"];
					$ext = explode(".",$copy["nome_file"]);
					$ext = end($ext);
					rename($path,$path.".".$ext);
					$img = new abeautifulsite\SimpleImage($path.".".$ext);
					$img->fit_to_width(1024)->save($path.".".$ext);
					rename($path.".".$ext,$path);
					$_POST["logo"] = $copy["nome_file"];
					$_POST["riferimento"] = $copy["nome_fisico"];
				}
			}

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_enti";
			$salva->operazione = $operazione;
			$salva->oggetto = $_POST;
			$codice = $salva->save();
			if ($codice > 0) {
				$strsql = "DELETE FROM r_moduli_ente WHERE cod_ente = :codice";
				$risultato = $pdo->bindAndExec($strsql,[":codice" => $codice]);

				$moduli = explode(";",$cod_moduli);

				foreach($moduli as $modulo) {
					if ($modulo != "") {
						$r_modulo=array("cod_ente"=>$codice,"cod_modulo"=>$modulo);

						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_moduli_ente";
						$salva->operazione = "INSERT";
						$salva->oggetto = $r_modulo;
						$salva->save();
						if ($modulo == "91") {
							if (isset($config["url-art-80"][$_POST["providerArt80"]])) {
								$art80 = $config["url-art-80"][$_POST["providerArt80"]];
								$uri = $art80["url"];
								$requests = [];
								$body = [
									"claimant_id"=> $codice,
									"name"=> $_POST["denominazione"],
									"url"=> "https://{$_POST["dominio"]}",
									"taxcode"=> $_POST["cf"],
									"email"=> $_POST["email"],
									"pec"=> $_POST["pec"],
									"address"=> $_POST["indirizzo"],
									"city"=> $_POST["citta"],
									"region"=> $_POST["provincia"],
									"zipcode"=> $_POST["cap"],
									"phone"=> $_POST["telefono"],
								];
								try {
										$curl80 = curl_init();
										curl_setopt_array($curl80, array(
											CURLOPT_URL => "{$uri}/claimant/store",
											CURLOPT_RETURNTRANSFER => true,
											CURLOPT_ENCODING => "",
											CURLOPT_MAXREDIRS => 10,
											CURLOPT_TIMEOUT => 0,
											CURLOPT_FOLLOWLOCATION => true,
											CURLOPT_SSL_VERIFYPEER => FALSE,
											CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
											CURLOPT_CUSTOMREQUEST => "POST",
											CURLOPT_POSTFIELDS => json_encode($body),
											CURLOPT_HTTPHEADER => array(
												"claimant-id: {$codice}",
												"token: {$art80["application_token"]}",
												"Content-Type: application/json"
											),
										));
										$curl80 = addCurlAuth($curl80);
										$response = curl_exec($curl80);
										if(! empty($response)) $curl80s = json_decode($response, TRUE);
										curl_close($curl80);
								} catch (\Exception $e) {
									unset($e);
								}
							}
						}
					}
				}

				if ($_POST["operazione"]=="UPDATE") {
				?>
					alert('Modifica effettuata con successo');
	    	  window.location.href = '/enti/id<?= $codice ?>-edit';
	        <?
				} elseif ($_POST["operazione"]=="INSERT") {
					?>
					if (confirm('Inserimento effettuato con successo. Vuoi effettuare un altro inserimento?')) {
	    	    window.location.href = '/enti/id0-edit';
					} else {
						window.location.href = '/enti/id<?= $codice ?>-edit';
					}
					<?
				}
			} else { ?>
				alert('Errore nel salvataggio');
			<? }
		}
	}



?>
