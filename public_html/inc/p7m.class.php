<?
class P7Manager {

  private $filePath;
  public $fileContent;
  private $cafolder;
  private $bash_folder;
  private $tmp_folder;

  function __construct($path) {
    if (strpos($path, "..")===false) {
      if (file_exists($path) && !is_dir($path)) {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '-1');
        $this->filePath = $path;
        $this->fileContent = file_get_contents($this->filePath);

        global $config;
        $this->cafolder = $config["cafolder"];
        $this->bash_folder = $config["bash_folder"];
        $this->tmp_folder = $config["chunk_folder"];
      }
    }
  }

  static public function der2smime($file)
  {
    $to = "MIME-Version: 1.0" . PHP_EOL;
    $to .="Content-Disposition: attachment; filename=\"smime.p7m\"" . PHP_EOL;
    $to .="Content-Type: application/x-pkcs7-mime; smime-type=signed-data; name=\"smime.p7m\"" . PHP_EOL;
    $to .="Content-Transfer-Encoding: base64" . PHP_EOL . PHP_EOL;
    $tmp=file_get_contents($file);
    $from = base64_decode($tmp,true);
    if (empty($from)) $from = $tmp;
    $to.=chunk_split(base64_encode($from));
    return file_put_contents($file.".smime",$to);
  }

  public function opensslExtract($objectPath,$extractType = false) {
    $return = false;
    if (empty($objectPath)) $objectPath =  $this->filePath;
    if (file_exists($objectPath)) {
      if (self::der2smime($objectPath)) {
        $tmp_file = $this->tmp_folder . "/" . session_id() . time();
        $certPath = $tmp_file.".pem";;
        $contentPath = $tmp_file.".tmp";;
        $caPath = $this->cafolder . "/european_ca.pem";
        if (openssl_pkcs7_verify($objectPath.".smime",PKCS7_BINARY,$certPath,[$caPath],$caPath,$contentPath) === true) {
          $return = true;
          if ($extractType == "cert") {
            $return = openssl_x509_parse(file_get_contents($certPath));
          } else if ($extractType == "content") { 
            $return = file_get_contents($contentPath);
          }
          @unlink($certPath);
          @unlink($contentPath);
        } 
      }
    }
    return $return;
  }

  static public function getXMLFromURL($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,15);
    curl_setopt($ch, CURLOPT_HTTP_VERSION,'CURL_HTTP_VERSION_1_1' );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
    $ch = addCurlAuth($ch);
    $result=curl_exec ($ch);
    curl_close ($ch);

    return $result;
  }

  static public function updatePEM($force = false) {
    global $config;
    libxml_use_internal_errors(true);
    $EU_xml = "https://ec.europa.eu/tools/lotl/eu-lotl.xml";
    if (file_exists($config["cafolder"] . '/european_ca.pem')) $ultima_modifica = filemtime($config["cafolder"] . '/european_ca.pem');
    if (!isset($ultima_modifica) || $ultima_modifica < strtotime("-1 day") || $force) {
      $pem = "";
      try {
        $xmlContent = self::getXMLFromURL($EU_xml);
        $xml_list = new SimpleXMLElement($xmlContent);
      } catch (Exception $error_pem) {
        $error_list = true;
      }
      if (!empty($xml_list->SchemeInformation->PointersToOtherTSL->OtherTSLPointer) && !isset($error_list)) {
        // if (P7Manager::verifyXmlSignature($xml_list->asXml())) {
          foreach($xml_list->SchemeInformation->PointersToOtherTSL->OtherTSLPointer AS $tsl_pointer) {
            $error_pem = false;
            $url = explode(".",$tsl_pointer->TSLLocation);
            if (end($url) == "xml") {
              try {
                $xmlContent = self::getXMLFromURL($tsl_pointer->TSLLocation);
                $xml = @new SimpleXMLElement($xmlContent);
              } catch (Exception $error_pem) {
                $error_pem = true;
              }
              if (!empty($xml->TrustServiceProviderList->TrustServiceProvider) && !$error_pem) {
                // if (P7Manager::verifyXmlSignature($xml->asXml())) {
                  foreach ($xml->TrustServiceProviderList->TrustServiceProvider as $provider) {
                    foreach ($provider->TSPServices->TSPService as $service) {
                      if (!empty($service->ServiceInformation->ServiceDigitalIdentity->DigitalId->X509Certificate)) {
                        if(strstr($service->ServiceInformation->ServiceDigitalIdentity->DigitalId->X509Certificate, PHP_EOL)) {
                          $pem .= "-----BEGIN CERTIFICATE-----";
                          $pem .= $service->ServiceInformation->ServiceDigitalIdentity->DigitalId->X509Certificate;
                          $pem .= "-----END CERTIFICATE-----";
                        } else {
                          $pem .= "-----BEGIN CERTIFICATE-----" . PHP_EOL;
                          $pem .= chunk_split($service->ServiceInformation->ServiceDigitalIdentity->DigitalId->X509Certificate,64);
                          $pem .= "-----END CERTIFICATE-----" . PHP_EOL;
                        }
                      }
                    }
                  }
                // }
              }
            }
          // }
        }
        $others = [];
        $others = ["https://www.ssi.gouv.fr/uploads/2016/07/tl-fr.xml"];
        $others = ["https://portail-qualite.public.lu/content/dam/qualite/fr/publications/confiance-numerique/liste-confiance-nationale/tsl-xml/tsl.xml"];
        $others = ["http://tsl.gov.cz/publ/TSL_CZ.xtsl"];
        $others = ["http://crc.bg/files/_bg/TSL-CRC-BG-signed.xml"];
        $others = ["https://sr.riik.ee/tsl/estonian-tsl.xml"];
        $others = ["https://sede.minetur.gob.es/Prestadores/TSL/TSL.xml"];
        $others = ["http://www.nmhh.hu/tl/pub/HU_TL.xml"];
        $others = ["http://www.neytendastofa.is/library/Files/TSl/tsl.xml"];
        $others = ["https://tl-norway.no/TSL/NO_TSL.XML"];
        $others = ["http://www.gns.gov.pt/media/1894/TSLPT.xml"];
        $others = ["https://www.tscheme.org/sites/default/files/tsl-uk0019signed.xml"];
        foreach($others AS $tslLocation) {
          $error_pem = false;
          try {
            $xmlContent = self::getXMLFromURL($tslLocation);
            $xml = @new SimpleXMLElement($xmlContent);
          } catch (Exception $error_pem) {
            $error_pem = true;
          }
          if (!empty($xml->TrustServiceProviderList->TrustServiceProvider) && !$error_pem) {
            // if (P7Manager::verifyXmlSignature($xml->asXml())) {
              foreach ($xml->TrustServiceProviderList->TrustServiceProvider as $provider) {
                foreach ($provider->TSPServices->TSPService as $service) {
                  if (!empty($service->ServiceInformation->ServiceDigitalIdentity->DigitalId->X509Certificate)) {
                    if(strstr($service->ServiceInformation->ServiceDigitalIdentity->DigitalId->X509Certificate, PHP_EOL)) {
                      $pem .= "-----BEGIN CERTIFICATE-----";
                      $pem .= $service->ServiceInformation->ServiceDigitalIdentity->DigitalId->X509Certificate;
                      $pem .= "-----END CERTIFICATE-----";
                    } else {
                      $pem .= "-----BEGIN CERTIFICATE-----" . PHP_EOL;
                      $pem .= chunk_split($service->ServiceInformation->ServiceDigitalIdentity->DigitalId->X509Certificate,64);
                      $pem .= "-----END CERTIFICATE-----" . PHP_EOL;
                    }
                  }
                }
              }
            // }
          }
        } 
        if ($pem != "") {
          $fp = fopen($config["cafolder"] . '/european_ca.pem', 'w');
          fwrite($fp, $pem);
          fclose($fp);
        }
      }
    }
  }

  private function pdfUtility($type,$objectPath="") {
    $return_value = false;
    if ($type =="Certificati") $return_value = array();
    if (empty($objectPath)) $objectPath = $this->filePath;
    if (file_exists($objectPath)) {
      $content = file_get_contents($objectPath);
      if (preg_match_all('/\/Contents\s?<([a-zA-Z0-9]+)[>]{1}/s',$content,$signatures)) {
        $signatures = $signatures[1];
        $esito = array();
        foreach($signatures AS $pkcs7) {
          $pkcs7 = hex2bin($pkcs7);
          file_put_contents($objectPath.".p7b",$pkcs7);
          $comando = $this->bash_folder.'/estrai_Certificato.bash \'' . $objectPath. '.p7b\'';
          $cert = shell_exec("sh " . $comando . " 2>&1");
          switch ($type) {
            case 'Certificati':
              $comando = $this->bash_folder.'/estrai_firme.bash \'' . $objectPath. '.p7b\'';
              $firme = shell_exec("sh " . $comando . " 2>&1");
              $certificati = explode("Certificate:",$firme);
              unset($certificati[0]);
              $return_value = array_merge($return_value,$certificati);
            break;
            default:
              file_put_contents($objectPath.".p7b",substr($cert, strpos($cert, '-----BEGIN CERTIFICATE----- ')));
              $comando = $this->bash_folder.'/verifica_SignedPDF.bash \'' . $objectPath . '.p7b\' \'' . $this->cafolder . '/european_ca.pem\'';
              $msg = shell_exec("sh " . $comando . " 2>&1");
              if (strpos($msg, ": OK")!== false) {
                $esito[] = true;
              } else {
                $esito[] = false;
              }
              if (count($esito) > 0 && !in_array(false,$esito,true)) {
                $return_value = "Verification successful";
              } else {
                $return_value = false;
              }
            break;
          }
        }
        unlink($objectPath. '.p7b');
      }
    }
    if ($type =="Certificati" && empty($return_value)) $return_value = false;
    return $return_value;
  }

  static public function verifyXmlSignature($xml_string) {
    $return = false;
    if (!empty($xml_string)) {
      $xmlDoc = new DOMDocument();
      if ($xmlDoc->loadXML($xml_string)) {
        $xpath = new DOMXPath($xmlDoc);
        $xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
        $query = ".//ds:Signature";
        $nodeset = $xpath->query($query, $xmlDoc);
        if ($nodeset!=false) {
          $signatureNode = $nodeset->item(0);
          $query = "./ds:SignedInfo";
          $nodeset = $xpath->query($query, $signatureNode);
          if ($nodeset!=false) {
            $signedInfoNode = $nodeset->item(0);
            $signedInfoNodeCanonicalized = $signedInfoNode->C14N(true, false);
            if ($signedInfoNodeCanonicalized != false) {
              $query = 'string(./ds:SignatureValue)';
              $signature_value = $xpath->evaluate($query, $signatureNode);
              if ($signature_value!=false) {
                $signature_value = base64_decode($signature_value);
                $query = 'string(./ds:KeyInfo/ds:X509Data/ds:X509Certificate)';
                $x509cert = $xpath->evaluate($query, $signatureNode);
                if ($x509cert !=false) {
                  $x509cert = str_replace(PHP_EOL, "", $x509cert);
                  $x509cert = str_replace("-----BEGIN CERTIFICATE-----","",$x509cert);
                  $x509cert = str_replace("-----END CERTIFICATE-----","",$x509cert);
                  $cert = "-----BEGIN CERTIFICATE-----" . PHP_EOL;
                  $cert .= chunk_split($x509cert,64);
                  $cert .= "-----END CERTIFICATE-----" . PHP_EOL;
                  $publicKey = openssl_get_publickey($cert);
                  $return = openssl_verify($signedInfoNodeCanonicalized, $signature_value, $publicKey,OPENSSL_ALGO_SHA256);
                }
              }
            }
          }
        }
      }
    }
    return $return;
  }

  public function checkSignatures($objectPath="") {
    if (empty($objectPath)) $objectPath =  $this->filePath;
    self::updatePEM();
    $data = file_get_contents($objectPath);
    $file_info = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $file_info->file($objectPath);
    $esito = false;
    if (strpos($mime_type,"pdf") !== false) {
      $esito = $this->pdfUtility("Valida",$objectPath);
    } else {
      $testPath = $objectPath;
      $unlink = false;
      $comando = $this->bash_folder.'/verifica.bash \'' . $objectPath . '\' \'' . $this->cafolder . '/european_ca.pem\'';
      $esito = shell_exec("sh " . $comando . " 2>&1");
      $esito = trim($esito);
      if ($esito != "Verification successful") {
        $data = base64_decode($data, true);
        if ($data !== false) {
          $tmp_file = $this->tmp_folder . "/" . session_id() . ".tmp";
          file_put_contents($tmp_file, $data);
          $comando = $this->bash_folder.'/verifica.bash \'' . $tmp_file . '\' \'' . $this->cafolder . '/european_ca.pem\'';
          $esito = shell_exec("sh " . $comando . " 2>&1");
          $esito = trim($esito);
          unlink($tmp_file);
        }
      }

    }
    return $esito;
  }

  public function extractContent() {
    $return = false;
    $tmp_file = $this->tmp_folder . "/" . session_id() . ".tmp";
    $comando = $this->bash_folder.'/estrai.bash \'' . $this->filePath . '\' \'' . $tmp_file .'\'';
    $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
    if (trim($esito_apertura)=="Verification successful") {
      $return = file_get_contents($tmp_file);
      unlink($tmp_file);
    }
    return $return;
  }

  public function extractSignatures($objectPath="") {
    if (empty($objectPath)) $objectPath =  $this->filePath;
    $data = file_get_contents($objectPath);
    $file_info = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $file_info->buffer($data);
    $certificati = array();
    if (strpos($mime_type,"pdf") !== false) {
      $certificati = $this->pdfUtility("Certificati",$objectPath);
    } else {
      $comando = $this->bash_folder.'/estrai_firme.bash \'' . $objectPath . '\'';
      $firme = shell_exec("sh " . $comando . " 2>&1");
      $certificati = explode("Certificate:",$firme);
      if (strpos($firme,"Certificate:") === false) {
        $data = base64_decode($data, true);
        if ($data !== false) {
          $tmp_file = $this->tmp_folder . "/" . session_id() . ".tmp";
          file_put_contents($tmp_file, $data);
          $comando = $this->bash_folder.'/estrai_firme.bash \'' . $tmp_file . '\'';
          $firme = shell_exec("sh " . $comando . " 2>&1");
          $certificati = explode("Certificate:",$firme);
          unlink($tmp_file);
        }
      }
      unset($certificati[0]);
    }
    return $certificati;
  }

  function find($hash,$algorithm,$checkContent=false) {
    $found = false;
    $result = array();
    $tmp_file = $this->tmp_folder . "/" . session_id() . ".tmp";
    $comando = $this->bash_folder.'/estrai.bash \'' . $this->filePath . '\' \'' . $tmp_file .'\'';
    $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
    if (trim($esito_apertura)!="Verification successful") {
      $data = base64_decode(file_get_contents($this->filePath), true);
      if ($data !== false) {
        $tmp_file_1 = $this->tmp_folder . "/tmp_" . session_id() . ".tmp";
        file_put_contents($tmp_file_1, $data);
        $comando = $this->bash_folder.'/estrai.bash \'' . $tmp_file_1 . '\' \'' . $tmp_file .'\'';
        $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
        unlink($tmp_file_1);
      }
    }
    if (trim($esito_apertura)=="Verification successful") {
      $file_info = new finfo(FILEINFO_MIME_TYPE);
      $mime_type = $file_info->buffer(file_get_contents($tmp_file));
      if (strpos($mime_type,"pdf") !== false) {
        if (!$checkContent) {
          $dataContent = file_get_contents($tmp_file);
        } else {
          try {
            $dataContent = @new PdfToText($tmp_file);
            $dataContent =  $dataContent->Text;
            $dataContent = preg_replace("/[^a-zA-Z0-9]/", '', $dataContent);
          } catch (Exception $e) {
            $dataContent = "";
          }
        }
        $result[] = hash($algorithm,$dataContent);
      } else if ((strpos($mime_type,"zip") !== false)||(strpos($mime_type,"rar") !== false)||(strpos($mime_type,"7z") !== false)) {
        $result[] = hash($algorithm,file_get_contents($tmp_file));
        $zip_folder = $this->tmp_folder .'/'. session_id() . '/zip/';
        $unzipped = false;
        if (strpos($mime_type,"zip")) {
          $zip = new ZipArchive;
          $res_zip = $zip->open($tmp_file);
          if ($res_zip === true) {
            $unzipped = true;
            $zip->extractTo($zip_folder);
            $zip->close();
          }
        } else if (strpos($mime_type,"rar")) {
          $res_zip = RarArchive::open($tmp_file);
          $entries = $res_zip->getEntries();
          if ($entries !== false) {
            $unzipped = true;
            foreach ($entries as $e) {
              $e->extract($zip_folder);
            }
            $res_zip->close();
          }
        } else if (strpos($mime_type,"7z")) {
          $comando = $this->bash_folder.'/estrai_7z.bash \''.$tmp_file.'\' \''.$zip_folder.'\'';
          $esito_extract = shell_exec("sh " . $comando . " 2>&1");
          if (strpos($esito_extract,"Everything is Ok") !== false) $unzipped = true;
        }
        if ($unzipped) {
          $it = new RecursiveDirectoryIterator($zip_folder, RecursiveDirectoryIterator::SKIP_DOTS);
          $filesZip = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
          foreach($filesZip as $fileZip) {
            if (!$fileZip->isDir()) {
              $evaluating_tmp = $zip_folder . ".evaluating.tmp";
              rename($fileZip->getRealPath(),$evaluating_tmp);
              $ev_data = file_get_contents($evaluating_tmp);
              $file_info = new finfo(FILEINFO_MIME_TYPE);
              $mime_type = $file_info->buffer($ev_data);
              $keep = true;
              while($keep) {
                $comando = $this->bash_folder.'/estrai.bash \''.$evaluating_tmp.'\' \''.$evaluating_tmp.'\'';
                $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
                if (trim($esito_apertura)!="Verification successful") {
                  $ev_data = base64_decode($ev_data, true);
                  if ($ev_data !== false) {
                    file_put_contents($evaluating_tmp, $ev_data);
                    $comando = $this->bash_folder.'/estrai.bash \''.$evaluating_tmp.'\' \''.$evaluating_tmp.'\'';
                    $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
                  }
                }
                if (trim($esito_apertura)=="Verification successful") {
                  $file_info = new finfo(FILEINFO_MIME_TYPE);
                  $mime_type = $file_info->buffer(file_get_contents($evaluating_tmp));
                  if (strpos($mime_type,"pdf") !== false) {
                    if (!$checkContent) {
                      $dataContent = file_get_contents($evaluating_tmp);
                    } else {
                      try {
                        $dataContent = @new PdfToText($evaluating_tmp);
                        $dataContent =  $dataContent->Text;
                        $dataContent = preg_replace("/[^a-zA-Z0-9]/", '', $dataContent);
                      } catch (Exception $e) {
                        $dataContent = "";
                      }
                    }
                    $result[] = hash($algorithm,$dataContent);
                    $keep = false;
                  } else {
                    $result[] = hash($algorithm,file_get_contents($evaluating_tmp));
                  }
                } else {
                  if ($checkContent) {
                    $esito = $this->pdfUtility("Valida", $evaluating_tmp);
                    if (trim($esito)=="Verification successful") {
                      try {
                        $dataContent = @new PdfToText($evaluating_tmp);
                        $dataContent =  $dataContent->Text;
                        $dataContent = preg_replace("/[^a-zA-Z0-9]/", '', $dataContent);
                      } catch (Exception $e) {
                        $dataContent = "";
                      }
                      $result[] = hash($algorithm,$dataContent);
                    }
                  }
                  $keep = false;
                }
              }
            }
          }
          $it = new RecursiveDirectoryIterator($zip_folder, RecursiveDirectoryIterator::SKIP_DOTS);
          $filesZip = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
          foreach($filesZip as $fileZip) {
              if ($fileZip->isDir()){
                  rmdir($fileZip->getRealPath());
              } else {
                  unlink($fileZip->getRealPath());
              }
          }
          rmdir($zip_folder);
        }
      } else {
        $keep = true;
        while($keep) {
          $data_tmp = file_get_contents($tmp_file);
          $comando = $this->bash_folder.'/estrai.bash \'' . $tmp_file . '\' \'' . $tmp_file .'\'';
          $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
          if (trim($esito_apertura)!="Verification successful") {
            $data_tmp = base64_decode($data_tmp ,true);
            if ($data_tmp !== false) {
              file_put_contents($tmp_file, $data_tmp);
              $comando = $this->bash_folder.'/estrai.bash \'' . $tmp_file . '\' \'' . $tmp_file .'\'';
              $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
            }
          }
          if (trim($esito_apertura)=="Verification successful") {
            $file_info = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $file_info->buffer(file_get_contents($tmp_file));
            if ((strpos($mime_type,"zip") !== false)||(strpos($mime_type,"rar") !== false)||(strpos($mime_type,"7z") !== false)) {
              $result[] = hash($algorithm,file_get_contents($tmp_file));
              $zip_folder = $this->tmp_folder .'/'. session_id() . '/zip/';
              $unzipped = false;
              if (strpos($mime_type,"zip")) {
                $zip = new ZipArchive;
                $res_zip = $zip->open($tmp_file);
                if ($res_zip === true) {
                  $unzipped = true;
                  $zip->extractTo($zip_folder);
                  $zip->close();
                }
              } else if (strpos($mime_type,"rar")) {
                $res_zip = RarArchive::open($tmp_file);
                $entries = $res_zip->getEntries();
                if ($entries !== false) {
                  $unzipped = true;
                  foreach ($entries as $e) {
                    $e->extract($zip_folder);
                  }
                  $res_zip->close();
                }
              } else if (strpos($mime_type,"7z")) {
                $comando = $this->bash_folder.'/estrai_7z.bash \''.$tmp_file.'\' \''.$zip_folder.'\'';
                $esito_extract = shell_exec("sh " . $comando . " 2>&1");
                if (strpos($esito_extract,"Everything is Ok") !== false) $unzipped = true;
              }
              if ($unzipped) {
                $it = new RecursiveDirectoryIterator($zip_folder, RecursiveDirectoryIterator::SKIP_DOTS);
                $filesZip = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
                foreach($filesZip as $fileZip) {
                  if (!$fileZip->isDir()) {
                    $evaluating_tmp = $zip_folder . ".evaluating.tmp";
                    rename($fileZip->getRealPath(),$evaluating_tmp);
                    $file_info = new finfo(FILEINFO_MIME_TYPE);
                    $ev_data = file_get_contents($evaluating_tmp);
                    $mime_type = $file_info->buffer(file_get_contents($ev_data));
                    $keep = true;
                    while($keep) {
                      $comando = $this->bash_folder.'/estrai.bash \''.$evaluating_tmp.'\' \''.$evaluating_tmp.'\'';
                      $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
                      if (trim($esito_apertura)!="Verification successful") {
                        $ev_data = base64_decode($ev_data, true);
                        if ($ev_data !== false) {
                          file_put_contents($evaluating_tmp, $ev_data);
                          $comando = $this->bash_folder.'/estrai.bash \''.$evaluating_tmp.'\' \''.$evaluating_tmp.'\'';
                          $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
                        }
                      }
                      if (trim($esito_apertura)=="Verification successful") {
                        $file_info = new finfo(FILEINFO_MIME_TYPE);
                        $mime_type = $file_info->buffer(file_get_contents($evaluating_tmp));
                        if (strpos($mime_type,"pdf") !== false) {
                          if (!$checkContent) {
                            $dataContent = file_get_contents($evaluating_tmp);
                          } else {
                            try {
                              $dataContent = @new PdfToText($evaluating_tmp);
                              $dataContent =  $dataContent->Text;
                              $dataContent = preg_replace("/[^a-zA-Z0-9]/", '', $dataContent);
                            } catch (Exception $e) {
                              $dataContent = "";
                            }
                          }
                          $result[] = hash($algorithm,$dataContent);
                          $keep = false;
                        } else {
                          $result[] = hash($algorithm,file_get_contents($evaluating_tmp));
                        }
                      } else {
                        if ($checkContent) {
                          $esito = $this->pdfUtility("Valida", $evaluating_tmp);
                          if (trim($esito)=="Verification successful") {
                            try {
                              $dataContent = @new PdfToText($evaluating_tmp);
                              $dataContent =  $dataContent->Text;
                              $dataContent = preg_replace("/[^a-zA-Z0-9]/", '', $dataContent);
                            } catch (Exception $e) {
                              $dataContent = "";
                            }
                            $result[] = hash($algorithm,$dataContent);
                          }
                        }
                        $keep = false;
                      }
                    }
                  }
                }
                $it = new RecursiveDirectoryIterator($zip_folder, RecursiveDirectoryIterator::SKIP_DOTS);
                $filesZip = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
                foreach($filesZip as $fileZip) {
                    if ($fileZip->isDir()){
                        rmdir($fileZip->getRealPath());
                    } else {
                        unlink($fileZip->getRealPath());
                    }
                }
                rmdir($zip_folder);
              }
            } else if (strpos($mime_type,"pdf") !== false) {
              if (!$checkContent) {
                $dataContent = file_get_contents($tmp_file);
              } else {
                try {
                  $dataContent = @new PdfToText($tmp_file);
                  $dataContent =  $dataContent->Text;
                  $dataContent = preg_replace("/[^a-zA-Z0-9]/", '', $dataContent);
                } catch (Exception $e) {
                  $dataContent = "";
                }
              }
              $result[] = hash($algorithm,$dataContent);
              $keep = false;
            }
          } else {
            if ($checkContent) {
              $esito = $this->pdfUtility("Valida", $tmp_file);
              if (trim($esito)=="Verification successful") {
                try {
                  $dataContent = @new PdfToText($tmp_file);
                  $dataContent =  $dataContent->Text;
                  $dataContent = preg_replace("/[^a-zA-Z0-9]/", '', $dataContent);
                } catch (Exception $e) {
                  $dataContent = "";
                }
                $result[] = hash($algorithm,$dataContent);
              }
            }
            $keep = false;
          }
        }
      }
      unlink($tmp_file);
    } else {
      if ($checkContent) {
        $esito = $this->pdfUtility("Valida", $this->filePath);
        if (trim($esito)=="Verification successful") {
          try {
            $dataContent = @new PdfToText($this->filePath);
            $dataContent = $dataContent->Text;
            $dataContent = preg_replace("/[^a-zA-Z0-9]/", '', $dataContent);
          } catch (Exception $e) {
            $dataContent = "";
          }
          $result[] = hash($algorithm,$dataContent);
        }
      }
    }
    if (!isset($result)) { $result = array(''); }
    else if (isset($result) && !is_array($result)) { $result = array($result); }
    if (in_array($hash,$result)!==false) $found = true;

    return $found;
  }

  function verifySignedContent() {
    $found = false;

    $tmp_file = $this->tmp_folder . "/" . session_id() . ".tmp";
    $comando = $this->bash_folder.'/estrai.bash \'' . $this->filePath . '\' \'' . $tmp_file .'\'';
    $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
    if (trim($esito_apertura)=="Verification successful") {
      $found = true;
    } else {
      $file_info = new finfo(FILEINFO_MIME_TYPE);
      $mime_type = $file_info->buffer(file_get_contents($this->filePath));
      if (strpos($mime_type,"pdf") !== false) {
        $esito = $this->pdfUtility("Valida", $this->filePath);
        if (trim($esito)=="Verification successful") {
          $found = true;
        }
      } else if ((strpos($mime_type,"zip") !== false)||(strpos($mime_type,"rar") !== false)||(strpos($mime_type,"7z") !== false)) {
        $result = array();
        $zip_folder = $this->tmp_folder .'/'. session_id() . '/zip/';
        $unzipped = false;
        if (strpos($mime_type,"zip")) {
          $zip = new ZipArchive;
          $res_zip = $zip->open($this->filePath);
          if ($res_zip === true) {
            $unzipped = true;
            $zip->extractTo($zip_folder);
            $zip->close();
          }
        } else if (strpos($mime_type,"rar")) {
          $res_zip = RarArchive::open($this->filePath);
          $entries = $res_zip->getEntries();
          if ($entries !== false) {
            $unzipped = true;
            foreach ($entries as $e) {
              $e->extract($zip_folder);
            }
            $res_zip->close();
          }
        } else if (strpos($mime_type,"7z")) {
          $comando = $this->bash_folder.'/estrai_7z.bash \''.$this->filePath.'\' \''.$zip_folder.'\'';
          $esito_extract = shell_exec("sh " . $comando . " 2>&1");
          if (strpos($esito_extract,"Everything is Ok") !== false) $unzipped = true;
        }
        if ($unzipped) {
          $it = new RecursiveDirectoryIterator($zip_folder, RecursiveDirectoryIterator::SKIP_DOTS);
          $filesZip = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
          foreach($filesZip as $fileZip) {
            if (!$fileZip->isDir()) {
              $evaluating_tmp = $zip_folder . ".evaluating.tmp";
              rename($fileZip->getRealPath(),$evaluating_tmp);
              $file_info = new finfo(FILEINFO_MIME_TYPE);
              $mime_type = $file_info->buffer(file_get_contents($evaluating_tmp));
              $comando = $this->bash_folder.'/estrai.bash \''.$evaluating_tmp.'\' \''.$evaluating_tmp.'\'';
              $esito_apertura = shell_exec("sh " . $comando . " 2>&1");
              if (trim($esito_apertura)=="Verification successful") {
                $found = true;
              } else {
                $esito = $this->pdfUtility("Valida", $evaluating_tmp);
                if (trim($esito)=="Verification successful") {
                  $found = true;
                }
              }
            }
          }
          $it = new RecursiveDirectoryIterator($zip_folder, RecursiveDirectoryIterator::SKIP_DOTS);
          $filesZip = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
          foreach($filesZip as $fileZip) {
              if ($fileZip->isDir()){
                  rmdir($fileZip->getRealPath());
              } else {
                  unlink($fileZip->getRealPath());
              }
          }
          rmdir($zip_folder);
        }
      }
    }
    return $found;
  }

  static function compHash($file,$algorithm) {
    $hash = hash($algorithm,$file);
    return $hash;
  }

  function getHash($algorithm) {
    return $this->compHash($this->fileContent,$algorithm);
  }

  function encryptAndSave($salt,$path,$filename,$easy_salt = false) {
    if (strlen($salt) >= 12 || $easy_salt) {
      $enc_busta = openssl_encrypt($this->fileContent,$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
      if ($enc_busta !== FALSE) {
        if (!is_dir($path)) mkdir($path,0770,true);
        file_put_contents($path.$filename,$enc_busta);
        if (file_exists($path.$filename)) {
          return true;
        } else {
          return "Errore nel salvataggio";
        }
      } else {
        return "Errore Nella Criptazione";
      }
    } else {
      return "Chiave personalizzata Errata";
    }
  }

  function Save($path,$filename) {
    if (!is_dir($path)) mkdir($path,0770,true);
    file_put_contents($path.$filename,$enc_busta);
    if (file_exists($path.$filename)) {
      return true;
    } else {
      return "Errore nel salvataggio";
    }
  }

  static function publicEncrypt($data,$public) {
    $public = openssl_pkey_get_public(trim($public));
    if (openssl_public_encrypt($data,$encrypted,$public)) {
      return $encrypted;
    } else {
      return false;
    }
  }
}
?>
