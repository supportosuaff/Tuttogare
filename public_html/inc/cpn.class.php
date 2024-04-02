<?php

class Cpn
{
  private $pdo;
  private $config;
  private $root;
  private $uri;
  private $clientKey;
  private $clientId;

  public function __construct($id = null)
  {
    global $pdo, $config, $root;

    $this->pdo = $pdo;
    $this->config = $config;
    $this->root = $root;

    if(!isset($_SESSION) || empty($_SESSION)) throw new Exception("Errore di inizializzazione. Impossibile recuperare i dati della sessione.");
    $this->clientKey = ! empty($_SESSION["ente"]["cpnClientKey"]) ? $_SESSION["ente"]["cpnClientKey"] : null;
    $this->clientId = ! empty($_SESSION["ente"]["cpnClientId"]) ? $_SESSION["ente"]["cpnClientId"] : null;

    if(! empty($id)) {
      $codice_ente = $pdo->go("SELECT codice_ente FROM b_contratti_pubblici_nazionali WHERE codice = :id", array(':id' => $id))->fetch(PDO::FETCH_COLUMN, 0);
      if(! empty($codice_ente)) {
        $ente = $pdo->go("SELECT cpnClientKey, cpnClientId FROM b_enti WHERE codice = :codice_ente", array(':codice_ente' => $codice_ente))->fetch(PDO::FETCH_ASSOC);
        if(! empty($ente["cpnClientKey"])) $this->clientKey = $ente["cpnClientKey"];
        if(! empty($ente["cpnClientId"])) $this->clientId = $ente["cpnClientId"];
      }
    }

    $this->setEndPoints();
    $this->checkLogin();
  }

  private function setEndPoints()
  {
    $this->uri = "https://www.serviziocontrattipubblici.it/WSLoginCollaudo/rest";
    if(! $_SESSION["developEnviroment"]) $this->uri = "https://www.serviziocontrattipubblici.it/WSLogin/rest";
  }

  private function checkLogin()
  {
    if(! empty($_POST["cpn"]["username"]) && ! empty($_POST["cpn"]["password"])) $this->login($_POST["cpn"]["username"], $_POST["cpn"]["password"]);
    if(! empty($_SESSION["cpn"]["timestamp"]) && ! empty($_SESSION["cpn"]["username"]) && $_SESSION["cpn"]["timestamp"] < strtotime('-30 minutes', time())) $this->login($_SESSION["cpn"]["username"], $_SESSION["cpn"]["password"]);
    $this->returnLoginView();
  }

  private function returnLoginView()
  {
    if(empty($_SESSION["cpn"]["_token"])) {
      if(! empty($_POST["cpn"]["form"])) $_POST = json_decode(base64_decode($_POST["cpn"]["form"]), TRUE);
      include "{$this->root}/cpn/views/login.php";
      exit();
    }
  }

  public function login($username, $password)
  {
    $error = "Si è verificato un errore e non è stato possibile effettuare il login. Si prega di riprovare.";
    try {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "{$this->uri}/Account/LoginPubblica",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
          'username' => $username,
          'password' => $password,
          'clientId' => html_entity_decode($this->clientId),
          'clientKey' => html_entity_decode($this->clientKey)
        ]),
      ));
      $curl = addCurlAuth($curl);
      $response = curl_exec($curl);
      curl_close($curl);
      if(! empty($response)) {
        $response = json_decode($response, TRUE);
        if((bool) $response["esito"] && ! empty($response["token"])) {
          $_SESSION["cpn"]["_token"] = $response["token"];
          $_SESSION["cpn"]["timestamp"] = strtotime('now');
          $_SESSION["cpn"]["username"] = $username;
          $_SESSION["cpn"]["password"] = $password;
          $error = NULL;
        }
      }
    } catch (ClientException $e) {
      if(curl_errno($curl) == 400) $error = "Richiesta formalmente invalida. Si prega di riprovare.";
      if(curl_errno($curl) == 404) $error = "Utente non riconosciuto. Si prega di riprovare.";
      if(curl_errno($curl) == 500) $error = "Un errore del servizio dei contratti pubblici ha impedito l'esecuzione della richiesta. Si prega di riprovare.";
      if ($_SESSION["gerarchia"] === "0") {
        $error .= "<br>" . $e->getMessage();
        $error .= "<br> 'url' => {$this->uri}/Account/LoginPubblica";
        $error .= "<br> 'username' => {$username}";
        $error .= "<br> 'password' => {$password}";
        $error .= "<br> 'clientId' => {$this->clientId}";
        $error .= "<br> 'clientKey' => {$this->clientKey}";
      }
    }

    if(! empty($error)) {
      $_SESSION["cpn"]["response"]["error"]["message"] = $error;
      $this->returnLoginView();
    }
  }

  public function inviaAtto($atto, $modalitaInvio)
  {
    return $this->invia('Atti/Pubblica', $atto, $modalitaInvio);
  }

  public function inviaGara($gara, $modalitaInvio)
  {
    return $this->invia('Anagrafiche/GaraLotti', $gara, $modalitaInvio);
  }
  public function inviaProgrammaLavori($programma, $modalitaInvio)
  {
    return $this->invia('Programmi/PubblicaLavori', $programma, $modalitaInvio, "WSProgrammi");
  }
  public function inviaProgrammaFornitureServizi($programma, $modalitaInvio)
  {
    return $this->invia('Programmi/PubblicaFornitureServizi', $programma, $modalitaInvio, "WSProgrammi");
  }

  public function ScaricaProgramma($id)
  {
    $uri = "https://www.serviziocontrattipubblici.it/WSProgrammiCollaudo/rest/Programmi/ScaricaPdf";
    if(! $_SESSION["developEnviroment"]) $uri = "https://www.serviziocontrattipubblici.it/WSProgrammi/rest/Programmi/ScaricaPdf";
    try {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "{$uri}?idRicevuto={$id}&token={$_SESSION["cpn"]["_token"]}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
      ));
      $curl = addCurlAuth($curl);
      $response = curl_exec($curl);
      curl_close($curl);
      if(! empty($response)) return $response;
    } catch (ClientException $e) {
      $errors = ["code" => curl_errno($curl), "request" => $e->getRequest()];
      if ($e->hasResponse()) {
        $errors["response"] = $e->getResponse();
      }
      return $errors;
    }
  }

  private function invia($basePath, $body, $modalitaInvio, $ws = "WSPubblicazioni")
  {
    $uri = "https://www.serviziocontrattipubblici.it/{$ws}Collaudo/rest/{$basePath}";
    if(! $_SESSION["developEnviroment"]) $uri = "https://www.serviziocontrattipubblici.it/{$ws}/rest/{$basePath}";
    try {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "{$uri}?modalitaInvio={$modalitaInvio}&token={$_SESSION["cpn"]["_token"]}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($body),
      ));
      $curl = addCurlAuth($curl);
      $response = curl_exec($curl);
      curl_close($curl);
      if(! empty($response)) {
        $response = json_decode($response, TRUE);
        return $response;
      }
    } catch (ClientException $e) {
      $errors = ["code" => curl_errno($curl), "request" => $e->getRequest()];
      if ($e->hasResponse()) {
        $errors["response"] = $e->getResponse();
      }
      return $errors;
    }
  }

  public static function getAtti()
  {
    $uri = "https://www.serviziocontrattipubblici.it/WSTabelleDiContestoCollaudo/rest/Tabellati/Atti?language=it";
    if(! $_SESSION["developEnviroment"]) $uri = "https://www.serviziocontrattipubblici.it/WSTabelleDiContesto/rest/Tabellati/Atti?language=it";
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "{$uri}",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_SSL_VERIFYPEER => FALSE,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET"
    ));
    $curl = addCurlAuth($curl);
    $response = curl_exec($curl);
    curl_close($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);;
    if($code == (int) 200) {
      $body = json_decode($response,true);
      if(! empty($body["data"])) return $body["data"];
    }
    return [];
  }

  public static function getValore($chiave, $selettore = null)
  {
    $uri = "https://www.serviziocontrattipubblici.it/WSTabelleDiContestoCollaudo/rest";
    if(! $_SESSION["developEnviroment"]) $uri = "https://www.serviziocontrattipubblici.it/WSTabelleDiContesto/rest";

    $available_values = ["Indizione", "TipologiaSA", "TipologiaProcedura", "Area", "Fase", "TipoInvio", "TipoAvviso", "SN", "TipoAppalto", "CriterioAggiudicazione", "TipoRealizzazione", "SceltaContraente", "SceltaContraente50", "MotivoCompletamento", "TipologiaAggiudicatario", "RuoloAssociazione", "TipologiaCC", "Categorie", "Classe", "Settore", "FormaGiuridica", "Entita", "Stato", "TipoProgramma", "Determinazioni", "Ambito", "Causa", "StatoRealizzazione", "DestinazioneUso", "TipologiaIntervento", "CategoriaIntervento", "Priorita", "Finalita", "StatoProgettazione", "TrasferimentoImmobile", "ImmobileDisponibile", "ProgrammaDismissione", "TipoProprieta", "TipologiaCapitalePrivato", "TipoDisponibilita", "Variato", "MesePrevisto", "TipologiaInterventoDM112011", "CategoriaInterventoDM112011", "FinalitaDM112011", "StatoProgettazioneDM112011", "TipologiaCapitalePrivatoDM112011", "UnitaMisura", "AcquistoRicompreso", "ProceduraAffidamento", "Acquisto", "Valutazione", "PrestazioniComprese", "ModalitaAcquisizioneForniture", "TipologiaLavoro", "Condizione"];

    if(! empty($chiave) && in_array($chiave, $available_values)) {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "{$uri}/Tabellati/Valori?cod={$chiave}&language=it",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
      ));
      $curl = addCurlAuth($curl);
      $response = curl_exec($curl);
      curl_close($curl);
      $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      if($code == (int) 200) {
        $body = json_decode($response,true);
        if(! empty($body["data"])) return $body["data"];
      }
    }

    if($chiave == "Province") {
      $query = ['language' => 'it'];
      $regioni = ["001", "002", "003", "004", "005", "006", "007", "008", "009", "010", "011", "012", "013", "014", "015", "016", "017", "018", "019", "020"];
      if(! empty($selettore)) $regioni = [$selettore];
      $province = [];
      foreach ($regioni as $regione) {
        $query["regione"] = $regione;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "{$uri}/Tabellati/Province?regione={$regione}&language=it",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_SSL_VERIFYPEER => FALSE,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET"
        ));
        $curl = addCurlAuth($curl);
        $response = curl_exec($curl);
        curl_close($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($code == (int) 200) {
          $body = json_decode($response,true);
          if(! empty($body["data"])) $province = array_merge($province, $body["data"]);
        }
      }
      return $province;
    }
    return [];
  }
}


?>
