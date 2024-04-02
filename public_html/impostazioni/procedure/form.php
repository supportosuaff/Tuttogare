<?
if (isset($_POST["id"])) {
	session_start();
	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
;
	$record = get_campi("b_procedure");
	$id = $_POST["id"];

}

$colore = "#3C0";
if ($record["attivo"] == "N") { $colore = "#C00"; }
?>
<tr id="procedura_<? echo $id ?>">
	<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>" class="handle"></td>
	<td>
		<input type="hidden" name="procedura[<? echo $id ?>][codice]"id="codice_procedura_<? echo $id ?>" value="<? echo $record["codice"]  ?>">
		<input type="text" class="titolo_edit" name="procedura[<? echo $id ?>][nome]"  title="Nome" rel="S;3;255;A" id="nome_procedura_<? echo $id ?>" value="<? echo $record["nome"] ?>">
		<input type="hidden" name="procedura[<? echo $id ?>][ordinamento]"id="ordinamento_procedura_<? echo $record["codice"] ?>" class="ordinamento" value="<? echo $record["ordinamento"]  ?>">
		<input type="text" style="width:98%" name="procedura[<? echo $id ?>][riferimento_normativo]"  title="Riferimento Normativo" rel="S;3;255;A" id="riferimento_normativo_procedura_<? echo $id ?>" value="<? echo $record["riferimento_normativo"] ?>">
		<table width="100%" class="_dettaglio">
			<tr>
				<td class="etichetta">Gestione</td><td width="20%"><input type="text" style="width:95%" name="procedura[<? echo $id ?>][directory]"  title="Directory" rel="S;3;255;A" id="directory_procedura_<? echo $id ?>" value="<? echo $record["directory"] ?>"></td>
				<td class="etichetta">Mercato Elettronico</td>
				<td>
					<select name="procedura[<? echo $id ?>][mercato_elettronico]" title="Mercato Elettronico" rel="S;1;1;A" id="mercato_elettronico_procedura_<? echo $id ?>" >
						<option value="S">Si</option>
						<option value="N">No</option>
					</select>
				</td>
				<td class="etichetta">Bando</td>
				<td>
					<select name="procedura[<? echo $id ?>][bando]" title="Bando" rel="S;1;1;A" id="bando_procedura_<? echo $id ?>" >
						<option value="S">Si</option>
						<option value="N">No</option>
					</select>
				</td>
				<td class="etichetta">Invito</td>
				<td>
					<select name="procedura[<? echo $id ?>][invito]" title="Invito" rel="S;1;1;A" id="invito_procedura_<? echo $id ?>" >
						<option value="S">Si</option>
						<option value="N">No</option>
					</select>
				</td>
				<td class="etichetta">2 Fasi</td>
				<td>
					<select name="procedura[<? echo $id ?>][fasi]" title="2 Fasi" rel="S;1;1;A" id="fasi_procedura_<? echo $id ?>" >
						<option value="S">Si</option>
						<option value="N">No</option>
					</select>
				</td>
				<td class="etichetta">Aggiudicazione Multipla</td>
				<td>
					<select name="procedura[<? echo $id ?>][aggiudicazione_multipla]" title="Aggiudicazione multipla" rel="S;1;1;A" id="aggiudicazione_multipla_procedura_<? echo $id ?>" >
						<option value="S">Si</option>
						<option value="N">No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="etichetta">Tipologia</td>
				<td colspan="13">

					<select name="procedura[<? echo $id ?>][tipologie][]" multiple id="tipologie_procedura_<? echo $id ?>" rel="S;0;0;ARRAY" title="Tipologia">
						<? $sql = "SELECT * FROM b_tipologie WHERE eliminato = 'N' ORDER BY codice";
						$ris = $pdo->query($sql);
						if ($ris->rowCount()>0) {
							while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
								?><option value="<? echo $rec["codice"] ?>"><? echo $rec["tipologia"] ?></option><?
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="etichetta">Criteri di aggiudicazione</td>
				<td colspan="13">
					<select multiple name="procedura[<? echo $id ?>][criteri][]" id="criteri_procedura_<? echo $id ?>" rel="S;0;0;ARRAY" title="Criteri di aggiudicazione">
						<? $sql = "SELECT * FROM b_criteri WHERE eliminato = 'N' ORDER BY codice";
						$ris = $pdo->query($sql);
						if ($ris->rowCount()>0) {
							while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
								?><option value="<? echo $rec["codice"] ?>"><? echo $rec["criterio"] ?></option><?
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="etichetta">AVCP</td>
				<td colspan="13">
					<select name="procedura[<? echo $id ?>][avcp][]" multiple title="Tipologie AVCP" rel="S;0;0;ARRAY" id="avcp_procedura_<? echo $id ?>" value="<? echo $record["avcp"] ?>">
						<option>01-PROCEDURA APERTA</option>
						<option>02-PROCEDURA RISTRETTA</option>
						<option>03-PROCEDURA NEGOZIATA PREVIA PUBBLICAZIONE</option>
						<option>04-PROCEDURA NEGOZIATA SENZA PREVIA PUBBLICAZIONE</option>
						<option>05-DIALOGO COMPETITIVO</option>
						<option>06-PROCEDURA NEGOZIATA SENZA PREVIA INDIZIONE DI GARA (SETTORI SPECIALI)</option>
						<option>07-SISTEMA DINAMICO DI ACQUISIZIONE</option>
						<option>08-AFFIDAMENTO IN ECONOMIA - COTTIMO FIDUCIARIO</option>
						<option>14-PROCEDURA SELETTIVA EX ART 238 C.7, D.LGS. 163/2006</option>
						<option>17-AFFIDAMENTO DIRETTO EX ART. 5 DELLA LEGGE 381/91</option>
						<option>21-PROCEDURA RISTRETTA DERIVANTE DA AVVISI CON CUI SI INDICE LA GARA</option>
						<option>22-PROCEDURA NEGOZIATA CON PREVIA INDIZIONE DI GARA (SETTORI SPECIALI)</option>
						<option>23-AFFIDAMENTO DIRETTO</option>
						<option>24-AFFIDAMENTO DIRETTO A SOCIETA' IN HOUSE</option>
						<option>25-AFFIDAMENTO DIRETTO A SOCIETA' RAGGRUPPATE/CONSORZIATE O CONTROLLATE NELLE CONCESSIONI E NEI PARTENARIATI</option>
						<option>26-AFFIDAMENTO DIRETTO IN ADESIONE AD ACCORDO QUADRO/CONVENZIONE</option>
						<option>27-CONFRONTO COMPETITIVO IN ADESIONE AD ACCORDO QUADRO/CONVENZIONE</option>
						<option>28-PROCEDURA AI SENSI DEI REGOLAMENTI DEGLI ORGANI COSTITUZIONALI</option>
						<option>29-PROCEDURA RISTRETTA SEMPLIFICATA</option>
						<option>30-PROCEDURA DERIVANTE DA LEGGE REGIONALE</option>
						<option>31-AFFIDAMENTO DIRETTO PER VARIANTE SUPERIORE AL 20% DELL'IMPORTO CONTRATTUALE</option>
						<option>32-AFFIDAMENTO RISERVATO</option>
						<option>33-PROCEDURA NEGOZIATA PER AFFIDAMENTI SOTTO SOGLIA</option>
						<option>34-PROCEDURA ART.16 COMMA 2-BIS DPR 380/2001 PER OPERE URBANIZZAZIONE A SCOMPUTO PRIMARIE SOTTO SOGLIA COMUNITARIA</option>
						<option>35-PARTERNARIATO PER L’INNOVAZIONE</option>
						<option>36-AFFIDAMENTO DIRETTO PER LAVORI, SERVIZI O FORNITURE SUPPLEMENTARI</option>
						<option>37-PROCEDURA COMPETITIVA CON NEGOZIAZIONE</option>
						<option>38-PROCEDURA DISCIPLINATA DA REGOLAMENTO INTERNO PER SETTORI SPECIALI</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="etichetta">Derivazione</td>
				<td colspan="13"><select name="procedura[<? echo $id ?>][derivata]" title="Derivazione" rel="N;0;0;N" id="derivata_procedura_<? echo $id ?>">
					<option value="0">Nessuna</option>
					<?
					$sql = "SELECT * FROM b_procedure ";
					if (is_numeric($id)) $sql.="WHERE codice <> ". $id;
					$sql.= " ORDER BY ordinamento ";
					$ris_procedure = $pdo->query($sql);
					if ($ris_procedure->rowCount()>0) {
						while($rec = $ris_procedure->fetch(PDO::FETCH_ASSOC)) {
							?>
							<option value="<?=$rec["codice"]?>"><?=$rec["nome"]?></option>
							<?
						}
					}
					?></select>
				</td>
			</tr>
			<tr>
				<td class="etichetta">Bando GUUE</td>
				<td colspan="13">
					<select name="procedura[<? echo $id ?>][guue]" title="Guue" rel="S;1;1;N" id="guue_procedura_<? echo $id ?>" value="<? echo $record["guue"] ?>">
						<option value="0">Nessuno</option>
						<option value="1">Avviso di preinformazione</option>
						<option value="2">Bando di gara</option>
						<option value="3">Avviso relativo agli appalti aggiudicati</option>
						<option value="4">Avviso indicativo periodico – settori speciali</option>
						<option value="5">Bando di gara – settori speciali</option>
						<option value="6">Avviso relativo agli appalti aggiudicati – settori speciali</option>
						<option value="7">Sistema di qualificazione – settori speciali</option>
						<option value="8">Avviso sul profilo di committente</option>
						<option value="9">Bando di gara semplificato nell&apos;ambito di un sistema dinamico di acquisizione</option>
						<option value="10">Concessione di lavori pubblici</option>
						<option value="11">Bando di gara d&apos;appalto - concessione</option>
						<option value="12">Bando di concorso di progettazione</option>
						<option value="13">Risultati di un concorso di progettazione</option>
						<option value="14">Avviso relativo a informazioni complementari, informazioni su procedure incomplete o rettifiche</option>
						<option value="15">Avviso volontario per la trasparenza ex ante</option>
						<option value="16">Avviso di preinformazione per appalti nel settore della difesa e della sicurezza</option>
						<option value="17">Avviso di gara per appalti nel settore della difesa e della sicurezza</option>
						<option value="18">Avviso di aggiudicazione di appalti nel settore della difesa e della sicurezza</option>
						<option value="19">Avviso di subappalto</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="etichetta">ESENDER</td>
				<td colspan="13">
					<input type="text" name="procedura[<? echo $id ?>][esender]" title="Riferimento ESENDER" value="<? echo $record["esender"] ?>">
				</td>
			</tr>
		</table>
	</td>
	<td width="10"><button class="btn-round btn-warning" onClick="$('#procedura_<? echo $id ?> ._dettaglio').toggle(); return false"><span class="fa fa-search"></span></td>
	<td width="10"><button class="btn-round btn-default" onClick="disabilita('<? echo $id ?>','impostazioni/procedure');return false;" title="Abilita/Disabilita">
		<span class="fa fa-refresh"></span>
	</button></td>
	<td width="10"><button class="btn-round btn-warning" onClick="elimina('<? echo $id ?>','impostazioni/procedure');return false;" title="Elimina"><span class="fa fa-remove"></span></button></td>
</tr>
<?

if (!isset($_POST["id"])) {
	?><script><?
	if ($record["avcp"]!="") {
		?>
		var values="<? echo $record["avcp"] ?>";
		$("#avcp_procedura_<? echo $id ?>").val(values.split(";"));
	<? } ?>
	var values="<? echo $record["tipologie"] ?>";
	$("#tipologie_procedura_<? echo $id ?>").val(values.split(";"));
	var values="<? echo $record["criteri"] ?>";
	$("#criteri_procedura_<? echo $id ?>").val(values.split(";"));

	$("#mercato_elettronico_procedura_<? echo $id ?>").val('<? echo $record["mercato_elettronico"] ?>');
	$("#bando_procedura_<? echo $id ?>").val('<? echo $record["bando"] ?>');
	$("#invito_procedura_<? echo $id ?>").val('<? echo $record["invito"] ?>');
	$("#fasi_procedura_<? echo $id ?>").val('<? echo $record["fasi"] ?>');
	$("#aggiudicazione_multipla_procedura_<? echo $id ?>").val('<? echo $record["aggiudicazione_multipla"] ?>');
	$("#derivata_procedura_<? echo $id ?>").val('<? echo $record["derivata"] ?>');
	$("#guue_procedura_<? echo $id ?>").val('<? echo $record["guue"] ?>');
</script>
<?
}
?>
