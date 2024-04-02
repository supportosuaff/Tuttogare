<?
  session_start();
  include("../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  if(empty($_SESSION["codice_utente"]) || !check_permessi("scadenzario/gestione",$_SESSION["codice_utente"]) || empty($_POST)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
    die();
  } else {
    $data = $_POST;
    if(!empty($_POST["promemoria"])) {
      $promemoria = $data["promemoria"];
      unset($data["promemoria"]);
    }

    $errore = FALSE;

    $salva = new salva();
    $salva->debug = FALSE;
    $salva->codop = $_SESSION["codice_utente"];
    $salva->nome_tabella = "b_scadenze";
    if(!is_numeric($data["codice"])) $data["codice"] = 0;
    $salva->operazione = !empty($data["codice"]) ? 'UPDATE' : 'INSERT';
    $salva->oggetto = $data;
    $codice_scadenza = $salva->save();

    if(is_numeric($codice_scadenza)) {
      if(!empty($promemoria)) {
        $salva->nome_tabella = "b_alert_scadenze";
        foreach ($promemoria as $prome) {
          if(!is_numeric($prome["codice"])) $prome["codice"] = 0;
          $salva->operazione = !empty($prome["codice"]) ? 'UPDATE' : 'INSERT';
          $prome["codice_scadenza"] = $codice_scadenza;
          $salva->oggetto = $prome;
          if(!is_numeric($salva->save())) {$errore = true; break;}
        }
      }
    } else {
      $errore = TRUE;
    }

    if($errore) {
      if(is_numeric($codice_scadenza)) {
        $pdo->bindAndExec("DELETE FROM b_scadenze WHERE codice = :codice_scadenza",array(":codice_scadenza"=>$codice_scadenza));
      }
      ?>
      jalert('Si è verificato un errore durante il salvataggio. Si prega di riprovare');
      <?
    } else {
      ?>
      alert('Modifica effettuata con successo');
      <?
      if(is_numeric($data["codice"])) {
        ?>
        window.location.reload();
        <?
      } else {
        ?>
        window.location.href= '/scadenzario/gestione';
        <?
      }
    }
  }
?>
