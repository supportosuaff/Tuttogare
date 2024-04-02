<?
  if (($_SESSION["gerarchia"] === "0" || $_SESSION["ente"]["codice"] == $_SESSION["record_utente"]["codice_ente"] || $_SESSION["ente"]["permit_cross"] == "S") && !empty($sezione_conservazione) && !empty($oggetto_conservazione)) {
    if (file_exists("conserva.php")) {
      $messaggi = getRicevuteNonConservate($sezione_conservazione,$oggetto_conservazione["codice"]);
      if (!empty($messaggi)) {
        if (!isset($status_conservazione[-1])) $status_conservazione[-1] = [];
        $status_conservazione[-1] = array_merge($status_conservazione[-1],$messaggi);
      }
    if (!empty($status_conservazione[-1])) {
      ?><div style="float:left; width:49%"><?
    }
    if (empty($stati_conservazione)) $stati_conservazione = json_decode(file_get_contents($root."/conservazione/stati.json"),true);

    $bind = array(":sezione"=>$sezione_conservazione,":codice_oggetto"=>$oggetto_conservazione["codice"]);
    $sql = "SELECT * FROM b_conservazione WHERE sezione = :sezione AND codice_oggetto = :codice_oggetto";
    if($_SESSION["gerarchia"] > 0) {$sql .= " AND (b_conservazione.codice_gestore = :codice_ente OR b_conservazione.codice_ente = :codice_ente)"; $bind[":codice_ente"] = $_SESSION["record_utente"]["codice_ente"]; }
    $ris_pacchetti = $pdo->bindAndExec($sql,$bind);

    if ($ris_pacchetti->rowCount() > 0) {
     ?>
     <table width="100%">
       <thead>
         <tr>
           <th></th>
           <th>Stato</th>
           <th>Pacchetto</th>
           <th width="10">Files</th>
           <th width="10"></th>
           <th width="10"></th>
         </tr>
       </thead>
       <tbody>
       <?
       while($pacchetto = $ris_pacchetti->fetch(PDO::FETCH_ASSOC)) {
         include($root."/conservazione/tr_pacchetto.php");
       }
       ?>
       </tbody>
     </table>
     <?
   } else { ?>
     <h3>Non sono presenti pacchetti</h3>
   <? }
     if (!empty($status_conservazione[-1])) {
       ?></div>
       <script>
       function escludi_conservazione(el) {
         var relazioni_str = $("#esclusioni_conservazione").val();
         relazioni_str = relazioni_str.trim();
         var relazioni = new Array();
         relazioni = relazioni_str.split(",");
         var checked = el.hasClass("button-action");
         var codice = String(el.data("codice"));
         if (checked) {
           relazioni.push(codice);
           el.removeClass('button-action').addClass("btn-danger");
         } else {
           var pos = $.inArray(codice, relazioni);
           relazioni.splice(pos, 1);
           el.addClass('button-action').removeClass("btn-danger");
         }
         relazioni_str = relazioni.join(",");
         $('#esclusioni_conservazione').val(relazioni_str);
         return false;
       };
       </script>
       <div style="float:right; width:49%">
         <form action="conserva.php" rel="validate" method="post">
           <input type="hidden" name="codice" value="<?= $_GET["codice"] ?>">
           <input type="hidden" name="esclusioni" id="esclusioni_conservazione" value="">

           <table width="100%">
             <tr>
               <td class="etichetta">Nome</td>
             </tr>
             <tr>
               <td><input type="text" name="denominazione" title="Nome" value="<?= ucfirst($sezione_conservazione) ?> CIG: <?= $oggetto_conservazione["cig"] ?>" style="width:99%"></td>
             </tr>
             <tr>
               <td class="etichetta">Descrizione</td>
             </tr>
             <tr>
               <td><input type="text" name="descrizione" title="Descrizione" value="Pacchetto di conservazione per la <?= $sezione_conservazione ?> CIG: <?= $oggetto_conservazione["cig"] ?> del <?= date("d/m/Y") ?>" style="width:99%"></td>
             </tr>
             <tr>
               <td style="text-align:center" class="padding">
                 <strong id="count_files"><?= count($status_conservazione[-1]) ?></strong> Files<br>
                 <button onclick="$('#list_files_cons').toggle();return false;" class="btn-round btn-default"><span class="fa fa-list"></span></button>
                 <table width="100%" id="list_files_cons" style="display:none">
                   <? foreach($status_conservazione[-1] AS $file_cons) {
                     if (isset($file_cons["online"])) {
                       $codice = "allegati_".$file_cons["codice"];
                       $icon = "";
                       if ($file_cons["online"]=="N") $icon = "<span class='fa fa-lock'></span>";
                       $titolo = $file_cons["titolo"];
                       $path = $file_cons["cartella"] . "/" . $file_cons["nome_file"] ;
                     } else {
                       $icon = "<span class='fa fa-envelope'></span>";
                       $codice = "msg_".$file_cons["codice"];
                       $titolo = "Ricevuta comunicazione {$file_cons["codice"]}";
                       $path = "";
                     }
                     ?>
                     <tr>
                       <td width="1"><?= $icon ?></td>
                       <td>
                         <strong><?= $titolo ?></strong><br>
                         <small><?= $path ?></small>
                       </td>
                       <td width="10">
                         <button class="btn-round button-action" onclick='escludi_conservazione($(this)); return false;' data-codice="<? echo $codice ?>">
                           <span class="fa fa-archive"></span>
                         </button>
                       </td>
                     </tr>
                   <? } ?>
                 </table>
               </td>
             </tr>
             <tr>
               <td>
                 <button type="submit" class="submit_big">Crea il pacchetto</span></button>
               </td>
             </tr>
           </table>
         </form>
       </div>
       <?
     }
   } else { ?>
     <h2>Il generatore di pacchetto non &egrave; configurato. Contattare l'assistenza</h2>
   <? }
   } else { ?>
   <h2>Non sono presenti allegati o non si dispone dei permessi necessari</h2>
 <? } ?>
