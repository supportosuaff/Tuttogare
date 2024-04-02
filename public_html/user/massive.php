<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if ($_SESSION["amministratore"] && !isset($_SESSION["ente"])) {
    if (isset($_POST["submit"])) {
      $upload_path = $config["chunk_folder"]."/";
      $allowed_filetypes = array('.csv');
      $filename = $_FILES["utenti"]["name"];
      $ext = substr($filename, strpos($filename, '.'), strlen($filename) - 1);
      $msg='';
      if (!in_array($ext, $allowed_filetypes)) {
        echo 'File non corretto, riprovare.';
      } else {
        if (move_uploaded_file($_FILES["utenti"]["tmp_name"], $upload_path . $filename)){
          $utenti = array();
          ini_set('auto_detect_line_endings',TRUE);
          $file = fopen($upload_path . $filename, "r");
          while(! feof($file))
            $utenti[]=fgetcsv($file,0,';');
          fclose($file);
          $errori = array();
          if (count($utenti) > 0) {
            foreach($utenti as $index => $utente){
              $error = true;
              if (!empty($utente[0]) && !empty($utente[1]) && !empty($utente[2]) && !empty($utente[3])) {
                $email = $utente[0];
                $nome = $utente[1];
                $cognome = $utente[2];
                $codice_ente = $utente[3];
                $riferimento = (!empty($utente[4])) ? $utente[4] : 0;
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                  $check_usr = $pdo->bindAndExec("SELECT codice FROM b_utenti WHERE email = :email LIMIT 0,1",array(":email"=>$email));
                  $check_ente = $pdo->bindAndExec("SELECT * FROM b_enti WHERE codice = :codice",array(":codice"=>$codice_ente));
                  if ($check_usr->rowCount()===0 && $check_ente->rowCount()===1) {
                    $ente = $check_ente->fetch(PDO::FETCH_ASSOC);
                    if (empty($ente["dominio"]) && !empty($ente["sua"])) {
                      $check_ente = $pdo->bindAndExec("SELECT * FROM b_enti WHERE codice = :codice",array(":codice"=>$ente["sua"]));
                      if ($check_ente->rowCount() == 1) $ente = $check_ente->fetch(PDO::FETCH_ASSOC);
                    }
                    $dominio = (!empty($ente["dominio"])) ? $ente["dominio"] : $config["link_sito"];

                    $new = array();
                    $new["email"] = $email;
                    $new["nome"] = $nome;
                    $new["cognome"] = $cognome;
                    $new["codice_ente"] = $codice_ente;
                    $new["password"] = genpwd(8);
                    $new["password"] =password_hash(md5($new["password"]), PASSWORD_BCRYPT);
                    $new["attivo"] = "S";
                    $new["password_request"] = date("Y-m-d H:m:i");
            				$new["password_token"] = tokenGen();
                    if (!empty($riferimento) && is_numeric($riferimento)) {
                      $rif_usr = $pdo->bindAndExec("SELECT * FROM b_utenti WHERE codice = :codice LIMIT 0,1",array(":codice"=>$riferimento));
                      if ($rif_usr->rowCount() === 1) {
                        $rif_usr = $rif_usr->fetch(PDO::FETCH_ASSOC);
                        $new["gruppo"] = $rif_usr["gruppo"];
                        $new["procedureAttive"] = $rif_usr["procedureAttive"];
                      }
                    }
                    if (empty($new["gruppo"])) {
                      $new["gruppo"] = "3";
                      $new["procedureAttive"] = "0";
                    }
                    $salva = new salva();
                    $salva->debug = false;
                    $salva->codop = $_SESSION["codice_utente"];
                    $salva->nome_tabella = "b_utenti";
                    $salva->operazione = "INSERT";
                    $salva->oggetto = $new;
                    $codice_utente = $salva->save();
                    if ($codice_utente != false) {
                      $error = false;

                      $link = "https://" . $dominio . "/user/change_pwd.php?email=" . base64_encode($new["email"]) . "&token=" . base64_encode($new["password_token"]);
                      $link = "<a href='".$link."' target=\"_blank\" title=\"Cambia password\">".$link."</a>";
                      $messaggio = "<h1>" . $ente["denominazione"] . "</h1>";
                      $messaggio.= "In data " . date("d/m/Y") . " alle ore " . date("H:i") . " &egrave; stata richiesta la rigenerazione della password per l'accesso ai servizi del portale ";
                      $messaggio.= $link;
                      $messaggio.= "<br><br>Clicca o incolla il link nel browser per creare una nuova password. Il link sar&agrave; attivo per le prossime 48 ore<br><br>";
                      $oggetto = "Cambio Password";

                      $mailer = new Communicator();
                      $mailer->oggetto = $oggetto;
                      $mailer->corpo = $messaggio;
                      $mailer->codice_pec = -3;
                      $mailer->comunicazione = false;
                      $mailer->coda = false;
                      $mailer->destinatari = $new["email"];
                      $esito = $mailer->send();

                      if (isset($rif_usr["codice"]) && $rif_usr["codice"] == $riferimento) {
                        $ris_permessi = $pdo->bindAndExec("SELECT * FROM r_moduli_utente WHERE cod_utente = :riferimento",array(":riferimento"=>$riferimento));
                        if ($ris_permessi->rowCount() > 0) {
                          while($permesso = $ris_permessi->fetch(PDO::FETCH_ASSOC)) {
                            $risultato = $pdo->bindAndExec("INSERT INTO r_moduli_utente (cod_utente,cod_modulo) VALUES (:codice,:modulo)",array(":codice"=>$codice_utente,":modulo"=>$permesso["cod_modulo"]));
                  					scrivilog("r_moduli_utente","INSERT",$pdo->getSQL(),$_SESSION["codice_utente"]);
                          }
                        }
                      }
                    }
                  }
                }
              }
              if ($error) $errori[] = $index;
            }
            if (count($errori) > 0) {
              echo "<h1>Record con errori</h1>";
              echo "<pre style=\"color:#F00\">";
              print_r($errori);
              echo "</pre>";
              echo "<a href=\"/user/\" title=\"Utenti\">Ritorna</a>";
            }
          }
          unlink($upload_path . $filename);
        }
      }
    }
  }
?>
