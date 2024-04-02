<?php
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");


/*##################################################################################################################*\

                                                       AlberT-CodFis-1.6

   Questo programma calcola il CODICE FISCALE.
   E' sufficiente includere questo file nella propria pagina PHP con una direttiva del tipo:

      include_once("/home/httpd/html/path/AlberT-CodFis.php");

   Sara' a questo punto sufficiente richiamare la funzione AlberT_CodFis() per ottenere il codice fiscale:

      $codicefiscale = AlberT_CodFis("COGNOME","NOME","DATA_DI_NASCITA","SESSO","COMUNE_DI_NASCITA", "PROVINCIA");

   mettendo eventualmente al posto delle stringhe le variabili che provengino dalla form HTML:

      $codicefiscale = AlberT_CodFis($cognome, $nome, $data, $sesso, $comune, $prov);

   Non � necessario che le stringhe siano maiuscole, mentre la DATA_DI_NASCITA DEVE essere nel formato "ggmmaaaa";
   l' argomento SESSO deve essere "M" o "F" (maiuscolo o minuscolo).
   La funzione esegue una mondatura automatica di NOME COGNOME e COMUNE da APOSTROFI, SIMBOLI E CARATTERI ACCENTATI.

   La funzione AlberT_CodFis restituisce il codice fiscale o una stringa esplicativa di errore.


   Questo programma � Free Software, by Emiliano <AlberT> Gabrielli.
   Qualsiasi uso o modifica di questo programma deve sottostare alla Gnu GPL 2. Il suo uso e diffusione e' incentivato
   dal'autore, ma ne e' espressamente vietata la modifica e/o la distribuzione se non sotto questa stessa licenza.

   Questa licenza NON DEVE ESSERE RIMOSSA!

   Per bugs, consigli e quant'altro ( ringraziamenti!?!! :-)  ) contattate l'autore all'indirizzo e-mail:

                                                    AlberT@SuperAlberT.it
   e visitate il sito:

                                                 http://www.SuperAlberT.it

   Questo programma � stato possibile grazie ai sorgenti di kodicefiscale di Catuzzi Samuele <samuele.c@yahoo.it> !
   Un ringraziamento va a Valter Sini che mi ha segnalato un bug nel calcolo del codice per cognomi come il suo. :-)
   Grazie agli amici di php-it@ziobudda.net � stata semplificata la procedura di controllo della data con l'uso
   di checkdate().
   Grazie anche a Luigi Tatangelo per l'inclusione della ricerca della provincia (me l'ero scordata :-P).

\*##################################################################################################################*/





/////////////////////////////////////////////////////////////////////////////////////////
// MODIFICARE QUESTA VARIABILE PER PUNTARE ALLA DIR CONTENENTE I FILES DEI COMUNI!!!!  //
/////////////////////////////////////////////////////////////////////////////////////////
$CFconf["thisdir"]        =        dirname(__FILE__).'/';                                  //
/////////////////////////////////////////////////////////////////////////////////////////



function levaconsonanti($stringa){
 $temp=str_replace(" ","",$stringa);
 $temp=preg_replace("/[BCDFGHJKLMNPQRSTVWXYZ]/","",$temp);
 return $temp;
}


function levavocali($stringa){
 $temp=str_replace(" ","",$stringa);
 $temp=preg_replace("/[AEIOU]/","",$temp);
 return $temp;
}

function levastranezze($stringa){
 $stringa=str_replace("' "," ",$stringa);
 $stringa=str_replace("'"," ",$stringa);
 $stringa=str_replace("�","a",$stringa);
 $stringa=str_replace("�","e",$stringa);
 $stringa=str_replace("�","e",$stringa);
 $stringa=str_replace("�","i",$stringa);
 $stringa=str_replace("�","o",$stringa);
 $stringa=str_replace("�","u",$stringa);
 $stringa=str_replace("-","",$stringa);
 $stringa=str_replace("[","",$stringa);
 $stringa=str_replace("]","",$stringa);
 $stringa=preg_replace("/[\\\"!?^$|�%&)(=`,;:.+*\/<>@#���]/","",$stringa);
 $stringa=trim($stringa);
 return $stringa;
}

function sceglifile($comune){                                        // I COMUNI SONO SUDDIVISI IN TRE FILES
  $temp=strtoupper($comune);                                         //  A SECONDA DELLA LORO LETTERA INIZIALE
  $iniziale=substr($temp,0,1);                                       //  PER MIGLIORARE LE PRESTAZIONI:
  if ( strchr("ABCDE",$iniziale) ) {                                 //  [A-E] --> COMUNI_1.DAT
    $suffisso="1";                                                     //  [F-R] --> COMUNI_2.DAT
  }                                                                     //  [S-Z] --> COMUNI_3.DAT     :-)
  elseif ( strchr("FGHIJKLMNOPQR",$iniziale) ) {
    $suffisso="2";
  }
  elseif ( strchr("STUVWXYZ",$iniziale) ) {
    $suffisso="3";
  }
  else{
    $error = "ERRORE!!! \nERRORE: il comune DEVE iniziare con una lettera!!";
    return $error;
  }
  $file="comuni_$suffisso.dat";
  return $file;
}

function cercacitta($comune, $pr) {
	global $pdo;
  if ($comune=="") {
    $error = "ERRORE: SPECIFICARE UN COMUNE!";
    return $error;
  }

  $error="Errore!!!\n Non � stato possibile trovare il Comune. Verificare i dati.";
	$bind = array(":comune"=>$comune);
	$sql = "SELECT * FROM b_comuni WHERE descr = :comune";
	$ris = $pdo->bindAndExec($sql,$bind);
	$daticitta = "";
	if ($ris->rowCount()>0) {
		$rec = $ris->fetch(PDO::FETCH_ASSOC);
		$daticitta = array();
		$daticitta[0] = $comune;
		$daticitta[1] = "";
		$daticitta[2] = $rec["cf"];
	}

  return $daticitta;
}

function controlladati($cognome, $nome, $datadinascita, $sesso, $citta, $pr) {

 ////////////////////////////////////////////////////////////////////////////////////////////
 //                                 GESTIONE DEGLI ERRORI                                  //
 ////////////////////////////////////////////////////////////////////////////////////////////


 // DATA DI NASCITA
 if ( (strlen($datadinascita)!=8) || ($datadinascita<=0)) {
   $error="Errore nella data di nascita, la data deve essere nella forma ggmmaaaa";
   return $error;
 }
 $giorno = substr($datadinascita,0,2);
 $mese   = substr($datadinascita,2,2);
 $anno   = substr($datadinascita,4,4);
 $error  ="Errore nella data di nascita, la data non risulta valida!";
 if (!checkdate($mese, $giorno, $anno)) return $error;

 // SESSO
 $sesso=strtoupper($sesso);
 if (($sesso != "M") && ($sesso != "F")) {
   $error = "Errore !!!  Errore nel campo 'sesso' (M/F)" ;
   return $error;
 }
 // COMUNE
 if ($citta == "") {
   $error = "Errore !!!\nErrore, specificare il comune di nascita." ;
   return $error;
 }
 else {
  $daticitta=cercacitta($citta, $pr);
  if (gettype ($daticitta) == "string") return $daticitta;           // COMUNE NON TROVATO!!!
  $sigla  = $daticitta[1];
  $cod_comune = $daticitta[2];
 }
 // COGNOME
 if ( (strlen($cognome) == 0 ) ) {
  $error = "Errore !!!\nErrore sul campo Cognome, dato non valido.";
   return $error;
 }
 // NOME
 if ( (strlen($nome) == 0 ) ) {
  $error = "Errore !!!\nErrore sul campo Nome, dato non valido.";
   return $error;
 }
 return $daticitta;

}

function AlberT_CodFis($cognome, $nome, $datadinascita, $sesso, $citta, $pr = "") {

// Calcola il codicefiscale in caso di successo, ritorna una stringa descrittiva se c'e' un errore
 static $error;

 $nome=levastranezze($nome);
 $cognome=levastranezze($cognome);
 $citta=levastranezze($citta);
 $pr = levastranezze($pr);

 $cod_comune="";
 $numvocali=0;
 $numconsonanti=0;
 $numeri="0123456789";
 $alfabeto    =    "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
 $alfabeto_dispari="BAKPLCQDREVOSFTGUHMINJWZYX";

 $daticitta=controlladati($cognome, $nome, $datadinascita, $sesso, $citta, $pr);
 if (gettype ($daticitta) == "string") return $daticitta;            // ERRORE!!!
 $sigla=$daticitta[1];
 $cod_comune=$daticitta[2];
//  Ora calcolo il cognome

 $ooe = levaconsonanti(strtoupper($cognome));
 $cgnm = levavocali(strtoupper($cognome));

 $numvocali=strlen($ooe);
 $numconsonanti=strlen($cgnm);


 switch($numconsonanti) {
   case 0 :         // zero consonanti
           if ($numvocali==2){
             $codicefiscale=$ooe."X";
           }
           else {   //  caso in cui le vocali o sono 0 o >=3
               $error ="Errore !!! \nErrore sul cognome, caso non previsto da questo algoritmo \n...impossibile calcolare il codice fiscale.";
               return $error;
           }
           break;

   case 1 :         // una consonante
           switch($numvocali) {
              case 0:
               $error ="Errore !!! \nErrore sul cognome, caso non previsto da questo algoritmo \n...impossibile calcolare il codice fiscale.";
               return $error;

              case 1 :
               $codicefiscale=$cgnm.$ooe."X";
               break;

              default :
               $codicefiscale=$cgnm.substr ($ooe,0,2);
               break;
           }
        break;

   case 2 :         // due consonanti
        if ($numvocali>=1) {
          $codicefiscale=$cgnm.substr($ooe,0,1);
        }
        else {
          $error = "Errore !!! \nErrore sul cognome, caso non previsto da questo algoritmo \n...impossibile calcolare il codice fiscale";
          return $error;
        }
        break;

   default :
        $codicefiscale=substr($cgnm,0,3);
        break;
 }

//  Ora calcolo il nome

 $oe = levaconsonanti(strtoupper($nome));
 $nm = levavocali(strtoupper($nome));
 $numvocali = strlen($oe);
 $numconsonanti = strlen($nm);

 switch($numconsonanti) {
   case 0 :
        if ($numvocali==2) {
          $codicefiscale.=$oe."X";
        }
        else {
         $error =  "Errore !!! \nErrore sul nome, caso non previsto da questo algoritmo \n...impossibile calcolare il codice fiscale";
         return $error;
        }
        break;

   case 1 :
        switch($numvocali) {
          case 0 :
            $error = "Errore !!! \nErrore sul nome, caso non previsto da questo algoritmo \n...impossibile calcolare il codice fiscale";
            return $error;

          case 1 :
            $codicefiscale.=$nm.substr($oe,0,1)."X";
            break;

          default :
            $codicefiscale.=$nm.substr($oe,0,2);
            break;
        }
        break;

   case 2 :
        if ($numvocali>=1) {
          $codicefiscale.=substr($nm,0,2).substr($oe,0,1);
        }
        else {
          $error = "Errore !!! \nErrore sul nome, caso non previsto da questo algoritmo \n...impossibile calcolare il codice fiscale";
          return $error;
        }
        break;

   case 3 :
        $codicefiscale.=$nm;
        break;

   default :
        $codicefiscale.=substr($nm,0,1).substr($nm,2,2);
        break;
 }

//   Ora calcolo la data

 $codicefiscale.=substr($datadinascita,-2);

//   Ora calcolo il carattere del mese

 (int) $mese=substr($datadinascita,2,2);

 switch ($mese) {                                                    // attenzione le lettere non sono in successione alfabetica
   case 1 : $codicefiscale.='A' ; break;
   case 2 : $codicefiscale.='B' ; break;
   case 3 : $codicefiscale.='C' ; break;
   case 4 : $codicefiscale.='D' ; break;
   case 5 : $codicefiscale.='E' ; break;
   case 6 : $codicefiscale.='H' ; break;
   case 7 : $codicefiscale.='L' ; break;
   case 8 : $codicefiscale.='M' ; break;
   case 9 : $codicefiscale.='P' ; break;
   case 10 : $codicefiscale.='R' ; break;
   case 11 : $codicefiscale.='S' ; break;
   case 12 : $codicefiscale.='T' ; break;
   default :
     $error = "  Errore !!! \nErrore sul mese, dato non valido.";
      return $error;
 }

 //   Ora calcolo giorno di nascita

 (int) $giorno=substr($datadinascita,0,2);

 $sesso=strtoupper($sesso);
 if ($sesso=='M')    {
   $codicefiscale.=substr($datadinascita,0,2);
 }
 else {
   $giorno+=40;
   $codicefiscale.=substr($numeri, $giorno/10, 1);
   $codicefiscale.=substr($numeri, $giorno%10, 1);
 }

 //   Ora inserisco codice comune, non effettuo controlli

 $codicefiscale.=trim($cod_comune);

 //   Ora calcolo l'ultimo carattere, quello di controllo

 $numero=0;
 for ($i=0; $i<=14; $i+=2) {                                         // ciclo per i caratteri dispari
   if ( strchr($numeri,substr($codicefiscale,$i,1))==false )  {
     for ($j=0; $j<=26; $j++) {
       if ( substr($codicefiscale,$i,1)==substr($alfabeto_dispari,$j,1) ) {
         $numero += $j;
         break;
       }
     }
   }
   else {
     switch ( substr($codicefiscale,$i,1) )  {                       // � un numero
       case '0' : $numero+=1;  break;
       case '1' : break;
       case '2' : $numero+=5;  break;
       case '3' : $numero+=7;  break;
       case '4' : $numero+=9;  break;
       case '5' : $numero+=13; break;
       case '6' : $numero+=15; break;
       case '7' : $numero+=17; break;
       case '8' : $numero+=19; break;
       case '9' : $numero+=21; break;
     };
   }
 }
 for ($i=1;$i<=13;$i+=2)  {                                          // ciclo per i caratteri pari
   if ( strchr( $numeri, substr($codicefiscale,$i,1) )==false ) {
     for ($j=0;$j<=26;$j++) {
       if ( substr($codicefiscale,$i,1)==substr($alfabeto,$j,1) ) {
         $numero+=$j;
         break;
       }
     }
   }
   else  {
     $numero+=substr($codicefiscale,$i,1);                           // e' un numero
   }
 }

 $numero=$numero%26;                                                 // il resto della divisione
 $codicefiscale.=substr($alfabeto,$numero,1);

 return $codicefiscale ;
}

/*
 echo  AlberT_CodFis("gabrielli","emiliano","01031975","m","L'Aquil�")."<br>";
 echo  AlberT_CodFis("gabrielli","emiliano","01031975","m","SANTA MARGHERITA DI")."<br>";
 echo  AlberT_CodFis("gabrielli","emiliano","01031975","m","SANTA MARGHERITA DI", "PT")."<br>";
 echo  AlberT_CodFis("gabrielli","emiliano","01031975","m","SANTA MARGHERITA DI", "Pv")."<br>";
 echo  AlberT_CodFis("gabrielli","emiliano","01031975","m","SANTA MARGHERITA DI", "ag")."<br>";
*/
?>
