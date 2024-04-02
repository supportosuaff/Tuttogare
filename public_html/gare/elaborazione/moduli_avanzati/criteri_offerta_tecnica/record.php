<?
  if (isset($_POST["id"])) {
    session_start();
    include("../../../../../config.php");
    include_once($root."/inc/funzioni.php");
;
    $criterio_valutazione = get_campi("b_valutazione_tecnica");
    $id = $_POST["id"];
    $padre = false;
    $executed_post = true;
    if ($_POST["target"] == "#criteri_offerta_tecnica") {
      $padre = true;
    } else {
      $criterio_valutazione["codice_padre"] = substr(substr($_POST["target"],0,strpos($_POST["target"],"_sub")),1);
    }
  }

  $destinazione_totale = "complessivo";
  if ($criterio_valutazione["codice_padre"] != "" && $criterio_valutazione["codice_padre"] != "0") $destinazione_totale = $criterio_valutazione["codice_padre"];
  if (!$padre) {
     $id_valutazione = $criterio_valutazione["codice_padre"];
   } else {
     $id_valutazione = $id;
   }
   $bind = array();
   $bind[":codice_padre"] = $criterio_valutazione["codice"];
   $bind[":codice_gara"] = $_SESSION["gara"]["codice"];
   $strsql = "SELECT * FROM b_valutazione_tecnica WHERE codice_padre = :codice_padre AND codice_gara = :codice_gara";
   $figli = $pdo->bindAndExec($strsql,$bind);
   $sql_tecnici = "SELECT * FROM b_criteri_punteggi WHERE (economica = 'N' AND temporale = 'N') OR (economica = 'S' AND migliorativa = 'S')";
   $ris_tecnici = $pdo->query($sql_tecnici);
   $codice_tecnici = array();
   if ($ris_tecnici->rowCount() > 0) {
     while($rec_tecnici = $ris_tecnici->fetch(PDO::FETCH_ASSOC)) $codice_tecnici[] = $rec_tecnici["codice"];
   }

  $sql_no_sub = "SELECT * FROM b_criteri_punteggi WHERE (economica = 'S' OR temporale = 'S') AND migliorativa = 'N'";
 	$ris_no_sub = $pdo->query($sql_no_sub);
 	$codice_no_sub = array();
 	if ($ris_no_sub->rowCount() > 0) {
 		while($rec_no_sub = $ris_no_sub->fetch(PDO::FETCH_ASSOC)) $codice_no_sub[] = $rec_no_sub["codice"];
 	}
?>
<div <? if (!$padre) echo "style='padding-left:30px' class='sub_" .$criterio_valutazione["codice_padre"] . "'"; ?>>
        <table width="100%">
        <? if ($padre) { ?>
            <tr style="background-color: #CCC;">
              <td><strong>Tipo</strong></td>
              <td><strong>Descrizione</strong></td>
              <td><strong>Punteggio</strong></td>
              <td><strong>Riferimento</strong></td>
              <td><strong>Sub</strong></td>
              <td><strong>Elimina</strong></td>
            </tr>
        <? } ?>
            <tr>
                <? if ($padre) { ?>
                    <td width="10">
                    <select rel="S;1;1;A" name="criterio_valutazione[<? echo $id ?>][tipo]" onChange="verifica_valutazione()" title="Tipo" id="tipo_cr_valutazione_<? echo $id ?>">
                        <option value="">Seleziona...</option>
                        <option value="Q">Qualitativo</option>
                        <option value="N">Quantitativo</option>
                    </select>
                    <? if (!isset($executed_post)) { ?>
                        <script>
                            $("#tipo_cr_valutazione_<? echo $id ?>").val('<? echo $criterio_valutazione["tipo"] ?>');
                        </script>
                    <? } ?>
                </td>
                <? } else { ?>
                    <input type="hidden" name="criterio_valutazione[<? echo $id ?>][tipo]" id="tipo_cr_valutazione_<? echo $id ?>" value="<? echo $criterio_valutazione["tipo"] ?>">
                    <?
                }?>
                <td><input type="hidden" name="criterio_valutazione[<? echo $id ?>][codice]" id="codice_cr_valutazione_<? echo $id ?>" value="<? echo $id ?>">
                <input type="hidden" name="criterio_valutazione[<? echo $id ?>][codice_padre]" id="codice_padre_cr_valutazione_<? echo $id ?>" value="<? echo $criterio_valutazione["codice_padre"] ?>">
                    <input title="Descrizione" rel="S;3;0;A" class="titolo_edit" name="criterio_valutazione[<? echo $id ?>][descrizione]" id="descrizione_cr_valutazione_<? echo $id ?>" value="<? echo $criterio_valutazione["descrizione"] ?>">
                    <? if (!isset($record_gara["online"]) || (isset($record_gara["online"]) && $record_gara["online"]=="S")) { ?>
                      <div class="div_valutazione_automatica <? if ($padre) echo 'padre' ?>" rel="valutazione_automatica_<?= $id_valutazione ?>"
                        <? if (((!in_array($criterio_valutazione["punteggio_riferimento"], $codice_tecnici)) && ($criterio_valutazione["punteggio_riferimento"] != "0")) || ($criterio_valutazione["tipo"] != "N") || (@$figli->rowCount()>0)) echo "style=\"display:none\"" ?>>
                        <strong>Valutazione automatica:</strong>
                          <input type="radio" class="valutazione_valutazione_<? echo $id ?>" name="criterio_valutazione[<? echo $id ?>][valutazione]" value=""> OFF
                          <input type="radio" class="valutazione_valutazione_<? echo $id ?>" name="criterio_valutazione[<? echo $id ?>][valutazione]" value="P"> Proporzionale
                          <input type="radio" class="valutazione_valutazione_<? echo $id ?>" name="criterio_valutazione[<? echo $id ?>][valutazione]" value="I"> Inversa
                          <input type="radio" class="valutazione_valutazione_<? echo $id ?>" name="criterio_valutazione[<? echo $id ?>][valutazione]" value="S"> A step

                          <table width="100%" class="step_div step_<?= $id ?>" <? if ($criterio_valutazione["valutazione"] != "S") echo "style=\"display:none\""; ?>>
                            <thead>
                              <tr>
                                <td>Minimo</td>
                                <td>Massimo</td>
                                <td>Punteggio</td>
                                <td><input type="image" onClick="aggiungi('moduli_avanzati/criteri_offerta_tecnica/record_step.php','#list_step_<?= $id ?>');check_steps('<?= $id ?>');return false"
                                      src="/img/add.png" style="vertical-align:middle; width:20px" alt="Aggiungi step"></td>
                              </tr>
                            </thead>
                            <tbody id="list_step_<?= $id ?>">
                              <?
                                $is_step = "";
                                if (is_numeric($id)) {
                                $bind = array();
                                $bind[":id"] = $id;
                                $sql_step = "SELECT * FROM r_step_valutazione WHERE codice_criterio	= :id ORDER BY minimo";
                                $ris_step = $pdo->bindAndExec($sql_step,$bind);
                                if ($ris_step->rowCount()>0) {
                                  $is_step = "true";
                                  while($step = $ris_step->fetch(PDO::FETCH_ASSOC)) {
                                    $id_step = $step["codice"];
                                      include("record_step.php");
                                    }
                                  }
                                }
                              ?>
                            </tbody>
                          </table>
                          <?
                            $rel_step = "N;1;0;A";
                            if ($criterio_valutazione["valutazione"] == "S") $rel_step = "S;1;0;A";
                          ?>
                          <input type="hidden" id="is_steps_<?= $id ?>" value="<?= $is_step ?>" rel="<?= $rel_step ?>" title="Step">
                          <script>
                            $(".valutazione_valutazione_<? echo $id ?>").each(function(){
                              if ($(this).val() == "<?= $criterio_valutazione["valutazione"] ?>") {
                                $(this).prop("checked",true);
                              } else {
                                $(this).prop("checked",false);
                              }
                            });
                            $(".valutazione_valutazione_<? echo $id ?>").change(function(){
                              if ($(this).val() == "S") {
                                $(".step_<?= $id ?>").show();
                                $("#is_steps_<?= $id ?>").attr("rel","S;1;0;A");
                                check_steps('<?= $id ?>');
                              } else {
                                $(".step_<?= $id ?>").hide();
                                $("tbody",".step_<?= $id ?>").html("");
                                $("#is_steps_<?= $id ?>").val("");
                                $("#is_steps_<?= $id ?>").attr("rel","N;1;0;A");
                                check_steps('<?= $id ?>');
                              }
                            })
                          </script>
                      </div>
                    <? } ?>

                </td>
                <td width="50">
                  <input class="punteggio" rif="punteggio_<? echo $destinazione_totale ?>" onChange="verifica_punteggi()"  title="Punteggio" rel="S;1;6;N;" name="criterio_valutazione[<? echo $id ?>][punteggio]" id="punteggio_cr_valutazione_<? echo $id ?>" value="<? echo $criterio_valutazione["punteggio"] ?>">
                </td>
                <? if ($padre) { ?>
                    <?
                        $bind = array();
                        $bind[":codice_criterio"] = $_SESSION["gara"]["criterio"];
                        $sql_punteggi_riferimento = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio = :codice_criterio ORDER BY ordinamento";
                        $ris_punteggi_riferimento = $pdo->bindAndExec($sql_punteggi_riferimento,$bind);
                        if ($ris_punteggi_riferimento->rowCount() > 0) {
                    ?>
                <td width="50">
                <select rel="S;1;1;A" name="criterio_valutazione[<? echo $id ?>][punteggio_riferimento]" onChange="verifica_valutazione();" title="Riferimento" id="punteggio_riferimento_cr_valutazione_<? echo $id ?>" class="punteggio_riferimento" data-identificativo="<?= $id ?>">
                    <? while ($rec_punteggi_riferimento=$ris_punteggi_riferimento->fetch(PDO::FETCH_ASSOC)) { ?>
                        <option value="<? echo $rec_punteggi_riferimento["codice"] ?>"><? echo $rec_punteggi_riferimento["nome"] ?></option>
                    <? } ?>
                </select>
                </td>
                <? } ?>
                <td width="10" style="text-align:center">
                      <input title="Aggiungi sub criterio" type="image" src="/img/add.png" class="aggiungi add_sub_<?= $id ?>" onClick="aggiungi('moduli_avanzati/criteri_offerta_tecnica/record.php','#<? echo $id ?>_sub_criteri');verifica_valutazione();return false;"
                      <?= (in_array($criterio_valutazione["punteggio_riferimento"],$codice_no_sub)!=false) ? "style=\"display:none\"":""; ?>>
                </td>
                <? } ?>
                <td width="10">
                  <script>
                    $("#punteggio_riferimento_cr_valutazione_<? echo $id ?>").val('<? echo $criterio_valutazione["punteggio_riferimento"] ?>');
                  </script>
        <input type="image" onClick="$(this).parents('div').first().remove();verifica_punteggi();verifica_valutazione();" src="/img/del.png" title="Elimina">
        </td>
            </tr>
        </table>
        <? if ($padre) { ?>
        <input type="hidden" class="totale_punteggio" rif="punteggio_cr_valutazione_<? echo $id ?>" title="Totale sub-criteri" rel="S;0;0;N;punteggio_cr_valutazione_<? echo $id ?>" id="punteggio_<? echo $id ?>">

          <div class="clear"></div>
        <div id="<? echo $id ?>_sub_criteri">
          <?
            $bind = array();
            $bind[":codice_padre"] = $criterio_valutazione["codice"];
            $bind[":codice_gara"] = $_SESSION["gara"]["codice"];
            $strsql = "SELECT * FROM b_valutazione_tecnica WHERE codice_padre = :codice_padre AND codice_gara = :codice_gara";
            $ris_sub_valutazioni = $pdo->bindAndExec($strsql,$bind);
            if (@$ris_sub_valutazioni->rowCount()>0) {
              while($criterio_valutazione = $ris_sub_valutazioni->fetch(PDO::FETCH_ASSOC)) {
                $padre = false;
                $id = $criterio_valutazione["codice"];
                include("moduli_avanzati/criteri_offerta_tecnica/record.php");
              }
            }
          ?>
        </div>
        <? } ?>
     </div>
