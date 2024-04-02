<?
  include_once("../../../config.php");
  $edit = false;
  $lock = true;
  if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
    $strsql = "SELECT * FROM b_gestione_gare WHERE link LIKE '/gare/valutazione_offerta/edit.php%'";
    $risultato = $pdo->query($strsql);
    if ($risultato->rowCount()>0) {
      $gestione = $risultato->fetch(PDO::FETCH_ASSOC);
      $esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
      $edit = $esito["permesso"];
      $lock = $esito["lock"];
    }
    if (!$edit) {
      die();
    }
  } else {
    die();
  }
  if ($edit && !$lock)
  {
    if (isset($_POST["valutazione"])) {
      $sql = "SELECT codice FROM b_punteggi_criteri
                           WHERE codice_criterio = :criterio AND codice_partecipante = :partecipante";
      $ris = $pdo->prepare($sql);
      $error = false;
      foreach($_POST["valutazione"] AS $codice_partecipante => $punteggio) {
        $ris->bindValue(':partecipante',$codice_partecipante);
        foreach($punteggio AS $criterio => $valore) {
          $dati = [];
          $operazione_query = "INSERT";
          $ris->bindValue(":criterio",$criterio);
          $ris->execute();
          if ($ris->rowCount() > 0) {
            $dati["codice"] = $ris->fetch(PDO::FETCH_ASSOC)["codice"];
            $operazione_query = "UPDATE";
          }
          $dati["codice_gara"] = $_POST["codice_gara"];
          $dati["codice_lotto"] = $_POST["codice_lotto"];
          $dati["codice_criterio"] = $criterio;
          $dati["codice_partecipante"] = $codice_partecipante;
          $dati["punteggio"] = $valore;
          $salva = new salva();
          $salva->debug = false;
          $salva->codop = $_SESSION["codice_utente"];
          $salva->nome_tabella = "b_punteggi_criteri";
          $salva->operazione = $operazione_query;
          $salva->oggetto = $dati;
          if ($salva->save() == false) $error = true;
        }
      }
      if ($error) {
        echo "alert('Si sono verificati degli errori durante il salvataggio!');";
      } else {
        log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Salvataggio punteggi partecipanti");
        echo "alert('Salvataggio effettuato con successo!');";
      }
      echo "window.location.reload();";
    }
  }
?>
