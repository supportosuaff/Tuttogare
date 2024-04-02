<?php

class ConservazioneDefault {

  public $profiloArchivistico;
  public $profiloDocumento;
  public $proprietaDocumentoWS;
  public $tipo_documento;
  public $chiave;
  public $files;
  public $allegati;
  public $type;
  public $short_type;
  public $tipologia;
  public $codice_riferimento;
  public $error;

  protected $pdo;
  protected $client;
  protected $azienda;
  protected $config;
  public $documento;

  public function __construct() {
    global $pdo;
    global $root;
    global $config;

    $this->pdo = $pdo;
    $this->root = $root;
    $this->config = $config;
    $this->profiloArchivistico = array();
    $this->profiloDocumento = array();
    $this->chiave = array();
    $this->files = array();
  }

  public function creaPacchetto() {
    $documento = [];

    if(! empty($this->chiave)) {
      $documento['chiave']['proprieta'] = null;
      foreach ($this->chiave as $key => $value) {
        $documento['chiave']['proprieta'][] = ['chiave' => $key, 'valore' => $value];
      }
    }

    if(! empty($this->allegati)) {
      $documento['allegati'] = [];
      foreach ($this->allegati as $index => $file) {
        $allegato = [];
        $allegato['proprieta'][] = ['chiave' => 'tipo_allegato', 'valore' => 'GENERICO'];
        $allegato['proprieta'][] = ['chiave' => 'chiave_allegato', 'valore' => "{$file['proprieta']['identificativo_di_riconciliazione']}/{$file['proprieta']['id_cliente']}"];
        $allegato['proprieta'][] = ['chiave' => 'forza_conservazione', 'valore' => 'true'];
        $allegato['proprieta'][] = ['chiave' => 'forza_accettazione', 'valore' => 'true'];

        $allegato["versioneOriginale"]["proprieta"][] = ['chiave' => 'formato_versione', 'valore' => $file['proprieta']['formato_file']];
        $allegato["versioneOriginale"]["parti"]["proprieta"][] = ['chiave' => 'formato_parte', 'valore' => $file['proprieta']['formato_file']];
        $allegato["versioneOriginale"]["parti"]["proprieta"][] = ['chiave' => 'numero_parte', 'valore' => 0];

        $allegato["versioneOriginale"]["parti"]["files"]["dati"] = $file["base64"];

        $allegato["versioneOriginale"]["parti"]["files"]["proprieta"][] = ['chiave' => 'ordinePresentazione', 'valore' => 0];
        $allegato["versioneOriginale"]["parti"]["files"]["proprieta"][] = ['chiave' => 'formato_file', 'valore' => $file['proprieta']['formato_file']];
        $allegato["versioneOriginale"]["parti"]["files"]["proprieta"][] = ['chiave' => 'nomeFile', 'valore' => $file['proprieta']['nome_file']];
        $allegato["versioneOriginale"]["parti"]["files"]["proprieta"][] = ['chiave' => 'id_cliente', 'valore' => $file['proprieta']['id_cliente']];

        $documento['allegati'][] = $allegato;
      }
    }

    $documento['proprieta'] = null;
    $documento['proprieta'][] = ['chiave' => 'tipoDocumento', 'valore' => $this->tipo_documento];
    $documento['proprieta'][] = ['chiave' => 'forza_conservazione', 'valore' => 'true'];
    $documento['proprieta'][] = ['chiave' => 'forza_accettazione', 'valore' => 'true'];


    if(! empty($this->profiloArchivistico)) {
      $documento['profiloArchivistico']['proprieta'] = null;
      foreach ($this->profiloArchivistico as $key => $value) {
        $documento['profiloArchivistico']['proprieta'][] = ['chiave' => $key, 'valore' => $value];
      }
    }

    if (! empty($this->files)) {

      $file = $this->files[0];
      $documento["versioneOriginale"]["proprieta"][] = ['chiave' => 'formato_versione', 'valore' => $file['proprieta']['formato_file']];
      $documento['versioneOriginale']['parti']['proprieta'][] = ['chiave' => 'formato_parte', 'valore' => $file['proprieta']['formato_file']];
      $documento['versioneOriginale']['parti']['proprieta'][] = ['chiave' => 'numero_parte', 'valore' => 0];
      $documento['versioneOriginale']['parti']['files']['dati'] = $file["base64"];
      $documento['versioneOriginale']['parti']['files']['proprieta'][] = ['chiave' => 'ordinePresentazione', 'valore' => 0];
      $documento['versioneOriginale']['parti']['files']['proprieta'][] = ['chiave' => 'formato_file', 'valore' => $file['proprieta']['formato_file']];
      $documento['versioneOriginale']['parti']['files']['proprieta'][] = ['chiave' => 'nomeFile', 'valore' => $file['proprieta']['nome_file']];
      $documento['versioneOriginale']['parti']['files']['proprieta'][] = ['chiave' => 'idCliente', 'valore' => $file['proprieta']['id_cliente']];
      if(! empty($file["proprieta"])) {
        foreach ($file["proprieta"] as $key => $value) {
          if(empty($value) && ! is_numeric($value)) continue;
          if(in_array($key, array('ordine_presentazione', 'nome_file', 'id_cliente', 'formato_file'))) continue;
          $documento['versioneOriginale']['parti']['files']['proprieta'][] = ['chiave' => $key, 'valore' => $value];
        }
      }
    }

    $this->documento = $documento;
  }

  protected function getTextBetweenTags($string, $tagname) {
    $string = str_replace(['\t', '\n', '\r'], '', $string);
    $string = preg_replace('~[[:cntrl:]]~', '', $string);
    $pattern = "/<{$tagname}>(.*?)<\/{$tagname}>/s";
    preg_match($pattern, $string, $matches);
    return ! empty($matches[1]) ? $matches[1] : null;
  }

  protected function getCredential() {
    $proprieta = [];
    if(! empty($_SESSION["ente"])) {
      $proprieta["item"][] = ['chiave' => 'FAMILY', 'valore' => ! empty($_SESSION["ente"]["family_conservazione"]) ? $_SESSION["ente"]["family_conservazione"] : null];
      $proprieta["item"][] = ['chiave' => 'ORGANIZZAZIONE', 'valore' => ! empty($_SESSION["ente"]["organizzazione_conservazione"]) ? $_SESSION["ente"]["organizzazione_conservazione"] : null];
      $proprieta["item"][] = ['chiave' => 'STRUTTURA', 'valore' => ! empty($_SESSION["ente"]["struttura_conservazione"]) ? $_SESSION["ente"]["struttura_conservazione"] : null];
      $proprieta["item"][] = ['chiave' => 'USER_ID', 'valore' => ! empty($_SESSION["ente"]["versatore_conservazione"]) ? $_SESSION["ente"]["versatore_conservazione"] : null];
    }
    return $proprieta;
  }

  protected function getEndPoint() {
    if (DEVELOP_ENV) return "";
    return "";
  }

  public function send() {

    $proprieta = $this->getCredential();

    $xml = [];
    $xml['soapenv:Envelope']['@xmlns:soapenv'] = 'http://schemas.xmlsoap.org/soap/envelope/';
    $xml['soapenv:Envelope']['@xmlns:xsd'] = 'http://www.w3.org/2001/XMLSchema';
    $xml['soapenv:Envelope']['@xmlns:xsi'] = 'http://www.w3.org/2001/XMLSchema-instance';
    $xml['soapenv:Envelope']['@xmlns:ant']= "http://anticipati.versamenti.service.unirepo.unimaticaspa.it/";
    // $xml['soapenv:Envelope']['soapenv:Body']["ant:ConsegnaAnticipata"]["@xmlns"] = "http://anticipati.versamenti.service.unirepo.unimaticaspa.it/";
    // $xml['soapenv:Envelope']['soapenv:Body']["ant:ConsegnaAnticipata"]["Documento"] = [];
    $xml['soapenv:Envelope']['soapenv:Body']["ant:ConsegnaAnticipata"]["Documento"]['$'] = $this->documento;
    // $xml['soapenv:Envelope']['soapenv:Body']["ant:ConsegnaAnticipata"]["Proprieta"] = [];
    $xml['soapenv:Envelope']['soapenv:Body']["ant:ConsegnaAnticipata"]["Proprieta"]['$'] = $proprieta;
    $xml = array2XML($xml, null, false, true, true);


    /*************
     * XML DEBUG *
     *************/
    // header('Content-Type: text/xml');
    // echo $xml;
    // die();

    $header = [
      'Content-type: text/xml;charset="utf-8"',
      'Accept: text/xml',
      'Cache-Control: no-cache',
      'Pragma: no-cache',
      'SOAPAction: "run"',
      'Content-length: ' . strlen($xml),
    ];

    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $this->getEndPoint());
    curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($request, CURLOPT_TIMEOUT,        10);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($request, CURLOPT_POST,           true );
    curl_setopt($request, CURLOPT_POSTFIELDS,     $xml);
    curl_setopt($request, CURLOPT_HTTPHEADER,     $header);
    $request = addCurlAuth($request);
    $response = curl_exec($request);

    $this->error = true;

    if($response === false) {
      $response = base64_encode(curl_error($request));
    } else {
      $xml_response = $this->getTextBetweenTags($response, 'return');
      $base64_response = base64_encode($response);
      if(! empty($xml_response)) {
        $xml_response = simplexml_load_string("<response>{$xml_response}</response>");
        $response = json_decode(json_encode((array) $xml_response), 1);

        if ($response["esito"] === '0000') {
          $this->error = false;
        } else {
          if ($response["esito"] === '0001' && ! empty($response["anomalie"]["codice"])) {
            if($response["anomalie"]["codice"] === '1011' || $response["anomalie"]["codice"] === '1004') {
              $this->error = false;
            }
          }
        }
      }
      // $this->readableSOAP($xml_response);
      // $this->readableSOAP($response);
    }
    curl_close($request);

    return array(
      'response' => $base64_response,
      'request'  => $xml,
      'xml'      => $xml_response,
      'result'   => $response,
    );
  }

  protected function readableSOAP($value) {
    echo '<h1>Show Readable:</h1>';
    echo '<textarea style="width: 100%; min-height:500px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">';
    var_dump($value);
    echo '</textarea>';
  }
}

?>
