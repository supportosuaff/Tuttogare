<?
// if(!empty($root)) {
  $settings = array(
    "scadenze" => array(
      'color' => '#009688',
      'label' => traduci('Promemoria'),
    ),
    "gare" => array(
      'color' => '#E53935',
      'label' => traduci('Scadenza gara'),
    ),
    'richiesta_info_gara' => array(
      'color' => '#01579B',
      'label' => traduci('Termine Chiarimenti'),
    ),
    'apertura_buste_gara' => array(
      'color' => '#43A047',
      'label' => traduci('Apertura Buste'),
    ),
    'avvio_2fase' => array(
      'color' => '#00BCD4',
      'label' => traduci('Avvio seconda fase'),
    ),
    'avvio_asta' => array(
      'color' => '#F9A825',
      'label' => traduci('Avvio Asta'),
    ),
    'scadenza_asta' => array(
      'color' => '#EF6C00',
      'label' => traduci('Scadenza Asta'),
    ),
    'scadenza_integrazioni' => array(
      'color' => '#795548',
      'label' => traduci('Scadenza Integrazioni'),
    ),
    'bandi_sda' => array(
      'color' => '#C2185B',
      'label' => traduci('Scadenza S.D.A.'),
    ),
    'mercato_elettronico' => array(
      'color' => '#C2185B',
      'label' => traduci('Scadenza Mercato Elettronico'),
    ),
    'scadenza_fabbisogno' => array(
      'color' => '#1A237E',
      'label' => traduci('Scadenza Fabbisogno'),
    ),
    'termine_contrato' => array(
      'color' => '#455A64',
      'label' => traduci('Termine Contratto'),
    ),
    'promemoria_termine_contrato' => array(
      'color' => '#009688',
      'label' => traduci('Promemoria Termine Contratto'),
    ),
    'consegna_lavori' => array(
      'color' => '#5cfb67',
      'label' => traduci('Appuntamento consegna lavori'),
    ),
    'bando_albo' => array(
      'color' => '#C2185B',
      'label' => traduci('Scadenza Albo Fornitori'),
    ),
    'bando_dialogo' => array(
      'color' => '#C2185B',
      'label' => traduci('Scadenza Dialogo Competitivo'),
    )
  );

  function array2eventsList($array, $day = FALSE) {
    global $settings;

    if(!empty($array)) {
      $days = array_keys($array);
      sort($days);
      foreach ($days as $date) {
        $day_events = $array[$date];
        $keys = array_keys($day_events);
        sort($keys);
        foreach ($keys as $hour) {
          $hour_events = $day_events[$hour];
          foreach ($hour_events as $event) {
            ?>
            <div class="next-prome" style="border-left: 5px solid <?= $settings[$event["tipologia"]]["color"] ?>">
              <?
              if($day) { echo '<i class="fa fa-calendar-o"></i> ' . mysql2date($event["data"]) . " "; }
              if(!empty($event["link"]) && isset($_SESSION["ente"])) {?><a style="float:right; margin-left:5px; color:<?= $settings[$event["tipologia"]]["color"] ?>" href="<?= $event["link"] ?>"><i class="fa fa-search"></i></a><?}
              if(!empty($hour)) {
                ?><span <?= $day ? 'style="float:right;"' : 'style="font-size:20px;"' ?>><i class="fa fa-clock-o" ></i> <?= substr($hour, 0, 5) ?> <? if(!$day) {?><b><?= $settings[$event["tipologia"]]["label"] ?></b><?} ?></span><?
              } else {
                if(!$day) {?><span style="font-size:20px; font-weight:bold;"><?= $settings[$event["tipologia"]]["label"] ?></span><?}
              }
              ?>
              <br><hr style="border-bottom: 1px solid #ddd;">
              <? if (!empty($event["online"]) && $event["online"] == "S") { ?>
                 <span class="fa fa-globe"></span>
              <? } ?>
              <? if($day) {?><b><?= $settings[$event["tipologia"]]["label"] ?>:</b> <?} ?><span style="font-weight:bold"><?= $event["oggetto"] ?></span>
              <hr style="border-bottom: 1px solid #ddd;">
              <span style="font-size:13px; color:#757575;"><?= ucfirst(strtolower(html_entity_decode($event["descrizione"], ENT_QUOTES, 'UTF-8'))) ?></span>
              <? if (!isset($_SESSION["ente"]) && !empty($event["ente"])) { ?><br>
                 <div style='text-align:right'><small><span class="fa fa-university"></span> <?= $event["ente"] ?></small></div>
              <? } ?>
            </div>
            <?
          }
        }
      }
    }
  }

  include_once 'getAndSetCalendarVariable.php';

  $promemoria = array();
  include_once 'data.php';

  //trasformo i mesi dal formato standard a quello italiano
  switch (date("F", $first_day)) {
    case 'January': $mese = "Gennaio"; $n = 1; break;
    case "February": $mese = 'Febbraio'; $n = '02'; break;
    case "March": $mese = 'Marzo'; $n = '03'; break;
    case "April": $mese = 'Aprile'; $n = '04'; break;
    case "May": $mese = 'Maggio'; $n = '05'; break;
    case "June": $mese = 'Giugno'; $n = '06'; break;
    case "July": $mese = 'Luglio'; $n = '07'; break;
    case "August": $mese = 'Agosto'; $n = '08'; break;
    case "September": $mese = 'Settembre'; $n = '09'; break;
    case "October": $mese = 'Ottobre'; $n = '10'; break;
    case "November": $mese = 'Novembre'; $n = '11'; break;
    case "December": $mese = 'Dicembre'; $n = '12'; break;
    default: $mese = ""; break;
  }
  $mese = traduci($mese);
  //imposto le variabili per navigare negli anni
  $next_year = $year + 1;
  $prev_year = $year - 1;
  ?>
  <table border="0" cellspacing="0" width="100%" class="triang">
    <tr>
      <th style="width:14%">Dom</th>
      <th style="width:14%">Lun</th>
      <th style="width:14%">Mar</th>
      <th style="width:14%">Mer</th>
      <th style="width:14%">Gio</th>
      <th style="width:14%">Ven</th>
      <th style="width:14%">Sab</th>
    </tr>
    <tr class="calendar">
    <?
    $end = $start_day > 4 ? 6 : 5;
    $count = 0;
    for ($row = 1; $row <= $end; $row++) {
      for ($col = 1; $col <= 7; $col++) {
        if (@$dates[$row][$col] == '') $dates[$row][$col] = ' ';
        if (!strcmp($dates[$row][$col], ' ')) $count += 1;
        $t = $dates[$row][$col];
        $mon = substr("00" . $mon,-2);
        $class = "day";
        if (($t == date("d")) && ($mon == date("m")) && ($year == date("Y"))) $class = "today";
        ?>
        <td data-target="day_<?= $t ?>" <?= $col == 1 || $col == 7 ? 'style="background-color:rgba(158, 158, 158, 0.2)"' : null ?>>
          <div class="<?= $class ?>"><?= $t ?></div><div class="clear"></div>
          <?
          if(!empty($promemoria[$t])) {
            $show_pin = FALSE;
            ?>
            <div id="day_<?= $t ?>" style="display:none;"><h1><i class="fa fa-calendar-o"></i> <?= date($t.'/m/Y',$first_day); ?></h1><?= array2eventsList(array($t => $promemoria[$t])) ?></div>
            <div class="prome-container">
              <?
              $events = array_fill_keys(array_keys($settings), 0);
              $online = array();
              foreach ($promemoria[$t] as $hour => $event) {
                foreach ($event as $prome) {
                  $events[$prome["tipologia"]] += 1;
                  if (!empty($prome["online"]) && $prome["online"] == "S") $online[$prome["tipologia"]] = true;
                }
              }
              if(empty($min)) {
                foreach ($events as $key => $count) {
                  if($count > 0) {
                    ?>
                    <div class="prome" style="background-color: <?= $settings[$key]["color"] ?>;">
                      <? if (!empty($online[$key])) echo "<span class='fa fa-globe'></span>" ?> <b><?= $count ?></b> <?= $settings[$key]["label"] ?>
                    </div>
                    <?
                  }
                }
              } else {
                $events = array_filter($events);
                if(count($events) > 0) {$show_pin = TRUE;}
              }
              ?>
            </div>
            <?
            if($show_pin) {?><div class="pin"></div><?}
          }
          ?>
        </td>
        <?
      }
      if (($row + 1) != ($end+1)) echo '</tr>'.PHP_EOL.'<tr class="calendar">';
    }
    ?>
    </tr>
  </table>
  <script type="text/javascript">
    $(document).on('click', 'tr.calendar > td', function(event) {
      event.preventDefault();
      if(typeof $(this).data('target') !== 'undefined' && $(document).find('#'+$(this).data('target')).length > 0) {
        $('#'+$(this).data('target')).dialog({
            title: 'Info',
            modal: true,
            width: '60%',
            position: 'center',
            create: function() {
                $(this).css("maxHeight", "100%");
            },
            close: function() {
              $(this).dialog('destroy');
            }
        });
      }
    });
  </script>
  <?
// }
?>
