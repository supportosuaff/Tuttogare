<?
  @session_start();

  /**
   * Exception Handling
   */
  class CupException extends Exception
  {
    private $view;

    public function __construct($message = null, $code = 0, Exception $previous = null, $file = null)
    {
      $this->view = file_exists($file) ? $file : null;
      parent::__construct($message, $code, $previous);
    }
    /**
     * Restituisce una stringa HTML se esiste
     * @return String
     */
    final public function getHTML() {
      if(!empty($this->view)) {
        include_once $this->view;
      }
    }
  }

  class CUPSoap extends SoapClient {
    function fixRequest($request) {
      /* $request = preg_replace('/(\S+)=/',"ns1:$1=",$request);
      $request = str_replace('ns1:xmlns','xmlns',$request);
      $request = str_replace('<?xml ns1:version="1.0" ns1:encoding="UTF-8"?>','<?xml version="1.0" encoding="UTF-8"?>',$request); */
      return $request;
    }
    function __getLastRequest() {
      $request = parent::__getLastRequest();
      $request = $this->fixRequest($request);
      return $request;
    }
    function __doRequest($request, $location, $action, $version,$one_way = NULL) {
      $request = $this->fixRequest($request);
      // parent call
      return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
  }

  class SOAPStruct {
    function SOAPStruct($titoloRichiesta, $richiesta)
    {
      $this->TitoloRichiesta = $titoloRichiesta;
      $this->richiesta = $richiesta;
    }
  }
  /**
   * CUPWS Interface
   */
  if(! function_exists('array2XML')) include_once $root. '/inc/funzioni.php';
  if(! function_exists('xmlToArray')) include_once $root.'/inc/xml2json.php';
  class CUPWS
  {
    public $error;
    public $errorMessage;
    public $username;
    public $password;
    public $risposta;
    public $debug;

    private $coll;
    private $views;
    private $ticket;
    private $session;
    private $wsEndPoint;
    private $allowedMethod;

    private $log;

    private $client;
    private $bdapFolder;

    function __construct() {
      global $root;
      $this->debug = FALSE;
      $this->views = "{$root}/programmazione/cup/views";
      $this->bdapFolder = substr($root,0,-11) . "bdap";
      $this->log = array();
      $this->log["ip"] = getenv("REMOTE_ADDR");
      if(!isset($_SESSION) || empty($_SESSION)) throw new Exception("Errore di inizializzazione. Impossibile recuperare i dati della sessione.");
      $this->session = $_SESSION;
      $this->risposta = FALSE;

      $this->allowedMethod = array(
        "RichiestaRispostaSincrona_RichiestaGenerazioneCUP",
        "RichiestaRispostaSincrona_RichiestaChiusuraRevocaCUP"
      );

      $this->setEndPoints();
      if (file_exists("{$this->bdapFolder}/WSDL_WS_CUP.wsdl") && !empty($this->wsEndPoint)) {
        $this->client = new CUPSoap("{$this->bdapFolder}/WSDL_WS_CUP.wsdl", array('location' => $this->wsEndPoint, 'trace' => 1));
      } else throw new Exception("Errore di inizializzazione. Verificare la configurazione.");

    }

    private function setEndPoints() {
      $this->wsEndPoint = "http://cupwebwscoll.tesoro.it/CUPServicesCollaudo";
      if(!$this->session["developEnviroment"]) {
        $this->wsEndPoint = "";  // TODO: Inserire indirizzo di produzione
      }
    }

    /**
     * Invia la richiesta all'endpoint del CUP
     * @param  string $endEpoint [url del servizio]
     * @param  array  $post
     * @return $response
     *
     */
    private function request($method, $arguments) {
      if($this->debug) {echo '<h1>Richiesta '.$method.'</h1><pre>'; print_r($arguments); echo '</pre>';}
      try {
        $response = $this->client->{$method}($arguments);
      } catch (Exception $e) {

      }

      if($this->debug) {echo '<h1>Busta richiesta '.$method.'</h1><textarea style="width: 100%; min-height:100px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">'; print_r($this->client->__getLastRequest()); echo '</textarea>';}
      if($this->debug) {echo '<h1>Vardump Response '.$method.'</h1><textarea style="width: 100%; min-height:100px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">';
      var_dump($response); echo '</textarea>';}
      $this->log["busta_inviata"] = $this->client->__getLastRequest();
      return $response;
    }


    /**
     * Crea la busta xml da inviare
     * @param  Array $array
     * @param  String $search_key
     * @return Array
     */
    private function makeXML(&$array, $search_key) {
      foreach ($array as $key => &$value) {
        if($key == $search_key) {
          $value = '<?xml version="1.0" encoding="UTF-8"?>'.array2XML($array[$key]);
        }
        if(is_array($value)) $value = $this->makeXML($value, $search_key);
      }
      return $array;
    }

    /**
     * Invoca un metodo privato tra quelli consentiti
     * @param  string $method
     * @param  mixed  $arguments [il primo parametro deve contenere il post]
     * @return void
     *
     */
    public function __call($method, $arguments) {
      if(method_exists($this, $method) && is_callable($method)) {
        $this->method($arguments);
      } else if(in_array($method, $this->allowedMethod) && !empty($arguments) && is_array($arguments)) {
        $arguments = $arguments[0];
        $risposta = $this->request($method, $arguments);
        $last_response = $this->client->__getLastResponse();

        if($this->debug) {echo '<h1>Busta Risposta '.$method.' - __LASTRESPONSE</h1><textarea style="width: 100%; min-height:100px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">'; print_r($last_response); echo '</textarea>';}
        if($this->debug) {echo '<h1>Busta Risposta '.$method.'</h1><pre>'; print_r($risposta); echo '</pre>';}
        $envelope = simplexml_load_string($last_response);
        if(FALSE === $envelope) throw new CupException("Errore bloccante <b>0x12</b>. Un errore grave ha impedito il corretto funzionamento del sistema.<br>Se il problema persiste rivolgersi all&#39;HelpDesk Tecnico.");
        $busta = xmlToArray($envelope);
        if($this->debug) {echo '<h1>Risposta '.$method.'</h1><pre>'; var_dump($busta); echo '</pre>';}

        $this->log["operazione"] = $method;
        $this->log["busta_risposta"] = $last_response;

        $salva = new salva();
        $salva->debug = false;
        $salva->codop = $_SESSION["codice_utente"];
        $salva->nome_tabella = "b_log_cup";
        $salva->operazione = "INSERT";
        $salva->oggetto = $this->log;
        $codice_log = $salva->save();

        return $busta;

      } else {
        throw new CupException("Operazione non consentita. Errore di esecuzione. Verificare il metodo e/o l'oggetto della richiesta.");
      }
    }

  }
?>
