<script type="text/javascript" src="/js/resumable.js"></script>
<script type="text/javascript" src="/allegati/resumable-uploader.js"></script>

<?
$id = "";
if (isset($form_upload["id"])) $id = $form_upload["id"];
?>
<div id="div_allegati<? echo $id ?>" class="div_allegati">
  <div id="upload_allegati<? echo $id ?>">
    <div id="resumable-drop<? echo $id ?>" class="resumable-drop" onMouseOut="$(this).removeClass('resumable-dragover');" ondragenter="$(this).addClass('resumable-dragover');" ondragend="$(this).removeClass('resumable-dragover');" ondrop="$(this).removeClass('resumable-dragover');">
      Trascina il file o <br>
      <a style="cursor:pointer" id="resumable-browse<? echo $id ?>" class="resumable-browse"><u>Seleziona dal computer</u></a>
    </div>
    <form name="upload_allegato" id="upload_allegato<? echo $id ?>" action="/allegati/upload_allegati.php" method="post" rel="validate">
      <?
      if (!isset($form_upload["online"])) $form_upload["online"] = '';
      if (isset($form_upload["cartella"])) { ?><input type="hidden" name="cartella" value="<? echo $form_upload["cartella"] ?>"><? }
	    if (isset($form_upload["codice_gara"])) { ?><input type="hidden" name="codice_gara" value="<? echo $form_upload["codice_gara"] ?>"><? }
      if (isset($form_upload["sezione"])) { ?><input type="hidden" name="sezione" value="<? echo $form_upload["sezione"] ?>"><? }
      ?>
      <div class="big_progress_bar" id="progress_bar<? echo $id ?>"><div class="progress_bar"></div></div>
      <input type="submit" value="Salva" id="submit_allegati<? echo $id ?>" class="submit_big"><br>
      <div class="progress" style="display:none;"><div id="list<? echo $id ?>"></div></div>
    </form>
    <script>
      var uploader = (function($){
        return (new ResumableUploader($('#resumable-browse<? echo $id ?>'), $('#resumable-drop<? echo $id ?>'), $('.progress'), '<? echo $form_upload["online"] ?>','<? echo $id ?>'));
      })(jQuery);
    </script>
    <div class="clear"></div>
  </div>
    <?
  	$tab_elenco = "tab_allegati";
    if (isset($form_upload["online"]) && $form_upload["online"] == "N") {$tab_elenco = "tab_riservati";}?>
</div>
<script>
	function aggiungi_allegato(codice) {
		var allegati = $("#cod_allegati").val();
		if (allegati != undefined) {
			allegati = allegati.split(";");
			allegati.push(codice);
			allegati = allegati.join(";");
			$("#cod_allegati").val(allegati);
			$("#allegato_disponibile_"+codice).slideUp();
			$.ajax({
				type: "POST",
				url: "/allegati/tr_allegati.php",
				dataType: "html",
				data: "codice="+codice,
				async:false,
				success: function(script) {
					$("#<? echo $tab_elenco ?><? echo $id ?>").append(script);
				}
			});
		}
		f_ready();
	}
</script>
