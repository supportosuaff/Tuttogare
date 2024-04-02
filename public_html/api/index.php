<?
session_start();
if(isset($_SESSION["ente"])){
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$bind = array();
	$bind[":codice"] = $_SESSION["ente"]["codice"];
	$sql_token = "SELECT dominio,token FROM b_enti WHERE codice = :codice ";
	$ris_token = $pdo->bindAndExec($sql_token,$bind);
	if($ris_token->rowCount()>0){
		$ente_p = $ris_token->fetch(PDO::FETCH_ASSOC);
		if ($ente_p["token"]=="") {
			$token = array();
			$token["token"] = sha1($ente_p["dominio"].time());
			$token["codice"] = $ente["codice"];
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_enti";
			$salva->operazione = "UPDATE";
			$salva->oggetto = $token;
			$codice = $salva->save();
			$token = $token["token"];
		} else {
			$token = $ente_p["token"];
		}

	?>
	<style>
		.parameters{
			width:100%;
		}
		.parameters td {
			text-align: center;
		}
	</style>
	<h1>API</h1>
	<div>
		<br/>
		WebService SOAP raggiungibile all'indirizzo <b>https://gare.comune.roma.it/api/service.php?WSDL</b> per l'estrazione delle le gare dell'ente, con possibilit&agrave; di applicare filtri tramite uno o pi&ugrave parametri.
		La risposta del WebService sar&agrave; in formato JSON<br>
		<br>
		<strong>Struttura JSON risposta</strong>
<code>
<pre  style="border:1px solid #000; padding:10px; height: 300px; overflow:scroll">
{
	"gara":
	{
		"codice",
		"codice_ente",
		"codice_gestore",
		"id",
		"stato",
		"numero_atto_indizione",
		"data_atto_indizione",
		"estremi_progetto",
		"data_validazione",
		"validatore",
		"rup",
		"struttura_proponente",
		"responsabile_struttura",
		"oggetto",
		"descrizione",
		"somme_disponibili",
		"prezzoBase",
		"cig",
		"cup",
		"nuts",
		"ribasso",
		"importoAggiudicazione",
		"data_pubblicazione",
		"data_accesso",
		"data_scadenza",
		"data_apertura",
		"numero_atto_commissione",
		"numero_atto_seggio",
		"data_atto_commissione",
		"data_atto_seggio",
		"allegato_atto_commissione",
		"allegato_atto_seggio",
		"numero_atto_esito",
		"data_atto_esito",
		"allegati_esito",
		"annullata",
		"numero_annullamento",
		"data_annullamento",
		"deserta",
		"tipologia",
		"criterio",
		"procedura",
		"fase",
		"modalita",
		"flag_gestione_autonoma",
		"url"
	}
	"importi": {
		{
			"tipologia",
			"importo_base",
			"importo_oneri_ribasso",
			"importo_oneri_no_ribasso",
			"importo_personale"
		}
	}
	"cpv": {
		{
			"codice",
			"codice_completo",
			"descrizione"
		}
	}
	"allegati": {
		{
			"codice",
			"sezione",
			"nome_file",
			"titolo",
			"descrizione",
			"url",
			"timestamp"
		}
	},
	"partecipanti": {
		{
    	"codice",
      "partita_iva",
      "ragione_sociale",
      "identificativoEstero",
      "pec",
      "tipo",
      "primo",
      "anomalia",
      "anomalia_facoltativa",
      "verifica",
      "ammesso",
      "escluso",
      "motivazione",
			"motivazione_anomalia",
			"codice_operatore",
			"codice_utente",
			"email",
			"pec",
			"indirizzo_legale",
			"citta_legale",
			"provincia_legale",
			"regione_legale",
			"stato_legale",
			"indirizzo_operativa",
			"citta_operativa",
			"provincia_operativa",
			"regione_operativa",
			"stato_operativa",
      "raggruppamento": {
       	{
          "codice",
          "partita_iva",
          "ragione_sociale",
          "identificativoEstero",
          "pec",
          "tipo"
        }
      }
    }
	},
	"invitati": {
		{
			"partita_iva",
      "ragione_sociale",
			"pec",
			"codice_operatore",
			"codice_utente"
		}
	}
}
</pre>
</code>
		<!-- <br>Token: <b><?=$token?></b>. -->
		<br/>
		<br/>
		<table class="parameters">
			<thead>
				<tr>
					<th colspan="3">Parametri richiesta</th>
				</tr>
				<tr>
					<th>Nome</th><th>Descrizione</th><th>Tipo</th>
				</tr>
			</thead>
			<tbody>
				<tr><td><strong>token* (Obbligatorio)</strong></td><td>Token di riconoscimento</td><td>stringa</td></tr>
				<tr><td>tipologia</td><td>Tipologia di gara</td><td>intero</td></tr>
				<tr><td>modalita</td><td>Modalit&agrave di gara</td><td>intero</td></tr>
				<tr><td>criterio</td><td>Criterio di aggiudicazione</td><td>intero</td></tr>
				<tr><td>procedura</td><td>Procedura</td><td>intero</td></tr>
				<tr><td>codice</td><td>Codice di gara</td><td>intero</td></tr>
				<tr><td>cod_minimo</td><td>Codice minimo di gara</td><td>intero</td></tr>
				<tr><td>cod_massimo</td><td>Codice massimo di gara</td><td>intero</td></tr>
				<tr><td>stato</td><td>Stato di gara</td><td>intero</td></tr>
				<tr><td>stato_minimo</td><td>Stato minimo di gara</td><td>intero</td></tr>
				<tr><td>stato_massimo</td><td>Stato massimo di gara</td><td>intero</td></tr>
				<tr><td>cup</td><td>CUP</td><td>intero</td></tr>
				<tr><td>cig</td><td>CIG</td><td>intero</td></tr>
				<tr><td>avvisi</td><td>Avvisi di gara</td><td>boolean</td></tr>
				<tr><td>quesiti</td><td>Quesiti di gara</td><td>boolean</td></tr>
				<tr><td>data</td><td>Data di interesse <i>Array</i></td><td>dateType</td></tr>
				<tr><td>typeDate</td><td>Tipo di data richiesta</td><td>stringa</td></tr>
				<tr><td>startDate</td><td>Data d&#39;inizio</td><td>stringa</td></tr>
				<tr><td>endDate</td><td>Data di fine</td><td>stringa</td></tr>
			</tbody>
		</table>
		<br/>
		<strong>Vocabolari filtri</strong><br>
		<table class="parameters">
			<thead>
				<tr><th colspan="2">Tipologia</th></tr>
				<tr><th>Valore</th><th>Descrizione</th></tr>
			</thead>
			<tbody>
				<?
				$sql_tipologia = "SELECT codice, tipologia FROM b_tipologie WHERE 1";
				$ris_tipologia = $pdo->query($sql_tipologia);
				if($ris_tipologia->rowCount()>0){
					while($tipo = $ris_tipologia->fetch(PDO::FETCH_ASSOC)){
						echo "<tr><td style=\"width: 120px\">".$tipo["codice"]."</td><td>".$tipo["tipologia"]."</td></tr>";
					}
				}
				?>
			</tbody>
		</table>
		<br/>
		<table class="parameters">
			<thead>
				<tr><th colspan="2">Modalita</th></tr>
				<tr><th>Valore</th><th>Descrizione</th></tr>
			</thead>
			<tbody>
				<?
				$sql_modalita = "SELECT codice, modalita FROM b_modalita WHERE 1";
				$ris_modalita = $pdo->query($sql_modalita);
				if($ris_modalita->rowCount()>0){
					while($modalita = $ris_modalita->fetch(PDO::FETCH_ASSOC)){
						echo "<tr><td style=\"width: 120px\">".$modalita["codice"]."</td><td>".$modalita["modalita"]."</td></tr>";
					}
				}
				?>
			</tbody>
		</table>
		<br/>
		<table class="parameters">
			<thead>
				<tr><th colspan="3">Criterio</th></tr>
				<tr><th>Valore</th><th>Descrizione</th><th>Riferimento Normativo</th></tr>
			</thead>
			<tbody>
				<?
				$sql_criterio = "SELECT codice, criterio, riferimento_normativo FROM b_criteri WHERE 1";
				$ris_criterio = $pdo->query($sql_criterio);
				if($ris_criterio->rowCount()>0){
					while($criterio = $ris_criterio->fetch(PDO::FETCH_ASSOC)){
						echo "<tr><td style=\"width: 120px\">".$criterio["codice"]."</td><td>".$criterio["criterio"]."</td><td>".$criterio["riferimento_normativo"]."</td></tr>";
					}
				}
				?>
			</tbody>
		</table>
		<br/>
		<table class="parameters">
			<thead>
				<tr><th colspan="3">Procedura</th></tr>
				<tr><th>Valore</th><th>Descrizione</th><th>Riferimento Normativo</th></tr>
			</thead>
			<tbody>
				<?
				$sql_procedure = "SELECT codice, nome,riferimento_normativo FROM b_procedure WHERE 1";
				$ris_procedure = $pdo->query($sql_procedure);
				if($ris_procedure->rowCount()>0){
					while($procedure = $ris_procedure->fetch(PDO::FETCH_ASSOC)){
						echo "<tr><td style=\"width: 120px\">".$procedure["codice"]."</td><td>".$procedure["nome"]."</td><td>".$procedure["riferimento_normativo"]."</td></tr>";
					}
				}
				?>
			</tbody>
		</table>
		<br/>
		In merito al codice di gara, il sistema prevede di selezionare una singola gara utilizzando il solo campo <i>codice</i> oppure permette di selezionare una serie di gare inserendo un codice minimo, identificato dal parametro <i>cod_minimo</i>, e/o un codice massimo, identificato dal parametro <i>cod_massimo</i>.
		<table class="parameters">
			<thead>
				<tr><th colspan="2">Codice</th></tr>
				<tr><th>Valore</th><th>Descrizione</th></tr>
			</thead>
			<tbody>
				<tr><td style="width: 120px">codice</td><td>Dettaglio sulla singola gara</td></tr>
				<tr><td style="width: 120px">cod_minimo</td><td>Codice minimo di gara</td></tr>
				<tr><td style="width: 120px">cod_massimo</td><td>Codice massimo di gara</td></tr>
			</tbody>
		</table>
		<br/>
		Anche per lo stato di gara sar&agrave possibile richiedere un singolo stato oppure sar&agrave possibile richiedere tutte le gare che si trovano in uno stato di gara compreso tra due valori, <i>stato_minimo</i> e/o <i>stato_massimo</i>. Gli stati di gara consentiti saranno:
		<table class="parameters">
			<thead>
				<tr><th colspan="3">Stato</th></tr>
				<tr><th>Valore</th><th>Descrizione</th><th width="1"></th></tr>
			</thead>
			<tbody>
				<?
				$sql_stati = "SELECT fase, titolo, colore FROM b_stati_gare WHERE fase > 2 ORDER BY fase";
				$ris_stati = $pdo->query($sql_stati);
				if($ris_stati->rowCount()>0){
					while($stati = $ris_stati->fetch(PDO::FETCH_ASSOC)){
						echo "<tr><td style=\"width: 120px\">".$stati["fase"]."</td><td>".$stati["titolo"]."</td><td width=\"1\" style=\"background-color:#".$stati["colore"]."\"></td></tr>";
					}
				}
				?>
			</tbody>
		</table>
		<br/>
		&Egrave prevista la possibilit&agrave di selezionare una gara inserendo il numero di CIG o CUP corrispondente.
		<table class="parameters">
			<thead>
				<tr><th colspan="2">CIG - CUP</th></tr>
				<tr><th>Valore</th><th>Descrizione</th></tr>
			</thead>
			<tbody>
				<tr><td style="width: 120px">cig</td><td>Dettaglio sulla gara con il CIG richiesto</td></tr>
				<tr><td style="width: 120px">cup</td><td>Dettaglio sulla gara con il CUP richiesto</td></tr>
			</tbody>
		</table>
		<br/>
		Il sistema, normalmente, non visualizza gli avvisi di gara o i quesiti corrispondenti, ma sar&agrave comunque possibile attivarli specificando il rispettivo parametro con il valore true.
		<table class="parameters">
			<thead>
				<tr><th colspan="2">Avvisi e Quesiti</th></tr>
				<tr><th>Valore</th><th>Descrizione</th></tr>
			</thead>
			<tbody>
				<tr><td style="width: 120px">true</td><td>Visualizzazione degli eventuali avvisi di gara</td></tr>
				<tr><td style="width: 120px">true</td><td>Visualizzazione degli eventuali quesiti di gara</td></tr>
			</tbody>
		</table>
		<br/>
		Riguardo alla data di gara, per selezionare una specifica data sar&agrave necessario definire innanzitutto a quale debba fare riferimento sulla base dei valori proposti nella tabella seguente, poi occorrer&agrave inserire una data di inizio ed una di fine nel formato <i>YYYY-MM-DD</i>. L&#39;inserimento del solo valore <i>typeDate</i> senza le rispettive date verr&agrave ignorato dal sistema.
		<table class="parameters">
			<thead>
				<tr><th colspan="2">Data</th></tr>
				<tr><th>Valore</th><th>Descrizione</th></tr>
			</thead>
			<tbody>
				<tr><td style="width: 120px">typeDate</td><td>
					<table style="width:100%">
						<tbody>
							<tr><td>data_pubblicazione</td><td>Data di pubblicazione</td></tr>
							<tr><td>data_atto_indizione</td><td>Data dell&#39;atto di indizione</td></tr>
							<tr><td>data_protocollo</td><td>Data di protocollazione</td></tr>
							<tr><td>data_validazione</td><td>Data di validazione</td></tr>
							<tr><td>data_accesso</td><td>Termine accesso agli atti</td></tr>
							<tr><td>data_scadenza</td><td>Scadenza presentazione offerte</td></tr>
							<tr><td>data_apertura</td><td>Apertura delle offerte</td></tr>
							<tr><td>data_atto_esito</td><td>Data atto esito di gara (nel caso di aggiudicazione definitiva)</td></tr>
							<tr><td>data_annullamento</td><td>Data di annullamento</td></tr>
						</tbody>
					</table>
				</td></tr>
				<tr><td>startDate</td><td>Data di inizio (formato <i>YYYY-MM-DD</i>)</td></tr>
				<tr><td>endDate</td><td>Data di fine (formato <i>YYYY-MM-DD</i>)</td></tr>
			</tbody>
		</table>
		<br/>
		Questo è un esempio scritto in codice PHP di una richiesta effettuata per questo ente riguardo le gare Lavori in procedura Aperta, modalit&agrave tradizionale, pubblicate dal 01/01/2015 al 10/02/2015:
		<div class="box">
			<code>
$parameters = array('token' =&gt; '<?=$token?>', 'tipologia' =&gt; '7', 'procedura' =&gt; '1', 'modalita' =&gt; '1', 'data' =&gt; array('typeDate' =&gt; 'data_pubblicazione', 'startDate' =&gt; '2015-01-01', 'endDate' =&gt; '2015-02-10'));
</code><pre><code>$client = new SoapClient('https://gare.comune.roma.it/api/service.php?WSDL',array('cache_wsdl' => WSDL_CACHE_NONE));
$response = $client->getGare($parameters);</code></pre>
		</div>
	</div>
	<?
	include_once($root."/layout/bottom.php");
}else{
	echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
	die();
}
}else{
	echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
	die();
}
?>
