<?
  if (isset($_POST["id"])) {
    session_start();
    include_once("../../../config.php");
    include_once($root."/inc/funzioni.php");
    $modulo = get_campi("b_modulistica_contratto");
    $id = $_POST["id"];
  }
  if (isset($modulo)) {
    ?>
    <tr id="modulo_<?= $id ?>" style="background-color:#eeeeee">
      <td>
        <input type="hidden" name="modulo[<?= $id ?>][codice]" id="codice_modulo_<?= $id ?>" value="<?= $id ?>">
        <input title="Titolo" rel="S;1;255;A" class="titolo_edit" name="modulo[<?= $id ?>][titolo]" id="titolo_modulo_<?= $id ?>" value="<?= $modulo["titolo"] ?>">
      </td>
      <td width="30%" style="vertical-align:middle !important;">
        <input type="hidden" class="filechunk" id="filechunk_<?= $id ?>" name="modulo[<?= $id ?>][filechunk]" title="Allegato">
        <input type="hidden" class="terminato" id="terminato_<?= $id ?>" title="Termine upload">
        <span id="nome_file_<?=$id ?>" style="float:left;"><?= $modulo["nome_file"] ?></span>
        <div id="modulistica_<?=$id ?>" rel="<?=$id ?>" class="scegli_file" style="float:right"><img src="/img/folder.png" height="30" style="vertical-align:middle"></div>
        <div class="clear"></div>
        <div id="progress_bar_<?=$id ?>" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
        <script>
          tmp = (function($){
            return (new ResumableUploader($("#modulistica_<?=$id ?>")));
          })(jQuery);
          uploader.push(tmp);
        </script>
      </td>
      <td width="10">
        <select rel="S;1;1;A" name="modulo[<?=$id ?>][obbligatorio]" title="Obbligatorio" id="obbligatorio_modulo_<?=$id ?>">
          <option value="">Seleziona...</option>
          <option value="S">Si</option>
          <option value="N">No</option>
        </select>
        <script>
          $("#obbligatorio_modulo_<?=$id ?>").val('<?= $modulo["obbligatorio"] ?>');
        </script>
      </td>
      <td width="10" style="text-align:center">
        <button type="button" class="button button-caution button-circle button-small" onClick="elimina('<?= $id ?>','contratti/modulistica');return false;"><i class="fa fa-times"></i></button>
      </td>
    </tr>
    <?
    if(!empty($allegati_modulo)) {
      ?>
      <tr class="modulo_<?= $id ?>">
        <td colspan="4">
          <table style="width:100%">
            <thead>
              <tr>
                <th width="10%"><b>P.IVA</b></th>
                <th width="20%"><b>Ragione Sociale</b></th>
                <th><b>File</b></th>
              </tr>
            </thead>
            <?
            foreach ($allegati_modulo as  $allegato) {
              ?>
              <tr>
                <td><?= $allegato["partita_iva"] ?></td>
                <td><?= $allegato["ragione_sociale"] ?></td>
                <td>
                  <strong><?= $allegato["nome_file"] ?></strong> <a class="btn" href="#" onclick="$('#note_<?= $allegato["codice"] ?>').dialog({title: 'Informazioni firma', modal: true});"><i class="fa fa-info"></i></a><br>
                  <?
                  $file = "{$config["arch_folder"]}/allegati_contratto/{$modulo["codice_contratto"]}/{$allegato["riferimento"]}";
                  $p7m = new P7Manager($file);
                  $certificati = $p7m->extractSignatures();
                  ?>
                  <div id="note_<?= $allegato["codice"] ?>" style="display:none;">
                    <ul>
                      <?
                      foreach ($certificati as $esito) {
                        ?>
                        <li>
                          <?
                          if (!empty($data["subject"]["commonName"])) echo "<h1>" . $data["subject"]["commonName"] . "</h1>";
                          if (!empty($data["subject"]["organizationName"])) echo "<strong>" . $data["subject"]["organizationName"] . "</strong><br>";
                          if (!empty($data["subject"]["title"])) echo  $data["subject"]["title"] . "<br>";
                          if (!empty($data["issuer"]["organizationName"])) echo  "<br>Emesso da:<strong> " . $data["issuer"]["organizationName"] . "</strong><br>";
                          $data = openssl_x509_parse($esito,false);
                          $validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
                          $validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
                          echo "Valido dal <strong> " . $validFrom . "</strong> al <strong>" . $validTo . "</strong>";
                          ?>
                        </li>
                        <?
                      }
                      ?>
                    </ul>
                  </div>
                  <?
                  $file_info = new finfo(FILEINFO_MIME_TYPE);
                  $mime_type = $file_info->buffer(file_get_contents($file));
                  ?><a href="download_allegato.php?codice_contratto=<?= $modulo["codice_contratto"] ?>&codice_allegato=<?= $allegato["codice"] ?>" target="_blank" title="Scarica Allegato"><img src="/img/p7m.png" alt="allegato p7m" width="25" /></a><?
                  if (strpos($mime_type,"pdf") === false) {
                    ?><a href="open_p7m.php?codice_contratto=<?= $modulo["codice_contratto"] ?>&codice_allegato=<?= $allegato["codice"] ?>" target="_blank" title="Estrai Contenuto"><img src="/img/download.png" alt="Estrai Contenuto" width="25" /></a><?
                  }
                  ?>
                </td>
              </tr>
              <?
            }
            ?>
          </table>
        </td>
      </tr>
      <?
    }
  }
?>
