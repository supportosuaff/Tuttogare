<?
include_once("../../config.php");
include_once($root."/layout/top.php");
$edit = false;

if (isset($_SESSION["codice_utente"])) {
  if (isset($_SESSION["ente"])) {
    $edit = check_permessi("albo",$_SESSION["codice_utente"]);
    if(! $edit) {
        echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
        die();
    }
  } else {
    if(! check_permessi("supporto",$_SESSION["codice_utente"]) || ! in_array($_SESSION["tipo_utente"], array('SAD', 'SUP'))) {
      echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
      die();
    }
  }
} else {
  echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
  die();
}

if (isset($_GET["cod"])) {

 $bind = array(":codice" => $_GET["cod"]);
 $strsql = "SELECT b_utenti.*, b_gruppi.id as tipo FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice WHERE b_utenti.codice = :codice ";
 $risultato = $pdo->bindAndExec($strsql,$bind);
 if ($risultato->rowCount() > 0) {
   $record_utente = $risultato->fetch(PDO::FETCH_ASSOC);
   $strsql = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice";
   $risultato = $pdo->bindAndExec($strsql,$bind);
   if ($risultato->rowCount() > 0) {
    $record_operatore = $risultato->fetch(PDO::FETCH_ASSOC);
    $bind = array(":codice_operatore" => $record_operatore["codice"]);
    $operazione = "UPDATE";
    $strsql = "SELECT * FROM b_ccnl WHERE codice_operatore = :codice_operatore";
    $risultato_ccnl = $pdo->bindAndExec($strsql,$bind);
    $strsql = "SELECT * FROM b_rappresentanti WHERE codice_operatore = :codice_operatore";
    $risultato_rappresentanti = $pdo->bindAndExec($strsql,$bind);

    $string_cpv = "";
    $cpv = array();
    $strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_operatori ON b_cpv.codice = r_cpv_operatori.codice WHERE r_cpv_operatori.codice_operatore = :codice_operatore ORDER BY codice";
    $risultato_cpv = $pdo->bindAndExec($strsql,$bind);

    $strsql = "SELECT * FROM b_committenti WHERE codice_operatore = :codice_operatore";
    $risultato_committenti = $pdo->bindAndExec($strsql,$bind);
    $strsql = "SELECT * FROM b_certificazioni_qualita WHERE codice_operatore = :codice_operatore";
    $risultato_qualita = $pdo->bindAndExec($strsql,$bind);
    $strsql = "SELECT b_certificazioni_soa.*, b_classifiche_soa.id AS id_classifica, b_categorie_soa.id, b_categorie_soa.descrizione FROM b_certificazioni_soa JOIN b_categorie_soa
    ON b_certificazioni_soa.codice_categoria = b_categorie_soa.codice
    JOIN b_classifiche_soa ON b_certificazioni_soa.codice_classifica = b_classifiche_soa.codice
    WHERE b_certificazioni_soa.codice_operatore = :codice_operatore";
    $risultato_soa = $pdo->bindAndExec($strsql,$bind);

    $strsql = "SELECT b_certificazioni_soa.*, b_categorie_soa.descrizione FROM b_certificazioni_soa JOIN b_categorie_soa
    ON b_certificazioni_soa.codice_categoria = b_categorie_soa.codice
    WHERE b_certificazioni_soa.codice_classifica = 0 AND b_certificazioni_soa.codice_operatore = :codice_operatore";
    $risultato_soa_fatturato = $pdo->bindAndExec($strsql,$bind);

    $strsql = "SELECT b_esperienze_progettazione.*, b_categorie_progettazione.id, b_categorie_progettazione.destinazione, b_categorie_progettazione.descrizione AS descrizione_categoria FROM b_esperienze_progettazione
                JOIN b_categorie_progettazione ON b_esperienze_progettazione.codice_categoria = b_categorie_progettazione.codice
                WHERE b_esperienze_progettazione.codice_operatore = :codice_operatore";
    $risultato_progettazione = $pdo->bindAndExec($strsql,$bind);
    $strsql = "SELECT * FROM b_certificazioni_ambientali WHERE codice_operatore = :codice_operatore";
    $risultato_ambientali = $pdo->bindAndExec($strsql,$bind);
    $strsql = "SELECT * FROM b_altre_certificazioni WHERE codice_operatore = :codice_operatore";
    $risultato_certificazioni = $pdo->bindAndExec($strsql,$bind);
    $strsql = "SELECT * FROM b_brevetti WHERE codice_operatore = :codice_operatore";
    $risultato_brevetti = $pdo->bindAndExec($strsql,$bind);

    /* $bind[":codice_ente"] = 0;
    if (isset($_SESSION["ente"]["codice"])) $bind[":codice_ente"]=$_SESSION["ente"]["codice"];
    $strsql = "SELECT r_partecipanti.controllo_possesso_requisiti, b_gare.codice as codice_gara, b_gare.oggetto as oggetto_gara FROM r_partecipanti JOIN b_gare ON b_gare.codice = r_partecipanti.codice_gara WHERE r_partecipanti.codice_utente = :codice AND b_gare.codice_ente = :codice_ente AND r_partecipanti.controllo_possesso_requisiti = 'S'";
    $risultato_controllo_possesso_requisiti = $pdo->bindAndExec($strsql,$bind); */
    $style = "";
    $checkArt80 = checkStatoArt80($record_operatore["codice_fiscale_impresa"]);
    if (!empty($checkArt80["color"])) {
      $style= "border-bottom: 5px solid {$checkArt80["color"]}";
      $checkArt80Status = $checkArt80["status"];
    }
    ?>
    <h1 style="<?= $style ?>">
      <? echo $record_operatore["ragione_sociale"] ?>
      <? if (!empty($checkArt80Status)) { ?><small style="font-size: 10px; float:right"><?= $checkArt80Status ?></small><div class="clear"></div><?
        } else if (check_permessi("verifica-art-80", $_SESSION["codice_utente"])) {
          ?><div style="text-align:right; font-size:0.5em"><a href="#" onClick="sendArt80Request('<?= $record_operatore["codice"] ?>')" title="Richiedi verifica art.80">Verifica Articolo 80</a></div><?
        }
    ?>
    </h1>

    <div id="tabs">
     <ul>
       <li><a href="#referente">Referente</a></li>
       <? if ($record_utente["tipo"] == "OPE") { ?><li><a href="#azienda">Azienda</a></li>
       <li><a href="#organizzazione">Organizzazione</a></li><? } ?>
       <li><a href="#categorie">Categorie</a></li>
       <li><a href="#committenti">Committenti</a></li>
       <li><a href="#certificazioni">Certificazioni</a></li>
       <li><a href="#brevetti">Brevetti</a></li>
       <?
       if(isset($_SESSION["ente"])) {
        ?>
        <li><a href="#feedback">Feedback</a></li>
        <!-- <li><a href="#comunicazioni">Comunicazioni</a></li> 
        <li><a href="#storico">Storico</a></li>-->
        <?
       }
       ?>
     </ul>
     <div id="referente">
      <h1>Referente</h1>
      <div class="box">
        <h2>Dati anagrafici</h2>
        <table width="100%" id="anagrafici">
          <tr>
            <td class="etichetta">Nome</td><td><? echo $record_utente["nome"] ?></td>
            <td class="etichetta">Cognome</td><td><? echo $record_utente["cognome"] ?></td>
          </tr>
          <tr>
            <td class="etichetta">Luogo nascita</td><td><? echo $record_utente["luogo"] ?></td>
            <td class="etichetta">Provincia nascita</td><td><? echo $record_utente["provincia_nascita"] ?></td>
          </tr>
          <tr>
            <td class="etichetta">Data di nascita</td><td><? echo mysql2date($record_utente["dnascita"]) ?></td>
            <td class="etichetta">Sesso</td><td><? echo $record_utente["sesso"] ?></td>
          </tr>
          <tr>
            <td class="etichetta">Codice Fiscale</td><td><? echo $record_utente["cf"] ?></td>
            <? if ($record_utente["tipo"] != "OPE") { ?>
            <td class="etichetta">Partita IVA</td><td><? echo $record_operatore["partita_iva"] ?></td>
            <? } ?>
          </tr>
       </table>
     </div>
     <? if ($record_utente["tipo"] == "OPE") { ?>
     <div class="box">
      <h2>Ruolo</h2>
      <table width="100%">
        <tr><td class="etichetta">Ruolo</td><td colspan="5"><? echo $record_operatore["ruolo_referente"] ?>
        </td></tr>
        <tr><td class="etichetta">Procura</td>
         <td><? echo $record_operatore["tipo_procura"] ?></td><td class="etichetta">Numero</td><td><? echo $record_operatore["numero_procura"] ?></td>
         <td class="etichetta">Data</td><td><? echo mysql2date($record_operatore["data_procura"]) ?></td></tr>
       </table>
     </div>
     <? } else { ?>
     <div class="box">
       <h2>Dati professionali</h2>
       <table width="100%">
         <tr><td class="etichetta">Titolo di studio</td><td colspan="3"><? echo $record_operatore["titolo_studio"] ?></td>
         </tr>
         <tr><td class="etichetta">Ordine</td><td><? echo $record_operatore["ordine_professionale"] ?></td>
          <td class="etichetta">Iscrizione</td><td>
          <?
          if ($record_operatore["iscrizione_ordine"] != "") {
            ?>
            <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["iscrizione_ordine"] ?>" title="File allegato"><img src="/img/<? echo substr($record_operatore["iscrizione_ordine"],-3)?>.png" alt="File <? echo substr($record_operatore["iscrizione_ordine"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
            <?
          }
          ?>
        </td>
      </tr>
      <tr><td class="etichetta">Numero</td><td><? echo $record_operatore["numero_iscrizione_professionale"] ?></td>
        <td class="etichetta">Data</td><td><? echo mysql2date($record_operatore["data_iscrizione_professionale"]) ?></td></tr>
        <tr><td class="etichetta">Curriculum</td><td colspan="3">
          <?
          if ($record_operatore["curriculum"] != "") {
           ?>
           <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["curriculum"] ?>" title="File allegato"><img src="/img/<? echo substr($record_operatore["curriculum"],-3)?>.png" alt="File <? echo substr($record_operatore["curriculum"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
           <?
         }
         ?>
       </td></tr>
     </table>
   </div>
   <? } ?>
   <div class="box">
     <h2>Recapiti</h2>
     <table width="100%" id="recapiti">
      <tr><td class="etichetta">Indirizzo</td><td colspan="3"><? echo $record_utente["indirizzo"] ?></td></tr>
      <tr>
        <td class="etichetta">Citta</td><td><? echo $record_utente["citta"] ?></td>
        <td class="etichetta">Provincia</td><td><? echo $record_utente["provincia"] ?></td></tr>
        <tr>
         <td class="etichetta">Regione</td><td><? echo $record_utente["regione"] ?></td>
         <td class="etichetta">Stato</td><td><? echo $record_utente["stato"] ?></td></tr>
         <tr><td class="etichetta">Telefono</td><td><? echo $record_utente["telefono"] ?></td>
          <td class="etichetta">Cellulare</td><td><? echo $record_utente["cellulare"] ?></td></tr>
          <tr>
           <td class="etichetta">E-mail</td><td width="300">
           <? echo $record_utente["email"] ?>
         </td><td class="etichetta">PEC</td><td><? echo $record_utente["pec"] ?></td></tr>
       </table>
     </div>
   </div>
   <? if ($record_utente["tipo"] == "OPE") { ?>
   <div id="azienda">
    <h1>Dati aziendali</h1>
    <div class="box">
     <table width="100%">
       <tr><td class="etichetta">Ragione Sociale</td><td colspan="3"><? echo $record_operatore["ragione_sociale"] ?></td>
       </tr>
       <tr><td class="etichetta">Partita IVA</td><td><? echo $record_operatore["partita_iva"] ?></td>
         <td class="etichetta">Codice Fiscale</td><td><? echo $record_operatore["codice_fiscale_impresa"] ?></td>
       </tr>
       <td class="etichetta">Numero dipendenti</td><td><? echo $record_operatore["n_dipendenti"] ?></td>
       <td class="etichetta">Codice attivit&agrave;</td>
       <td><? echo $record_operatore["codice_attivita"] ?>
       </td>
     </tr>
     <tr>
       <td class="etichetta">Capitale Sociale</td><td><? echo $record_operatore["capitale_sociale"] ?></td>
       <td class="etichetta">Capitale Versato</td><td><? echo $record_operatore["capitale_versato"] ?></td>
     </tr>
     <tr>
      <td class="etichetta">Dimensione</td><td><? switch ($record_operatore["pmi"]) {
        case "C": echo "Micro"; break;
        case "P": echo "Piccola"; break;
        case "M": echo "Media"; break;
        case "G": echo "Grande"; break;
      } ?></td>
      <td class="etichetta">Curriculum aziendale</td><td>
      <?
      if ($record_operatore["curriculum"] != "") {
       ?>
       <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["curriculum"] ?>" title="File allegato"><img src="/img/<? echo substr($record_operatore["curriculum"],-3)?>.png" alt="File <? echo substr($record_operatore["curriculum"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
       <?
     }
     ?>
   </td></tr>
 </table>
</div>
<div class="box">
 <h2>Sede legale</h2>
 <table width="100%" id="recapiti">
  <tr><td class="etichetta">Indirizzo</td><td colspan="3"><? echo $record_operatore["indirizzo_legale"] ?></td></tr>
  <tr><td class="etichetta">Citta</td><td><? echo $record_operatore["citta_legale"] ?></td>
   <td class="etichetta">Provincia</td><td><? echo $record_operatore["provincia_legale"] ?></td></tr>
   <tr>
     <td class="etichetta">Regione</td><td><? echo $record_operatore["regione_legale"] ?></td>
     <td class="etichetta">Stato</td><td><? echo $record_operatore["stato_legale"] ?></td></tr>
   </table>
 </div>
 <div class="box">
   <h2>Sede operativa</h2>
   <table width="100%" id="recapiti">
    <tr><td class="etichetta">Indirizzo</td><td colspan="3"><? echo $record_operatore["indirizzo_operativa"] ?></td></tr>
    <tr><td class="etichetta">Citta</td><td><? echo $record_operatore["citta_operativa"] ?></td>
     <td class="etichetta">Provincia</td><td><? echo $record_operatore["provincia_operativa"] ?></td></tr>
     <tr>
       <td class="etichetta">Regione</td><td><? echo $record_operatore["regione_operativa"] ?></td>
       <td class="etichetta">Stato</td><td><? echo $record_operatore["stato_operativa"] ?></td></tr>
     </table>
   </div>
   <div class="box">
    <h2>Camera di commercio</h2>
    <table width="100%">
     <tr><td class="etichetta">Sede</td><td><? echo $record_operatore["sede_cc"] ?></td>
      <td class="etichetta">Numero iscrizione</td><td><? echo $record_operatore["numero_iscrizione_cc"] ?></td>
      <td class="etichetta">Data iscrizione</td><td><? echo mysql2date($record_operatore["data_iscrizione_cc"]) ?></td></tr>
      <tr><td class="etichetta">Certificato camerale</td><td colspan="3">
       <?
       if ($record_operatore["certificato_camerale"] != "") {
         ?>
         <a href="/documenti/operatori/<? echo $record_operatore["codice"] ?>/<? echo $record_operatore["certificato_camerale"] ?>" title="File allegato">
          <img src="/img/<? echo substr($record_operatore["certificato_camerale"],-3)?>.png" alt="File <? echo substr($record_operatore["certificato_camerale"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato
        </a><br>
        <?
      }
      ?>
    </td>
    <td class="etichetta">Data emissione</td><td><? echo mysql2date($record_operatore["data_emissione_certificato"]) ?></td></tr>
  </table>
</div>
<div class="box">
  <h2>INPS</h2>
  <table width="100%">
   <tr><td class="etichetta">Sede</td><td><? echo $record_operatore["sede_inps"] ?></td>
    <td class="etichetta">Matricola</td><td><? echo $record_operatore["matricola_inps"] ?></td></tr>

  </table>
</div>
<div class="box">
  <h2>INAIL</h2>
  <table width="100%">
   <tr><td class="etichetta">Sede</td><td><? echo $record_operatore["sede_inail"] ?></td>
    <td class="etichetta">Codice</td><td><? echo $record_operatore["codice_inail"] ?></td>
    <td class="etichetta">PAT</td><td><? echo $record_operatore["pat_inail"] ?></td></tr>
  </table>
</div>
<div class="box">
  <h2>Cassa Edile</h2>
  <table width="100%">
   <tr><td class="etichetta">Sede</td><td><? echo $record_operatore["sede_cassaedile"] ?></td>
    <td class="etichetta">Codice</td><td><? echo $record_operatore["codice_cassaedile"] ?></td>
    <td class="etichetta">Matricola</td><td><? echo $record_operatore["matricola_cassaedile"] ?></td></tr>
  </table>
</div>
<div class="box">
 <h2>Dati Bancari</h2>
 <table width="100%">
   <tr><td class="etichetta">Banca</td><td><? echo $record_operatore["banca"] ?></td></tr>
   <tr><td class="etichetta">IBAN</td><td><? echo $record_operatore["iban"] ?></td></tr>
   <tr><td class="etichetta">Intestatario</td><td><? echo $record_operatore["intestatario"] ?></td></tr>
 </table>
</div>
</div>
<div id="organizzazione">
  <h1>Organizzazione</h1>
  <div class="box">
    <h2>Rappresentanti legali</h2>
    <table width="100%" >
      <tbody id="rappresentanti">
        <? if (isset($risultato_rappresentanti) && $risultato_rappresentanti->rowCount() > 0) {
          while ($rappresentanti = $risultato_rappresentanti->fetch(PDO::FETCH_ASSOC)) {
            include("rappresentanti/view.php");
          }
        } ?>
      </tbody>
    </table>
  </div>
  <div class="box">
    <h2>CCNL applicati</h2>
    <table width="100%" >
      <tbody id="ccnl">
        <? if (isset($risultato_ccnl) && $risultato_ccnl->rowCount() > 0) {
          while ($ccnl = $risultato_ccnl->fetch(PDO::FETCH_ASSOC)) {
            ?><tr><td><? echo $ccnl["nome"] ?></td></tr><?
          }
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
<? } ?>
<div id="categorie">
 <h1>Categorie merceologiche</h1>
 <? include("categorie/view.php"); ?>
</div>
<div id="committenti">
  <h1>Committenti</h1>
  <div class="box">
    <table width="100%" >
      <tbody id="tab_committenti">
       <? if (isset($risultato_committenti) && $risultato_committenti->rowCount() > 0) {
        while ($committenti = $risultato_committenti->fetch(PDO::FETCH_ASSOC)) {
          include("committenti/view.php");
        }
      }
      ?>
    </tbody>
  </table>
</div>
</div>
<div id="certificazioni">

 <div class="box">
   <h2>Certificazioni di qualit&agrave;</h2>
   <table width="100%" >
    <tbody id="tab_qualita">
     <? if (isset($risultato_qualita) && $risultato_qualita->rowCount() > 0) {
      while ($qualita = $risultato_qualita->fetch(PDO::FETCH_ASSOC)) {
        include("qualita/view.php");
      }
    }
    ?>
  </tbody>
</table>
<div class="clear"></div>
</div>

<div class="box">
 <h2>Certificazioni di gestione ambientale</h2>
 <table width="100%" >
  <tbody id="tab_ambientali">
   <? if (isset($risultato_ambientali) && $risultato_ambientali->rowCount() > 0) {
    while ($ambientali = $risultato_ambientali->fetch(PDO::FETCH_ASSOC)) {
      include("ambientali/view.php");
    }
  }
  ?>
</tbody>
</table>
<div class="clear"></div>
</div>
<div class="box">
 <h2>Attestazioni SOA</h2>
 <table width="100%" >
  <tbody id="tab_soa">
   <? if (isset($risultato_soa) && $risultato_soa->rowCount() > 0) {
    while ($soa = $risultato_soa->fetch(PDO::FETCH_ASSOC)) {
      include("soa/view.php");
    }
  }
  ?>
</tbody>
</table>
<div class="clear"></div>
</div>
<div class="box">
 <h2>Fatturati</h2>
 <table width="100%" >
  <tbody id="tab_soa_fatt">
   <? if (isset($risultato_soa_fatturato) && $risultato_soa_fatturato->rowCount() > 0) {
    while ($soa_fatt = $risultato_soa_fatturato->fetch(PDO::FETCH_ASSOC)) {
      include("soa_fatturato/view.php");
    }
  }
  ?>
</tbody>
</table>
<div class="clear"></div>
</div>
<div class="box">
 <h2>Progettazione</h2>
 <table width="100%" >
  <tbody id="tab_progettazione">
   <? if (isset($risultato_progettazione) && $risultato_progettazione->rowCount() > 0) {
    while ($progettazione = $risultato_progettazione->fetch(PDO::FETCH_ASSOC)) {
      include("progettazione/view.php");
    }
  }
  ?>
</tbody>
</table>
<div class="clear"></div>
</div>
<div class="box">
 <h2>Altre certificazioni</h2>
 <table width="100%" >
  <thead>
    <tr>
     <th>Tipo</th>
     <th>Ente certificatore</th>
     <th>Certificazione</th>
   </tr>
   <tbody id="tab_certificazioni">
     <? if (isset($risultato_certificazioni) && $risultato_certificazioni->rowCount() > 0) {
      while ($certificazioni = $risultato_certificazioni->fetch(PDO::FETCH_ASSOC)) {
        ?><tr><td><? echo $certificazioni["tipo"] ?></td><td><? echo $certificazioni["denominazione"] ?></td><td><? echo $certificazioni["certificazione"] ?></td>
      </tr><?
    }
  }
  ?>
</tbody>
</table>
<div class="clear"></div>
</div>
</div>
<div id="brevetti">
 <h2>Brevetti</h2>
 <div class="box">
   <table width="100%" >
    <tbody id="tab_brevetti">
     <? if (isset($risultato_brevetti) && $risultato_brevetti->rowCount() > 0) {
      while ($brevetti = $risultato_brevetti->fetch(PDO::FETCH_ASSOC)) {
        include("brevetti/view.php");
      }
    }
    ?>
  </tbody>
</table>
<div class="clear"></div>
</div>
</div>
<?
if(isset($_SESSION["ente"]) ) {
  ?>

  <div id="feedback">
    <div class="box">
      <?
      $criteriFeedback = getCriteriFeedBack();
      if (!empty($criteriFeedback)) {
        $sql_feedback  = "SELECT codice_riferimento, dettaglio_riferimento, tipologia, GROUP_CONCAT(utente_modifica) AS valutatori FROM b_feedback 
                          WHERE b_feedback.codice_ente = :cod_ente 
                          AND b_feedback.codice_operatore = :cod_operatore GROUP BY tipologia, codice_riferimento, dettaglio_riferimento ";
                          
        $sql_dettaglio = "SELECT b_feedback.*, b_utenti.nome, b_utenti.cognome 
                          FROM b_feedback JOIN b_utenti ON b_feedback.utente_modifica = b_utenti.codice 
                          WHERE b_feedback.codice_riferimento = :riferimento AND b_feedback.tipologia = :tipologia AND b_feedback.codice_operatore = :operatore
                          AND b_feedback.dettaglio_riferimento = :dettaglio AND b_feedback.codice_punteggio = :punteggio AND b_feedback.utente_modifica = :utente";
        $ris_dettaglio = $pdo->prepare($sql_dettaglio);

        $sql_gara  = "SELECT b_gare.oggetto, b_gare.id FROM b_gare ";
        $sql_gara .= "WHERE b_gare.codice = :codice_riferimento ";
        $sql_gara .= "AND ( codice_ente = :codice_ente OR codice_gestore = :codice_ente)";
        $sth_gara = $pdo->prepare($sql_gara);

        $sql_contratti  = "SELECT b_contratti.oggetto, b_contratti.codice FROM b_contratti ";
        $sql_contratti .= "WHERE b_contratti.codice = :codice_riferimento ";
        $sql_contratti .= "AND ( codice_ente = :codice_ente OR codice_gestore = :codice_ente)";
        $sth_contratti = $pdo->prepare($sql_contratti);

        $ris_feedback = $pdo->bindAndExec($sql_feedback,array(':cod_ente' => $_SESSION["ente"]["codice"],':cod_operatore' => $record_operatore["codice"]));
        // echo $myPDO->getSQL();
        if ($ris_feedback->rowCount() > 0)
        {
          $feed = $pdo->go("SELECT feedback FROM r_enti_operatori WHERE cod_ente = :codice_ente AND cod_utente = :codice_utente",[":codice_ente"=>$_SESSION["ente"]["codice"],":codice_utente"=>$record_operatore["codice_utente"]])->fetch(PDO::FETCH_ASSOC);
          if (!empty($feed)) { 
            $feed = $feed["feedback"]; 
            ?>
            <div class="box" style="text-align:center">
              <img src="/img/<?= number_format($feed,0) ?>.png" alt="<?= $feed ?>"><br>
              <h1 style="text-align:center"><?= $feed ?></h1>
            </div>
          <?
          }
          ?>
          <table style="width:100%">
            <tbody>
              <tr>
                <th class="etichetta" style="width:10px">ID</th>
                <th class="etichetta">Oggetto</th>
                <th class="etichetta" style="width:10px">Tipologia</th>
                <th class="etichetta">Valutatore</th>
                <?
                  foreach($criteriFeedback AS $criterio) { 
                    ?>
                    <th class="etichetta" style="width:10px"><?= $criterio["titolo"] ?></th>
                    <?
                  } 
                ?>
                <th class="etichetta" style="width:10px">Soggettivo</th>
                <th class="etichetta" style="width:10px">Totale</th>
              </tr>
              <?
              while ($rec_feedback = $ris_feedback->fetch(PDO::FETCH_ASSOC)) 
              {
                $bind_dettaglio = array(
                  ":codice_riferimento" => $rec_feedback["codice_riferimento"],
                  ':codice_ente' => $_SESSION["ente"]["codice"]
                  );

                if ($rec_feedback["tipologia"] == "G")
                {
                  $sth_gara->execute($bind_dettaglio);
                  $rec_dettaglio = $sth_gara->fetch(PDO::FETCH_ASSOC);
                  $id = $rec_dettaglio["id"];
                  $oggetto = $rec_dettaglio["oggetto"];
                  $tipologia = "Gara";
                  $oggetto .= ($rec_feedback["dettaglio_riferimento"] != 0 ? "Lotto #".$rec_feedback["dettaglio_riferimento"] : "");
                }
                else if ($rec_feedback["tipologia"] == "C")
                {
                  $sth_contratti->execute($bind_dettaglio);
                  $rec_dettaglio = $sth_contratti->fetch(PDO::FETCH_ASSOC);
                  $id = $rec_dettaglio["codice"];
                  $oggetto = $rec_dettaglio["oggetto"];
                  $tipologia = "Contratto";
                }
                $dettagli = [];
                $ris_dettaglio->bindValue(":riferimento",$rec_feedback["codice_riferimento"]);
                $ris_dettaglio->bindValue(":tipologia",$rec_feedback["tipologia"]);
                $ris_dettaglio->bindValue(":dettaglio",$rec_feedback["dettaglio_riferimento"]);
                $ris_dettaglio->bindValue(":operatore",$record_operatore["codice"]);
                $valutatori = explode(",",$rec_feedback["valutatori"]);
                $valutatori = array_unique($valutatori);
                $rowspan= count($valutatori);
                ?>
                <tr>
                  <td rowspan="<?= $rowspan ?>" style="text-align:center"><?php echo $id?></td>
                  <td rowspan="<?= $rowspan ?>"><?php echo $oggetto?></td>
                  <td rowspan="<?= $rowspan ?>" style="text-align:center"><?php echo $tipologia?></td>
                  <? 
                    $somme = [];
                    $proceedGeneral = true;
                    $first = true;
                    $idRow = $rec_feedback["tipologia"] . $rec_feedback["codice_riferimento"] . $rec_feedback["dettaglio_riferimento"];
                    foreach($valutatori AS $valutatore) {
                      $current = "";
                      $ris_dettaglio->bindValue(":utente",$valutatore);
                      $somma = 0;
                      $proceed = true;
                      foreach($criteriFeedback AS $criterio) {
                        $ris_dettaglio->bindValue(":punteggio",$criterio["codice"]);
                        $ris_dettaglio->execute();
                        if ($val = $ris_dettaglio->fetch(PDO::FETCH_ASSOC));
                        if (empty($current)) {
                          $entered = true;
                          $current = "<td></td>";
                          if (!empty($val["cognome"])) {
                            $current = "<td style='text-align:center'>" . $val["cognome"] . " " . $val["nome"] . "</td>";
                          }
                          echo $current;
                        }
                        ?><td style="text-align:center">
                        <? 
                          if (!empty($val["valutazione"])) {
                            $somma += $val["valutazione"] * $criterio["ponderazione"];
                             ?>
                            <img src="/img/<?= number_format($val["valutazione"],0) ?>.png" alt="Ranking" height="12" valign="middle" style="margin-top:-3px"><br><b><?= number_format($val["valutazione"], 1) ?></b>
                          <? } else {
                            $proceed = false;
                            $proceedGeneral = false;
                          }
                        ?>
                        </td>
                        <?
                      }
                      if ($proceed) {
                        $parziale = $somma / count($criteriFeedback);
                      ?>
                        <td style="text-align:center">
                          <img src="/img/<?= number_format($parziale,0) ?>.png" alt="Ranking" height="12" valign="middle" style="margin-top:-3px"><br><b><?= number_format($parziale, 1) ?></b>
                        </td>
                        <?
                        $somme[] = $parziale;
                      }
                      if ($first) {
                        $first = false;
                        ?>
                        <td style="text-align:center" id="result-<?= $idRow ?>" rowspan="<?= $rowspan ?>">
                        </td>
                        <?
                      }
                      ?>
                      </tr>
                      <tr>
                      <?
                    }
                    if ($proceedGeneral && count($somme) >= $_SESSION["ente"]["required_feedback"]) {
                      $totale = array_sum($somme) / count($somme);
                      ob_start();
                     ?><img src="/img/<?= number_format($totale,0) ?>.png" alt="Ranking" height="12" valign="middle" style="margin-top:-3px"><br><b><?= number_format($totale, 1) ?></b><?   
                     $totale = ob_get_clean();
                     ?>
                     <script>
                       $("#result-<?= $idRow ?>").html('<?= $totale ?>');
                      </script>
                     <?
                    }
                  ?>
                  </tr>
                <?
              }
              ?>
            </tbody>
          </table>
          <?
        }
        else
        {
          ?>
          <div class="padding">
            <h2>Non &egrave; possibile determinare un punteggio per questo operatore.</h2>
          </div>
          <?
        }
      }
      ?>
    </div>
  </div>
  <? /*
  <div id="comunicazioni">
    <?
      $strsql = "SELECT b_comunicazioni.*, r_comunicazioni_utenti.sync, r_comunicazioni_utenti.codice as codice_relazione FROM b_comunicazioni JOIN r_comunicazioni_utenti ON b_comunicazioni.codice = r_comunicazioni_utenti.codice_comunicazione
                 WHERE  b_comunicazioni.codice_ente = :codice_ente AND
                 codice_utente = :codice_utente ORDER BY b_comunicazioni.timestamp DESC ";
      $bind = array();
      $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
      $bind[":codice_utente"] = $record_operatore["codice_utente"];
      $risultato = $pdo->bindAndExec($strsql,$bind);
      $user = $pdo->prepare("SELECT CONCAT(cognome,' ',nome) AS user FROM b_utenti WHERE codice = :codice");

      if ($risultato->rowCount()>0){
      ?>
              <table style="font-size:12px" width="100%" class="elenco">
              <thead><tr><td>Data</td><td>Oggetto</td><td width="20px" style="width:20px !important">Ricevute</td></thead>
              <tbody>
                <? while ($comunicazione = $risultato->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                  <td width="120"><? echo mysql2datetime($comunicazione["timestamp"]) ?></td>
                  <td><a href="#" onclick='$("#comunicazione<? echo $comunicazione["codice"] ?>").dialog({title:"Comunicazione del <? echo mysql2datetime($comunicazione["timestamp"]) ?>",modal:"true",width:"700px"})'><? echo substr($comunicazione["oggetto"],0,180) . "..." ?></a>
                    <div id="comunicazione<? echo $comunicazione["codice"] ?>" style="display:none">
                        <h2><? echo $comunicazione["oggetto"] ?></h2><br>
                        <? echo $comunicazione["corpo"] ?>
                    </div>
                    <div style="text-align:right">
                      <small>Utente: <? $user->bindValue(":codice",$comunicazione["utente_modifica"]); $user->execute(); echo $user->fetch(PDO::FETCH_ASSOC)["user"]; ?></small>
                    </div>
                  </td>
                  <td width="20px" style="width:20px !important">
                    <?
                    if($comunicazione["sync"] == "S") {
                      $hash = simple_encrypt($comunicazione["codice_relazione"], "ricevute-pec");
                      $hash = base64_encode($hash);
                      ?><a href="/comunicazioni/download-ricevute.php?ricevuta=<?= $hash ?>" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a><?
                    }
                    ?>
                  </td>
                </tr>
              <? } ?>
              </tbody>
              </table>
              <div class="clear"></div>
        <?
        }
    ?>
  </div>
  <div id="storico">
    <?
      $gare_partecipato = array();

      $bind = array();
      $bind[":codice_ente"] = (!empty($_SESSION["record_utente"]["codice_ente"])) ? $_SESSION["record_utente"]["codice_ente"] : $_SESSION["ente"]["codice"];
      $bind[":codice_utente"] = $record_operatore["codice_utente"];
      $sql_gare = "SELECT b_enti.dominio, b_gare.cig, b_gare.stato, b_gare.codice AS codice_gara, b_gare.oggetto, r_partecipanti.*
                   FROM b_gare JOIN r_partecipanti ON b_gare.codice = r_partecipanti.codice_gara
                   JOIN b_enti ON b_gare.codice_gestore = b_enti.codice
                   WHERE
                   b_gare.data_scadenza < now() AND r_partecipanti.codice_utente = :codice_utente AND (
                     b_gare.codice_gestore = :codice_ente OR b_gare.codice_ente = :codice_ente
                   ) AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ORDER BY b_gare.data_scadenza DESC";
      $ris_gare = $pdo->bindAndExec($sql_gare,$bind);
      if ($ris_gare->rowCount() > 0) {
        ?>
        <div class="box">
          <a class="btn-round btn-warning" style="float:right" onClick="$('#gare-content').slideToggle();"><span class="fa fa-search"></span></a>
          <h2>Gare</h2>
          <div class="clear"></div>
          <div id="gare-content" style="display:none">
            <table width="100%" class="elenco">
              <thead>
                <tr>
                  <th width="90">CIG</th>
                  <th>Oggetto</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              <?
                while($gara = $ris_gare->fetch(PDO::FETCH_ASSOC)) {
                  $gare_partecipato[] = $gara["codice_gara"];
                  $color = "";
                  $stato = "";
                  if ($gara["ammesso"] == "N") {
                    $color = "#FF6600";
                    $stato = "Escluso";
                  }
                  if ($gara["primo"] == "S") {
                    $color = "#99FF66";
                    $stato = "Aggiudicatario";
                  }
                  if ($gara["primo"]=="S") {
                    $stato = "<span class=\"fa fa-star\"></span> Aggiudicatario";
                  }
                  ?>
                  <tr >
                    <td><?= $gara["cig"] ?></td>
                    <td><a href="<?= $config["protocollo"] ?><?= $gara["dominio"] ?>/gare/id<? echo $gara["codice_gara"] ?>-dettaglio"><?= $gara["oggetto"] ?></a></td>
                    <td style="background-color:<?= $color ?>">
                      <?= $stato ?>
                    </td>
                  </tr>
                  <?
                }
              ?>
              </tbody>
            </table>
            <div class="clear"></div>

          </div>
        </div>
        <?
      }

      $bind = array();
      $bind[":codice_ente"] = (!empty($_SESSION["record_utente"]["codice_ente"])) ? $_SESSION["record_utente"]["codice_ente"] : $_SESSION["ente"]["codice"];
      $bind[":codice_utente"] = $record_operatore["codice_utente"];
      $sql_gare = "SELECT b_enti.dominio, b_gare.cig, b_gare.stato, b_gare.codice AS codice_gara, b_gare.oggetto, r_inviti_gare.*
                   FROM b_gare JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara
                   JOIN b_enti ON b_gare.codice_gestore = b_enti.codice
                   WHERE
                   b_gare.data_scadenza < now() AND r_inviti_gare.codice_utente = :codice_utente AND (
                     b_gare.codice_gestore = :codice_ente OR b_gare.codice_ente = :codice_ente
                   ) ORDER BY b_gare.data_scadenza DESC";
      $ris_gare = $pdo->bindAndExec($sql_gare,$bind);
      if ($ris_gare->rowCount() > 0) {
        ?>
        <div class="box">
          <a class="btn-round btn-warning" style="float:right" onClick="$('#inviti-content').slideToggle();"><span class="fa fa-search"></span></a>
          <h2>Inviti</h2>
          <div class="clear"></div>
          <div id="inviti-content" style="display:none">
            <table width="100%" class="elenco">
              <thead>
                <tr>
                  <th width="90">CIG</th>
                  <th>Oggetto</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              <?
                while($gara = $ris_gare->fetch(PDO::FETCH_ASSOC)) {
                  $color = "";
                  $stato = "Partecipato";
                  if (in_array($gara["codice_gara"], $gare_partecipato) === false) {
                    $color = "#FF6600";
                    $stato = "Non partecipato";
                  }
                  ?>
                  <tr>
                    <td><?= $gara["cig"] ?></td>
                    <td><a href="<?= $config["protocollo"] ?><?= $gara["dominio"] ?>/gare/id<? echo $gara["codice_gara"] ?>-dettaglio"><?= $gara["oggetto"] ?></a></td>
                    <td style="background-color:<?= $color ?>">
                      <?= $stato ?>
                    </td>
                  </tr>
                  <?
                }
              ?>
              </tbody>
            </table>
            <div class="clear"></div>

          </div>
        </div>
        <?
      }

      $tipi = array("albo","me","sda");
      foreach($tipi AS $tipo) {
        $tabella = "b_bandi_".$tipo;
        if ($tipo=="me") $tabella = "b_bandi_mercato";
        $sql_albi = "SELECT $tabella.codice AS codice_bando, $tabella.oggetto, r_partecipanti_$tipo.* FROM r_partecipanti_$tipo JOIN $tabella ON r_partecipanti_$tipo.codice_bando = $tabella.codice
                     WHERE $tabella.codice_gestore = :codice_ente AND r_partecipanti_$tipo.codice_operatore = :codice_operatore AND r_partecipanti_$tipo.ammesso = 'S' ";
        $bind_albi = array(':codice_ente' => $_SESSION["ente"]["codice"], ':codice_operatore' => $record_operatore["codice"]);
        $ris_albi = $pdo->bindAndExec($sql_albi,$bind_albi);
        if ($ris_albi->rowCount() > 0) {
          ?>
          <div class="box">
            <a class="btn-round btn-warning" style="float:right" onClick="$('#<?= $tipo ?>-content').slideToggle();"><span class="fa fa-search"></span></a>
            <h2>
              <?
                switch ($tipo) {
                  case 'albo':
                    echo "Albo fornitori";
                    $link = "albo_fornitori";
                  break;
                  case 'me':
                    echo "Mercato Elettronico";
                    $link = "mercato_elettronico";
                  break;
                  case 'sda':
                    echo "Sistema dinamico acquisizione";
                    $link = "sda";
                  break;
                }
              ?>
            </h2>
            <div class="clear"></div>
            <div id="<?= $tipo ?>-content" style="display:none">
              <table width="100%" class="elenco">
                <thead>
                  <tr>
                    <th>Oggetto</th>
                  </tr>
                </thead>
                <tbody>
                <?
                  while($albo = $ris_albi->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr>
                      <td><a href="/<?= $link ?>/id<? echo $albo["codice_bando"] ?>-dettaglio"><?= $albo["oggetto"] ?></a></td>
                    </tr>
                    <?
                  }
                ?>
                </tbody>
              </table>
              <div class="clear"></div>

            </div>
          </div>
          <?
        }
      }
      $bindArt80 = [":codice_richiesta"=>$record_operatore["codice_fiscale_impresa"]];
      $bindArt80[":codice_ente"] = $_SESSION["ente"]["codice"];
      $strsql = "SELECT b_verifiche_art80.* FROM b_verifiche_art80 WHERE b_verifiche_art80.codice_gestore = :codice_ente";
      if ($_SESSION["gerarchia"] > 0) {
          $bindArt80[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
          $strsql .= "AND (b_verifiche_art80.codice_ente = :codice_ente_utente OR b_verifiche_art80.codice_gestore = :codice_ente_utente) ";
      }
      $strsql .= " AND b_verifiche_art80.codice_fiscale = :codice_richiesta ORDER BY b_verifiche_art80.codice DESC ";
      $risultato_art80  = $pdo->bindAndExec($strsql,$bindArt80);
      if ($risultato_art80->rowCount() > 0) {
        $states = getArt80States();
        ?>
        <div class="box">
          <a class="btn-round btn-warning" style="float:right" onClick="$('#art80-content').slideToggle();"><span class="fa fa-search"></span></a>
          <h2>Verifiche Art.80</h2>
          <div class="clear"></div>
          <div id="art80-content" style="display:none">
            <table width="100%" class="elenco">
              <thead>
                 <tr>
                    <td style="width:10px">#</td>
                    <td></td>
                    <td style="width: 200px;">Stato</td>
                    <td width="120">Modalit&agrave;</td>
                    <td style="font-size:0.8em; text-align:center">Scaduti</td>
                    <td style="font-size:0.8em; text-align:center">In scadenza</td>
                    <td style="font-size:0.8em; text-align:center">Non scaduti</td>
                    <td style="font-size:0.8em; text-align:center">Non ricevuti</td>
                    <td style="font-size:0.8em; text-align:center">Ricevuti</td>
                    <td style="font-size:0.8em; text-align:center">Non Richiesti</td>
                    <td width="120"></td>
                </tr>
              </thead>
              <tbody>
              <?
                while($rec = $risultato_art80->fetch(PDO::FETCH_ASSOC)) {
                  $scaduti = 0;
                  $scadenza = 0;
                  $nonscaduti = 0;
                  $nonricevuti = 0;
                  $ricevuti = 0;
                  $nonrichiesti = 0;
                  $richiestiPA = json_decode($rec["documenti"],true);
                  if (!empty($rec["id_richiesta"])) $request = getArt80Request($rec["id_richiesta"]);
                  if(! empty($request)) {
                      $rec["stato"] = $request["status"]["code"];
                      $documents = [];
                      $request = getArt80Request($rec["id_richiesta"]);
                      $documents = $request["documents"];
                      if (!empty($request["company_reps"])) {
                          foreach($request["company_reps"] AS $person) {
                              if (!empty($person["documents"])) $documents = array_merge($documents,$person["documents"]);
                          }
                      }
                      $found = [];
                      if (count($documents) > 0) {
                          foreach($documents AS $document) {
                              if (in_array($document["uuid"],$found) === false) $found[] = $document["uuid"];
                              if (!empty($document["expiration"])) {
                                  $diff = strtotime($document["expiration"]) - time();
                                  $deadline = 45 * 84600; // trasformo 45 gg in secondi
                                  if($diff < 0) {
                                      $scaduti++;
                                  } else if ($diff <= $deadline) {
                                      $scadenza++;
                                  } else {
                                      $nonscaduti++;
                                  }
                              }
                              if ((isset($document["claimed"]) && $document["claimed"]) || !isset($document["claimed"])) {
                                  if ($document["present"] == "1") {
                                      $ricevuti++;
                                  } else {
                                      $nonricevuti++;
                                  }
                              } else {
                                  $nonrichiesti++;
                              }

                          }
                          foreach($richiestiPA AS $uuid) {
                              if (in_array($uuid,$found) === false) $nonrichiesti++;
                          }
                      } else {
                          $nonrichiesti = count($richiestiPA);
                      }
                  }
                  $color = (!empty($states[$rec["stato"]]["color"])) ? $states[$rec["stato"]]["color"] : "";
                  $stato = (!empty($states[$rec["stato"]]["title"])) ? $states[$rec["stato"]]["title"] : "Bozza";
                  ?>
                  <tr>
                      <td style="background-color:<?= $color ?>"></td>
                      <td><?= $rec["codice"] ?></td>
                      <td style="width: 130px;"><?= $stato ?></td>
                      <td style="width: 130px; text-align:center">
                          <?= ($rec["type"] == "spot") ? "Spot" : "Monitoraggio" ?>
                      </td>
                      <td style="<?= ($scaduti > 0) ? "background-color: #F00; color: #FFF; " : "" ?> text-align:center"><label title="Scaduti"><?= $scaduti ?></label></td>
                      <td style="<?= ($scadenza > 0) ? "background-color: #FC0; color: #FFF; " : "" ?> text-align:center"><label title="In scadenza"><?= $scadenza ?></label></td>
                      <td style="<?= ($nonscaduti > 0) ? "background-color: #0F0; color: #FFF; " : "" ?> text-align:center"><label title="Non scaduti"><?= $nonscaduti ?></label></td>
                      <td style="<?= ($nonricevuti > 0) ? "background-color: #FC0; color: #FFF; " : "" ?> text-align:center"><label title="Non ricevuti"><?= $nonricevuti ?></label></td>
                      <td style="<?= ($ricevuti > 0) ? "background-color: #0F0; color: #FFF; " : "" ?> text-align:center"><label title="Ricevuti"><?= $ricevuti ?></label></td>
                      <td style="<?= ($nonrichiesti > 0) ? "background-color: #F00; color: #FFF; " : "" ?> text-align:center"><label title="Non Richiesti"><?= $nonrichiesti ?></label></td>
                      <!-- <td><a href="/verifica-art-80/id<?= $rec["codice"] ?>-documenti-allegati"><i class="fa fa-download" style="padding: 10px; font-size: 20px" aria-hidden="true"></i></a></td> -->
                      <td>
                          <a href="/verifica-art-80/id<?= $rec["codice"] ?>-edit"><i class="fa fa-<?= (!empty($rec["stato"])) ? "search" : "pencil" ?> style="padding: 10px; font-size: 20px" aria-hidden="true"></i>
                      </a></td>
                  </tr>
                  <?
                  }
                  ?>
              </tbody>
            </table>
            <div class="clear"></div>

          </div>
        </div>
        <?
      }

    ?>
  </div>
  <? */
}
?>
</div>
<div class="clear"></div>
<script type="text/javascript">
  $("#tabs").tabs();
</script>
<?
}
}
}

include_once($root."/layout/bottom.php");
?>
