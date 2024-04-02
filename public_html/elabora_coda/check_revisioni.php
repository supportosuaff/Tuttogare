<?
  if (!empty($elabora_coda)) {
    $types = array("albo","mercato","sda");
    foreach ($types AS $type) {
      $sql_bandi = "SELECT * FROM b_bandi_".$type." WHERE periodo_revisione > 0 AND periodo_revisione IS NOT NULL AND (data_scadenza > now() OR data_scadenza = 0) AND pubblica > 0";
      $ris_bandi = $pdo->query($sql_bandi);
      if ($ris_bandi->rowCount() > 0) {
        while($bando = $ris_bandi->fetch(PDO::FETCH_ASSOC)) {
          $sql = "SELECT * FROM b_enti WHERE codice = :codice AND attivo = 'S'";
          $ris_ente = $pdo->bindAndExec($sql,array(":codice"=>$bando["codice_gestore"]));
          if ($ris_ente->rowCount() > 0) {
            $ente = $ris_ente->fetch(PDO::FETCH_ASSOC);
            $_SESSION["ente"] = $ente;

            $folder = $type;
            if ($type=="albo") $folder = "albo_fornitori";
            if ($type=="mercato") $folder = "mercato_elettronico";
            $href = "https://".$_SESSION["ente"]["dominio"]."/".$folder."/id".$bando["codice"]."-dettaglio";

            $limite_ultimo = date('Y-m-d');

            $limit = $bando["periodo_revisione"];
            $limit = date('Y-m-d',strtotime("-".$limit." month"));

            $tabella = "r_partecipanti_" . $type;
            if ($type == "mercato") $tabella = "r_partecipanti_me";
            $sql = "SELECT ".$tabella.".* FROM " . $tabella . " WHERE ammesso = 'S' AND DATE(timestamp_abilitazione) <= '".$limit."' AND codice_bando = " . $bando["codice"];
            $ris_partecipanti = $pdo->query($sql);
            if ($ris_partecipanti->rowCount() > 0) {
              while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {

                $time = strtotime($partecipante["timestamp_abilitazione"]);
                $scadenza = date('Y-m-d',strtotime('+'.$bando["periodo_revisione"].'month',$time));

                if (strtotime($scadenza) <= strtotime($limite_ultimo)) {
                  $oggetto = "Iscrizione scaduta " . $bando["oggetto"];

                  $corpo = "Si informa la S.V. che &egrave; scaduta l'abilitazione relativa al bando:<br>
                            <br><strong>" . $bando["oggetto"] . "</strong><br><br>
                            Da questo momento non potr&agrave; essere selezionato in iniziative collegate al bando in oggetto<br><br>
                            Le ricordiamo che entro i termini di scadenza pu&ograve; inviviare una nuova istanza collegandosi al link:
                            <a href='".$href."' title='Sito esterno'>" . $href . "</a><br><br>
                            Distinti Saluti<br><br>";

                  $sql_update = "UPDATE $tabella SET ammesso = 'N' WHERE codice = " . $partecipante["codice"];
                  $ris_update = $pdo->query($sql_update);
                
                  $_SESSION["codice_utente"] = -1;
                  $mailer = new Communicator();
                  $mailer->oggetto = $oggetto;
                  $mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
                  $mailer->codice_pec = $bando["codice_pec"];
                  $mailer->comunicazione = true;
                  $mailer->coda = false;
                  $mailer->sezione = $type;
                  $mailer->codice_gara = $bando["codice"];
                  $mailer->destinatari = $partecipante["codice_utente"];
                  $esito = $mailer->send();
                  unset($mailer);
                  unset($_SESSION["codice_utente"]);
                }
              }
            }
            if (isset($_SESSION["ente"])) unset($_SESSION["ente"]);
          }
        }
      }
      if (isset($_SESSION["ente"])) unset($_SESSION["ente"]);
    }
  }
?>
