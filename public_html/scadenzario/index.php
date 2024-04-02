<?
  include_once '../../config.php';
  include_once "{$root}/layout/top.php";
  ?>
  <style media="screen">
    .day,
    .today {
      text-align: center;
      padding: 3px;
      float: right;
      height: 20px;
      width: 20px;
    }
    .today {
      font-weight: bold;
      background-color: red;
      color: white;
      border-radius: 50%;
    }
    tr.calendar > td {
      height: 100px;
      max-height: 100px !important;
      border: 1px solid rgba(158, 158, 158, 0.30);
      vertical-align: top;
      cursor: pointer;
    }
    tr.calendar > th {
      font-weight: bold;
    }
    .cont {
      position: relative;
    }
    .cal-left {
      width: 70%;
      padding: 0px;
      float:left;
    }
    .cal-right {
      position: absolute;
      right: 0;
      top: 0;
      bottom: 0;
      width: 29%;
      float: right;
      padding: 0px;
      overflow: scroll;
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
      height: 80px;
      overflow: scroll;
    }
    .next-prome {
      margin: 5px 0px;
      padding: 7px;
      background-color:rgba(158, 158, 158, 0.1);
    }
    .prome-container {
      position: relative;
    }
  </style>
  <h1><?= traduci("SCADENZARIO") ?></h1>
  <?
  include 'getAndSetCalendarVariable.php';
  ?>
  <table border="0" cellspacing="0" style="width:100%; margin:25px 0px;">
    <tr class="triang">
      <td style="text-align:left; font-weight: lighter; font-size: 16px; width:100px;">
        <a href="<?= "index.php?year={$prev_year}&month={$mon_num}" ?>" style="text-decoration: none;"><i class="fa fa-arrow-circle-left"></i> <?= $prev_year ?></a>
      </td>
      <td style="text-align:center; font-weight: lighter; font-size: 20px;">
        <a href="<?= "index.php?month={$prev_month}&year={$prev_yr}" ?>" title="Mese precedente"><i class="fa fa-arrow-circle-left"></i></a>
        <span style="margin:0px 20px;"> <?= $mese . " " . $year ?></span>
        <a href="<?= "index.php?month={$next_month}&year={$next_yr}" ?>" title="Mese successivo"><i class="fa fa-arrow-circle-right"></i></a>
        <br>
        <a style="font-size:12px;" href="<?= "index.php?month=".date('m')."&year=".date('Y') ?>" title="Oggi"><i class="fa fa-calendar"></i> <?= traduci("oggi") ?></a>
      </td>
      <td style="text-align:right; font-weight: lighter; font-size: 16px; width:100px;">
        <a href="<?= "index.php?year={$next_year}&month={$mon_num}" ?>" style="text-decoration: none;"><?= $next_year ?> <i class="fa fa-arrow-circle-right"></i></a>
      </td>
    </tr>
  </table>
  <div class="cont">
    <div class="cal-left">
      <? include 'calendar.php' ?>
    </div>
    <div class="cal-right">
      <table border="0" cellspacing="0" width="100%" class="triang" cellspacing="1">
        <tbody>
          <tr class="calendar">
            <th colspan="2">
              <?= traduci("Scadenze") ?> <?= $mese . " " . $year ?>
            </th>
          </tr>
        </tbody>
      </table>
      <?
      if(!empty($promemoria)) {
        echo array2eventsList($promemoria, TRUE);
      }
      ?>
    </div>
    <div class="clear"></div>
  </div>
<?
include_once "{$root}/layout/bottom.php";
?>
