<?
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  $error = true;
  if (isset($_SESSION["codice_utente"])) {
    if (check_permessi("supporto",$_SESSION["codice_utente"]) && in_array($_SESSION["tipo_utente"], array('SAD', 'SUP')) && empty($_SESSION["ente"]) && !empty($_POST["codice"])) {
      $strsql  = "SELECT b_call_plus.stato, b_utenti.cognome, b_utenti.nome
                    FROM b_call_plus
                    JOIN b_utenti ON b_call_plus.utente_modifica = b_utenti.codice
                    WHERE b_call_plus.codice = :codice ";
      $risultato  = $pdo->bindAndExec($strsql,[":codice"=>$_POST["codice"]]);
      if ($risultato->rowCount() === 1) {
        $call = $risultato->fetch(PDO::FETCH_ASSOC);
        if (empty($_POST["stato"])) {
          if ($call["stato"] === "0") {
            $salva = new salva();
            $salva->debug = false;
            $salva->codop = $_SESSION["codice_utente"];
            $salva->nome_tabella = "b_call_plus";
            $salva->operazione = "UPDATE";
            $salva->oggetto = array("codice"=>$_POST["codice"],"stato"=>"10");
            if ($salva->save() > 0) {
              $error = false;
              ?>
              Operazione effettutata con successo!
              <?
            } else {
              ?>
              Errore durante la presa in carico della chiamata!
              <?
            }
          } else {
            ?>
            La chiamata &egrave; gi&agrave; in lavorazione da <?= $call["cognome"] ?> <?= $call["nome"] ?>
            <?
          }
        } else {
          $salva = new salva();
          $salva->debug = false;
          $salva->codop = $_SESSION["codice_utente"];
          $salva->nome_tabella = "b_call_plus";
          $salva->operazione = "UPDATE";
          $salva->oggetto = $_POST;
          if ($salva->save() > 0) {
            $error = false;
            ?>
              jalert("Operazione effettutata con successo!");
              elenco_call.draw();
            <?
          } else {
            ?>
            jalert("Errore durante la chiusura della chiamata!")
            elenco_call.draw();
            <?
          }
        }
      }
    }
  }
  if ($error) {
    header("HTTP/1.0 500 Forbidden");
		die();
  }
?>
