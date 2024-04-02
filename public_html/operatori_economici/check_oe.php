<?php

@session_start();
if(! empty($root) && ! empty($config)) {
  if(! empty($_SESSION["ente"]["profilo_completo_oe"]) && $_SESSION["ente"]["profilo_completo_oe"] == 'S') {
    $bind = array();
    $bind[":codice_utente"] = $codice_utente;

    $sql = "SELECT b_utenti.*, b_gruppi.id AS id_gruppo FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice WHERE b_utenti.codice = :codice_utente ";
    $ris_utente = $pdo->bindAndExec($sql,$bind);

    $sql = "SELECT b_operatori_economici.* FROM b_operatori_economici WHERE b_operatori_economici.codice_utente = :codice_utente ";
    $ris_operatore = $pdo->bindAndExec($sql,$bind);

    $utente = $ris_utente->fetch(PDO::FETCH_ASSOC);
		$operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);

    if(! empty($utente) && ! empty($operatore)) {
      $incomplete_fields = array();
      $check_utente = array( "email" => traduci("e-mail"), "nome" => traduci("Nome"), "cognome" => traduci("Cognome"), "luogo" => traduci("Luogo di nascita"), "provincia_nascita" => traduci("Provincia di nascita"), "dnascita" => traduci("Data di nascita"), "sesso" => traduci("Sesso"), "cf" => traduci("Codice fiscale"), "indirizzo" => traduci("Indirizzo"), "citta" => traduci("Citta"), "provincia" => traduci("Provincia"), "regione" => traduci("Regione"), "stato" => traduci("nazione"), "pec" => traduci("PEC"));
      foreach ($check_utente as $key => $title) {
        if(empty($utente[$key])) $incomplete_fields[] = $title . " " . traduci('obbligatorio');
      }
      $check_operatore = array( "titolo_studio" => traduci("Titolo di studio"), "ordine_professionale" => traduci("Ordine professionale"), "iscrizione_ordine" => traduci("Iscrizione"), "numero_iscrizione_professionale" => traduci("Numero iscrizione"), "data_iscrizione_professionale" => traduci("Data iscrizione"), "curriculum" => traduci("Curriculum"));
      if($utente["id_gruppo"] == "OPE") $check_operatore = array( "ruolo_referente" => traduci("Ruolo"), "partita_iva" => traduci("Partita IVA"), "ragione_sociale" => traduci("Ragione sociale"), "codice_fiscale_impresa" => traduci("Codice fiscale"), "pmi" => traduci('obbligatorio'), "indirizzo_legale" => traduci("Indirizzo"), "citta_legale" => traduci("Citta"), "provincia_legale" => traduci("Provincia"), "regione_legale" => traduci("Regione"), "stato_legale" => traduci("nazione"), "indirizzo_operativa" => traduci("Indirizzo"), "citta_operativa" => traduci("Citta"), "provincia_operativa" => traduci("Provincia"), "regione_operativa" => traduci("Regione"), "stato_operativa" => traduci("nazione"), "banca" => traduci("Banca"), "iban" => traduci("IBAN"), "intestatario" => traduci("Intestatario"));
      foreach ($check_operatore as $key => $title) {
        if(empty($operatore[$key])) $incomplete_fields[] = $title . " " . traduci('obbligatorio');
      }

      if(! empty($incomplete_fields)) {
        $_SESSION["record_utente"]["flash_user_incomplete_fields"] = $incomplete_fields;
        if (in_array($_SERVER["PHP_SELF"], array("/operatori_economici/edit.php", "/operatori_economici/save.php")) === false) {
          echo '<meta http-equiv="refresh" content="0;URL=/operatori_economici/id'.$codice_utente.'-edit">';
          die();
        }
      }
    }
  }

  $bind = array();
  $bind[":codice_utente"] = $codice_utente;
  $risultato = $pdo->bindAndExec("SELECT * FROM r_cpv_operatori WHERE codice_utente = :codice_utente", $bind);
  if($risultato->rowCount() < 1) {
    if (in_array($_SERVER["PHP_SELF"], array("/operatori_economici/edit.php", "/operatori_economici/save.php")) === false) {
      echo '<meta http-equiv="refresh" content="0;URL=/operatori_economici/id'.$codice_utente.'-edit">';
      die();
    }
  }
}