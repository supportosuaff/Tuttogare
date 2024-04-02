<?
    if (isset($_POST["id"])) {
        session_start();
        include_once("../../../config.php");
        include_once($root."/inc/funzioni.php");
        $modulo = get_campi("b_modulistica_albo");
        $id = $_POST["id"];
    }
    if (isset($modulo)) {
?>
            <tr id="modulo_<?= $id ?>">
                <td>
                    <input type="hidden" name="modulo[<? echo $id ?>][codice]" id="codice_modulo_<? echo $id ?>" value="<? echo $id ?>">
                    <input title="Titolo" rel="S;1;255;A" class="titolo_edit" name="modulo[<? echo $id ?>][titolo]" id="titolo_modulo_<? echo $id ?>" value="<? echo $modulo["titolo"] ?>">
                </td>
                <td width="30%">
                            <input type="hidden" class="filechunk" id="filechunk_<? echo $id ?>" name="modulo[<? echo $id ?>][filechunk]" title="Allegato">
                            <input type="hidden" class="terminato" id="terminato_<? echo $id ?>" title="Termine upload">
                  <div id="nome_file_<? echo $id ?>" style="float:left;"><? echo $modulo["nome_file"] ?></div>
                  <div id="modulistica_<? echo $id ?>" rel="<? echo $id ?>" class="scegli_file" style="float:right"><img src="/img/folder.png" height="30" style="vertical-align:middle"></div>
                  <div class="clear"></div>
                            <div id="progress_bar_<? echo $id ?>" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
                </td>
                 <script>
                  tmp = (function($){
                    return (new ResumableUploader($("#modulistica_<? echo $id ?>")));
                  })(jQuery);
                  uploader.push(tmp);
                </script>
                <td width="10">
                      <select rel="S;1;1;A" name="modulo[<? echo $id ?>][obbligatorio]" title="Obbligatorio" id="obbligatorio_modulo_<? echo $id ?>">
                        <option value="">Seleziona...</option>
                        <option value="S">Si</option>
                        <option value="N">No</option>
                      </select>
                     <script>
                         $("#obbligatorio_modulo_<? echo $id ?>").val('<? echo $modulo["obbligatorio"] ?>');
                     </script>
                </td>
                <td width="10">
                <input type="image" onClick="elimina('<? echo $id ?>','albo_fornitori/modulistica');return false;" src="/img/del.png" title="Elimina">
                </td>
            </tr>
    <? } ?>
