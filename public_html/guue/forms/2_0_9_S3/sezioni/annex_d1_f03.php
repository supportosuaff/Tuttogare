<h2 style="text-align: center; margin-top:30px">
	<b>
		Allegato D1 – Appalti generici<br>
		Motivazione della decisione di aggiudicare l&#39;appalto senza precedente pubblicazione di<br>
		un avviso di indizione di gara nella Gazzetta ufficiale dell&#39;Unione europea<br>
	</b>
	<i>
		Direttiva 2014/24/UE<br>
		<small>(selezionare l&#39;opzione pertinente e fornire una spiegazione)</small>
	</i>
</h2>
<br>
<table class="bordered">
  <tr>
    <td>
      <?
      $radio_as_select_for_annex_d1 = "";
      if(!empty($guue["PROCEDURE"]["radio_as_select_for_annex_d1"])) {
        $radio_as_select_for_annex_d1 = $guue["PROCEDURE"]["radio_as_select_for_annex_d1"];
      }
      ?>
      <script type="text/javascript">
        var radio_as_select_for_annex_d1_option = {
            'D_ACCORDANCE_ARTICLE' : [
              'ajax_load',
              ['sezioni', 'annex_d1_part1_f03'],
              [],
              'annex_d1_motivation'
            ],
            'D_OUTSIDE_SCOPE' : [
              'ajax_load',
              ['sezioni', 'annex_d1_part2_f03'],
              [],
              'annex_d1_motivation'
            ],
          };
      </script>
      <select name="guue[PROCEDURE][radio_as_select_for_annex_d1]" rel="S;1;0;A" onchange="add_extra_info($(this).val(), radio_as_select_for_annex_d1_option)">
        <option value="">Seleziona..</option>
        <option <?= $radio_as_select_for_annex_d1 == "D_ACCORDANCE_ARTICLE" ? 'selected="selected"' : null ?> value="D_ACCORDANCE_ARTICLE">Motivazione della scelta della procedura negoziata senza previa pubblicazione di un avviso di indizione di gara, conformemente all'articolo 32 della direttiva 2014/24/UE</option>
        <option <?= $radio_as_select_for_annex_d1 == "D_OUTSIDE_SCOPE" ? 'selected="selected"' : null ?> value="D_OUTSIDE_SCOPE">Altre motivazioni per l'aggiudicazione dell'appalto senza previa pubblicazione di un avviso di indizione di gara nella Gazzetta ufficiale dell'Unione europea</option>
      </select>
    </td>
  </tr>
</table>
<div id="annex_d1_motivation">
  <?
  if(! empty($radio_as_select_for_annex_d1) && $radio_as_select_for_annex_d1 == "D_ACCORDANCE_ARTICLE") {
    include 'annex_d1_part1_f03.php';
  } else {
    include 'annex_d1_part2_f03.php';
  }
  ?>
</div>