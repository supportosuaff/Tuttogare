function ltrim(s) {   
	return s.replace( /^\s*/, '' );   
}  

function rtrim(s) {   
	return s.replace( /\s*$/, '' );   
}   

function trim ( s ) {   
	return rtrim(ltrim(s));   
}

function okins(msg) {
var risp

	if (msg=="0") {
		alert("Scegliere l'Operazione da effettuare!!!");		
		return false;
	}
	risp = confirm ("Confermi l'operazione di " + msg + "?")
	if (risp)  {
		return true
	}
	{
		return false
	}
}

function okinvio(msg) {
var risp

	if (msg=="0") {
		alert("Scegliere l'Operazione da effettuare!!!");		
		return false;
	}
	risp = confirm ("Confermi l'operazione di Invio degli SMS?")
	if (risp)  {
		return true
	}
	{
		return false
	}
}

function okinviare(campo,i) {
//verifica se l'operatore vuole effettuare una MODIFICA  e non ha spuntato il record 
	if (campo=="Inserimento" | campo=="Modifica") {
		errore=true;
		for (j=1; j <= i; j++) {
			selez="document.box.selez" + j + ".checked";
			if ( eval(selez) == true) {
				return eval("document.box.codice" + j + ".value");
				break;
				//errore=false;
			}
		}
		if (errore==true) {
			alert("Selezionare il record da Inviare");
			return false;
		}
		else {
			return true;
		}
		
		
	}
}

//inizio: funzione conrtollo email
function sendMail1(i) {
  errore = "";
  Errore_email="";


  campo = "document.box.email.value";	
	
  campo1 = "document.box.email.name";
	

  for (j=1; j <= i; j++) {
	  
	campo = "document.box.email" +j +".value";	
	
	campo1 = "document.box.email" +j +".name";

	
	  if (trim(eval(campo)) != "") {
		Errore_email = Errore_email + check_email(eval(campo));
  	  }

		
  }

   if (trim(eval(campo)) != "") {
		Errore_email = Errore_email + check_email(eval(campo));
  	  }
  
  
  //campo = "document.box.email" +i +".value";

  

 
	

  if (Errore_email != "") {
    errore += "\nL'indirizzo E-MAIL del mittente non e' corretto:" + Errore_email;
  }

  //Errore_email = check_email(document.box.EDestinatario.value);
  //if (Errore_email != "") {
  //  errore += "\n\nL'indirizzo E-MAIL del destinatario non e' corretto:" + Errore_email;
  //}

 /* lunghezza_messaggio = document.box.messaggio.value.length
  if (lunghezza_messaggio > 500) {
    errore += "\n\nIl messaggio supera i 500 caratteri.\n";
  }
*/

  if (errore != "") {
    alert ("ATTENZIONE!\n" + errore + "\n\nCompleta l'inserimento dei dati o effettua le correzioni per poter procedere.\nGrazie");
    return false;
  }
}


function check_email(email) {

/*
LEGENDA DEGLI ERRORI:

1) La chiocciola e' presente: come primo o ultimo carattere o ne sono state digitate piu' di una;
2) L'e-mail contiene uno o piu' caratteri non ammessi contenuti nella variabile nochar;
3) Il punto e' presente: come primo, ultimo o penultimo carattere, prima o dopo la chiocciola;
4) Ci sono 2 punti (..) oppure due trattini (--) vicini;
5) Non c'e' nessun punto dopo la chiocciola
*/

var errors=""
var i

// Posizione della chiocciola.
var chiocPos=email.indexOf("@")

// Insieme dei caratteri non ammessi in un e-mail.
var nochar="\\/^,;:+�����'<>()%=?!| " + '"'

// Prima lettera dell'e-mail.
var first_letter=email.substring(0,1)

// Ultima lettera dell'e-mail.
var last_letter=email.substring(email.length-1,email.length)

// Penultima lettera dell'e-mail.
var Penultima_letter=email.substring(email.length-2,email.length-1)

// Lettera a sinistra della chiocciola.
var sx_chioc=email.substring(chiocPos-1,chiocPos)

// Lettera a destra della chiocciola.
var dx_chioc=email.substring(chiocPos+1,chiocPos+2)

if ((chiocPos<"1") || (chiocPos==(email.length-1)) || (chiocPos!=(email.lastIndexOf("@")))) {
errors+="\n- Carattere chiocciola (@) mancante o in posizione errata"
}
else {
  for (var i=0; i<=nochar.length-1; i++) {
    if (email.indexOf(nochar.substring(i,i+1))!="-1") {
     errors+="\n- Hai digitato dei caratteri non ammessi"
     break
    }
  }
}

if (errors=="") {
  if ((first_letter==".") || (sx_chioc==".") || (dx_chioc==".") || (last_letter==".") || (Penultima_letter==".") ) {
     errors+="\n- Il punto (.) e' in posizione errata"
  }  
  else {

    for (var i=0; i<=email.length-1; i++) {
      if ((email.substring(i,i+1)==".") && (email.substring(i+1,i+2)==".")) {
        errors+="\n- Ci sono due caratteri punto (.) vicini"
        break
      }
      if ((email.substring(i,i+1)=="-") && (email.substring(i+1,i+2)=="-")) {
        errors+="\n- Ci sono due caratteri trattino (-) vicini"
        break
      }
    }
  }
}
PuntoDopoChioc = 0
if (errors=="") {
  for (var i=chiocPos+1; i<=email.length-3; i++) {
    if (email.substring(i,i+1)==".") {
      PuntoDopoChioc = 1
      break
    }
  }
  if (PuntoDopoChioc == 0) {
    errors+="\n- Non hai indicato il dominio (.it .com .net ecc..)"
  }
}
return errors
}


// fne: funzione controllo email


//*****FUNZIONE VERIFICA TEL E FAX******************

function isTelFax(num)
{
   var i=new RegExp("([0-9]{2,})\\.([0-9]{5,})");
	//var i=new RegExp("([0-9]{2,})\\.([0-9]{5,})");

   if(trim(num)!= ""  && !i.test(num)) {  return false}   
   return true;
}


//*********************************


// funnzione verifica data

function VerificaData(p,i) { 
// i =  0 -> Data NON Obbligatoria
// i = -1 -> Data OBBLIGATORIA


 obj=p.replace(/[^\d]/g,'0');; 
   gg=obj.substr(0,2) 
   mm=obj.substr(3,2); 
   aa=obj.substr(6,4); 
   strdata=gg+'/'+mm+'/'+aa; 

   if (((strdata=="//") || (strdata=="00/00/0000")) && (i==0)) {
	return true
   }

   data = new Date(aa,mm-1,gg); 
   daa=data.getFullYear().toString(); 
   dmm=(data.getMonth()+1).toString(); 
   dmm=dmm.length==1?'0'+dmm:dmm 
   dgg=data.getDate().toString(); 
   dgg=dgg.length==1?'0'+dgg:dgg 
   dddata=dgg+'/'+dmm+'/'+daa 
   if (dddata!=strdata) 
      { 
      return false 
      } 
    return true 
}      




function okcanc(campo,i) {
//verifica se l'operatore vuole effettuare una CANCELLAZIONE  e non ha spuntato il record 
	if (campo=="Cancellazione") {
		errore=true;
		for (j=1; j <= i; j++) {
			selez="document.box.selez" + j + ".checked";
			if ( eval(selez) == true) {
				errore=false;
			}
		}
		if (errore==true) {
			alert("Selezionare i record da Eliminare");
			return false;
		}
		else {
			return true;
		}
		
		
	}
}


function okNoSms(campo,i) {
//verifica se l'operatore vuole effettuare una CANCELLAZIONE  e non ha spuntato il record 
	if (campo=="Cancellazione") {
		errore=true;
		for (j=1; j <= i; j++) {
			selez="document.box.selez" + j + ".checked";
			if ( eval(selez) == true) {
				errore=false;
			}
		}
		if (errore==true) {
			alert("Selezionare i record da NON inviare tramite SMS");
			return false;
		}
		else {
			return true;
		}
		
		
	}
}

function okmod(campo,i) {
//verifica se l'operatore vuole effettuare una MODIFICA  e non ha spuntato il record 
	if (campo=="Modifica") {
		errore=true;
		for (j=1; j <= i; j++) {
			selez="document.box.selez" + j + ".checked";
			if ( eval(selez) == true) {
				return eval("document.box.codice" + j + ".value");
				break;
				//errore=false;
			}
		}
		if (errore==true) {
			alert("Selezionare il record da Modificare");
			return false;
		}
		else {
			return true;
		}
		
		
	}
}


function ToggleCheckAll() {
	var sa=false;
	if(document.box.CheckAll.value=="true") sa=true;
	for (var i=0;i<document.box.elements.length;i++) {
		var e = document.box.elements[i];
		if( sa )
			e.checked=true;
		else
			e.checked=false;
	}
	if( sa )
			document.box.CheckAll.value="false";
		else
			document.box.CheckAll.value="true";
}


//************************* VERIFICA ESTENSIONE: ZIP - DOC - PDF ******************

function estzipdocpdf(nome)  {


	nome=nome.toLowerCase();

	if (nome.search(".zip")==-1 && nome.search(".doc")==-1 && nome.search(".pdf")==-1 && nome.search(".xls")==-1) {
		return false;
	}
	else {
		return true;
	}
                                               
}

//***********************  FINE VERIFICA ESTENSIONE: ZIP - DOC - PDF ******************


//************************* VERIFICA ESTENSIONE: GIF - JPG ******************

function estgifjpg(nome)  {


	nome=nome.toLowerCase();

	if (nome.search(".gif")==-1 && nome.search(".jpg")==-1  && nome.search(".jpeg")==-1) {
		return false;
	}
	else {
		return true;
	}
                                               
}

//***********************  FINE VERIFICA ESTENSIONE: GIF - JPG ******************

function aaaammgg(stringa) {
	
	str = stringa.slice(6,10);
	str = str + stringa.slice(3,5);
	str = str + stringa.slice(0,2);

	return str;



}


//*********************** FUNZIONE PER CONVERTIRE LA DATA DA GG/MM/AAAA IN AAAAMMGG




//*********************** FINE FUNZIONE PER CONVERTIRE LA DATA DA GG/MM/AAAA IN AAAAMMGG


//***************** funzione per validare la ricerca nella home page

function validsearch() {

	if (document.searchform.argomento.value=="0") {
		alert("Selezionare un Argomento!");
		return false;
	}
	if (document.searchform.keys.value=="") {
		alert("Inserire il testo da Ricercare!");
		return false;
	}

	return true;

}
//******************* fine funzione 




function svuota(campo) {

	eval("document.box." + campo + ".value=''");
	
}

function svuotakeys() {

	document.searchform.keys.value = "";
}
//***************** funzione per il calendario
function setCalendario( com,nomecampo ) {
				
		eval("document.box." + nomecampo + ".value='" + com +"'");

		
}
	
function calendar(nomecampo) {
	
	var PassaggioDati
	var url
	
	url = "calendario.php?N=" + nomecampo; 
	
	PassaggioDati = window.open(url,"","menubar=no status=no, toolbar=no,scrollbars=no,height=310, width=310")

	PassaggioDati.creator = self;
	
	return true
}	

//*********** fine funzione per il calendario

// Inizializza l'array delle descrizioni delle regioni;
var regioni_des = new Array();
regioni_des["ABR"]="Abruzzo";
regioni_des["BAS"]="Basilicata";
regioni_des["CAL"]="Calabria";
regioni_des["CAM"]="Campania";
regioni_des["EMR"]="Emilia Romagna";
regioni_des["FRI"]="Friuli Venezia Giulia";
regioni_des["LAZ"]="Lazio";
regioni_des["LIG"]="Liguria";
regioni_des["LOM"]="Lombardia";
regioni_des["MAR"]="Marche";
regioni_des["MOL"]="Molise";
regioni_des["PIE"]="Piemonte";
regioni_des["PUG"]="Puglia";
regioni_des["SAR"]="Sardegna";
regioni_des["SIC"]="Sicilia";
regioni_des["TOS"]="Toscana";
regioni_des["TRE"]="Trentino Alto Adige";
regioni_des["UMB"]="Umbria";
regioni_des["VAL"]="Valle d'aosta";
regioni_des["VEN"]="Veneto";
// Inizializza l'array delle regioni
var regioni = new Array();
regioni["ABR"] = new Array();
regioni["BAS"] = new Array();
regioni["CAL"] = new Array();
regioni["CAM"] = new Array();
regioni["EMR"] = new Array();
regioni["FRI"] = new Array();
regioni["LAZ"] = new Array();
regioni["LIG"] = new Array();
regioni["LOM"] = new Array();
regioni["MAR"] = new Array();
regioni["MOL"] = new Array();
regioni["PIE"] = new Array();
regioni["PUG"] = new Array();
regioni["SAR"] = new Array();
regioni["SIC"] = new Array();
regioni["TOS"] = new Array();
regioni["TRE"] = new Array();
regioni["UMB"] = new Array();
regioni["VAL"] = new Array();
regioni["VEN"] = new Array();
// Inizializza le associazioni regioni->province;
regioni["ABR"].push("AQ");
regioni["ABR"].push("CH");       
regioni["ABR"].push("PE");       
regioni["ABR"].push("TE");       
regioni["BAS"].push("MT");
regioni["BAS"].push("PZ");
regioni["CAL"].push("CS");       
regioni["CAL"].push("CZ");       
regioni["CAL"].push("KR");
regioni["CAL"].push("RC");       
regioni["CAL"].push("VV");
regioni["CAM"].push("AV");
regioni["CAM"].push("CE");       
regioni["CAM"].push("BN");       
regioni["CAM"].push("NA");       
regioni["CAM"].push("SA");       
regioni["EMR"].push("BO");       
regioni["EMR"].push("FE");       
regioni["EMR"].push("FC");       
regioni["EMR"].push("MO");       
regioni["EMR"].push("PC");       
regioni["EMR"].push("PR");       
regioni["EMR"].push("RA");       
regioni["EMR"].push("RE");       
regioni["EMR"].push("RN");             
regioni["FRI"].push("GO");       
regioni["FRI"].push("PN");       
regioni["FRI"].push("TS");             
regioni["FRI"].push("UD");       
regioni["LAZ"].push("FR");       
regioni["LAZ"].push("LT");       
regioni["LAZ"].push("RI");       
regioni["LAZ"].push("RM");       
regioni["LAZ"].push("VT");       
regioni["LIG"].push("GE");       
regioni["LIG"].push("IM");       
regioni["LIG"].push("SP");       
regioni["LIG"].push("SV");       
regioni["LOM"].push("MI");       
regioni["LOM"].push("CR");       
regioni["LOM"].push("LO");              
regioni["LOM"].push("PV");       
regioni["LOM"].push("VA");       
regioni["LOM"].push("BG");       
regioni["LOM"].push("BS");       
regioni["LOM"].push("CO");       
regioni["LOM"].push("LC");              
regioni["LOM"].push("MN");       
regioni["LOM"].push("SO");       
regioni["MAR"].push("AN");
regioni["MAR"].push("AP");
regioni["MAR"].push("MC"); 
regioni["MAR"].push("PU");       
regioni["MOL"].push("CB");       
regioni["MOL"].push("IS");
regioni["PIE"].push("AL");              
regioni["PIE"].push("AT");       
regioni["PIE"].push("BI");       
regioni["PIE"].push("CN");       
regioni["PIE"].push("NO");       
regioni["PIE"].push("TO");       
regioni["PIE"].push("VB");
regioni["PIE"].push("VC");       
regioni["PUG"].push("BA");       
regioni["PUG"].push("BR");       
regioni["PUG"].push("FG");
regioni["PUG"].push("LE");       
regioni["PUG"].push("TA");       
regioni["SAR"].push("CA");       
regioni["SAR"].push("NU");       
regioni["SAR"].push("OR");       
regioni["SAR"].push("SS");       
regioni["SIC"].push("AG");
regioni["SIC"].push("CL");       
regioni["SIC"].push("CT");       
regioni["SIC"].push("EN"); 
regioni["SIC"].push("ME"); 
regioni["SIC"].push("PA");
regioni["SIC"].push("RG");
regioni["SIC"].push("SR");
regioni["SIC"].push("TP");
regioni["TOS"].push("AR");       
regioni["TOS"].push("FI");       
regioni["TOS"].push("GR");       
regioni["TOS"].push("LI");       
regioni["TOS"].push("LU"); 
regioni["TOS"].push("MS");
regioni["TOS"].push("PI");       
regioni["TOS"].push("PO");       
regioni["TOS"].push("PT");       
regioni["TOS"].push("SI");       
regioni["TRE"].push("BZ");       
regioni["TRE"].push("TN");       
regioni["UMB"].push("PG");       
regioni["UMB"].push("TR");       
regioni["VAL"].push("AO");
regioni["VEN"].push("BL");
regioni["VEN"].push("PD");       
regioni["VEN"].push("RO");       
regioni["VEN"].push("VE");       
regioni["VEN"].push("VI");
regioni["VEN"].push("VR");
regioni["VEN"].push("TV");







var province = new Array();
province["AG"]="AGRIGENTO";
province["AL"]="ALESSANDRIA";
province["AN"]="ANCONA";
province["AO"]="AOSTA";
province["AP"]="ASCOLI PICENO";
province["AQ"]="L'AQUILA";
province["AR"]="AREZZO";
province["AT"]="ASTI";
province["AV"]="AVELLINO";
province["BA"]="BARI";
province["BG"]="BERGAMO";
province["BI"]="BIELLA";
province["BL"]="BELLUNO";
province["BN"]="BENEVENTO";
province["BO"]="BOLOGNA";
province["BR"]="BRINDISI";
province["BS"]="BRESCIA";
province["BZ"]="BOLZANO";
province["CA"]="CAGLIARI";
province["CB"]="CAMPOBASSO";
province["CE"]="CASERTA";
province["CH"]="CHIETI";
province["CL"]="CALTANISSETTA";
province["CN"]="CUNEO";
province["CO"]="COMO";
province["CR"]="CREMONA";
province["CS"]="COSENZA";
province["CT"]="CATANIA";
province["CZ"]="CATANZARO";
province["EN"]="ENNA";
province["FE"]="FERRARA";
province["FG"]="FOGGIA";
province["FI"]="FIRENZE";
province["FC"]="FORLI - CESENA";
province["FR"]="FROSINONE";
province["GE"]="GENOVA";
province["GO"]="GORIZIA";
province["GR"]="GROSSETO";
province["IM"]="IMPERIA";
province["IS"]="ISERNIA";
province["KR"]="CROTONE";
province["LE"]="LECCE";
province["LC"]="LECCO";
province["LI"]="LIVORNO";
province["LO"]="LODI";
province["LT"]="LATINA";
province["LU"]="LUCCA";
province["MC"]="MACERATA";
province["ME"]="MESSINA";
province["MI"]="MILANO";
province["MN"]="MANTOVA";
province["MO"]="MODENA";
province["MS"]="MASSA - CARRARA";
province["MT"]="MATERA";
province["NA"]="NAPOLI";
province["NO"]="NOVARA";
province["NU"]="NUORO";
province["OR"]="ORISTANO";
province["PA"]="PALERMO";
province["PC"]="PIACENZA";
province["PD"]="PADOVA";
province["PE"]="PESCARA";
province["PG"]="PERUGIA";
province["PI"]="PISA";
province["PN"]="PORDENONE";
province["PO"]="PRATO";
province["PR"]="PARMA";
province["PU"]="PESARO E URBINO";
province["PT"]="PISTOIA";
province["PV"]="PAVIA";
province["PZ"]="POTENZA";
province["RA"]="RAVENNA";
province["RC"]="REGGIO DI CALABRIA";
province["RE"]="REGGIO NELL'EMILIA";
province["RG"]="RAGUSA";
province["RI"]="RIETI";
province["RM"]="ROMA";
province["RN"]="RIMINI";
province["RO"]="ROVIGO";
province["SA"]="SALERNO";
province["SI"]="SIENA";
province["SO"]="SONDRIO";
province["SP"]="LA SPEZIA";
province["SR"]="SIRACUSA";
province["SS"]="SASSARI";
province["SV"]="SAVONA";
province["TA"]="TARANTO";
province["TE"]="TERAMO";
province["TN"]="TRENTO";
province["TO"]="TORINO";
province["TP"]="TRAPANI";
province["TR"]="TERNI";
province["TS"]="TRIESTE";
province["TV"]="TREVISO";
province["UD"]="UDINE";
province["VA"]="VARESE";
province["VB"]="VERBANO-CUSIO-OSSOLA";
province["VC"]="VERCELLI";
province["VE"]="VENEZIA";
province["VI"]="VICENZA";
province["VR"]="VERONA";
province["VT"]="VITERBO";
province["VV"]="VIBO VALENTIA";

// Funzioni di update
function updateProvince() {
  var s_regione = document.forms["box"].elements["regione"];
  var s_provincia = document.forms["box"].elements["provincia"];
  // rimuove eventuali option presenti
  for (i=s_provincia.length-1; i>=0; i--) {
    s_provincia[i] = null;
  }
  // aggiunge gli option alle province
  if (s_regione.selectedIndex >= 0) {
    var reg = regioni[s_regione.options[s_regione.selectedIndex].value];
    s_provincia[s_provincia.length] = new Option("", "");
    for (i=0; reg != undefined && i<reg.length; i++) {
      var o = new Option(province[reg[i]], reg[i]);
      s_provincia[s_provincia.length] = o;
      if (provsel == reg[i]) s_provincia.selectedIndex = s_provincia.length-1;
    }
  }
}

// Funzioni di update
function updateRegioni() {
  var s_paese = document.forms["box"].elements["paese"];
  var s_regione = document.forms["box"].elements["regione"];

	

  // rimuove eventuali option presenti
  for (i=s_regione.length-1; i>=0; i--) {
    s_regione[i] = null;
  }
  // aggiunge gli option alle regioni se ho selezionato italia
  if (s_paese.options[s_paese.selectedIndex].value == "ITA") {
    s_regione[s_regione.length] = new Option("", "");
    for (var i in regioni_des) {
      var o = new Option(regioni_des[i], i);
      s_regione[s_regione.length] = o;
      if (regsel == i) s_regione.selectedIndex = s_regione.length-1;
    }
  }
  updateProvince();
}


function windowfoto(NamePage){  
	window.open(NamePage,"","menubar=no status=no, toolbar=no,scrollbars=yes,height=610, width=680")
}


function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' deve essere un indirizzo email valido.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' � obbligatorio.\n'; }
  } if (errors) alert('Ci sono i seguenti errori:\n\n'+errors);
  document.MM_returnValue = (errors == '');
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}