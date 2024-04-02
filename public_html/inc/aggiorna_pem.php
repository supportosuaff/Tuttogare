<?
	if (file_exists($config["cafolder"] . '/caitalia.pem')) $ultima_modifica = filemtime($config["cafolder"] . '/caitalia.pem');
	if (!isset($ultima_modifica) || $ultima_modifica < strtotime("-1 day")) {
		$pem = "";
		$error_pem = false;
		try { $xml = new SimpleXMLElement("https://eidas.agid.gov.it/TL/TSL-IT.xml",0,1); } catch (Exception $error_pem) { $errore_pem = true; }
		if ($error_pem === false) {
			foreach ($xml->TrustServiceProviderList->TrustServiceProvider as $provider) {
				foreach ($provider->TSPServices->TSPService as $service) {
					$pem .= "-----BEGIN CERTIFICATE-----\n";
					$pem .= chunk_split($service->ServiceInformation->ServiceDigitalIdentity->DigitalId->X509Certificate,64);
					$pem .= "-----END CERTIFICATE-----\n";
				}
			}
			if ($pem != "") {
				$fp = fopen($config["cafolder"] . '/caitalia.pem', 'w');
				fwrite($fp, $pem);
				fclose($fp);
			}
		}
	}
?>
