<?
  @session_start();

  function fatal_handler() {
    $error = error_get_last();
	  if( $error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_USER_ERROR)) {
      $_SESSION["need_to_close_previus_simog_connection"] = TRUE;
    }
  }
  register_shutdown_function('fatal_handler');
  /**
   * Exception Handling
   */
  class SimogException extends Exception
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

  class LoaderSoap extends SoapClient {
    function fixRequest($request) {
      if (strpos($request,"<documento>") !== false) {
        $search_from = "<documento>";
        $search_to = "</documento>";
        $allegati = [];
        if (strpos($request,$search_from) !== false) {
          while(strpos($request,$search_from) !== false) {
            $start = strpos($request,$search_from);
            $end = strpos($request,$search_to) + strlen($search_to);
            $allegato = substr($request,$start,$end - $start);
            $request = str_replace($allegato,"",$request);
            $allegati[] = $allegato;           
          }
        }
      }
      $request = preg_replace('/(\S+)=/',"ns1:$1=",$request);
      $request = str_replace('ns1:xmlns','xmlns',$request);
      $request = str_replace('<?xml ns1:version="1.0" ns1:encoding="UTF-8"?>','<?xml version="1.0" encoding="UTF-8"?>',$request);
      if (!empty($allegati)) {
        $tag = "<allegato>";
        $offset = 0;
        foreach($allegati AS $allegato) {
          $pos = $offset = strpos($request,$tag,$offset) + strlen($tag);
          $request = substr_replace($request,$allegato,$pos,0);
        }
      }
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
  /**
   * Simog Interface
   */
  if(! function_exists('array2XML')) include_once $root. '/inc/funzioni.php';
  if(! function_exists('xmlToArray')) include_once $root.'/inc/xml2json.php';
  class Simog
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
    private $smartEndPoint;
    private $wspddEndPoint;
    private $allowedMethod;
    private $smartMethod;

    private $log;

    private $versioneSchede;
    private $client;
    private $selectedIndex;
    private $simog_folder;

    function __construct() {
      global $root;
      $this->debug = FALSE;
      $this->views = "{$root}/anac/simog/views";
      $this->simog_folder = substr($root,0,-11) . "simog";
      $this->selectedIndex = isset($_SESSION["simog"]["index"]) ? $_SESSION["simog"]["index"] : null;
      $this->log = array();
      $this->log["ip"] = getenv("REMOTE_ADDR");
      if(isset($_POST["index_collaborazione"])) {
        $this->selectedIndex = $_SESSION["simog"]["index"] = $_POST["index_collaborazione"];
      }
      // $this->simogWS = "{$this->simog_folder}/SimogWSPDD.wsdl";
      // $this->simogCert = "{$this->simog_folder}/simog.client.includingkey.pem";

      if(!isset($_SESSION) || empty($_SESSION)) throw new Exception("Errore di inizializzazione. Impossibile recuperare i dati della sessione.");
      $this->session = $_SESSION;
      $this->risposta = FALSE;
      $this->versioneSchede = "3.03.5.8";
      $this->allowedMethod = array(
        "inviaRequisiti",
        "inserisciGara",
        "modificaGara",
        "cancellaGara",
        "inserisciLotto",
        "modificaLotto",
        "cancellaLotto",
        "consultaGara",
        "consultaNumeroGara",
        "pubblica",
        "presaInCarico",
        "integraCuUP",
        "integraDL133",
        "loaderAppalto",
        "comunicaSingola",
        "annullaComunicazione",
        "consultaComunicazione"
      );
      $this->smartMethod = array(
        "comunicaSingola",
        "annullaComunicazione",
        "consultaComunicazione"
      );
      $this->setEndPoints();
      $this->client = new LoaderSoap("{$this->simog_folder}/SimogWSPDD.wsdl", array('location' => $this->wsEndPoint, 'local_cert' => "{$this->simog_folder}/simog.client.includingkey.pem", 'trace' => 1));
      // $this->client->charencoding = false;
      if(isset($_SESSION["need_to_close_previus_simog_connection"]) && $_SESSION["need_to_close_previus_simog_connection"]) {
        if(!empty($_SESSION["ticket_simog"])) $this->ticket = $_SESSION["ticket_simog"];
        $_SESSION["need_to_close_previus_simog_connection"] = FALSE;
      }
    }

    private function setEndPoints() {
      $this->wsEndPoint = "https://wstest.anticorruzione.it/COLL/SimogWSPDD/services/SimogWSPDD";
      $this->wspddEndPoint = "https://wstest.anticorruzione.it/COLL/SimogWSPDD/services/LoaderAppaltoWS";
      $this->smartEndPoint = "https://wstest.anticorruzione.it/COLL/TracciabilitaWS/ServizioGestioneComunicazioni";
      if(!$this->session["developEnviroment"]) {
        $this->wsEndPoint = "https://ws.anticorruzione.it/SimogWSPDD/services/SimogWSPDD";
        $this->wspddEndPoint = "https://ws.anticorruzione.it/SimogWSPDD/services/LoaderAppaltoWS";
        $this->smartEndPoint = "https://ws.anticorruzione.it/TracciabilitaWS/ServizioGestioneComunicazioni";
      }
    }

    private function login() {
      if(empty($this->username) || empty($this->password)) throw new SimogException("Errore login. E' necessario effettuare l'accesso al SIMOG.", 3, null, "{$this->views}/login.php");
      $method = "login";
      $arguments = array('login' => $this->username, 'password' => $this->password);
      $response = $this->request($method, $arguments);
      $loginReturn = json_decode(json_encode($response),true);
      $loginReturn = $loginReturn["return"];
      $this->log["operazione"] = "login";
      $this->log["busta_inviata"] = "";
      $this->log["busta_risposta"] = $this->client->__getLastResponse();

      $salva = new salva();
      $salva->debug = false;
      $salva->codop = $_SESSION["codice_utente"];
      $salva->nome_tabella = "b_log_simog";
      $salva->operazione = "INSERT";
      $salva->oggetto = $this->log;
      $codice_log = $salva->save();

      if (!empty($loginReturn["error"])) {
        throw new SimogException($loginReturn["error"]);
      } elseif (!empty($loginReturn["coll"]) && $loginReturn["success"] && !empty($loginReturn["ticket"])) {
        $this->coll = $loginReturn["coll"]["collaborazioni"];
        $simog["username"] = $this->username;
        $simog["password"] = $this->password;
        $_SESSION["ticket_simog"] = $simog["ticket"] = $this->ticket = $loginReturn["ticket"];
        $simog["collaborazioni"] = $this->coll;
        if(array_key_exists("azienda_codiceFiscale", $this->coll)) {
          $simog["collaborazioni"] = $this->coll = array("0" => $this->coll);
          $this->selectedIndex = $simog["index"] = 0;
        }
        if(isset($_SESSION["simog"]["index"])) $simog["index"] = $_SESSION["simog"]["index"];
        $_SESSION["simog"] = $simog;
      } else {
        throw new SimogException("Errore di login. Non è stato possibile effettuare l'accesso al SIMOG.", 3);
      }
    }

    private function logout() {
      if(empty($this->ticket)) throw new Exception("Errore logout. Nessuna connessione trovata.");
      $method = "chiudiSessione";
      $arguments = array('ticket' => $this->ticket);
      $response = $this->request($method, $arguments);
      if(!$response->return->success) {
        // TODO: Se l'errore è causato dalla sessione non valida allora non lanciare l'eccezione  SIMOGWS_WSSMANAGER_NULL_15
        throw new Exception($response->return->error);
      }
      $_SESSION["simog"]["ticket"] = $this->ticket = null;
    }

    /**
     * Invia la richiesta all'endpoint del SIMOG
     * @param  string $endEpoint [url del servizio]
     * @param  array  $post
     * @return $response
     *
     */
    private function request($method, $arguments) {
      if($this->debug) {echo '<h1>Richiesta '.$method.'</h1><pre>'; print_r($arguments); echo '</pre>';}
      $response = $this->client->{$method}($arguments);
      if($this->debug) {echo '<h1>Busta richiesta '.$method.'</h1><textarea style="width: 100%; min-height:100px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">'; print_r($this->client->__getLastRequest()); echo '</textarea>';}
      if($this->debug) {echo '<h1>Vardump Response '.$method.'</h1><textarea style="width: 100%; min-height:100px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">'; var_dump($response); echo '</textarea>';}
      $this->log["busta_inviata"] = $this->client->__getLastRequest();
      return $response;
    }

    private function smartRequest($method, $arguments) {
      if($this->debug) {echo '<h1>Richiesta '.$method.'</h1><pre>'; print_r($arguments); echo '</pre>';}
      $smartClient = new SoapClient("{$this->simog_folder}/SmartCIG.wsdl", array('location' => $this->smartEndPoint, 'local_cert' => "{$this->simog_folder}/simog.client.includingkey.pem", 'trace' => 1));
      $response = $smartClient->{$method}($arguments);
      if($this->debug) {echo '<h1>Busta richiesta '.$method.'</h1><textarea style="width: 100%; min-height:100px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">'; print_r($smartClient->__getLastRequest()); echo '</textarea>';}
      if($this->debug) {echo '<h1>Vardump Response '.$method.'</h1><textarea style="width: 100%; min-height:100px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">'; var_dump($response); echo '</textarea>';}
      $this->log["busta_inviata"] = $smartClient->__getLastRequest();
      return array("response"=>$response,"envelope"=>$smartClient->__getLastResponse());
    }

    private function LoaderRequest($method, $arguments) {
      if($this->debug) {echo '<h1>Richiesta '.$method.'</h1><pre>'; print_r($arguments); echo '</pre>';}
      $loaderClient = new LoaderSoap("{$this->simog_folder}/LoaderAppaltoWS.wsdl", array('location' => $this->wspddEndPoint, 'local_cert' => "{$this->simog_folder}/simog.client.includingkey.pem", 'trace' => 1));
      $response = $loaderClient->{$method}($arguments);
      if($this->debug) {echo '<h1>Busta richiesta '.$method.'</h1><textarea style="width: 100%; min-height:100px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">'; print_r($loaderClient->__getLastRequest()); echo '</textarea>';}
      if($this->debug) {echo '<h1>Vardump Response '.$method.'</h1><textarea style="width: 100%; min-height:100px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">';
      var_dump($response); echo '</textarea>';}
      $this->log["busta_inviata"] = $loaderClient->__getLastRequest();
      return array("response"=>$response,"envelope"=>$loaderClient->__getLastResponse());
    }

    /**
     * Vrifica che la connessione al Simog sia attiva o ne stabilisce una
     * @return void
     *
     */
    private function checkConnection() {
      if(empty($this->username) && empty($this->password) && !empty($_POST["username"]) && !empty($_POST["password"])) {
        $this->username = $_POST["username"];
        $this->password = $_POST["password"];
      }
      if(! empty($_SESSION["simog"]["ticket"])) {
        $this->ticket = $_SESSION["simog"]["ticket"];
        $this->logout();
      }
      if(empty($this->ticket)) $this->login();
    }

    /**
     * Aggiunge le chiavi ricevute dal login alla richiesta
     * @param  Array $post [description]
     * @return Array       [description]
     */
    private function checkPost($arguments) {
      $post = $arguments[0];
      $post['ticket'] = $this->ticket;
      if(!isset($post["indexCollaborazione"])) {
        if(!is_null($this->selectedIndex)) {
          $post['indexCollaborazione'] = (string) $this->selectedIndex;
        } else {
          throw new SimogException("Errore nella richiesta. Nessuna collaborazione selezionata.", 3, null, "{$this->views}/collaborazioni.php");
        }
      }
      if(!empty($arguments[1])) {
        $post = $this->setColl($post);
        // $post = $this->makeXML($post, $arguments[1]);
      }
      return $post;
    }

    private function smartCheckPost($arguments) {
      $post = array();
      $post['user']['ticket'] = $this->ticket;
      if(!isset($post["indexCollaborazione"])) {
        if(!is_null($this->selectedIndex)) {
          $post['user']['index'] = (string) $this->selectedIndex;
        } else {
          throw new SimogException("Errore nella richiesta. Nessuna collaborazione selezionata.", 3, null, "{$this->views}/collaborazioni.php");
        }
      }
      if (!empty($arguments[0]["comunicazione"])) $post["comunicazione"] = $arguments[0]["comunicazione"];
      if (!empty($arguments[0]["cig"])) $post["cig"] = $arguments[0]["cig"];
      return $post;
    }

    /**
     * Replace HTMLENTITIES nella richiesta
     * @param  [type] &$array [description]
     * @return [type]         [description]
     */
    private function replaceEntities(&$array) {
      if(is_array($array) && !empty($array)) {
        foreach ($array as $key => &$value) {
          if(!is_array($value)) {
            $array[$key] = str_replace('"', "'", $value);
          } else {
            $array[$key] = $this->replaceEntities($array[$key]);
          }
        }
      }
      return $array;
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
          $value = $this->replaceEntities($array[$key]);
          $value = '<?xml version="1.0" encoding="UTF-8"?>'.array2XML($array[$key]);
        }
        if(is_array($value)) $value = $this->makeXML($value, $search_key);
      }
      return $array;
    }

    /**
     * Imposta le informazioni della stazione appaltante nell'array
     * @param Array $array
     * @param String $search_key
     * @return Array
     */
    private function setColl($array) {
      $collaborazione = $this->coll[$this->selectedIndex];
      $_SESSION["simog"]["index"] = $collaborazione["index"];
      array_walk_recursive($array, function(&$value, $key) use ($collaborazione) {
        if(strpos($key, "ID_STAZIONE_APPALTANTE") !== FALSE) $value = $collaborazione["ufficio_id"];
        if(strpos($key, "DENOM_STAZIONE_APPALTANTE") !== FALSE) $value = $collaborazione["ufficio_denominazione"];
        if(strpos($key, "CF_AMMINISTRAZIONE") !== FALSE) $value = $collaborazione["azienda_codiceFiscale"];
        if(strpos($key, "DENOM_AMMINISTRAZIONE") !== FALSE) $value = $collaborazione["azienda_denominazione"];
        if(strpos($key, "CF_UTENTE") !== FALSE) $value = $this->username;
      });
      return $array;
    }

    /**
     * Recupera le informazioni sulle collaborazioni dell'utente
     * @return array [Lista delle collaborazioni]
     *
     */
    public function getCollaborazioni()
    {
      $this->checkConnection();
      return $this->coll;
    }

    /**
     * Recupera il ticket della connessione
     * @return String
     */
    public function getTicket() {
      return $this->ticket;
    }

    /**
     * Invoca un metodo privato tra quelli consentiti
     * @param  string $method
     * @param  mixed  $arguments [il primo parametro deve contenere il post]
     * @return void
     *
     */
    public function __call($method, $arguments) {
      if(($method == "inserisciGara" || $method == "comunicaSingola") && !isset($_POST["index_collaborazione"])) $_SESSION["simog"]["index"] = NULL;
      if(method_exists($this, $method) && is_callable($method)) {
        $this->method($arguments);
      } else if(in_array($method, $this->allowedMethod) && !empty($arguments) && is_array($arguments)) {
        $this->checkConnection();
        $smart_cig = false;
        if(in_array($method, $this->smartMethod)) {
          $smart_cig = true;
          $arguments = $this->smartCheckPost($arguments);
          $risposta = $this->smartRequest($method, $arguments);
          $last_response = $risposta["envelope"];
          $risposta = $risposta["response"];
        } else {
          $arguments = $this->checkPost($arguments);
          if ($method != "loaderAppalto") {
            $risposta = $this->request($method, $arguments);
            $last_response = $this->client->__getLastResponse();
          } else {
            $risposta = $this->LoaderRequest($method, $arguments);
            $last_response = $risposta["envelope"];
            $risposta = $risposta["response"];
          }
        }
        if($this->debug) {echo '<h1>Busta Risposta '.$method.' - __LASTRESPONSE</h1><textarea style="width: 100%; min-height:100px; background-color:#000; color:#FFF; font-size:1em; resize:none; border: none" disabled="disabled">'; print_r($last_response); echo '</textarea>';}
        if($this->debug) {echo '<h1>Busta Risposta '.$method.'</h1><pre>'; print_r($risposta); echo '</pre>';}
        $envelope = simplexml_load_string($last_response);
        if(FALSE === $envelope) throw new SimogException("Errore bloccante <b>0x12</b>. Un errore grave ha impedito il corretto funzionamento del sistema.<br>Se il problema persiste rivolgersi all&#39;HelpDesk Tecnico.");
        $busta = xmlToArray($envelope);
        if($this->debug) {echo '<h1>Risposta '.$method.'</h1><pre>'; var_dump($busta); echo '</pre>';}

        $this->log["operazione"] = $method;
        $this->log["busta_risposta"] = $last_response;

        $salva = new salva();
        $salva->debug = false;
        $salva->codop = $_SESSION["codice_utente"];
        $salva->nome_tabella = "b_log_simog";
        $salva->operazione = "INSERT";
        $salva->oggetto = $this->log;
        $codice_log = $salva->save();

        return $risposta;
      } else {
        throw new SimogException("Operazione non consentita. Errore di esecuzione. Verificare il metodo e/o l'oggetto della richiesta.");
      }
    }

    /**
     * Chiude la connessione al server
     *
     */
    public function __destruct() {
      if(!empty($this->ticket)) $this->logout();
    }

  }
?>
