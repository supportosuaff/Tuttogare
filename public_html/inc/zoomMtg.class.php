<?
  use \Firebase\JWT\JWT;
  class zoomMtg
  {
    private $token;
    private $hosts;

    public function __construct()
    {
      global $config;
      /* $header = base64url_encode(json_encode(["alg"=>"HS256","typ"=>"JWT"]));
      $data = "{$header}.{$payload}";
      $this->token = $data . "." . hash_hmac("sha256",$data,$config["zoom-JWT-APISECRET"]); */
      if (!empty($config["zoom-JWT-APIKEY"])) {
        $payload = ["iss"=>$config["zoom-JWT-APIKEY"],"exp"=>strtotime("+60minute")];
        $this->token = JWT::encode($payload, $config["zoom-JWT-APISECRET"]);
        $this->hosts = []; // Inserire gli indirizzi e-mail dell'utenze zoom da utilizzare
      }
    }

    public function generateJoinSignature($meeting_number,$role){
      global $config;
      $time = time() * 1000; //time in milliseconds (or close enough)
      $data = base64_encode($config["zoom-JWT-APIKEY"] . $meeting_number . $time . $role);
      $hash = hash_hmac('sha256', $data, $config["zoom-JWT-APISECRET"], true);
      $_sig = $config["zoom-JWT-APIKEY"] . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
      return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
    }

    public function getMeetingDetails($id) {
      $curl = curl_init(); 
      curl_setopt($curl, CURLOPT_URL, "https://api.zoom.us/v2/meetings/{$id}");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_TIMEOUT, 0);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer {$this->token}"));
      $curl = addCurlAuth($curl);
      $risposta = curl_exec($curl);
      if (!empty($risposta)) {
        $risposta = json_decode($risposta,true);
        if (!empty($risposta["id"])) {
          return $risposta;
        }
      }
      return false;
    }
    
    public function getPastMeeting($id) {
      $curl = curl_init(); 
      curl_setopt($curl, CURLOPT_URL, "https://api.zoom.us/v2/past_meetings/{$id}");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_TIMEOUT, 0);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer {$this->token}"));
      $curl = addCurlAuth($curl);
      $risposta = curl_exec($curl);
      if (!empty($risposta)) {
        $risposta = json_decode($risposta,true);
        if (!empty($risposta["id"])) {
          if ($risposta["participants_count"] > 0) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://api.zoom.us/v2/past_meetings/{$id}/participants");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 0);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer {$this->token}"));
            $curl = addCurlAuth($curl);
            $participants = curl_exec($curl);
            if (!empty($participants)) {
              $participants = json_decode($participants,true);
              if (!empty($participants["participants"])) {
                $risposta["participants"] = $participants["participants"];
              }
            }
          }
          return $risposta;
        }
      }
      return false;
    }

    public function getMeetingFromDB($sezione,$codice_elemento,$sub_elemento,$contesto,$codice_meeting=false) {
      global $pdo;
      $bind = [":sezione"=>$sezione,":codice_elemento"=>$codice_elemento,":sub_elemento"=>$sub_elemento,":contesto"=>$contesto];
      $sql = "SELECT * FROM b_zoom WHERE sezione = :sezione AND codice_elemento = :codice_elemento AND sub_elemento = :sub_elemento AND contesto = :contesto ";
      if (!empty($codice_meeting)) { 
        $bind[":codice"] = $codice_meeting;
        $sql .= "AND codice = :codice";
      }
      $sql .= " ORDER BY codice DESC LIMIT 0,1";
      $meetings = $pdo->go($sql,$bind);
      if ($meetings->rowCount() > 0) return $meetings->fetch(PDO::FETCH_ASSOC);
      return false;
    }

    public function createMeeting($sezione,$codice_elemento,$sub_elemento,$contesto) {
      $body = [];
      $body["type"] = 1;
      $body["topic"] = "{$sezione} - #{$codice_elemento} - {$contesto}";
      $body["password"] = randomPassword(10);
      $body["timezone"] = "Europe/Rome";
      $body["settings"] = [
        "audio" => "voip",
        "auto_recording" => "local",
        "waiting_room" => true,
        "contact_name" => $_SESSION["ente"]["denominazione"],
        "contact_email" => $_SESSION["ente"]["pec"],
        "approval_type" => 1
      ];
      $body = json_encode($body);
      $curl = curl_init(); 
      global $pdo;
      $check = $pdo->prepare("SELECT response FROM b_zoom WHERE host = :host AND DATE(timestamp) = curdate() ORDER BY codice DESC LIMIT 0,1");
      foreach($this->hosts AS $tmpHost) {
        $check->bindValue(":host",$tmpHost);
        $check->execute();
        if ($check->rowCount() > 0) {
          $tmpResponse = $check->fetch(PDO::FETCH_ASSOC);
          $meeting = json_decode($tmpResponse["response"],true);
          $status = $this->getMeetingDetails($meeting["id"]);
          if (empty($status["status"]) || $status["status"] == "finished") {
            $host = $tmpHost;
          }
        } else {
          $host = $tmpHost;
        }
        if (!empty($host)) break;
      }
      if (!empty($host)) {
        curl_setopt($curl, CURLOPT_URL, "https://api.zoom.us/v2/users/{$host}/meetings");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer {$this->token}"));
        $curl = addCurlAuth($curl);
        $risposta = $response = curl_exec($curl);
        if (!empty($risposta)) {
          $risposta = json_decode($risposta,TRUE);
          if (!empty($risposta["uuid"])) {  
            $salva = new salva();
            $salva->debug = false;
            $salva->codop = 0;
            $salva->nome_tabella = "b_zoom";
            $salva->operazione = "INSERT";
            $salva->oggetto = ["sezione"=>$sezione,"codice_elemento"=>$codice_elemento,"sub_elemento"=>$sub_elemento,"contesto"=>$contesto,"response"=>$response,"host"=>$host];
            $salva->save();
            return $risposta;
          }
        }
      }
      return false;
    }

    public function deleteMeeting($id) {
      $curl = curl_init(); 
      curl_setopt($curl, CURLOPT_URL, "https://api.zoom.us/v2/meetings/{$id}");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_TIMEOUT, 0);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer {$this->token}"));
      $curl = addCurlAuth($curl);
      $risposta = curl_exec($curl);
      if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 204) return true;
      return false;
    }
    
  }
  
?>