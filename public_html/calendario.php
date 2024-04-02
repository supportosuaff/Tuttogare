<?
  @session_start();
	include_once("../config.php");
	include_once($root."/inc/funzioni.php");
  ?>
  <style media="screen">
    .day,
    .today {
      text-align: center;
      padding: 3px;
      float: right;
      max-height: 20px;
      max-width: 20px;
      min-width: 15px;
      min-height: 15px;
    }
    .today {
      font-weight: bold;
      background-color:#E53935;
      border-radius: 50%;
      color: white;
      padding: 5px;
      border-radius: 50%;
    }
    tr.calendar > td {
      border: 1px solid rgba(158, 158, 158, 0.30);
      vertical-align: top;
      cursor: pointer;
      position: relative;
    }
    tr.calendar > th {
      font-weight: bold;
    }
    .cont {
      position: relative;
    }
    .prome {
      max-height: 100px;
      padding: 2px;
      overflow: hidden;
      font-size: 10px;
      color: #FFF;
      margin-bottom: 3px;
      cursor: pointer;
    }
    .prome-container {
    }
    .next-prome {
      margin: 5px 0px;
      padding: 7px;
      background-color:rgba(158, 158, 158, 0.1);
    }
    .prome-container {
      position: relative;
    }
    .pin {
      position: absolute; left:2px; right: 0; bottom: 2px; text-align:center; height:8px; width:8px; background-color:#E53935; border-radius: 50%;
    }
  </style>
  <?
  include $root . '/scadenzario/getAndSetCalendarVariable.php';
  ?>
  <span style="font-size:18px; text-transform:uppercase; color:#757575"> <?= $mese . " " . $year ?></span>
  <div style="float:right; text-align:right; font-size:18px; color:#757575">
    <a style="color:#757575; cursor:pointer" title="Mese precedente" onclick="event.preventDefault();update('<? echo $prev_month ?>','<? echo $prev_yr ?>')"><i class="fa fa-arrow-circle-left"></i></a>
    <a style="color:#757575; cursor:pointer" title="Oggi" onclick="event.preventDefault();update('<?=date("m") ?>','<?=date("Y") ?>')"><i class="fa fa-calendar"></i></a>
    <a style="color:#757575; cursor:pointer" title="Mese successivo"><i class="fa fa-arrow-circle-right" onclick="event.preventDefault();update('<? echo $next_month ?>','<? echo $next_yr ?>')"></i></a>
  </div>
  <div class="clear" style="padding: 5px 0px;"></div>
  <?
  $min = TRUE;
  include $root . '/scadenzario/calendar.php';
  ?>
  <script type="text/javascript">
    function update(month,year) {
      $('#calendar').fadeOut('fast');
      $('#lazy_loading').fadeIn('fast');
      $('#calendar').load('calendario.php',{"month":month, "year":year}, function(){
        //console.log(month,year);
        $('#lazy_loading').fadeOut('fast');
        $('#calendar').fadeIn('fast');
        f_ready();
      });
    }
  </script>
