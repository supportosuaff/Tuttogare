<?
  /**
  * TedEsender
  */
  class TedEsender
  {
    private $url;
    public $username = "TEDC6";
    private $password = "cm827gw6";
    private $header = array();
    private $post;
    private $auth;
    private $curl;
    private $response;
    private $roman_numbers = array('M'  => 1000, 'CM' => 900, 'D'  => 500, 'CD' => 400, 'C'  => 100, 'XC' => 90, 'L'  => 50, 'XL' => 40, 'X'  => 10, 'IX' => 9, 'V'  => 5, 'IV' => 4, 'I' => 1);

    public $debug = FALSE;
    public $path;
    public $submission_id;

    function __construct($param = array("trace" => 1))
    {
    }

    private function setUrl()
    {
      $this->url = "https://esentool.ted.europa.eu/api/production/latest/";
      if($_SESSION["developEnviroment"]) {
        $this->url = "https://esentool.ted.europa.eu/api/qualification/latest/";
      }
      $this->header[2] = "User-Agent: ".$_SERVER["HTTP_USER_AGENT"];
    }

    private function setAuth()
    {
      $this->auth = base64_encode($this->username.":".$this->password);
      $this->header[1] = "Authorization: Basic ".$this->auth;
    }

    private function number2roman($number)
    {
      $converted_number = '';
      if(is_numeric($number)) {
        while ($number > 0) {
          foreach ($this->roman_numbers as $char => $value) {
            if($number >= $value) {
              $number -= $value;
              $converted_number .= $char;
              break;
            }
          }
        }
        return $converted_number;
      } else {
        return 0;
      }
    }

    public function setPostData($post)
    {
      $this->post = base64_encode($post);
    }

    public function sendNotice()
    {

      $this->setUrl();
      $this->setAuth();
      $this->response = null;

      if(empty($this->post)) {
        throw new Exception("Error: missing data", 1);
        exit;
      }

      $post = http_build_query(array('notice' => $this->post));
      $this->header[3] = "Content-Length: " . strlen($post);

      $this->curl = curl_init($this->url."notice/submit");
      curl_setopt($this->curl, CURLOPT_POST, 1);
      curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);
      curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
      $this->curl = addCurlAuth($this->curl);
      $data = curl_exec($this->curl);

      if (curl_errno($this->curl)) {
        throw new Exception("Error Processing Request",curl_errno($this->curl));
      } else {
        if(!empty($data)) {
          $data = json_decode($data, TRUE);
          if(!empty($data["submission_id"]) && $data["status"] == "RECEIVED") {
            $this->response = $data;
          }
        }
      }
      curl_close($this->curl);
      unset($this->header[3]);
      if(empty($this->response)) throw new Exception("Empty response", 3);
      return $this->response;
    }

    public function getPDF()
    {
      $this->setUrl();
      $this->setAuth();
      $this->response = null;

      if(empty($this->submission_id)) {
        throw new Exception("Error: missing submission_id", 1);
        exit;
      }

      $post = http_build_query(array('submission_id' => $this->submission_id, 'format' => 'PDF'));
      $this->header[3] = "Content-Length: " . strlen($post);

      $this->curl = curl_init($this->url."notice/render");
      curl_setopt($this->curl, CURLOPT_POST, 1);
      curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);
      curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);

      $this->curl = addCurlAuth($this->curl);
      $data = curl_exec($this->curl);
      if (curl_errno($this->curl)) {
        throw new Exception("Error Processing Request", curl_errno($this->curl));
      } else {
        if(!empty($data)) {
          $data = json_decode($data, TRUE);
          if(!empty($data["result"])) {
            $this->response = $data;
          }
        }
      }
      curl_close($this->curl);
      unset($this->header[3]);
      if(empty($this->response)) throw new Exception("Empty response", 3);
      return $this->response;
    }

    public function getHTML()
    {
      $this->setUrl();
      $this->setAuth();
      $this->response = null;

      if(empty($this->submission_id)) {
        throw new Exception("Error: missing submission_id", 1);
        exit;
      }

      $post = http_build_query(array('submission_id' => $this->submission_id, 'format' => 'HTML'));
      $this->header[3] = "Content-Length: " . strlen($post);

      $this->curl = curl_init($this->url."notice/render");
      curl_setopt($this->curl, CURLOPT_POST, 1);
      curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);
      curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);

      $this->curl = addCurlAuth($this->curl);
      $data = curl_exec($this->curl);

      if (curl_errno($this->curl)) {
        throw new Exception("Error Processing Request",curl_errno($this->curl));
      } else {
        if(!empty($data)) {
          $data = json_decode($data, TRUE);
          if(!empty($data["result"])) {
            $this->response = $data;
          }
        }
      }
      curl_close($this->curl);
      unset($this->header[3]);
      if(empty($this->response)) throw new Exception("Empty response", 3);
      return $this->response;
    }

    public function getNoticeInfo($submission_id)
    {
      $this->setUrl();
      $this->setAuth();
      $this->response = null;

      if (empty($submission_id)) {
        throw new Exception("Submission ID cannot be empty", 4);
      }

      $this->curl = curl_init($this->url."notice/".$submission_id);
      curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);

      $this->curl = addCurlAuth($this->curl);
      $data = curl_exec($this->curl);

      if (curl_errno($this->curl)) {
        throw new Exception("Error Processing Request",curl_errno($this->curl));
      } else {
        if(!empty($data)) {
          $data = json_decode($data, TRUE);
          if(!empty($data["submission_id"])) {
            $this->response = $data;
          }
        }
      }
      curl_close($this->curl);
      if(empty($this->response)) throw new Exception("Empty response", 3);
      return $this->response;
    }

    public function getSezione($sezione)
    {
      $sezione = explode('-', $sezione);
      $sezione[0] = $this->number2roman(str_replace('S','',$sezione[0]));
      for ($i=1; $i < count($sezione); $i++) {
        if($sezione[$i] == "00")
        {
          unset($sezione[$i]);
          continue;
        }
        $sezione[$i] = (int) $sezione[$i];
      }
      return implode('.',$sezione).')';
    }
  }
