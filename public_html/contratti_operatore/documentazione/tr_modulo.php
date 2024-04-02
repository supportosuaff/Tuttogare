<?
  if (isset($_POST["id"])) {
    session_start();
    include_once("../../../config.php");
    include_once($root."/inc/funzioni.php");
    include_once $root . "/inc/p7m.class.php" ;
    $modulo = get_campi("b_modulistica_contratto");
    $id = $_POST["id"];
  }
  if (isset($modulo)) {
    ?>
    <tr id="modulo_<?= $id ?>" style="background-color:#ABB7B7">
      <td>
        <input type="hidden" name="modulo[<?= $id ?>][codice]" id="codice_modulo_<?= $id ?>" value="<?= $id ?>">
        <input title="Titolo" rel="S;1;255;A" class="titolo_edit" name="modulo[<?= $id ?>][titolo]" id="titolo_modulo_<?= $id ?>" value="<?= $modulo["titolo"] ?>">
      </td>
      <td width="30%" style="vertical-align:middle !important;">
        <input type="hidden" class="md5" name="modulo[<?= $id ?>][md5_file]" id="md5_file_<?= $id ?>" title="File">
        <input type="hidden" class="filechunk" id="filechunk_<?= $id ?>" name="modulo[<?= $id ?>][filechunk]" title="Allegato">
        <input type="hidden" class="terminato" id="terminato_<?= $id ?>" title="Termine upload">
        <div id="nome_file_<?=$id ?>" style="float:left;">
          <?
          if(!empty($modulo["riferimento"])) {
            ?><strong><?= $modulo["nome_file"] ?></strong> <a class="btn" href="#" onclick="$('#note_<?= $modulo["codice"] ?>').dialog({title: 'Informazioni firma', modal: true});"><i class="fa fa-info"></i></a><br> <?
            $file = "{$config["arch_folder"]}/allegati_contratto/{$modulo["codice_contratto"]}/{$modulo["riferimento"]}";
            $p7m = new P7Manager($file);
            $certificati = $p7m->extractSignatures();
            ?>
            <div id="note_<?= $modulo["codice"] ?>" style="display:none;">
              <ul>
                <?
                foreach ($certificati as $esito) {
                  ?>
                  <li>
                    <?
                    if (isset($data["subject"]["commonName"])) echo "<h1>" . $data["subject"]["commonName"] . "</h1>";
                    if (isset($data["subject"]["organizationName"])) echo "<strong>" . $data["subject"]["organizationName"] . "</strong><br>";
                    if (isset($data["subject"]["title"])) echo  $data["subject"]["title"] . "<br>";
                    if (isset($data["issuer"]["organizationName"])) echo  "<br>Emesso da:<strong> " . $data["issuer"]["organizationName"] . "</strong>";
                    $data = openssl_x509_parse($esito,false);
                    $validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
                    $validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
                    echo "<br><br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
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
            ?><a href="download_allegato.php?codice=<?= $modulo["codice"] ?>" target="_blank" title="Scarica Allegato"><img src="/img/p7m.png" alt="allegato p7m" width="25" /></a><?
            if (strpos($mime_type,"pdf") === false) {
              ?><a href="open_p7m.php?codice=<?= $modulo["codice"] ?>" target="_blank" title="Estrai Contenuto"><img src="/img/download.png" alt="Estrai Contenuto" width="25" /></a><?
            }
          }
          ?>
        </div>
        <div id="modulistica_<?=$id ?>" rel="<?=$id ?>" class="scegli_file" style="float:right"><img src="/img/folder.png" height="30" style="vertical-align:middle"></div>
        <div class="clear"></div>
        <div id="progress_bar_<?=$id ?>" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
      </td>
      <td width="10" style="text-align:center">
        <button type="button" class="btn btn-circle" style="background-color:#D91E18 !important" onClick="elimina('<?= $id ?>','contratti_operatore/documentazione');return false;"><i class="fa fa-times" aria-hidden="true"></i></button>
        <script>
          tmp = (function($){
            return (new ResumableUploader($("#modulistica_<?=$id ?>")));
          })(jQuery);
          uploader.push(tmp);
        </script>
      </td>
    </tr>
    <?
  }
?>
