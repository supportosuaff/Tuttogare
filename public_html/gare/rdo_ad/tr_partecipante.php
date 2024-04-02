<?
  if (isset($_POST["id"])) {
    $id = $_POST["id"];
  }
  if (isset($id)) {
  ?>
  <tr id="partecipante-<?= $id ?>">
    <td>
      <input type="hidden" name="partecipanti[<?= $id ?>][codice_operatore]" id="partecipante-<?= $id ?>-codice_operatore">
      <input type="hidden" name="partecipanti[<?= $id ?>][codice_utente]" id="partecipante-<?= $id ?>-codice_utente">
      <input type="text"class="partita_iva" size="16" name="partecipanti[<?= $id ?>][partita_iva]" title="Codice fiscale impresa" rel="S;8;0;A" id="partecipante-<?= $id ?>-partita_iva">
    </td>
    <td><input style="width:99%" type="text" name="partecipanti[<?= $id ?>][identificativoEstero]" onChange="if ($(this).val() != '') { $('#partecipante-<?= $id ?>-partita_iva').attr('rel','N;8;0;A'); } else { $('#partecipante-<?= $id ?>-partita_iva').attr('rel','S;8;0;A'); }"  title="Identificativo fiscale estero" rel="N;5;0;A" id="partecipante-<?= $id ?>-identificativoEstero"></td>
    <td><input type="text" style="width:99%" name="partecipanti[<?= $id ?>][ragione_sociale]"  title="Ragione Sociale" rel="S;3;255;A" id="partecipante-<?= $id ?>-ragione_sociale"></td>
    <td>
      <input type="text" style="width:99%" name="partecipanti[<?= $id ?>][pec]" title="Pec" rel="S;3;255;E" id="partecipante-<?= $id ?>-pec">
      <input type="text" style="width:99%" name="partecipanti[<?= $id ?>][email]" title="E-mail (opzionale)" rel="N;3;255;E" id="partecipante-<?= $id ?>-email">
    </td>
    <td>
      <? if ($id != "0") { ?><button class='btn-round btn-danger' onClick="$('#partecipante-<?= $id ?>').remove(); return false" title="Elimina">
        <span class="fa fa-remove"></span></button><? } ?>
        <script>
          $("#partecipante-<?= $id ?>-partita_iva").autocomplete({
            source: function(request, response) {
                $.ajax({
                url: "/gare/partecipanti/operatori.php",
                dataType: "json",
                data: {
                  term : request.term,
                },
                success: function(data) {
                  response(data);
                }
                });
              },
              minLenght: 3,
              search  : function(){$(this).addClass('working');},
              open    : function(){$(this).removeClass('working');},
              select: function(e, ui) {
                //e.preventDefault() // <--- Prevent the value from being inserted.
                $("#partecipante-<?= $id ?>-ragione_sociale").val($("<div></div>").html(ui.item.ragione_sociale).text());
                $("#partecipante-<?= $id ?>-pec").val($("<div></div>").html(ui.item.pec).text());
                $("#partecipante-<?= $id ?>-identificativoEstero").val(ui.item.identificativoEstero);
                $("#partecipante-<?= $id ?>-codice_operatore").val(ui.item.codice_operatore);
                $("#partecipante-<?= $id ?>-codice_utente").val(ui.item.codice_utente);
                $(this).focus();
              },
              focus: function(e, ui) {
                //e.preventDefault() // <--- Prevent the value from being inserted.
                $("#partecipante-<?= $id ?>-ragione_sociale").val($("<div></div>").html(ui.item.ragione_sociale).text());
                $("#partecipante-<?= $id ?>-pec").val($("<div></div>").html(ui.item.pec).text());
                $("#partecipante-<?= $id ?>-identificativoEstero").val(ui.item.identificativoEstero);
                $("#partecipante-<?= $id ?>-codice_operatore").val(ui.item.codice_operatore);
                $("#partecipante-<?= $id ?>-codice_utente").val(ui.item.codice_utente);
              }
            }).data("ui-autocomplete")._renderItem = function( ul, item ) {
                  return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> - " + item.label).appendTo( ul );
            }
        </script>
    </td>
  </tr>
  <?
    }
  ?>
