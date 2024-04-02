<?
  if (isset($_POST["id"])) {
    session_start();
    include("../../../config.php");
    include_once($root."/inc/funzioni.php");
;
    $fase = get_campi("b_fasi_concorsi");
    $id_fase = $_POST["id"];
  }
  if (!empty($fase)) {
    ?>
    <div class="box" id="fase_<? echo $id_fase ?>">
    	<table width="100%">
    		<tr>
    			<td>
            <input type="hidden" name="fase[<? echo $id_fase ?>][codice]" value="<? echo $id_fase ?>">
    				<input type="text" title="Oggetto" class="titolo_edit" style="width:98%" id="oggetto_<? echo $id_fase ?>" rel="S;0;0;A" name="fase[<? echo $id_fase ?>][oggetto]" value="<? echo purify($fase["oggetto"]) ?>">
          </td>
    			<td width="10"><button class="espandi btn-round btn-warning" onClick="$(this).parents('div').children('table.dettaglio').toggle(); return false;" title="Visualizza"><span class="fa fa-search"></span></button></td>
    			<td width="10"><button class="btn-round btn-danger" onClick="elimina('<? echo $id_fase ?>','concorsi/fasi');return false" title="Elimina"><span class="fa fa-remove"></span></button></td>
        </tr>
    	</table>
    	<table width="100%" class="dettaglio">
				<tr><td class="etichetta" colspan="4"><strong>Breve descrizione</strong></td></tr>
				<tr>
          <td colspan="4">
						<textarea title="Breve descrizione" id="descrizione_<? echo $id_fase ?>" rel="S;0;0;A" name="fase[<? echo $id_fase ?>][descrizione]" class="ckeditor_full"><? echo $fase["descrizione"] ?></textarea>
					</td>
				</tr>
        <tr>
          <td class="etichetta" colspan="4">
            <strong>Criteri valutazione</strong>
          </td>
        </tr>
        <tr>
          <td class="etichetta" colspan="4" id="criteri_offerta_tecnica_<?= $id_fase ?>">
            <input class="totale_punteggio" type="hidden" title="Punteggio complessivo" rel="S;0;0;N;100" id="punteggio_complessivo_<?= $id_fase ?>">
            <?
            $bind = array();
            $bind[":codice"] = (!empty($record["codice"])) ? $record["codice"] : 0;
            $bind[":codice_fase"] = $id_fase;
            $strsql = "SELECT * FROM b_criteri_valutazione_concorsi WHERE codice_fase = :codice_fase AND codice_padre = 0 AND codice_gara = :codice";
            $ris_valutazione = $pdo->bindAndExec($strsql,$bind);
            if ($ris_valutazione->rowCount()>0) {
              $include_from_fase = true;
              while($criterio_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC)) {
                $padre = true;
                $id = $criterio_valutazione["codice"];
                include("record.php");
              }
            } else {
              $padre = true;
              $id = "id-".$id_fase."-0";
              $criterio_valutazione = get_campi("b_criteri_valutazione_concorsi");
              include("record.php");
            }
          ?>
          </td>
        </tr>
        <tr>
          <td colspan="4">
            <button class="aggiungi" onClick="aggiungi('record.php?id_fase=<?= $id_fase ?>','#criteri_offerta_tecnica_<?= $id_fase ?>');verifica_valutazione();return false;"><img src="/img/add.png" alt="Aggiungi criterio">Aggiungi criterio</button>
          </td>
        </tr>
    	</table>
    </div>
    <?
  }

?>
