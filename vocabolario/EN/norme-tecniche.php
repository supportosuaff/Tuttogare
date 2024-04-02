<div id="norme">
<h1>NORME TECNICHE DI FUNZIONAMENTO DEL SISTEMA DI E-PROCUREMENT - <?= $_SESSION["ente"]["denominazione"] ?></h1>
<small>Valide dal 22/02/2019 - <a href="/storico-norme/v3.php">Versione precedente</a></small>
<div class="box">
  <strong>Al fine di agevolare le operazioni di iscrizione è stata modificata la procedura di registrazione al portale</strong>
</div>
<p>
  Il presente documento contiene le norme tecniche di funzionamento del sistema di e-procurement del <strong><?= $_SESSION["ente"]["denominazione"] ?></strong> e disciplina le regole di svolgimento,
  in modalit&agrave; telematica, delle procedure aperte, ristrette e negoziate per l'aggiudicazione di contratti di lavori, servizi e forniture.
</p>
<p>
  <h2>Indice</h2>
  <ol>
    <li>Oggetto</li>
    <li>Soggetti</li>
    <li>La registrazione dell'operatore economico</li>
    <li>Presentazione di istanze per elenchi di fornitori, manifestazioni d'interesse, sistema dinamico di acquisizione e mercato elettronico</li>
    <li>Avviso di gara</li>
    <li>Partecipazione alle gare telematiche</li>
    <li>Procedura di gara</li>
    <li>Utilizzazione del sistema - dotazioni</li>
    <li>Garanzie, limitazioni di responsabilit&agrave;</li>
    <li>Manleva</li>
    <li>Normativa applicabile</li>
    <li>Termini e condizioni di utilizzo del sistema</li>
    <li>Regole di condotta</li>
    <li>Accesso agli atti</li>
    <li>Foro competente</li>
  </ol>
</p>
<p>
  <ol>
    <li><strong>OGGETTO</strong>
      <ol>
        <li>
          Le presenti norme disciplinano le regole di svolgimento, in maniera telematica, delle procedure di scelta del contraente indette da <strong><?= $_SESSION["ente"]["denominazione"] ?></strong>, 
          da ora denominato <strong>Ente</strong>, per l'affidamento di lavori, forniture e servizi da eseguirsi tramite il Sistema, costituito da una piattaforma on-line di
          e-procurement propria dell'Ente, accessibile tramite l'indirizzo: <strong>https://<?= $_SERVER["SERVER_NAME"] ?></strong>, da ora denominata <strong>portale</strong>.
        </li>
        <li>
          Le presenti norme si applicano alle procedure aperte, ristrette, negoziate con o senza preventiva pubblicazione di bando,
          da aggiudicarsi con il criterio del minor prezzoo con il criterio dell'offerta economicamente più vantaggiosa.
        </li>
        <li>
          Per una corretta individuazione del ruolo, dei compiti e delle responsabilità gravanti, rispettivamente, sull'Ente, sul Gestore del Sistema
          e sui singoli Utenti abilitati ad operare sulla piattaforma, occorre far riferimento alle presenti norme tecniche e ai documenti relativi alle singole procedura di gara.
        </li>
        <li>
          Il Sistema è costituito da una piattaforma di e-procurement tramite la quale l'Ente può effettuare affidamenti di lavori, servizi e forniture
          agli Operatori Economici che si siano abilitati a seguito di registrazione sulla piattaforma TuttoGare del <strong><?= $_SESSION["ente"]["denominazione"] ?></strong>.
          L'Ente utilizza il Sistema ai fini dell'approvvigionamento delle risorse strumentali allo svolgimento della propria attività istituzionale.
        </li>
        <li>
          L'utilizzo dello strumento telematico per lo svolgimento delle procedure di gara assicura il rispetto della parità di condizioni dei partecipanti e
          dei principi di trasparenza e semplificazione delle procedure, in conformità alle disposizioni nazionali, anche tecniche, di recepimento della normativa
          comunitaria sulla firma elettronica e sulla PEC. L'utilizzo delle procedure telematiche, inoltre, offre il vantaggio di una maggiore sicurezza nella
          "conservazione" dell'integrità delle offerte, indipendentemente dalla presenza o meno del pubblico alle sedute di gara, escludendo in radice la possibilità di apportare modifiche.
        </li>
        <li>
          L'accesso tramite il proprio Account personale da parte degli Utenti registrati sulla piattaforma TuttoGare comporta
          l'accettazione di tutte le disposizioni contenute nelle presenti norme tecniche.
        </li>
      </ol>
    </li>
    <li>
      <strong>SOGGETTI</strong>
      <ol>
        <li>
          <?= $_SESSION["ente"]["norme_sezione_2"] ?>
        </li>
        <li>
          Il Gestore del sistema è stato incaricato dall’Ente per la conduzione tecnica delle applicazioni informatiche necessarie al funzionamento del Sistema,
          assumendone il Gestore stesso ogni responsabilità al riguardo.
        </li>
        <li>
          <strong>Il Gestore del Sistema</strong> controlla i principali parametri di funzionamento del Sistema, segnalandone eventuali anomalie.
          Il Gestore del Sistema è, in particolare, responsabile della sicurezza informatica, logica e fisica del Sistema e riveste
          il ruolo di Responsabile della Sicurezza e di Amministratore di Sistema ai sensi della disciplina che regola la materia.
          Lo stesso è altresì responsabile dell’adozione di tutte le misure stabilite dal D.LGS 196/2003 in tema di trattamento dei dati personali.
        </li>
        <li>
          Nell’ambito del Sistema ciascun <strong>Responsabile del Procedimento</strong> opera esclusivamente quale soggetto dotato dei poteri necessari ad impegnare l’Amministrazione di appartenenza.
          Ogni azione compiuta dal Responsabile del Procedimento è quindi imputata direttamente all’Amministrazione di pertinenza,
          con il conseguente obbligo di dare concreta attuazione ai diritti ed ai doveri all’azione stessa ricollegabili.
        </li>
        <li>
          Si considerano <strong>Soggetti Abilitati</strong> gli Operatori Economici abilitati. A detti soggetti è attribuita qualunque azione compiuta all’interno del sistema,
          anche sulla base del semplice accesso attraverso l’utilizzo del proprio Account.
        </li>
      </ol>
    </li>
    <li>
      <strong>LA REGISTRAZIONE DELL'OPERATORE ECONOMICO</strong>
      <ol>
        <li>
          La Registrazione consente l’abilitazione dell'Operatore Economico, e quindi la sua partecipazione alle gare d’appalto
          nel rispetto e in conformità delle presenti regole tecniche.
          I soggetti interessati dovranno seguire le fasi di Registrazione di seguito elencate e compilare correttamente, in ogni parte, gli appositi form presenti sulla piattaforma.
        </li>
        <li>
          La Registrazione dell'Operatore Economico prevede due fasi:<br>
          <br>
          a) Pre-iscrizione<br>
          b) Perfezionamento della registrazione<br>
          <br>
          <strong>a) Pre-iscrizione</strong><br>
          La fase di pre-iscrizione richiede l'inserimento dei seguenti dati <strong>obbligatori</strong>:<br>
          <br>
          - Indirizzo e-mail dell'Operatore Economico;<br>
          - Password d'accesso scelta dell'O.E.;<br>
          - Nome del referente dell'O.E.;<br>
          - Cognome del referente dell'O.E.;<br>
          - Codice Fiscale dell'O.E.;<br>
          - Indirizzo PEC dell'O.E.;<br>
          - Categorie Merceologiche di interesse;<br>
          - Tipologia di O.E. a scelta tra:<br>
          &nbsp;&nbsp;&nbsp;&nbsp;a) Azienda;<br>
          &nbsp;&nbsp;&nbsp;&nbsp;b) Professionista;<br>
          - Accettazione dell'informativa sulla privacy;<br>
          - Accettazione delle norme tecniche di utilizzo.<br>
          <br>
          La pre-iscrizione non necessita di alcuna operazione di conferma in quanto il semplice salvataggio dei dati abilita l'Operatore Economico alla fase di perfezionamento della Registrazione.<br>
          Una volta completata la pre-iscrizione, l'Operatore Economico, in qualsiasi momento, può accedere al perfezionamento della Registrazione sulla piattaforma inserendo nel Sistema le credenziali indicate nella pre-iscrizione.<br>
          <br>
          <strong>ATTENZIONE: La registrazione (e quindi l’abilitazione dell'Operatore Economico ai servizi della piattaforma) si conclude esclusivamente dopo
          la compilazione di tutti i dati obbligatori richiesti nella fase di pre-iscrizione e la conseguente conferma.
          Al termine dell’inserimento e del successivo salvataggio di tutti i dati obbligatori, infatti, l'Operatore Economico riceverà sull'indirizzo
          PEC indicato un messaggio contenente il link di conferma della registrazione su cui l'Operatore stesso dovrà opportunamente cliccare.</strong><br>
          <br>
          <strong>b) Perfezionamento della registrazione</strong><br>
          Il Perfezionamento della Registrazione sarà eseguibile in qualsiasi momento dopo la fase di pre-iscrizione, e sarà obbligatoria per l'invio di istanze di partecipazione a indagini di mercato, elenchi di fornitori, mercato elettronico, dialogo competitivo e sistemi dinamici di acquisizione.
          L'Operatore Economico può compilare gli ulteriori dati obbligatori per l'effettiva abilitazione ai servizi piattaforma.
          Per ciascuna Scheda i dati obbligatori sono i seguenti e sono segnalati sulla piattaforma dal simbolo *:<br>
          <br>
          <strong>Per gli Operatori Economici di tipo “Azienda”:</strong><br>
          <br>
          <strong>Scheda REFERENTE</strong><br>
          - Indirizzo e-mail<br>
          - Password<br>
          - Nome<br>
          - Cognome<br>
          - Luogo di nascita<br>
          - Provincia di nascita<br>
          - Data di nascita<br>
          - Sesso<br>
          - Codice fiscale<br>
          - Ruolo<br>
          - Indirizzo di residenza<br>
          - Città di residenza<br>
          - Provincia di residenza<br>
          - Regione di residenza<br>
          - Stato di residenza<br>
          - Indirizzo PEC<br>
          <br>
          <strong>Scheda AZIENDA</strong><br>
          - Partita IVA<br>
          - Ragione Sociale<br>
          - Codice Fiscale<br>
          - Numero dipendenti<br>
          - Codice attività<br>
          - Capitale Sociale<br>
          - Capitale versato<br>
          - Dimensione<br>
          - Indirizzo sede legale<br>
          - Città sede legale<br>
          - Provincia sede legale<br>
          - Regione sede legale<br>
          - Stato sede legale<br>
          - Indirizzo sede operativa<br>
          - Città sede operativa<br>
          - Provincia sede operativa<br>
          - Regione Stato sede operativa<br>
          - Stato sede operativa<br>
          - Sede Camera di Commercio<br>
          - Numero iscrizione Camera di Commercio<br>
          - Data iscrizione Camera di Commercio<br>
          - Certificato Camerale (inserire allegato)<br>
          - Data emissione certificato camerale<br>
          - Banca<br>
          - IBAN<br>
          - Intestatario<br>
          <br>
          <strong>Scheda ORGANIZZAZIONE</strong><br>
          - Qualità<br>
          - Nome<br>
          - Cognome<br>
          - Codice Fiscale<br>
          - Indirizzo<br>
          - Città<br>
          - Cap<br>
          - Provincia<br>
          - Stato<br><br>
          <strong>(* in caso di molteplicità di Rappresentanti cliccare sull’icona in verde e procedere con l’inserimento dei dati per ciascun Rappresentante)</strong><br>
          - CCNL applicati<br>
          <br>
          <strong>Scheda CATEGORIE</strong><br><br>
          - Categorie Merceologiche di interesse*<br>
          <br>
          <strong>
            *Le categorie merceologiche sono configurate sulla base del vocabolario comune per gli appalti pubblici (CPV) adottato dal Regolamento (CE) n. 213/2008.
            Esse possono essere scelte mediante inserimento della categoria di interesse nel motore di ricerca oppure cliccando su “Scegli da lista”.
          </strong><br>
          <br>
          <strong>Per gli Operatori Economici di tipo "Professionista":</strong><br>
          <br>
          <strong>Scheda REFERENTE</strong><br>
          - Indirizzo e-mail<br>
          - Password<br>
          - Nome<br>
          - Cognome<br>
          - Luogo di nascita<br>
          - Provincia di nascita<br>
          - Data di nascita<br>
          - Sesso<br>
          - Codice fiscale<br>
          - Partita IVA<br>
          - Identificativo Fiscale Estero<br>
          - Copia del documento di riconoscimento<br>
          - Numero del documento di riconoscimento<br>
          - Titolo di Studio<br>
          - Ordine<br>
          - Copia atto di iscrizione<br>
          - Numero atto di iscrizione<br>
          - Data iscrizione<br>
          - Curriculum Vitae<br>
          - Indirizzo di residenza<br>
          - Città di residenza<br>
          - Provincia di residenza<br>
          - Regione di residenza<br>
          - Stato di residenza<br>
          - Indirizzo PEC<br><br>
          <strong>Scheda CATEGORIE</strong><br>
          - Categorie Merceologiche di interesse*
          <strong>
            *Le categorie merceologiche sono configurate sulla base del vocabolario comune per gli appalti pubblici (CPV) adottato dal Regolamento (CE) n. 213/2008.
            Esse possono essere scelte mediante inserimento della categoria di interesse nel motore di ricerca oppure cliccando su “Scegli da lista”.
          </strong><br><br>
          L'inserimento delle predette informazioni può avvenire anche in momenti differenti in quanto il Sistema permette il salvataggio dei dati inseriti
          ed il successivo recupero delle informazioni tramite la funzione <strong>"Completa Iscrizione"</strong> presente nella pagina <strong>Registrazione Operatori Economici.</strong><br>
          <br>
        </li>
        <li>
          Effettuata la Registrazione, ogni Operatore Economico potrà cancellare o modifica i propri dati.
        </li>
        <li>
          La Registrazione sulla piattaforma da parte dell'Operatore Economico non esclude l'onere, in capo allo stesso, di tenersi aggiornato in
          ordine alle gare in corso, agli avvisi di gara, agli esiti di gara e/o altri avvisi. Di conseguenza nessuna responsabilità potrà essere imputata all'Ente
          o all'Amministratore del Sistema per mancata comunicazione.
        </li>
        <li>
          L'Operatore Economico garantisce circa l'esattezza e la veridicità dei dati personali e delle informazioni inseriti in fase di Registrazione,
          nonché di tutti gli ulteriori dati e informazioni che fornirà al Gestore del Sistema durante il periodo di efficacia dell'Abilitazione.
        </li>
        <li>
          La Registrazione comporta l'integrale conoscenza ed accettazione delle presenti Regole.<br>
          <br>
          <strong>IMPORTANTE. L’iscrizione sulla piattaforma delle gare telematiche costituisce condizione indispensabile ai fini della partecipazione alle gare stesse. </strong><br>
          <br>
          Per motivi di sicurezza la piattaforma richiederà all’O.E. di modificare la propria password d’accesso ogni 3 (tre) mesi.<br>
          La password scelta dovrà essere lunga almeno 8 caratteri e contenere almeno:<br>
          <br>
          - Un carattere maiuscolo<br>
          - Un carattere minuscolo<br>
          - Un numero<br>
          - Un carattere speciale (es. !?-_*)<br><br>
        </li>
        <li>
          Le Utenze non utilizzate per più di 6 (sei) mesi saranno automaticamente disabilitate. Sarà possibile procedere alla riattivazione, in autonomia,
          cliccando sul tasto Sblocca che comparirà al tentativo d'accesso. L'operazione comporterà l’invio, sull’indirizzo e-mail indicato in fase di Registrazione,
          di un link per procedere allo sblocco dell’utenza.
        </li>
        <li>
          L'Utente ha a disposizione 5 (cinque) tentativi per inserire correttamente le credenziali d’accesso; al quinto tentativo errato, l'utenza sarà bloccata.
          L'operazione comporterà l’invio, sull’indirizzo e-mail indicato in fase di Registrazione, di un link per procedere allo sblocco dell’utenza
        </li>
        <li>
          Le credenziali dell'Account (User ID e Password) necessarie per l'accesso e la successiva partecipazione sono personali. Gli Utenti del Sistema sono tenuti a conservarle con la massima diligenza e a mantenerle segrete e riservate, a non divulgarle o comunque a non cederle a terzi, e a utilizzarle sotto la propria esclusiva responsabilità, nel rispetto dei principi di correttezza e buona fede, in modo da non recare pregiudizio al Sistema, agli Utenti ivi operanti e, in generale, a Terzi.<br>
          <br>
          A tal fine gli Utenti del Sistema adottano tutte le misure tecniche ed organizzative idonee a garantire il corretto utilizzo delle credenziali stesse e si obbligano a
          comunicarne immediatamente al Gestore del Sistema l'eventuale smarrimento, sottrazione e/o uso abusivo o improprio.
          I Soggetti abilitati prendono atto del fatto che la conoscenza dell'Account da parte di terzi consentirebbe a questi ultimi l'accesso al Sistema ed il compimento
          di azioni ed atti giuridicamente vincolanti perché direttamente imputati al Soggetto abilitato.
          In ogni caso, ogniqualvolta l'O.E. entra nel sistema,  riceve una e-mail che lo informa del relativo accesso. Ciò consente all’O.E., in caso di accesso da parte
          di soggetti non autorizzati, di ottenere la disabilitazione dell’utenza dell’intruso dopo averne dato comunicazione al Gestore del Sistema.
        </li>
        <li>
          Gli Utenti esonerano, pertanto, l'Ente ed il Gestore del Sistema da qualsivoglia responsabilità per conseguenze pregiudizievoli di qualsiasi natura o danni, diretti o indiretti, arrecati ad essi o a Terzi a causa e in conseguenza dell'utilizzo dell'Account e, in generale, derivanti dall'utilizzo abusivo, improprio o comunque pregiudizievole degli stessi, impegnandosi a risarcire l'Ente ed il Gestore del Sistema dei danni di qualsiasi natura dagli stessi subìti in conseguenza di tali eventi.
        </li>
        <li>
          In ogni caso, i soggetti abilitati prendono atto ed accettano che l'utilizzo abusivo, improprio o, comunque, pregiudizievole dell'Account comporta
          l'immediata revoca della Registrazione.
        </li>
        <li>
          In caso di sospetta divulgazione o di comunicazione ad altri soggetti, ovvero ancora in caso di sospetta perdita della riservatezza dell'Account,
          il titolare dell'Account deve immediatamente procedere alla modifica della Password con le modalità indicate nel portale,
          fermo rimanendo che in ogni caso tutti gli atti compiuti con l'utilizzazione dei codici saranno ritenuti giuridicamente vincolanti ed imputabili al titolare dell'Account.
        </li>
        <li>
          Nel caso in cui un soggetto abilitato abbia dimenticato le credenziali del proprio Account,
          dovrà richiedere al Gestore del Sistema le istruzioni necessarie per la generazione dei nuovi codici.
          In caso di sottrazione o furto da cui possa derivare l'abusiva divulgazione delle credenziali dell'Account, il soggetto abilitato, titolare dell'Account,
          dovrà comunicare tale circostanza al Gestore del Sistema per il tramite del personale di 'Help Desk.
          Il personale di Help Desk provvederà ad effettuate le opportune verifiche al fine di identificare il chiamante e di sospendere la validità dell'Account.
          Il soggetto abilitato dovrà quindi provvedere entro le successive 48 ore ad inviare copia della denuncia effettuata presso le competenti Autorità.
        </li>
        <li>
          L'Ente ed il Gestore del Sistema si riservano il diritto di modificare in qualunque momento l'Account attribuito ai soggetti Abilitati.
          In tal caso comunicheranno loro i nuovi codici attribuiti.
        </li>
        <li>
          Tutti i Soggetti abilitati sono tenuti a rispettare le norme legislative, regolamentari e contrattuali in tema di conservazione
          ed utilizzo dello strumento di Firma Digitale (specificatamente l'art. 28 del D.P.R. 445/2000) e qualsiasi altra istruzione impartita dal Certificatore
          che ha rilasciato lo strumento. Inoltre i soggetti Abilitati esonerano espressamente l'Ente ed il Gestore del Sistema da qualsiasi
          responsabilità per conseguenze pregiudizievoli di qualsiasi natura e per danni, diretti o indiretti, arrecati ad essi o a Terzi a
          causa dell'utilizzo dello strumento di Firma Digitale.
        </li>
        <li>
          L'utilizzo delle credenziali dell'Account vale ad attribuire incontestabilmente ai soggetti cui sono stati rilasciate,
          e per essi ai soggetti rappresentati, tutte le manifestazioni di volontà, ed in generale tutte le azioni, gli atti e i fatti posti in essere tramite il Sistema,
          comprese le operazioni effettuate nell'ambito della Gara telematica.<br>
          Le operazioni effettuate nell'ambito del Sistema di Gare Telematiche sono riferibili al soggetto abilitato e si intendono compiute nell'ora
          e nel giorno risultanti dalle Registrazioni di Sistema.
        </li>
        <li>
          Gli atti e i documenti per i quali è richiesta la sottoscrizione a mezzo di Firma Digitale non potranno considerarsi validi
          ed efficaci se non verranno sottoscritti secondo la modalità richiesta dalla normativa vigente.
        </li>
      </ol>
    </li>
    <li>
      <strong>PRESENTAZIONE DI ISTANZE PER ELENCHI DI FORNITORI, MANIFESTAZIONI D'INTERESSE, SISTEMA DINAMICO DI ACQUISIZIONE E MERCATO ELETTRONICO</strong>
      <ol>
        <li>
          Gli Operatori Economici che intendono partecipare alle iniziative per l'istituzione di Elenchi di Fornitori,
          Manifestazioni di Interesse, Sistemi Dinamici di Acquisizione e Mercato Elettronico,a seguire denominate semplicemente "iniziative",
          dovranno preventivamente identificarsi sul Sistema secondo la procedura di Registrazione.
        </li>
        <li>
          La partecipazione alle Iniziative svolte telematicamente è aperta a tutti gli Operatori Economici interessati, previa identificazione,
          in possesso dei requisiti richiesti dalla singola Iniziativa.
        </li>
        <li>
          <strong>Istanze di partecipazione</strong>
          Per accedere alla presentazione di un'istanza per le Iniziative di cui al punto 4.1 è necessario, previa individuazione dell’Iniziativa inserita nella piattaforma,
          cliccare sul pulsante <strong>"Richiedi Abilitazione"</strong>.
          Nei casi in cui le Iniziative prevedano una scadenza, detto pulsante sarà visibile fino al previsto termine di presentazione dell'istanza.
          Scaduto il termine non sarà più possibile accedere o terminare operazioni già iniziate.
          <strong>
            E' importante, dunque, che la presentazione dell'istanza sia effettuata prima della scadenza dei termini dell'Iniziativa.<br>
            Si specifica, inoltre, che la procedura di partecipazione accetterà solo files firmati digitalemente in formato P7M (CAdES).<br><br>
          </strong>
          Cliccando sul tasto <strong>Richiedi Abilitazione</strong> l'Utente è indirizzato alla pagina per la gestione dell'istanza di partecipazione all'Iniziativa stessa.<br>
          La pagina di gestione dell'Istanza di partecipazione conterrà l'elenco della documentazione richiesta dall'Ente, da presentare ai fini dell'abilitazione all'Iniziativa e, se richiesto,
          l'elenco delle Categorie Merceologiche (CPV) d'interesse selezionabili ai fini dell'Iscrizione.<br>
          <br>
          <strong>Per facilitare l'Operatore Economico nella gestione dell'invio della documentazione, il Sistema permette di:</strong><br>
          <br>
          - Scaricare eventuali modelli di documentazione<br>
          - Effettuare l'upload della documentazione<br>
          - Sostituire la documentazione caricata<br>
          - Visualizzare la documentazione caricata<br>
          - Inviare l'istanza<br><br>
          <strong>
            Dopo aver effettuato l'invio dell'istanza di partecipazione all'Iniziativa ed entro i termini di validità della stessa, l'Operatore Economico ha la possibilità di:
          </strong><br>
          - Visualizzare la documentazione caricata<br>
          - Sostituire e aggiornare la documentazione già inviata<br>
          - Revocare la propria partecipazione all'Inziativa<br>
          <br>
          <ol>
            <li>
              <strong>Invio della Documentazione di partecipazione</strong><br>
              Nella pagina di gestione dell'Istanza, cliccando sulla scheda "Allegati", è visualizzato l'elenco della documentazione
              richiesta dall'Ente con l'indicazione dei documenti obbligatori ai fini della partecipazione.<br>
              <br>
              Per inviare la documentazione richiesta è necessario cliccare sul tasto <img src="/img/folder.png" alt="Icona cartella" height="30"> corrispondente e selezionare il file desiderato.<br>
              <br>
              <strong>Si specifica che il Sistema accetta solo files firmati digitalmente in formato P7M (CAdES). Nel caso, per una singola richiesta, si renda necessario l'invio multiplo di files è necessario:</strong><br>
              <br>
              - firmare digitalmente i singoli files;<br>
              - creare un archivio compresso di tipo ZIP;<br>
              - firmare digitalmente l'archivio compresso;<br>
              - selezionare l'archivio firmato digitalmente;<br>
              <br>
              Selezionato il file, una barra di stato indicherà l'avanzamento dell'upload e al termine il Sistema provvederà a:<br>
              <br>
              - controllare l'integrità del file;<br>
              - verificare la validità formale della firma digitale del file<br><br>
            </li>
            <li>
              <strong>Selezione delle Categorie Merceologiche</strong><br>
              Se richiesto dall'Iniziativa, selezionare le Categorie Merceologiche di interesse. Sulla scheda "Categorie Merceologiche" è visualizzato l'elenco delle Categorie disponibili. Per scegliere le Categorie di interesse si dovrà inserire nella barra di ricerca una parola chiave relativa alla Categoria di interesse, e il motore di ricerca individuerà la Categoria contenente la parola chiave inserita. In alternativa, la Categoria potrà essere scelta cliccando sul tasto “Scegli da lista” e, successivamente, sull’indicatore relativo alla Categoria di interesse.
            </li>
            <li>
              <strong>Invio della partecipazione</strong><br>
              Caricata tutta la documentazione obbligatoria richiesta dall'Iniziativa e le Categorie Merceologiche d'interesse,
              sarà possibile cliccare sul tasto <strong>Salva ed Invia</strong> che consentirà il salvataggio e l'invio dell'Istanza all'Ente.
              Cliccando sul tasto <strong>Salva ed Invia</strong> la piattaforma verificherà la presenza di
              tutta la documentazione obbligatoria richiesta, e contestualmente invierà, tramite PEC, conferma di avvenuto invio dell'istanza di partecipazione all'Iniziativa.
            </li>
          </ol>
        </li>
        <li>
          La presentazione dell'istanza costituisce accettazione, da parte dell'Operatore Economico, di tutte le condizioni
          previste per la partecipazione all'iniziativa e della relativa documentazione.
        </li>
        <li>
          La presentazione dell'istanza di partecipazione sarà perfezionata a seguito di ricezione, da parte dell’O.E., della PEC di corretta ricezione dell'istanza,
          con indicazione dell'orario dell'acquisizione dell’istanza da parte del Sistema.
        </li>
        <li>
          L’O.E. che abbia presentato istanza potrà, entro il termine di scadenza dell’Iniziativa, revocarla  e/o aggiornarla.
          Una volta entrato nella propria area riservata e, successivamente, nell’area di dettaglio, l’O.E.
          potrà revocare la propria istanza cliccando sul tasto <strong>"REVOCA PARTECIPAZIONE"</strong>.
          Il Sistema invierà poi una PEC di conferma circa l’avvenuta ricezione della revoca stessa.<br>
          Un'istanza revocata sarà cancellata dal Sistema ed equivarrà a un'istanza non presentata.
        </li>
        <li>
          II Sistema non accetta istanze presentate dopo la data e l'orario stabiliti come termine di presentazione delle domande.<br>
        </li>
        <li>
          <strong>Valutazione delle istanze</strong><br>
          L'istanza sarà valutata dall'Ente entro 30 (trenta) giorni dalla data di presentazione della stessa.
          L’esito della valutazione sarà successivamente comunicato tramite PEC all'indirizzo indicato dall'Operatore Economico nella sua Anagrafica sul Sistema.
          In caso di mancato accoglimento dell'istanza, il messaggio PEC di comunicazione conterrà le relative motivazioni,
          e sarà possibile, entro i termini previsti dall'Iniziativa, provvedere a regolarizzare l'istanza e reinviarla.
        </li>
        <li>
          <strong>Metogologia di utilizzo della graduatoria</strong><br>
          La formazione dell'elenco non impegna in alcun modo l'Ente ad avviare procedimenti di affidamento lavori, servizi o forniture,
          poiché gli stessi verranno effettuati sulla base delle scelte programmate dallo stesso Ente e secondo le procedure di affidamento decise dai RUP di ciascun intervento.<br>
          <br>
          Sulla scorta delle singole Iniziative e del Regolamento adottato dell'Ente, per le procedure negoziate bandite e gestite tramite la piattaforma si provvederà
          a effettuare il sorteggio tra gli iscritti alle singole Iniziative, escludendo i soggetti già invitati precedentemente in altre procedure oppure
          solo quelli già aggiudicatari di altre gare negoziate nell'ambito della stessa iniziativa.<br>
          Sulla base della scelta dell’Ente, la selezione potrà avvenire anche applicando i filtri sulle Categorie Merceologiche (CPV), o (in caso di Lavori),
          sulle Certificazioni SOA dichiarate degli Operatori Economici. A tal fine, ricordando che, una volta estratti, gli O.E. non verranno invitati a
          successive procedure, si prega di specificare nel dettaglio, tramite la sezione "Categorie" della sezione personale della Piattaforma telematica,
          le CPV di interesse; si sottolinea, infatti, che l'inserimento, ad esempio, di una generica Categoria "45 - Lavori di costruzione" potrebbe portare
          ad essere invitati ad una gara che, per tipologia, non risulta di interesse per l'Operatore Economico, e quindi, successivamente,
          la sua esclusione da successive procedure di gara.<br>
          <br>
          Nel caso di utilizzo del filtro su Certificazioni SOA per lavori contenenti anche Categorie scorporabili, l'estrazione verrà effettuata tenendo conto
          esclusivamente della Categoria prevalente. Nel caso l'Operatore Economico estratto non avesse i requisiti per partecipare alla gara, perché non qualificato
          per le scorporabili, potrà inviare una richiesta finalizzata alla reintroduzione nell'elenco, mantenendo quindi la possibilità di essere estratto nelle successive gare.<br>
          <br>
          In ogni caso gli Operatori Economici saranno selezionati tra quelli attivi e valutati positivamente nell'ambito delle rispettive sezioni dell'elenco al momento dell'estrazione.
        </li>
      </ol>
    </li>
    <li>
      <strong>Avviso di gara</strong>
      <ol>
        <li>
          L'Avviso di gara è pubblicato sul portale nelle forme previste dalla Legge. Gli Operatori Economici iscritti saranno, in ogni caso, avvisati tramite e-mail,
          della pubblicazione di un nuovo Avviso di Gara attinente alla Categoria di iscrizione. Tutti gli altri Operatori Economici non iscritti che vengano a
          conoscenza dell'Avviso di Gara, per poter partecipare alla Gara stessa dovranno registrarsi sul portale delle gare telematiche dell'Ente,
          avendo cura di farlo entro il termine di scadenza per la presentazione delle offerte.
        </li>
        <li>
          Fatto salvo quanto diversamente previsto per le singole Gare telematiche, le richieste di chiarimenti relative alla Gara telematica sono obbligatoriamente inviate,
          entro il termine preventivamente indicato, direttamente dal portale dopo aver fatto accesso alla propria area riservata, digitando il testo della Richiesta nel
          relativo box di testo denominato “Richiesta di chiarimenti”.<br>
          Non saranno prese in considerazione le Richieste di Chiarimenti pervenute all'Ente con modalità differenti da quelle indicate nel bando e/o sul portale.
        </li>
        <li>
          Scaduto il termine per la presentazione delle Richieste di Chiarimenti, la risposta al quesito proposto verrà inviata sull’indirizzo e-mail indicato dall’O.E.
          E’ in facoltà dell’ente, inoltre, rendere pubblica la Richiesta e il relativo chiarimento, dopo aver opportunamente reso anonima la Richiesta stessa.
          E', inoltre, in facoltà dell'Ente pubblicare i chiarimenti in tempi differenti.
        </li>
      </ol>
    </li>
    <li>
      <strong>PARTECIPAZIONE ALLE GARE TELEMATICHE</strong>
      <ol>
        <li>
          La partecipazione alle procedure di scelta del contraente svolte telematicamente è aperta, previa identificazione, a tutti gli Operatori Economici interessati
          in possesso dei requisiti richiesti dalla singola procedura di gara. La registrazione al portale delle gare telematiche da parte degli Operatori Economici deve
          essere unica e sola, anche se il Sistema provvede a scartare autonomamente quelle già registrate.
        </li>
        <li>Gli operatori economici che intendono partecipare alla gara telematica dovranno identificarsi sul Sistema seguendo la procedura di registrazione se non lo hanno già fatto.
          A tal fine si tenga conto che: per identificarsi gli operatori economici dovranno completare la procedura di registrazione
          on line presente sul portalestesso nella sezione dedicata alla procedura di registrazione.
          Informazioni possono essere richieste direttamente al Call Center messo a disposizione dall'Ente.
        </li>
        <li>
          <strong>Pannello di gara</strong><br>
          Per accedere alla partecipazione di una gara telematica è necessario, prima di tutto, individuare la gara inserita nella piattaforma e cliccare sul pulsante <strong>"Partecipa"</strong>. 
          Detto pulsante sarà visibile fino alla scadenza dei termini di presentazione dell'offerta, scaduti i quali non sarà più possibile accedere o terminare operazioni già iniziate.<br>
          <strong>
            E' importante, dunque, che l'operazione di partecipazione sia effettuata prima della scadenza dei termini di gara.<br>
            Si specifica inoltre che la procedura di partecipazione accetterà solo files firmati digitalemte in formato P7M (CAdES).<br>
          </strong>
          Cliccando sul tasto <strong>Partecipa</strong> l'utente è indirizzato al pannello inerente la gestione della partecipazione alla gara.<br>
          <br>
          <strong>Per facilitare l'Operatore Economico nella gestione dell'invio della documentazione di gara, il Sistema, entro i termini di scadenza della gara, permette di:</strong><br>
          <br>
          - Effettuare l'upload della documentazione<br>
          - Sostituire la documentazione caricata<br>
          - Visualizzare la documentazione caricata<br>
          - Compilare l'offerta economica e/o tecnica<br>
          - Sostituire l'offerta economica e/o tecnica<br>
          - Compilare l'eventuale struttura del Raggruppamento di Impresa<br>
          - Modificare o eliminare l'eventuale struttura del Raggruppamento<br>
          - Inviare la partecipazione<br>
          <br>
          <strong>Dopo aver effettuato l'invio della partecipazione alla gara, ed entro i termini di scadenza della stessa, l'Operatore Economico ha la possibilità di:</strong><br>
          <br>
          - Visualizzare la documentazione caricata<br>
          - Sostituire la documentazione già inviata<br>
          - Modificare l'offerta economica e/o tecnica<br>
          - Modificare l'eventuale struttura del Raggruppamento di Impresa<br>
          - Revocare la propria partecipazione alla gara<br>
          <br>
          <strong>
            Dopo la scadenza della gara, l'Operatore Economico potrà visualizzare la documentazione inviata.
          </strong><br>
        </li>
        <li>
          <strong>Partecipazione in raggruppamento</strong><br>
          In caso di partecipazione alla gara in forma di <strong>Raggruppamento d'Impresa</strong>, l'onere della trasmissione alla Piattaforma della documentazione di gara
          è in carico unicamente all'Operatore Economico Capogruppo che, prima dell'invio della documentazione di gara, dovrà inserire nel Sistema le ditte facenti parte del Raggruppamento.
          La Registrazione sul portale <strong>è obbligatoria</strong> per l'Operatore Economico mandatario mentre non è obbligatoria per gli Operatori Economici mandanti.<br>
          Per inserire la struttura del raggruppamento sarà sufficiente cliccare sul comando <strong><img alt="Aggiungi" src="/img/add.png" style="height:25px; vertical-align:middle; width:25px">Aggiungi partecipante al raggruppamento&nbsp;</strong> e compilare i campi richiesti:<br>
          <br>
          - Codice Fiscale dell'Impresa<br>
          - Ragione Sociale<br>
          - Eventuale Identificativo Fiscale Estero<br>
          - Ruolo all'interno del Raggruppamento<br>
          <br>
          <strong>
            ATTENZIONE: Nel caso di partecipazioni in Raggruppamento si consiglia di inserire subito la struttura dello stesso,
            in quanto la modifica del Raggruppamento comporta le necessità di rigenerare eventuali offerte tecniche e/o economiche già formulate,
            con conseguente revoca delle eventuali trasmissioni o partecipazioni già inviate.
          </strong>
        </li>
        <li>
          <strong>Invio delle "Buste" di partecipazione</strong><br>
          Una "Busta" di partecipazione è un archivio compresso di tipo <strong>ZIP</strong> firmato digitalmente in formato <strong>P7M (CAdES)</strong> 
          contenente la documentazione di gara firmata digitalmente, laddove richiesta dal bando di gara.<br>
          Le Buste di partecipazione, a seconda dei casi, potranno essere:<br>
          - Documentazione Amministrativa<br>
          - Offerta Tecnica<br>
          - Offerta Economica<br>
          Per trasmettere un Busta è necessario cliccare sul tasto <strong>Carica la documentazione</strong> della corrispondente tipologia di documentazione e completare i seguenti step:<br>
          <table border="0" cellpadding="2" cellspacing="0">
            <tbody>
              <tr class="even">
                <td><img alt="Step 1" src="/gare/telematica2.0/img/step1.png" style="height:100px; width:100px"></td>
                <td><strong>Step 1</strong><br>
                <span style="background-color:rgb(243, 243, 243); color:rgb(0, 0, 51); font-family:tahoma,geneva,sans-serif; font-size:14px">Firmare digitalmente in formato&nbsp;</span><strong>P7M (CAdES)</strong><span style="background-color:rgb(243, 243, 243); color:rgb(0, 0, 51); font-family:tahoma,geneva,sans-serif; font-size:14px">&nbsp;tutta la documentazione richiesta.</span></td>
              </tr>
              <tr class="odd">
                <td><img alt="Step 2" src="/gare/telematica2.0/img/step2.png" style="height:100px; width:100px"></td>
                <td><strong>Step 2</strong><br>
                <span style="background-color:rgb(243, 243, 243); color:rgb(0, 0, 51); font-family:tahoma,geneva,sans-serif; font-size:14px">Creare un archivio compresso di tipo&nbsp;</span><strong>ZIP</strong><span style="background-color:rgb(243, 243, 243); color:rgb(0, 0, 51); font-family:tahoma,geneva,sans-serif; font-size:14px">&nbsp;contenente tutti i files firmati digitalmente.</span></td>
              </tr>
              <tr class="even">
                <td><img alt="Step 3" src="/gare/telematica2.0/img/step3.png" style="height:100px; width:100px"></td>
                <td><strong>Step 3</strong><br>
                <span style="background-color:rgb(243, 243, 243); color:rgb(0, 0, 51); font-family:tahoma,geneva,sans-serif; font-size:14px">Firmare digitalmente in formato&nbsp;</span><strong>P7M (CAdES)</strong><span style="background-color:rgb(243, 243, 243); color:rgb(0, 0, 51); font-family:tahoma,geneva,sans-serif; font-size:14px">&nbsp;l'archivio ZIP creato.</span></td>
              </tr>
              <tr class="odd">
                <td><img alt="Step 4" src="/gare/telematica2.0/img/step3.png" style="height:100px; width:100px"></td>
                <td><strong>Step 4</strong><br>
                  <span style="background-color:rgb(243, 243, 243); color:rgb(0, 0, 51); font-family:tahoma,geneva,sans-serif; font-size:14px">
                    Selezionare l'archivio firmato digitalmente</span><span style="background-color:rgb(255, 255, 255)">,&nbsp;</span>
                    <span style="background-color:rgb(243, 243, 243); color:rgb(0, 0, 51); font-family:tahoma,geneva,sans-serif; font-size:14px">
                      inserire una chiave personalizzata di almeno 12 caratteri e cliccare su&nbsp;</span><strong>INVIA</strong><span style="background-color:rgb(243, 243, 243); color:rgb(0, 0, 51); font-family:tahoma,geneva,sans-serif; font-size:14px">.</span>
                </td>
              </tr>
            </tbody>
          </table>
          <br>
          Per tutelare la segretezza della documentazione inviata, la Piattaforma richiede, al momento dell'invio della Busta,
          l'inserimento di una password di minimo 12 (dodici) caratteri che dovrà essere custodita dall'Operatore Economico per l’eventualità in cui,
          eccezionalmente, sia richiesta dalla Stazione Appaltante per accedere al contenuto della documentazione inviata in sede di apertura delle Buste.
          <br>
          <br>
          Una volta inviato il file, una barra di stato indicherà l'avanzamento dell'upload e al termine il Sistema provvederà a:<br>
          <br>
          1. controllare l'integrità del file;<br>
          2. verificare la validità formale della firma digitale del file;<br>
          3. Criptare, tramite Sistema di chiavi asincrone, il file.<br>
          <br><br>
          Per l'invio dell'<strong>Offerta Economica</strong> la Piattaforma prevede la compilazione obbligatoria della stessa sul Sistema.<br>
          Per compilare l'Offerta è necessario cliccare sul tasto <strong>Genera Offerta Economica</strong> e compilare i form presenti.<br>
          <br>
          Al termine della compilazione il Sistema genera il file PDF dell'Offerta inserita che l'Operatore Economico dovrà scaricare e firmare digitalmente in formato P7M con firma CAdES.<br>
          <br>
          <strong>ATTENZIONE: Per firmare digitalmente il file di offerta, selezionare direttamente il file dalla cartella di download.</strong><br>
          <br>
          Il file P7M contenente l'Offerta firmata digitalmente dovrà essere inviato alla piattaforma cliccando sul pulsante <strong>Carica la documentazione</strong>.<br> 
          Nel caso in cui il bando preveda la trasmissione di ulteriori documenti facenti parte dell'Offerta Economica, gli stessi dovranno essere inclusi, unitamente
          al file di offerta firmato digitalmente, in un archivio compresso in formato ZIP, a sua volta firmato digitalmente.<br>
          <br>
          Per l'invio dell'<strong>Offerta Tecnica</strong>, la Piattaforma, ove previsto, prevede la compilazione obbligatoria della stessa sul Sistema
          con modalità analoghe a quanto indicato per l'Offerta Economica.<br><br>
        </li>
        <li>
          <strong>Invio della partecipazione</strong><br>
          Una volta caricate tutte le Buste di gara, il Sistema renderà disponibile il pulsante <strong>"INVIA LA PARTECIPAZIONE"</strong> che consentirà l'invio la partecipazione alla gara.<br>
          Cliccando sul tasto <strong>INVIA LA PARTECIPAZIONE</strong> la piattaforma verificherà la presenza di tutte le "buste" richieste dal bando, genererà un file di sistema
          contenente le impronte hash (MD5, SHA1, SHA256) delle buste inviate a cui apporrà marcatura temporale, certificando quindi la data e l'ora certa di conferma di partecipazione,
          requisito, questo, indispensabile per attestare che l'O.E. ha inviato i files riguardanti la gara entro il termine stabilito dal Bando.
          All'uopo si specifica che il tempo del Sistema è sincronizzato sull'ora italiana riferita alla scala di tempo UTC (IEN), di cui al decreto del Ministro dell'industria,
          del Commercio e dell'Artigianato 30 novembre 1993, n. 591.<br>
          Il tempo del Sistema è aggiornato con un collegamento diretto con l'Istituto Galileo Ferraris di Torino, secondo le metodologie descritte sul sito dello stesso Istituto;<br>
          Infine il sistema invierà, tramite PEC, conferma di avvenuta partecipazione alla gara dell'Operatore Economico.
        </li>
        <li>
          Ove la gara abbia ad oggetto diverse tipologie di lavori, forniture o servizi, la Stazione Appaltante potrà, discrezionalmente,
          consentire la partecipazione per singoli Lotti ovvero imporre la partecipazione per tutti i Lotti in gara.
        </li>
        <li>
          L'Offerta presentata entro la data e l'ora di chiusura della gara è vincolante per il concorrente Operatore Economico e lo impegna a
          stipulare il contratto qualora risulti aggiudicatario in applicazione del criterio di aggiudicazione adottato e specificato negli atti di gara.
        </li>
        <li>
          La presentazione dell'Offerta costituisce accettazione, da parte del concorrente, di tutte le condizioni
          previste per la partecipazione alla gara e della relativa documentazione.
        </li>
        <li>
          La presentazione delle Offerte è compiuta quando l'Operatore Economico riceve dal Sistema la PEC di corretta ricezione dell'Offerta,
          con l’indicazione dell'orario di acquisizione della stessa sul Sistema.
        </li>
        <li>
          Entro i termini di presentazione dell'offerta, chi ha presentato un'Offerta può sempre ritirarla e/o sostituirla: l'Operatore Economico,
          una volta entrato nell'area riservata e nella gara, direttamente dal Sistema la potrà <strong>revocare</strong> cliccando sul tasto <strong>"REVOCA PARTECIPAZIONE"</strong>.
          Successivamente il Sistema invierà all’O.E. una PEC di conferma di avvenuta ricezione dell’istanza di revoca della partecipazione alla gara.<br>
          L'Offerta revocata sarà cancellata dal Sistema ed equivarrà a Offerta non presentata. Insieme all'Offerta sarà cancellata tutta la documentazione
          per l'ammissione alla gara nonché l'eventuale documentazione presentata a corredo dell'Offerta.<br>
          Se l'Operatore Economico intende <strong>sostituire</strong> la precedente Offerta, dovrà inviare, entro i termini di scadenza della gara,
          i nuovi files nelle modalità di invio sopra descritte, i quali sostituiranno integralmente quelli inviati precedentemente.<br>
        </li>
        <li>
          II Sistema non accetta Offerte presentate dopo la data e l'orario stabiliti quale termine di presentazione delle Offerte.
        </li>
        <li>In caso di R.T.I. o Consorzio, l'Impresa mandataria o designata tale dal R.T.I. o dal Consorzio opererà sul Sistema come unico Operatore abilitato
           a presentare la documentazione e le Offerte nell'ambito della procedura di gara in nome e per conto del R.T.I. o del Consorzio. La documentazione
           dovrà essere compilata e sottoscritta, con le norme previste dal Disciplinare, da tutte le Imprese partecipanti al Raggruppamento o Consorzio.<br>
           In caso di R.T.I. o Consorzio non costituito, l'Offerta Economica dovrà essere sottoscritta dai legali rappresentanti o procuratori di tutti i membri del R.T.I. o del Consorzio;
           la stessa dovrà contenere l'impegno a costituire il Raggruppamento o il Consorzio.<br>
           In caso di R.T.I. o Consorzio costituito, l'Offerta Economica dovrà essere sottoscritta dal legale rappresentante o procuratore della mandataria,
           e dovrà presentare l'atto di costituzione del R.T.I. o del Consorzio in originale elettronico firmato digitalmente o in scansione elettronica
           dell'originale cartaceo firmato digitalmente.
         </li>
       </ol>
    </li>
    <li>
      <strong>PROCEDURE DI GARA</strong>
      <ol>
        <li>
          Alla scadenza del termine stabilito per l'espletamento della gara, l'Ente procederà a verificare l'inoltro, da parte degli offerenti,
          dei documenti richiesti nella documentazione di gara (dichiarazioni, cauzione provvisoria, atto costitutivo R.T.I. etc.).
          L'Ente procederà quindi a verificare le dichiarazioni del concorrente circa il possesso dei requisiti previsti ai fini della partecipazione,
          e, di conseguenza, ad ammettere i concorrenti alla gara.<br>
          Naturalmente solo con riferimento ai concorrenti ammessi l'Ente procederà, ove previsto, ad aprire il file contenente le Offerte Tecniche,
          che saranno sottoposte a valutazione da parte della Commissione, e successivamente i files contenenti le Offerte Economiche.<br>
          Espletate le suddette operazioni, e sulla base dei punteggi acquisiti dai rispettivi Operatori Economici partecipanti alla gara, la Commissione stilerà la graduatoria provvisoria.
          La posizione, nella graduatoria provvisoria, dei singoli Operatori partecipanti sarà resa nota agli stessi tramite PEC.
        </li>
        <li>
          L'aggiudicazione è, in qualunque caso, da intendersi provvisoria ed è subordinata all'emanazione del provvedimento di aggiudicazione definitiva.
          L'aggiudicazione definitiva avverrà a seguito dell'espletamento degli adempimenti previsti dal disciplinare e dagli altri documenti di gara.
        </li>
        <li>Nel caso in cui, prima della scadenza della presentazione delle offerte di gara, l'Ente disponga delle modifiche e/o delle integrazioni
          (ad esempio circa la documentazione amministrativa da presentare), la variazione verrà pubblicata nell'area del portale internet relativa
          alla documentazione di gara, e ai concorrenti che hanno già presentato l'Offerta verrà inviata una comunicazione PEC contenente l’invito
           a prendere visione della modifica e a ripresentare l'Offerta (così come sopra specificato), integrata secondo le nuove disposizioni.
        </li>
        <li>Qualunque comunicazione relativa alla gara verrà effettuata tramite pubblicazione sul Portale, nell'area riguardante la gara stessa.
          Il Sistema inoltrerà analoga comunicazione via PEC, considerata in ogni caso non obbligatoria. Per tale finalità sta al singolo Operatore Economico
          l'onere di tenere aggiornati sul Sistema i propri recapiti PEC su cui ricevere tali comunicazioni.
        </li>
      </ol>
    </li>
    <li>
      <strong>UTILIZZAZIONE DEL SISTEMA - DOTAZIONI</strong>
      <ol>
        <li>
          L'accesso e la partecipazione alle Gare telematiche è riservato ai soli Soggetti abilitati conformemente a quanto disposto dalle presenti norme tecniche
          e deve avvenire nel rispetto dello stesso, dei Documenti della procedura di gara e delle istruzioni contenute all'interno del portale e/o di volta in volta comunicate dall'Ente, anche tramite il Gestore del Sistema.</li>
        <li>
          Al fine di poter utilizzare l'applicativo delle gare on-line, gli Utenti del Sistema dovranno dotarsi, a propria cura e a proprie spese, della strumentazione
          tecnica ed informatica software ed hardware, inclusi gli strumenti di Posta Elettronica Certificata e Firma Digitale ed i collegamenti alle
          linee di telecomunicazione necessari per il collegamento alla rete Internet e, in generale, per compiere le attività all'interno del Sistema.
        </li>
      </ol>
    </li>
    <li>
      <strong>GARANZIE, LIMITAZIONI DI RESPONSABILITÀ</strong>
      <ol>
        <li>
          Il Sistema si basa su una piattaforma tecnologica avanzata, sperimentata ed affidabile, realizzata con modalità e soluzioni tendenti a impedire
          l’apporto di variazioni sui documenti, sulle registrazioni di Sistema e sulle altre rappresentazioni informatiche e telematiche degli atti e delle
          operazioni compiute nell'ambito delle procedure. Il Gestore del Sistema si impegna a mantenere elevati standard di qualità e sicurezza nella fornitura del servizio.
        </li>
        <li>
          Salvi i casi di dolo o colpa grave, l'Ente ed il Gestore del Sistema non saranno in alcun caso ritenuti responsabili per qualunque genere di danno, diretto o indiretto,
          per lucro cessante o danno emergente, che dovessero subire gli Utenti, le Amministrazioni o i Terzi a causa o comunque in conseguenza dell'accesso, dell'utilizzo,
          del mancato utilizzo, del funzionamento o del mancato funzionamento del Sistema e dei servizi dallo stesso offerti.
        </li>
        <li>
          Tutti i contenuti del portale e, in generale, i servizi che si riferiscono al Sistema informatico di Gare telematiche che siano forniti dall'Ente e dal Gestore del
          Sistema sono resi disponibili e prestati così come risultano dal portale e dal Sistema.
        </li>
        <li>
          L'Ente ed il Gestore del Sistema non garantiscono la rispondenza del contenuto del Sito, ed in generale di tutti i servizi offerti dal Sistema, alle esigenze,
          necessità o aspettative, espresse o implicite, degli altri Utenti del Sistema.
        </li>
        <li>
          L'Ente ed il Gestore del Sistema non assumono alcuna responsabilità circa i contenuti di Siti Internet di terze parti cui si può
          accedere tramite i link posti all'interno del portale dell'Ente in quanto al di fuori della propria area di controllo.
        </li>
      </ol>
    </li>
    <li>
      <strong>MANLEVA</strong><br>
      Gli Utenti si impegnano a manlevare e a tenere indenne l'Ente ed il Gestore del Sistema, risarcendo qualunque pregiudizio, danno, costo e onere di qualsiasi natura,
      ivi comprese le eventuali spese legali, che dovessero essere sofferti da questi e/o da Terzi a causa di violazioni delle presenti Norme Tecniche, di un utilizzo
      scorretto od improprio del Sistema o della violazione della normativa vigente.
    </li>
    <li>
      <strong>NORMATIVA APPLICABILE</strong>
      <ol>
        <li>
          Le presenti Norme Tecniche operano nel rispetto ed in attuazione della normativa vigente in materia di acquisti di beni e servizi e di appalti di lavori pubblici
          della Pubblica Amministrazione e, in generale, dalla Legge italiana e comunitaria, nonché delle norme vigenti in materia di Amministrazione Digitale, PEC e Firma Digitale.
          Per quanto non espressamente indicato dalle presenti regole, le gare telematiche ed ogni atto o negozio giuridico posti in essere nell'ambito delle stesse
          si intendono disciplinati dalle disposizioni normative e regolamentari summenzionate.
        </li>
        <li>
          L'Ente si riserva, a proprio insindacabile giudizio, il diritto di apportare alle presenti Norme Tecniche, tutte le modifiche che si rendessero opportune e comunque
          necessarie ad assicurare le funzionalità del Sistema, nel rispetto delle regole di trasparenza, correttezza ed imparzialità dell'azione amministrativa.
          In tal caso l'avvenuta modifica verrà comunicata agli Operatori Economici abilitati a mezzo Posta Elettronica Certificata e/o mediante pubblicazione
          sul Sito delle modifiche apportate: in tal caso, l'Operatore Economico potrà chiedere di essere disabilitato per mezzo dell'apposito modulo di comunicazione
          presente sul portale, sottoscritto con Firma Digitale ed inviato all'indirizzo indicato sul portale. In caso di mancato recesso,
          le nuove Regole si considereranno automaticamente accettate e saranno applicabili dalla data indicata per la loro entrata in vigore.
        </li>
      </ol>
    </li>
    <li>
      <strong>TERMINI E CONDIZIONI DI UTILIZZO DEL SISTEMA</strong>
      <ol>
        <li>
          L'accesso e la partecipazione al Sistema comporta l'accettazione di tutti i termini, le condizioni di utilizzo
          e le avvertenze contenute nelle presenti norme tecniche e/o di quanto portato a conoscenza degli Utenti tramite
          la pubblicazione nel Sito e/o l'invio presso la casella di Posta Elettronica Certificata deisoggetti abilitati.
        </li>
        <li>
          L'Ente si riserva il diritto di modificare, a suo esclusivo e insindacabile giudizio, in qualsiasi momento e senza alcun preavviso,
          i termini, le condizioni e le avvertenze suddette. E' interamente a carico degli Utenti la responsabilità del controllo costante di detti termini,
          condizioni ed avvertenze.
        </li>
      </ol>
    </li>
    <li>
      <strong>REGOLE DI CONDOTTA</strong>
      <ol>
        <li>
          Gli Utenti del Sistema sono tenuti ad utilizzare il Sistema stesso secondo buona fede ed esclusivamente per i fini ammessi dalle presenti Norme Tecniche.
          Gli Operatori Economici abilitati sono responsabili per le violazioni delle disposizioni di legge e regolamentari in materia di acquisti di beni e servizi
          e appalti di lavori pubblici della Pubblica Amministrazione e per qualunque genere di illecito amministrativo, civile o penale.
        </li>
        <li>
          I soggetti abilitati si obbligano a porre in essere tutte le condotte necessarie ad evitare che attraverso il Sistema si attuino turbative nel corretto
          svolgimento dei Sistemi di negoziazione con particolare riferimento a condotte quali, a titolo esemplificativo e non esaustivo: la turbativa d'asta, le offerte fantasma,
          gli accordi di cartello.
        </li>
      </ol>
    </li>
    <li>
      <strong>ACCESSO AGLI ATTI</strong>
      <ol>
        <li>
          Il diritto di accesso di cui alla legge 7 agosto 1990 n. 241, per gli atti ed i documenti diversi da quelli già pubblicati e/o resi disponibili sul portale,
          si esercita, previa autorizzazione specifica concessa dal Responsabile del Procedimento indicato nei documenti della procedura, con l'interrogazione delle
          registrazioni di Sistema che contengono la documentazione in formato elettronico degli atti della procedura. L'invio, al soggetto che vi abbia titolo,
          di copia autentica della documentazione è eseguito dall' Ente con l'invio del documento richiesto alla casella di Posta Elettronica Certificata comunicata
          al Sistema dal soggetto abilitato, ovvero da questo indicata al momento della presentazione della richiesta di accesso. In ogni caso sono legittimati ad
          accedere agli atti della procedura i soli soggetti abilitati.
        </li>
        <li>
          Le interrogazioni delle registrazioni di Sistema di cui al comma 1 possono essere effettuate soltanto all'esito della aggiudicazione definitiva.
        </li>
      </ol>
    </li>
    <li>
      <strong>FORO COMPETENTE</strong><br>
      Per qualsiasi controversia dovesse insorgere in merito alla esecuzione, interpretazione, attuazione e modificazione delle presenti Norme Tecniche, gli Utenti
      convengono circa la competenza esclusiva del Foro di <?= $_SESSION["ente"]["citta"] ?>.<br><br>
    </li>
    <li>
      <strong>PRIVACY</strong><br>
      I dati personali forniti formeranno oggetto di trattamento nel rispetto della normativa di cui al GDPR 2016/679 (GDPR 2018) e del D.lgs. 196/2003.<br>
      L'Ente è Titolare dei Trattamenti di dati effettuati per il corretto funzionamento del Sistema e per le finalità di volta in volta indicate nelle informative rese agli interessati
      al momento della raccolta dei dati. Dette informative descrivono anche l'ambito di comunicazione e diffusione dei dati.<br>
      Il Gestore del sistema è il Responsabile del trattamento dei dati con particolare riferimento alla materia della sicurezza del Sistema ed al rispetto delle misure minime di sicurezza.
      L'accesso alle aree riservate del Sistema tramite anche il solo account comporta l'accettazione delle informative mostrate all'Utente in sede di abilitazione e/o registrazione
      e il rilascio del consenso per i Trattamenti, ove ciò occorra per finalità legate alla comunicazione e diffusione dei dati.<br>
      <ol>
        <li>
          <strong>Documento tecnico di conformità</strong><br>
          Al fine della conformità alla normativa in materia di corretto trattamento dei dati personali, l’applicativo consta di una serie di elementi elencati di seguito.
          In particolare, si sottolinea che:<br>
          - le operazioni sui dati personali sono effettuate con modalità e soluzioni tecniche che assicurano confidenzialità, integrità e disponibilità dei dati,
            in coerenza con le misure di sicurezza espressamente previste nel Codice Privacy e nel relativo Allegato B, nonché nel Regolamento europeo (GDPR n. 679/2016)
            in materia di data protection;<br>
          - la riservatezza dei dati trattati è garantita dalle specifiche procedure di sicurezza relative al software “TuttoGare”;<br>
          - le misure a protezione dei dati trattati e di sicurezza sono in grado di<br>
          &nbsp;&nbsp;&nbsp;&nbsp;- proteggere i dati durante la memorizzazione e la trasmissione;<br>
          &nbsp;&nbsp;&nbsp;&nbsp;- consentire azioni preventive, correttive e mitigatrici contro le vulnerabilità o gli incidenti rilevati che possono rappresentare un pericolo per i dati;<br>
          &nbsp;&nbsp;&nbsp;&nbsp;- fornire al cliente strumenti di valutazione per verificare e documentare l’efficacia delle policy di sicurezza;<br>
          Studio Amica, per migliorare e gestire la propria infrastruttura tecnologica, attua politiche di sicurezza relativamente a:<br>
          - Gestione Firewall e Connettività<br>
          - Gestione policy password<br>
          - Sistema Software “TuttoGare”<br>
          - Gestione Backup;<br>
          - esistono meccanismi di recupero dei dati che consentono di ripristinare l’accesso ai dati qualora un incidente informatico ne pregiudichi la disponibilità;<br>
          - nel rispetto del principio di privacy by design, l’applicativo consente una raccolta (attraverso i form elettronici presenti) di un numero di campi limitato allo stretto necessario per il trattamento dei dati personali;<br>
          - la quantità di tempo per cui i dati personali vengono conservati o elaborati può essere limitata al raggiungimento delle finalità perseguite dalla Stazione Appaltante;<br>
          - si utilizzano soluzioni “crittografiche” sui dati più sensibili (anche durante la memorizzazione e la trasmissione);<br>
          - nel rispetto del principio di Privacy by Default sono stati sviluppati idonei sistemi di autenticazione e di autorizzazione per gli incaricati (utenti che utilizzano il servizio) in funzione dei ruoli e delle esigenze di accesso e trattamento (ad es., in relazione alla possibilità di consultazione, modifica e integrazione dei dati, ect.), che consente una riduzione, nell’utilizzo dell’applicativo, del numero di persone autorizzate che possono accedere ai dati personali;<br>
          - il software sviluppato consente il trattamento dei dati personali, anche eventualmente di natura giudiziaria, solo a responsabili o incaricati dotati di credenziali di autenticazione che abbiano superato una procedura di autenticazione relativa a uno specifico trattamento o a un insieme di trattamenti (ciò consente di fornire le garanzie rispetto all’accesso da parte dei vari operatori specificatamente autorizzati);<br>
          - le credenziali di autenticazione consistono in un codice per l'identificazione del responsabile o dell'incaricato associato a una parola chiave riservata (di almeno 8 caratteri alfanumerici e con specifici requisiti di complessità) conosciuta solamente dal medesimo;<br>
          - ad ogni responsabile o incaricato possono essere assegnate o associate individualmente una o più credenziali per l'autenticazione per l’accesso al programma informatico (creazione di profili differenziati per l'accesso agli archivi e procedure di autenticazione distinte per i vari profili individuati);<br>
          - le parole chiave che si possono generare sono composte da almeno otto caratteri alfanumerici e con le caratteristiche di complessità (maiuscole, minuscole, numeri e simboli) indicate nell’Allegato B del d. lgs. 196/2003 (essa può essere modificata dall’utente al primo utilizzo e, a cadenza periodica di almeno 90 giorni, viene imposto il successivo cambio);<br>
          - il codice per l'identificazione, laddove utilizzato, non può e, quindi, non viene assegnato ad altri incaricati, neppure in tempi diversi;<br>
          - le credenziali di autenticazione non utilizzate da almeno sei mesi sono automaticamente disattivate dal sistema;<br>
          - le credenziali possono essere disattivate anche in caso di perdita della qualità che consentiva al responsabile o all'incaricato l'accesso ai dati personali;<br>
          - quando per i responsabili o incaricati che possono accedere ai dati immagazzinati nel software sono individuati profili di autorizzazione di ambito diverso, il software permette l’utilizzazione di uno specifico e distinto sistema di autorizzazione in base alle informazioni che ciascuno può trattare in funzione dei ruoli e delle esigenze di accesso (l’accesso ai data base e all’applicativo, infatti, è regolato in base a precise autorizzazioni e alle esigenze di accesso di ciascun incaricato ai dati archiviati secondo il principio del “need to know”);<br>
          - il sistema di gestione dei dati consente la facile integrazione con un apposito sistema di back up e Disaster Recovery dei dati (anche nell’ambito dell’adozione di un più generale piano di continuità operativa) che garantisce il ripristino dell'accesso ai dati in caso di danneggiamento degli stessi o degli strumenti elettronici, in tempi certi compatibili con i diritti degli interessati e non superiori a sette giorni (nello specifico il back up sul sistema di Disaster Recovery viene effettuato in tempo reale, con una retention di 180 giorni);<br>
          - il software è composto da una struttura modulare delle varie cartelle elettroniche in modo da garantire la separazione (logica a livello di interfaccia) fra i diversi operatori rispetto alle finalità del trattamento e ai soggetti che vi accedono (distinti profili autorizzativi dei soggetti abilitati all’accesso);<br>
          - nell’utilizzo di sistemi di memorizzazione e archiviazione dei dati sono stati implementati idonei accorgimenti per la protezione dei dati registrati rispetto ai rischi di accesso abusivo, furto o smarrimento dei supporti di memorizzazione attraverso l’applicazione (anche parziale) di tecnologie crittografiche su file system o data base ovvero tramite l’adozione di altre misure di protezione che rendono i dati inintelligibili ai soggetti non legittimati;<br>
          - il software è dotato di un sistema di tracciabilità degli accessi e delle operazioni effettuate e consente un audit ex post degli accessi agli archivi contenenti i documenti per il controllo degli accessi al database e per il rilevamento di eventuali anomalie. Il processo di management dei log è in grado di rappresentare con completezza, per una determinata profondità temporale - che può essere opportunamente commisurata alle esigenze di controllo sul corretto utilizzo della base di dati e degli accessi da parte del titolare del trattamento - l'insieme delle operazioni effettuate sui documenti ed è in grado garantire l'inalterabilità dei log memorizzati (viene apposta una marcatura temporale sui log di sistema).<br>
          <br>
          <strong>Scheda riepilogativa delle principali misure di sicurezza a norma privacy</strong><br>
          <table>
          <tr><td colspan="2"><strong>SISTEMA DI AUTENTICAZIONE INFORMATICA</strong></td></tr>
          <tr><td>Login e password personali  </td><td>Conforme </td></tr>
          <tr><td>User e Password  </td><td>Conforme </td></tr>
          <tr><td>Complessità della parola chiave  </td><td>Conforme </td></tr>
          <tr><td>Periodo di validità della parola chiave </td><td>Conforme</td></tr>
          <tr><td>Disabilitazione automatica dopo 6 mesi  </td><td>Conforme </td></tr>
          <tr><td>Disabilitazione automatica dopo breve periodo per accesso inutilizzato  </td><td>Conforme </td></tr>
          <tr><td>Idonei sistemi di autenticazione per gli incaricati in funzione dei ruoli e delle esigenze di accesso e trattamento  </td><td>Conforme</td></tr>
          <tr><td colspan="2"><strong>SISTEMA DI AUTORIZZAZIONE</strong></td></tr>
          <tr><td>Profili Utenti  </td><td>Conforme </td></tr>
          <tr><td>Idonei sistemi di autorizzazione per gli incaricati in funzione dei ruoli e delle esigenze di accesso e trattamento </td><td>Conforme</td></tr>
          <tr><td colspan="2"><strong>ULTERIORI MISURE </strong></td></tr>
          <tr><td>Tempi di ripristino dei dati (back up e restore dei dati)    </td><td>Conforme </td></tr>
          <tr><td>Possibilità di riaprire nel tempo il documento informatico e la relativa immagine </td><td>Conforme</td></tr>
          <tr><td>Dati giudiziari o dati di gara in cifratura o tabelle relazionali in cui il dato non è individuato immediatamente </td><td>Conforme </td></tr>
          <tr><td>Minimizzazione nella raccolta dei dati secondo il principio della Privacy by Design e by Default </td><td>Conforme</td></tr>
          <tr><td>Corretta classificazione e fascicolazione dei documenti conservati </td><td>Conforme</td></tr>
          <tr><td>Nomina del cloud provider esterno a “Responsabile del trattamento” </td><td>Conforme</td></tr>
          <tr><td colspan="2"><strong>CONTROLLI </strong></td></tr>
          <tr><td>Controllo dell’accesso degli strumenti utilizzati </td><td>Conforme </td></tr>
          <tr><td>Controllo dei supporti dei dati </td><td>Conforme </td></tr>
          <tr><td>Controllo delle memorie </td><td>Conforme </td></tr>
          <tr><td>Controllo delle utilizzazioni </td><td>Conforme </td></tr>
          <tr><td>Controllo della comunicazione </td><td>Conforme </td></tr>
          <tr><td>Controllo a posteriori dell’accesso ai dati </td><td>Conforme </td></tr>
          <tr><td>Controllo del trasferimento (intercettazione) </td><td>Conforme </td></tr>
          <tr><td>Controllo della disponibilità (back-up) </td><td>Conforme </td></tr>
          <tr><td>Controllo dei log di accesso  </td><td>Conforme </td></tr>
          </table>


        </li>
      </ol>
    </li>
  </ol>
</p>
<p>
  <strong>GLOSSARIO</strong><br>
  <br>
  Nell'ambito delle presenti norme tecniche ciascuno dei seguenti termini in grassetto assume il significato a fianco riportato:
  - S.A.: Stazione Appaltante<br>
  - O.E.: Operatore Economico<br>
  - Abilitazione: il risultato finale di un procedimento che consente l'accesso e la partecipazione degli Operatori Economici abilitati al Sistema informatico, alle procedura di gara on-line;<br>
  - Account: attraverso il meccanismo dell'account, il Sistema mette a disposizione dell'Utente un ambiente con contenuti e funzionalità personalizzabili, oltre ad un conveniente grado di isolamento dalle altre utenze parallele. Il Sistema rilascia dei codici personali di identificazione costituiti da User ID e Password che consentono ai soggetti abilitati l'accesso al Sistema e ai servizi offerti;<br>
  - Avviso di Gara: l'avviso di gara avente ad oggetto la procedura di scelta del contraente attuata attraverso la gara telematica;<br>
  - PEC dell'Operatore Economico - (Casella di Posta Elettronica Certificata): la casella di Posta Elettronica Certificata comunicata al Sistema, al momento della presentazione della domanda di abilitazione e destinata esclusivamente alle comunicazioni, alle richieste e agli inviti inerenti l'attività svolta nel Sistema;<br>
  - Firma digitale: il risultato della procedura informatica (validazione) fondata su un Sistema di chiavi asimmetriche a coppia, basata su di un certificato qualificato rilasciato da un certificatore accreditato, e generata mediante un dispositivo per la creazione di una firma sicura, ai sensi di quanto previsto dall'articolo 38, comma 2 del D.P.R. 445/2000;<br>
  - Gare Telematiche (Gare on line): le procedure di scelta del contraente attuate in via elettronica e telematica per l'approvvigionamento di beni e/o servizi e/o lavori pubblici da parte dell'Ente, secondo le modalità indicate nelle presenti Regole e nei documenti della procedura;<br>
  - Gestore del Sistema: Studio Amica Società Cooperativa, di cui si avvale l'Ente per la gestione tecnica del Sistema delle gare telematiche;<br>
  - Invito: l'invito trasmesso, con le modalità e nei termini descritti nelle presenti Norme Tecniche, a tutti gli Operatori Economici abilitati che abbiano manifestato interesse a partecipare alla singola Gara telematica.<br>
  - Norme Tecniche: le presenti regole tecniche delle gare telematiche, contenenti i termini e le condizioni che disciplinano l'accesso e la partecipazione al Sistema;<br>
  - Portale: sito web, comunicato dall'Ente ovvero dal Gestore del Sistema, dove sono resi  disponibili i servizi e gli strumenti tecnologici necessari per l'attuazione delle Gare telematiche;<br>
  - Procedure Telematiche di acquisto: le procedure di gara telematica;<br>
  - Processo di Autorizzazione: la modalità informatica di verifica della correttezza e della validità dell'Account;<br>
  - Registrazione: il risultato del procedimento che consente l'accesso e la partecipazione degli Operatori Economici abilitati al Sistema informatico per le Procedure delle gare on-line;<br>
  - Registrazioni di Sistema: le risultanze degli archivi elettronici contenenti gli atti, i dati, i documenti e le informazioni relative alle Gare telematiche;<br>
  - Regole: le presenti Norme Tecniche delle gare telematiche, contenenti i termini e le condizioni che disciplinano l'accesso e la partecipazione al Sistema;<br>
  - Responsabile del Procedimento: ogni soggetto individuato tale, anche ai sensi della L.n.241/90;<br>
  - R.T.I.: Raggruppamento Temporaneo d'Impresa;<br>
  - Sistema: il Sistema Informatico per le Procedure telematiche di acquisto che supporta l'operatività delle Gare telematiche;<br>
  - Piattaforma: portale di e-procurement per lo svolgimento delle gare in modalità telematica;<br>
  - SUA: Stazione Unica Appaltante;<br>
  - Utente del Sistema: ogni soggetto che opera nel Sistema, ivi compresi la SUA, il Gestore del Sistema, i Responsabili dei diversi procedimenti, nonché qualsivoglia altro soggetto abilitato con un Account.<br>

</p>
</div>
