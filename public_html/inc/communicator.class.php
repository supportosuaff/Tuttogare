<?
class Communicator {

  public $attachment;
  public $codice_relazione;
  public $destinatari;
  public $comunicazione;
  public $codice_pec;
  public $oggetto;
  public $corpo;
  public $cod_allegati;
  public $coda;
  public $codice_gara;
  public $codice_lotto;
  public $sezione;
  public $intestazione;
  public $elaborazione_coda;
  public $root;
  public $config;
  public $configurazione;
  public $address;
  public $identifiers;
  public $utenti;
  public $type;
  public $codice_coda;
  public $comunicazione_tecnica;
  public $utenti_pec;
  public $sendOpen;

  private $class;

  function __construct()
	{
    global $root, $config;
    $this->root = $root;
    $this->config = $config;
    $this->class = null;
    $this->type = null;

    $this->configurazione = array();
    $this->codice_pec = -1;
    $this->coda = FALSE;
    $this->elaborazione_coda = FALSE;
    $this->comunicazione = FALSE;
    $this->address = array();
    $this->codice_relazione = 0;
    $this->utenti = array();
    $this->utenti_pec = array();
    $this->codice_lotto = 0;
    $this->intestazione = TRUE;
    $this->cod_allegati = "";
    $this->destinatari = "";
    $this->attachment = array();
    $this->comunicazione_tecnica = true;
    $this->sendOpen = false;
	}

  public function send()
  {
    require_once "{$this->root}/inc/communicator.default.class.php";
    if(!empty($_SESSION["ente"]["codice"]) && ($this->codice_pec >= 0 || $_SESSION["ente"]["force_bridge"] === "S") && file_exists("{$this->root}/inc/integrazioni/{$_SESSION["ente"]["codice"]}/communicator.bridge.class.php")) {
      require_once "{$this->root}/inc/integrazioni/{$_SESSION["ente"]["codice"]}/communicator.bridge.class.php";
      $class_name = "Communicator".$_SESSION["ente"]["codice"];
      $this->class = new $class_name($this);
    } else {
      $this->class = new CommunicatorDefault($this);
    }
    return $this->class->send();
  }
}
?>
