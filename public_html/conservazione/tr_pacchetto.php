<?
  if (isset($_SESSION["codice_utente"]) && !is_operatore() && !empty($pacchetto)) {
    $error_pacchetto = false;
    ?>
    <tr id="pacchetto_<?= $pacchetto["codice"] ?>">
      <td width="2" style="background-color:<?= $stati_conservazione[$pacchetto["stato"]]["colore"] ?>">
        <?
        $sql = "SELECT * FROM r_conservazione_file WHERE codice_pacchetto = :codice_pacchetto";
        $ris_files = $pdo->bindAndExec($sql,array(":codice_pacchetto" => $pacchetto["codice"]));
        if ($ris_files->rowCount() > 0) {
          ?>
          <div id="listFiles_<?= $pacchetto["codice"] ?>" style="display:none">
            <table width="100%">
            <?
            if(! empty($pacchetto["codice_documento"])) {
              $documento = $pdo->go("SELECT * FROM b_conservazione_documento WHERE codice = :codice", array(":codice" => $pacchetto["codice_documento"]))->fetch(PDO::FETCH_ASSOC);
              if(!empty($documento["documento"])) {
                ?>
                <tr>
                  <td width="10"><img src="/img/pdf.png" width="20" alt="File pdf"></td>
                  <td><strong><a href="/conservazione/documento.php?codice=<?= $documento["codice"] ?>&pacchetto=<?= $pacchetto["codice"] ?>">Documento di riferimento del pacchetto di conservazione (NON INCLUSO NELL'ARCHIVIO)</a></strong></td>
                  <td width="70"><?= human_filesize(strlen($documento["documento"])) ?></td>
                </tr>
                <?
              }
            }
            while ($file = $ris_files->fetch(PDO::FETCH_ASSOC)) {
              $titolo_file = $file["nome_file"];
              if ($file["tabella"] == "allegati" || $file["tabella"] == "buste_gara" || $file["tabella"] == "marcatura_temporale") {
                $sql_titolo = "SELECT * FROM b_allegati WHERE codice = :codice ";
                $ris_titolo = $pdo->bindAndExec($sql_titolo,array(":codice"=>$file["codice_file"]));
                if ($ris_titolo->rowCount() > 0) {
                  $rec_all = $ris_titolo->fetch(PDO::FETCH_ASSOC);
                  $file["nome_file"] = $rec_all["cartella"] . "/" . $file["nome_file"];
                  $titolo_file = $rec_all["titolo"];
                  if ($file["tabella"] == "buste_gara") $titolo_file .= " - Busta crittografata";
                  if ($file["tabella"] == "marcatura_temporale") $titolo_file .= " - Marcatura temporale";
                }
              }
              $file_ext = file_exists($file["file_path"]);
              if (!$file_ext) $error_pacchetto = true;
              $estensione = explode(".",$file["nome_file"]);
              $estensione = end($estensione);
              ?>
              <tr>
                <td width="10"><? if(file_exists($root."/img/".$estensione.".png")) { ?><img src="/img/<?= $estensione ?>.png" width="20" alt="File <?= $estensione ?>"><? } ?></td>
                <td><strong><?= $titolo_file ?></strong>
                <? if ($titolo_file != $file["nome_file"]) { ?>
                  <br><small><?= $file["nome_file"] ?></small>
                <? } ?></td>
                <td width="70"><?= (!$file_ext) ? "<span class='fa fa-warning' style='color:#c00'></span><br>Il file non esiste" : human_filesize(filesize($file["file_path"])) ?></td>
              </tr>
              <?
            }
            ?>
            </table>
          </div>
        <? } ?>
      </td>
      <td width="130"><?= $stati_conservazione[$pacchetto["stato"]]["descrizione"] ?></td>
      <td>
        <?= $pacchetto["denominazione"] ?> <span onClick="$('#description_<?= $pacchetto["codice"] ?>').slideToggle(); return false;" style="cursor:pointer; color:#0040ff" class="fa fa-info-circle"></span><br>
        <small style="display:none;" id="description_<?= $pacchetto["codice"] ?>"><?= $pacchetto["descrizione"] ?></small>
      </td>
      <td width="10" style="text-align:center">
        <a style="cursor:pointer" onClick="$('<div></div>').html($('#listFiles_<?= $pacchetto["codice"] ?>').html()).dialog({title:'Informazioni di pacchetto',modal:'true',width:'800px',close: function(event, ui) { $(this).dialog('destroy').remove();}}); return false;"><? if ($error_pacchetto) { ?><span class='fa fa-warning' style='color:#c00'></span><br><? } ?>
        <?= $ris_files->rowCount() ?></a>
      </td>
      <? if (isset($modulo_conservazione)) { ?>
        <td width="150"><?= $pacchetto["cognome"] . " " . $pacchetto["nome"] ?></td>
        <td width="120"><?= mysql2datetime($pacchetto["timestamp"]) ?></td>
      <? } ?>
      <td style="width:32px;">
        <?
        if (!$error_pacchetto) {
          if(empty($pacchetto["download"]) || $pacchetto["download"] !== "L") {
            ?><a href="/conservazione/pacchetto.php?codice=<?= $pacchetto["codice"] ?>&operazione=download" title="Scarica il pacchetto di conservazione" class="btn-round btn-warning"><span class="fa fa-cloud-download"></span></a><?
          } else {
            ?><a href="javascript:void(0)" title="Scarica il pacchetto di conservazione" class="btn-round btn-default" disabled><i class="fa fa-spinner fa-spin" style="margin-top: 9px;" aria-hidden="true"></i></a><?
          }
         }
        ?>
      </td>
      <? if (isset($modulo_conservazione)) { ?>
      <td style="width:32px;">
        <?
        if (!$error_pacchetto && $invia_pacchetto) {
          if(empty($pacchetto["send"])) {
            ?><a href="/conservazione/pacchetto.php?codice=<?= $pacchetto["codice"] ?>&operazione=send" title="Crea il pacchetto di conservazione" class="btn-round btn-primary"><span class="fa fa-cloud-upload"></span></a><?
          } else if ($pacchetto["send"] == "L") {
            ?><a href="javascript:void(0)" title="Invia il pacchetto di conservazione" class="btn-round btn-default" disabled><i class="fa fa-spinner fa-spin" style="margin-top: 9px;" aria-hidden="true"></i></a><?
          }
        }
        ?>
        <!-- <a href="/conservazione/conservazione/upload.php?codice=<?= $pacchetto["codice"] ?>" title="Crea il pacchetto di conservazione" class="btn-round btn-primary"><span class="fa fa-cloud-upload"></span></a>-->
      </td>
      <? } ?>
      <td style="width:32px;">
        <?
          if ((check_permessi("conservazione",$_SESSION["codice_utente"]) || $pacchetto["stato"] == 0) && $pacchetto["send"] !== "C") {
            ?><button class="btn-round btn-danger" onClick="elimina('<?= $pacchetto["codice"] ?>','conservazione');" title="Elimina"><span class="fa fa-remove"></span></button><?
          }
        ?>
      </td>
    </tr>
    <?
  }
?>
