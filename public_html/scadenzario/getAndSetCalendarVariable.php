<?
if(empty($mon)) $mon = (int)(isset($_GET['month']) ? $_GET['month'] : (!empty($_POST["month"]) ? $_POST["month"] : date("m")));
if(empty($year)) $year = (int)(isset($_GET['year']) ? $_GET['year'] : (!empty($_POST["year"]) ? $_POST["year"] : date("Y")));
if (empty($mon)) $mon = date("m");
if (empty($year)) $year = date("Y");
$first_day = mktime(0,0,0,$mon,1,$year);
$start_day = date("w",$first_day);

$res = getdate($first_day);
$month_name = $res["month"];
$no_days_in_month = date("t", $first_day);    // t -> numero dei giorno del mese

$queryStart = date('Y-m-d',$first_day);
$queryEnd = date('Y-m-',$first_day).$no_days_in_month." 23:59:59";

for ($i = 1; $i <= $start_day;$i++) {
  $dates[1][$i] = " ";
}

$row = 1;
$col = $start_day + 1;
$num = 1;    //contatore dei giorni del mese
while($num <= 31) {
  if ($num > $no_days_in_month)    break;    //se non ci sono più giorni nel mese esco da ciclo
    $dates[$row][$col] = $num;
    $num += 1;
    $col += 1;
    if ($col > 7) {
      $row += 1; $col = 1;
    }
}	//fine while

$mon_num = date("m",$first_day);          //calcolo del numero del mese -> n = senza eventuali 0
$temp_yr = $next_yr = $prev_yr = $nav_year = $year;    //imposto l'anno di visualizzazione

$prev_month = $mon_num - 1;
$next_month = $mon_num + 1;
$prev_yr = $nav_year;
$next_yr = $nav_year;
//Se Gennaio è il mese corrente imposto il mese precedente a Dicembre dell'anno precedente
if ($mon_num == 1){
  $prev_yr = $year - 1;
  $prev_month = 12;
}

//Se il dicembre è il mese corrente imposto il mese successivo a Gennaio dell'anno successivo
if ($mon_num == 12){
  $next_yr = $year + 1;
  $next_month = 1;
}

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
