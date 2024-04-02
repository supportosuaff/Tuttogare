<div style="height:200px; overflow:auto; padding:10px; background-color:#FFF; text-align:justify;">
  Ai sensi dell'art.13 del Reg. UE 2016/679, Vi informiamo che <?= $_SESSION["ente"]["denominazione"] ?>,
  titolare del trattamento, procede al trattamento dei dati personali mediante il proprio applicativo dedicato
  alla gestione delle gare telematiche, attraverso la registrazione al form elettronico e l'invio di documentazione
  attraverso la piattaforma telematica, al solo scopo di consentire l'accesso e la fruizione del servizio,
  nonché per fornire agli utenti un codice identificativo e PW necessario ad accedere all'area riservata
  per partecipare alle gare telematiche.<br>
  Il trattamento è obbligatorio al fine di ottemperare agli obblighi previsti dalla legge e dai regolamenti,
  per il perseguimento di finalità di rilevante interesse pubblico, nonché per fornire assistenza all'utente,
  verificare la qualità del servizio offerto o inviare ulteriori informazioni.<br>
  I dati forniti potranno essere portati a conoscenza di terzi per i quali ciò risulti necessario ed indispensabile
  (o comunque funzionale) per lo svolgimento delle attività di <?= $_SESSION["ente"]["denominazione"] ?> inerenti il presente servizio;
  in particolare potranno essere comunicati a nostri collaboratori e dipendenti appositamente incaricati o altri soggetti terzi
  (quali il manutentore dei sistemi informatici o Studio Amica, che gestisce la piattaforma informatica e
  ne cura le misure di sicurezza) sempre nell'ambito delle relative mansioni; in ogni caso, tali
  dati non saranno diffusi.<br>
  Il conferimento di tali dati, eventualmente anche di natura giudiziaria, è necessario per dare seguito alla registrazione,
  per consentire la partecipazione alla procedura in esame e per verificare il possesso dei requisiti di partecipazione;
  l'eventuale vostro rifiuto - totale o parziale - al trattamento dei dati comporterà l'impossibilità dell'iscrizione
  al servizio e la partecipazione alla gara telematica.<br>
  <?= $_SESSION["ente"]["denominazione"] ?> assicura che il presente trattamento dei dati personali si svolge
  nel rispetto dei diritti e delle libertà fondamentali, nonché della dignità dell'interessato,
  con particolare riferimento alla riservatezza dell'identità personale e al diritto
  alla protezione dei dati personali.<br>
  I dati saranno trattati per tutto il tempo necessario per l’espletamento della procedura di affidamento e,
  successivamente, saranno conservati in conformità alle norme sulla conservazione della documentazione amministrativa,
  nonché fino al tempo permesso dalla legge italiana a tutela dei legittimi interessi di <?= $_SESSION["ente"]["denominazione"] ?>.<br>
  Ai fornitori che si registrano e partecipano alle gare telematiche sono riconosciuti i diritti di cui agli
  artt. 15-22 del Regolamento UE 2016/679, in particolare, il diritto di accedere ai propri dati personali,
  di chiederne la rettifica, l’aggiornamento e la cancellazione, se incompleti, erronei o raccolti in violazione
  della legge, nonché di opporsi al loro trattamento per motivi legittimi rivolgendo le richieste al
  Responsabile della Protezione dei Dati all’indirizzo: <?= $_SESSION["ente"]["dpo"] ?>.
  Per ogni altro chiarimento è possibile inviare una semplice comunicazione a mezzo posta al seguente indirizzo e-mail
  <?= $_SESSION["ente"]["email"] ?> <?= (!empty($_SESSION["ente"]["fax"])) ? "o fax " .$_SESSION["ente"]["fax"] : "" ?>.<br>
  Ulteriori informazioni sul trattamento dei dati personali potranno essere acquisite visitando la <a href="/privacy.php" title="Policy Privacy" target="_blank">Privacy policy</a>
  presente nelle note legali sul nostro sito web. <br>
</div>