<?
session_start();
include_once("../../config.php");
include_once($root."/layout/top.php");

if(isset($_GET)){
  $advanced=(isset($_GET["advanced"]))? $_GET["advanced"] : 0;
  if(isset($_POST["query"])) $value=(isset($_POST["query"]))? $_POST["query"] : null;
}
?>
<? if($advanced==1){ ?>
<h1>RICERCA</h1>
<div id="maschera">
    <ul>
        <li><a href="#mask-1">Gare</a></li>
       <? if(isset($_SESSION["codice_utente"])){ ?> <li><a href="#mask-2">Catalogo</a></li> <? } ?>
    </ul>
    <div id="mask-1">
        <form action="<?echo $_SERVER['PHP_SELF']; ?>" rel="validate" method="post" target="_self">
            <input type="hidden" name="advGare" id="advGare" value="1"/>
            <table id="advanced" class="box" width="100%">
              <tr>
                <td class="etichetta">Testo</td>
                <td colspan="3"><input type="text" rel="N;0;0;A" title="Testo" name="oggetto" id="oggetto" style="width:99%"/></td>
            </tr>
            <tr>
                <td class="etichetta">ID</td>
                <td><input type="text" name="id" id="id" rel="N;0;0;A" title="Id" style="width:98%"/></td>
                <td class="etichetta">CPV</td>
                <td>
                  <input type="hidden" rel="N;0;0;A" name="codice_cpv" id="codice_cpv"/>
                  <input type="text" class="cerca_cpv_simple" rel="N;2;0;A" title="CPV" name="cpv" id="cpv" style="width:98%"/></td>
              </tr>
              <tr>
                <td class="etichetta">CIG</td>
                <td><input type="text" name="cig" id="cig" rel="N;0;10;A" title="CIG" style="width:98%"/></td>
                <td class="etichetta">CUP</td>
                <td><input type="text" name="cup" id="cup" rel="N;0;15;A" title="CUP" style="width:98%"/></td>
            </tr>
            <tr>
                <td class="etichetta">Tipologia</td>
                <td><select name="tipologia" id="tipologia" rel="N;0;0;N" title="Tipologia">
                  <option value="0">Tutte</option><?
                  $sql = "SELECT codice, tipologia FROM b_tipologie WHERE attivo = 'S'";
                  $ris_tipologie = $pdo->query($sql);
                  if($ris_tipologie->rowCount()>0)
                    while($tipo = $ris_tipologie->fetch(PDO::FETCH_ASSOC))
                      echo "<option value='".$tipo['codice']."'>".$tipo['tipologia']."</option>";
                  ?></select></td>
                <td class="etichetta">Modalit&agrave;</td>
                <td><select name="modalita" id="modalita" rel="N;0;0;N" title="Modalità">
                <option value="0">Tutte</option><?
                $sql = "SELECT codice, modalita FROM b_modalita WHERE attivo = 'S'";
                $ris_tipologie = $pdo->query($sql);
                if($ris_tipologie->rowCount()>0)
                  while($tipo = $ris_tipologie->fetch(PDO::FETCH_ASSOC))
                    echo "<option value='".$tipo['codice']."'>".$tipo['modalita']."</option>";
                ?>
                </select>
                </td>
        </tr>
        <tr>
            <td class="etichetta">Criterio</td>
            <td><select name="criterio" id="criterio" rel="N;0;0;N" title="Criterio">
                <option value="0">Tutte</option><?
                    $sql = "SELECT codice, criterio FROM b_criteri WHERE attivo = 'S'";
                    $ris_tipologie = $pdo->query($sql);
                    if($ris_tipologie->rowCount()>0)
                        while($tipo = $ris_tipologie->fetch(PDO::FETCH_ASSOC))
                            echo "<option value='".$tipo['codice']."'>".$tipo['criterio']."</option>";
                ?>
            </select></td>
            <td class="etichetta">Procedura</td>
            <td><select name="procedura" id="procedura" rel="N;0;0;N" title="Procedura">
            <option value="0">Tutte</option><?
                    $sql = "SELECT codice, nome FROM b_procedure WHERE attivo = 'S'";
                    $ris_tipologie = $pdo->query($sql);
                    if($ris_tipologie->rowCount()>0)
                        while($tipo = $ris_tipologie->fetch(PDO::FETCH_ASSOC))
                            echo "<option value='".$tipo['codice']."'>".$tipo['nome']."</option>";
                ?>
            </select></td>
        </tr>
        <tr>
            <td class="etichetta">Struttura Proponente</td>
            <td><input type="text" name="struttura" rel="N;0;0;A" title="Struttura Proponente" id="struttura" style="width:98%"/></td>
            <td class="etichetta">RUP</td>
            <td><input type="text" name="rup" id="rup" rel="N;0;0;A" title="RUP" style="width:98%"/></td>
        </tr>
        <tr>
            <td class="etichetta">Responsabile Struttura</td>
            <td><input type="text" name="responsabile" id="responsabile" rel="N;0;0;A" title="Responsabile Struttura" style="width:98%"/></td>
        </tr>
    </table>
    <input type="submit" class="submit_big" value="Ricerca">
    </form>
    </div>
    <? if(isset($_SESSION["codice_utente"])){ ?>
    <div id="mask-2">
        <form action="<?echo $_SERVER['PHP_SELF']; ?>" rel="validate" method="post" target="_self">
            <input type="hidden" name="advCatalogo" id="advCatalogo" value="1"/>
            <table id="advanced" class="box" width="100%">
                <tr>
                    <td class="etichetta">Testo</td>
                    <td colspan="3"><input type="text" name="oggetto" id="oggetto" rel="N;0;0;A" title="Testo" style="width:99%"/></td>
                </tr>
                <tr>
                    <td class="etichetta">Bando</td>
                    <td>
                        <select name="bando" id="bando" rel="N;0;0;N" title="Bando">
                            <option value="0"/><?
                            $bind = array(":codice_ente" => $_SESSION["ente"]["codice"]);
                            $sql_bando  = "SELECT * ";
                            $sql_bando .= "FROM b_bandi_mercato ";
                            $sql_bando .= "WHERE (pubblica = '2' OR pubblica = '1') AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
                            $sql_bando .= "ORDER BY id DESC, codice DESC" ;
                            $ris_bando = $pdo->bindAndExec($sql_bando,$bind);
                            if($ris_bando->rowCount()>0)
                                while($rec_bando=$ris_bando->fetch(PDO::FETCH_ASSOC))
                                    echo "<option value='".$rec_bando['codice']."'>".$rec_bando['oggetto']."</option>";
                        ?></select>
                    </td>
                    <td class="etichetta">CPV</td>
                    <td>
                        <input type="hidden" rel="N;0;0;A" name="codice_cpv_catalogo" id="codice_cpv_catalogo"/>
                        <input type="text" class="cerca_cpv_simple2" rel="N;2;0;A" title="CPV" name="cpv" id="cpv" style="width:98%"/>
                    </td>
                </tr>
                <? if(!is_operatore()){ ?>
                <tr>
                    <td class="etichetta">Operatore Economico</td>
                    <td colspan="3">
                        <input type="hidden" rel="N;0;0;A" name="codice_operatore" id="codice_operatore"/>
                        <input type="text" class="cerca_operatori" rel="N;3;0;A" title="Operatore Economico" name="opEconomico" id="opEconomico" style="width:98%"/>
                    </td>
                </tr>
                <? } ?>
            </table>
        <input type="submit" class="submit_big" value="Ricerca">
        </form>
    </div>
    <? }
    }

if(isset($_POST)&&($_POST!=null)){

    if(isset($_POST["advGare"])&&($_POST["advGare"]==1)){
//RICERCA GARE
        $bind = array(":codice_ente" => $_SESSION["ente"]["codice"]);
        if (isset($_SESSION["ente"])) {
            if (!isset($_SESSION["codice_utente"])) {
                $strsql_gare  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_cpv.codice as cpv_codice , b_cpv.descrizione as cpv_descrizione ";
                $strsql_gare .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
                $strsql_gare .= "JOIN b_criteri ON b_gare.criterio = b_criteri.codice ";
                $strsql_gare .= "JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice ";
                $strsql_gare .= "JOIN r_cpv_gare on b_gare.codice = r_cpv_gare.codice_gara ";
                $strsql_gare .= "JOIN b_cpv on r_cpv_gare.codice = b_cpv.codice ";
                $strsql_gare .= "WHERE pubblica = '2' AND annullata = 'N' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
        } else {
            if (is_operatore()) {
                $bind[":codice_utente"] = $_SESSION["codice_utente"];
                $strsql_gare  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_cpv.codice as cpv_codice , b_cpv.descrizione as cpv_descrizione FROM b_gare LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara ";
                $strsql_gare .= "JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
                $strsql_gare .= "JOIN b_criteri ON b_gare.criterio = b_criteri.codice ";
                $strsql_gare .= "JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice ";
                $strsql_gare .= "JOIN r_cpv_gare on b_gare.codice = r_cpv_gare.codice_gara ";
                $strsql_gare .= "JOIN b_cpv on r_cpv_gare.codice = b_cpv.codice ";
                $strsql_gare .= "WHERE annullata = 'N' AND  (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
                $strsql_gare .= "AND (pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente)))) ";
            } else {
                $strsql_gare  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_cpv.codice as cpv_codice , b_cpv.descrizione as cpv_descrizione ";
                $strsql_gare .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
                $strsql_gare .= "JOIN b_criteri ON b_gare.criterio = b_criteri.codice ";
                $strsql_gare .= "JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice ";
                $strsql_gare .= "JOIN r_cpv_gare on b_gare.codice = r_cpv_gare.codice_gara ";
                $strsql_gare .= "JOIN b_cpv on r_cpv_gare.codice = b_cpv.codice ";
                $strsql_gare .= "WHERE annullata = 'N' AND  (pubblica > 0) AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
            }
        }
        $where_gare=array();
        if(!isset($_GET["advanced"])&&(isset($value))){
            if(preg_match('/\s/', $value)){
                $value = str_replace(" ", "%", $value);
            }
            $bind[":value"] = '%'.$value.'%';
            $where_gare[]= "(b_gare.cig LIKE :value OR b_gare.oggetto LIKE :value OR b_gare.cup LIKE :value OR b_gare.descrizione LIKE :value OR b_cpv.codice LIKE :value OR b_cpv.descrizione LIKE :value) ";
        }else{
            if(isset($_POST["id"])&&$_POST["id"]!="") {
              $bind[":id"] = $_POST["id"];
              $where_gare[]= "(b_gare.id = :id) ";
            }
            if(isset($_POST["oggetto"])&&$_POST["oggetto"]!=""){
                $oggetto = $_POST["oggetto"];
                if(preg_match('/\s/', $_POST["oggetto"])){
                    $oggetto = str_replace(" ", "%", $_POST["oggetto"]);
                }
                $bind[":oggetto"] = '%'.$oggetto.'%';
                $where_gare[]= "(b_gare.oggetto LIKE :oggetto OR b_gare.descrizione LIKE :oggetto )";
            }

            if(isset($_POST["cig"])&&$_POST["cig"]!="") {
              $bind[":cig"] = $_POST["cig"];
              $where_gare[]= "(b_gare.cig LIKE :cig) ";
            }

            if(isset($_POST["cup"])&&$_POST["cup"]!="") {
              $bind[":cup"] = $_POST["cup"];
              $where_gare[]= "(b_gare.cup LIKE :cup) ";
            }

            if(isset($_POST["struttura"])&&$_POST["struttura"]!="") {
              $bind[":struttura"] = $_POST["struttura"];
              $where_gare[]= "(b_gare.struttura_proponente :struttura) ";
            }

            if(isset($_POST["rup"])&&$_POST["rup"]!="") {
              $bind[":rup"] = $_POST["rup"];
              $where_gare[]= "(b_gare.rup LIKE :rup) ";
            }

            if(isset($_POST["responsabile"])&&$_POST["responsabile"]!="") {
              $bind[":responsabile"] = $_POST["responsabile"];
              $where_gare[]= "(b_gare.responsabile_struttura :responsabile) ";
            }

            if(isset($_POST["tipologia"])&&$_POST["tipologia"]!="") {
                if($_POST["tipologia"]!=0) {
                  $bind[":tipologia"] = $_POST["tipologia"];
                    $where_gare[]= "(b_gare.tipologia = :tipologia)";
                }
            }
            if(isset($_POST["criterio"])&&$_POST["criterio"]!="") {
                if($_POST["criterio"]!=0) {
                  $bind[":criterio"] = $_POST["criterio"];
                    $where_gare[]= "(b_gare.criterio = :criterio)";
                }
            }
            if(isset($_POST["modalita"])&&$_POST["modalita"]!="") {
                if($_POST["modalita"]!=0) {
                  $bind[":modalita"] = $_POST["modalita"];
                    $where_gare[]= "(b_gare.modalita = :modalita)";
                }
            }
            if(isset($_POST["procedura"])&&$_POST["procedura"]!="") {
                if($_POST["procedura"]!=0) {
                  $bind[":procedura"] = $_POST["procedura"];
                    $where_gare[]= "(b_gare.procedura = :procedura)";
                }
            }
            if(isset($_POST["codice_cpv"])&&$_POST["codice_cpv"]!="") {
                $bind[":cpv"] = "%".$_POST["codice_cpv"]."%";
                $where_gare[]= "(b_cpv.codice LIKE :cpv)";
            }else{
                if(isset($_POST["cpv"])&&$_POST["cpv"]!=""){
                    $bind[":cpv"] = "%".$_POST["cpv"]."%";
                    if(is_numeric($_POST["cpv"]))
                        $where_gare[]= "(b_cpv.codice LIKE :cpv) ";
                    else
                        $where_gare[]= "(b_cpv.descrizione LIKE :cpv) ";
                }
        }
    }

    $where = "";
    foreach ($where_gare as $chiave => $valore) {
        $where .= $valore ." AND ";
    }
    $where = substr($where,0, strlen($where)-4);

    if (isset($where) && $where != "")
        $strsql_gare .= " AND (" . $where .")";

    $strsql_gare .= " GROUP BY b_gare.codice ";
    $strsql_gare .= " ORDER BY id DESC, codice DESC" ;
    //echo $strsql_gare;
    $risultato_gare = $pdo->bindAndExec($strsql_gare,$bind);

    $codici_gara = array();
    $ris_gare = $pdo->bindAndExec($strsql_gare,$bind);
    if($ris_gare->rowCount()>0) {
        while($rec = $ris_gare->fetch(PDO::FETCH_ASSOC)) {
            $codici_gara[]=$rec["codice"];
        }
      }
    }

    //RICERCA AVVISI
    $checkOggetto = false;
    $checkArray = false;
    if (isset($_SESSION["ente"])) {
      $bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
      if (!isset($_SESSION["codice_utente"])) {
        $strsql_avvisi  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id ";
        $strsql_avvisi .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
        $strsql_avvisi .= "WHERE pubblica = '2' AND (b_gare.codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
        $strsql_avvisi.= " AND b_avvisi.data <= now() ";
    } else {
        if (is_operatore()) {
          $bind[":codice_utente"] = $_SESSION["codice_utente"];
          $strsql_avvisi  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id ";
          $strsql_avvisi .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
          $strsql_avvisi .= "LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara JOIN b_procedure ON b_procedure.codice = b_gare.procedura ";
          $strsql_avvisi .= "WHERE ((b_gare.codice_ente  = :codice_ente OR codice_gestore = :codice_ente) ";
          $strsql_avvisi .= "AND (pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente)))) ";
          $strsql_avvisi .= " AND b_avvisi.data <= now() ";
        } else {
            $strsql_avvisi  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id ";
            $strsql_avvisi .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
            $strsql_avvisi .= "WHERE ((pubblica > 0) AND (b_gare.codice_ente  = :codice_ente OR codice_gestore = :codice_ente)) ";
        }
    }
    $where_avvisi=array();
    if(!isset($_GET["advanced"])&&(isset($value))){
        $checkOggetto=true;
        if(preg_match('/\s/', $value)){
            $value = str_replace(" ", "%", $value);
        }
        $bind[":simple"] = "%".$value."%";

        $tmp = "(b_avvisi.titolo LIKE :simple OR b_avvisi.testo LIKE :simple";
        if(isset($codici_gara)&&!empty($codici_gara))
            $tmp .= " OR b_avvisi.codice_gara IN (".implode(",",$codici_gara).")) ";
        else
            $tmp .= " ) ";
        $where_avvisi[]=$tmp;
    }else{
        if(isset($_POST["oggetto"])&&$_POST["oggetto"]!=""){
            $tmp ="";
            $oggetto = $_POST["oggetto"];
            if(preg_match('/\s/', $oggetto)){
                $oggetto = str_replace(" ", "%", $oggetto);
            }
            $bind[":oggetto"] = $oggetto;
            $tmp .= "(b_avvisi.titolo LIKE :oggetto  OR b_avvisi.testo LIKE :oggetto";

        if(isset($codici_gara)&&!empty($codici_gara))
            $tmp .= " OR b_avvisi.codice_gara IN (".implode(",",$codici_gara).")) ";
        else
            $tmp .= " ) ";
        $where_avvisi[]=$tmp;
    }else{
        if(isset($codici_gara)&&!empty($codici_gara))
            $where_avvisi[]= " (b_avvisi.codice_gara IN (".implode(",",$codici_gara).")) ";
        }
    }

    $where = "";
    foreach ($where_avvisi as $chiave => $valore) {
        $where .= $valore ." AND ";
    }
    $where = substr($where,0, strlen($where)-4);

    if (isset($where) && $where != "")
        $strsql_avvisi.= " AND (" . $where .")";

    $strsql_avvisi .= " ORDER BY data DESC, codice DESC" ;

    if(isset($_POST["oggetto"])&&($_POST["oggetto"]!=""))
        $checkOggetto = true;
    else
        $checkOggetto = false;

    if(isset($codici_gara)&&!empty($codici_gara))
        $checkArray = true;
    else
        $checkArray = false;

    //echo $strsql_avvisi;
    if($checkOggetto||$checkArray)
        $risultato_avvisi  = $pdo->bindAndExec($strsql_avvisi,$bind);
    }

    //RICERCA NEWS
    $bind = array(":codice_ente" => $_SESSION["ente"]["codice"]);
    $strsql_news  = "SELECT b_news.* ";
    $strsql_news .= "FROM b_news ";
    $strsql_news .= "WHERE  b_news.data <= curdate() ";
    if (isset($_SESSION["ente"]))
        $strsql_news.= " AND (codice_ente = :codice_ente)";
    else
        $strsql_news.= " AND (codice_ente = 0) ";

    $where_news ="";
    if(!isset($_GET["advanced"])&&(isset($value))){
        if(preg_match('/\s/', $value)){
            $value = str_replace(" ", "%", $value);
        }
        $bind[":value"] = "%".$value."%";
        $where_news .= " (b_news.titolo LIKE :value OR b_news.testo LIKE :value) ";
    }
    else{
        if(isset($_POST["oggetto"])&&$_POST["oggetto"]!=""){
            $oggetto = $_POST["oggetto"];
            if(preg_match('/\s/', $oggetto)){
                $oggetto = str_replace(" ", "%", $oggetto);
            }
            $bind[":oggetto"] = "%" . $oggetto . "%";
            $where_news .= " (b_news.titolo LIKE :oggetto OR b_news.testo LIKE :oggetto) ";
        }
    }

    if (isset($where_news) && $where_news != "")
        $strsql_news .= " AND (" . $where_news .")";


    $strsql_news .= " ORDER BY b_news.data DESC,  b_news.timestamp DESC " ;

    $risultato_news  = $pdo->bindAndExec($strsql_news,$bind);
    }

if(isset($_POST["advCatalogo"])&&($_POST["advCatalogo"]==1)){
    if(isset($_SESSION["codice_utente"])){
        //RICERCA CATALOGO
        $bind = array(":codice_ente" => $_SESSION["ente"]["codice"]);
        if(isset($_SESSION["ente"])){
          if(isset($_SESSION["codice_utente"])){
              if(!is_operatore()){
                $strsql_catalogo = "SELECT b_catalogo.*, b_operatori_economici.ragione_sociale, b_bandi_mercato.oggetto AS bando, b_cpv.descrizione AS cpv FROM b_catalogo JOIN b_bandi_mercato ON b_catalogo.codice_bando = b_bandi_mercato.codice ";
                $strsql_catalogo.= "JOIN b_cpv ON b_catalogo.codice_cpv = b_cpv.codice JOIN b_operatori_economici ON b_catalogo.codice_operatore = b_operatori_economici.codice ";
                $strsql_catalogo.= "WHERE b_catalogo.codice_ente = :codice_ente ";
                $strsql_catalogo.= " AND b_bandi_mercato.annullata = 'N' AND b_bandi_mercato.data_scadenza > now() ";
                $strsql_catalogo.= " AND (b_bandi_mercato.codice_ente = :codice_ente OR b_bandi_mercato.codice_gestore = :codice_ente) ";
                $strsql_catalogo.= " AND (b_bandi_mercato.pubblica = '2' OR b_bandi_mercato.pubblica = '1') AND b_catalogo.eliminato = 'N' AND b_catalogo.attivo = 'S' ";
            }else{
                $bind[":codice_utente"] = $_SESSION["codice_utente"];
                $strsql_catalogo = "SELECT b_catalogo.*,b_operatori_economici.ragione_sociale, b_bandi_mercato.oggetto AS bando, b_cpv.descrizione AS cpv FROM b_catalogo JOIN b_bandi_mercato ON b_catalogo.codice_bando = b_bandi_mercato.codice ";
                $strsql_catalogo.= "JOIN b_cpv ON b_catalogo.codice_cpv = b_cpv.codice JOIN b_operatori_economici ON b_catalogo.codice_operatore = b_operatori_economici.codice ";
                $strsql_catalogo.= "WHERE b_catalogo.codice_ente = :codice_ente ";
                $strsql_catalogo.= " AND b_catalogo.eliminato = 'N' AND b_operatori_economici.codice_utente = :codice_utente AND b_catalogo.codice NOT IN (";
                $strsql_catalogo.= "SELECT codice_aggiornamento FROM b_catalogo JOIN b_operatori_economici ON b_catalogo.codice_operatore = b_operatori_economici.codice ";
                $strsql_catalogo.= "WHERE b_catalogo.codice_ente =:codice_ente AND b_operatori_economici.codice_utente = :codice_utente ";
                $strsql_catalogo.= ")";
            }
        }
        $where_catalogo=array();
        if(!isset($_GET["advanced"])&&(isset($value))){
            if(preg_match('/\s/', $value)){
                $value = str_replace(" ", "%", $value);
            }
            $bind[":value"] = "%".$value."%";
            $where_catalogo[] = "(b_catalogo.denominazione LIKE :value OR b_catalogo.descrizione LIKE :value)";
        }else{
            if(isset($_POST["oggetto"])&&$_POST["oggetto"]!=""){
              $oggetto = $_POST["oggetto"];
              if(preg_match('/\s/', $oggetto)){
                  $oggetto = str_replace(" ", "%", $oggetto);
              }
              $bind[":oggetto"] = "%" . $oggetto . "%";
              $where_catalogo[] = "(b_catalogo.denominazione LIKE :oggetto OR b_catalogo.descrizione LIKE :oggetto)";
            }

            if(isset($_POST["bando"])&&$_POST["bando"]!="")
                if($_POST["bando"]!=0) {
                  $bind[":codice_bando"] = $_POST["bando"];
                  $where_catalogo[] = "(b_catalogo.codice_bando = :codice_bando)";
                }
            if(isset($_POST["codice_operatore"])&&$_POST["codice_operatore"]!=""){
                  $bind[":codice_operatore"] = $_POST["codice_operatore"];
                  $where_catalogo[] = "(codice_operatore = :codice_operatore)";
            }else{
                if(isset($_POST["opEconomico"])&&$_POST["opEconomico"]!=""){
                    $bind[":opEconomico"] = "%".$_POST["opEconomico"]."%";
                    $where_catalogo[] = "(ragione_sociale LIKE :opEconomico)";
                }
            }


            if(isset($_POST["codice_cpv_catalogo"])&&$_POST["codice_cpv_catalogo"]!="") {
                $bind[":cpv"] = $_POST["codice_cpv_catalogo"]."%";
                $where_catalogo[]= "(b_catalogo.codice_cpv LIKE :cpv)";
            }else{
                if(isset($_POST["cpv"])&&$_POST["cpv"]!=""){
                    $bind[":cpv"] = "%".$_POST["cpv"]."%";
                    if(is_numeric($_POST["cpv"]))
                        $where_catalogo[]= "(b_catalogo.codice_cpv LIKE :cpv) ";
                    else
                        $where_catalogo[]= "(b_cpv.descrizione LIKE :cpv) ";
            }
}
}
        $where = "";
        foreach ($where_catalogo as $chiave => $valore) {
            $where .= $valore ." AND ";
        }
        $where = substr($where,0, strlen($where)-4);

        if (isset($where) && $where != "")
            $strsql_catalogo.= " AND (" . $where .")";

        if(!is_operatore())
          $strsql_catalogo .=" ORDER BY oggetto";
        else
          $strsql_catalogo.= " ORDER BY identificativo_fornitore ";

        //echo $strsql_catalogo;

        $risultato_catalogo = $pdo->bindAndExec($strsql_catalogo,$bind);
        }
    }
}

if(isset($_POST["advGare"])||isset($_POST["advCatalogo"])){
?>
<div id="tabs">
<?

if(isset($risultato_gare))
    $num_gare=$risultato_gare->rowCount();
else
    $num_gare=0;

if(isset($risultato_avvisi))
    $num_avvisi=$risultato_avvisi->rowCount();
else
    $num_avvisi=0;

if(isset($risultato_news))
    $num_news=$risultato_news->rowCount();
else
    $num_news=0;

if(isset($risultato_catalogo))
    $num_catalogo=$risultato_catalogo->rowCount();
else
    $num_catalogo=0;

if(($num_gare>0)||($num_avvisi>0)||($num_news>0)||($num_catalogo>0)) { ?>
<ul>
    <? if ($num_gare>0) { ?><li style="position:relative;padding-right: 50px;"><a href="#tabs-gare">Gare<div class="badge"><?echo $num_gare?></div></a></li><? } ?>
    <? if ($num_avvisi>0) { ?><li style="position:relative;padding-right: 50px;"><a href="#tabs-avvisi">Avvisi di gara<div class="badge"><?echo $num_avvisi?></div></a></li><? } ?>
    <? if ($num_news>0) {  ?><li style="position:relative;padding-right: 50px;"><a href="#tabs-news">Notizie<div class="badge"><?echo $num_news?></div></a></li><? } ?>
    <? if ($num_catalogo>0) {  ?><li style="position:relative;padding-right: 50px;"><a href="#tabs-catalogo">Catalogo<div class="badge"><?echo $num_catalogo?></div></a></li><? } ?>
</ul>
<?}else{
    echo "<h1>Nessun risultato presente nel sistema</h1>";
}

if ($num_gare>0) {
    ?>
    <div id="tabs-gare">
      <table width="100%" id="gare" class="elenco">
        <thead>
          <tr><td>ID</td><td>CIG</td><td>Tipologia</td><td>Criterio</td><td>Procedura</td><td>Oggetto</td><td>Scadenza</td></tr></thead>
        <tbody>
            <? while ($record = $risultato_gare->fetch(PDO::FETCH_ASSOC)) { ?>
            <tr id="<? echo $record["codice"] ?>">
                <td width="5%"><? echo $record["id"] ?></td>
                <td><? echo $record["cig"]; ?></td>
                <td><? echo $record["tipologia"] ?></td>
                <td><? echo $record["criterio"] ?></td>
                <td><? echo $record["procedura"] ?></td>
                <td width="75%">
                  <? if ($record["annullata"] == "S") {
                    echo "<strong>Annullata con atto n. " . $record["numero_annullamento"] . " del " . mysql2date($record["data_annullamento"]) . "</strong> - ";
                } ?>
                <a href="/gare/id<? echo $record["codice"] ?>-dettaglio" title="Dettagli gara"><? echo $record["oggetto"] ?></a></td>
                <td><? echo mysql2datetime($record["data_scadenza"]) ?></td>
            </tr>
            <?}?>
        </tbody>
    </table>
    <div class="clear"></div>
    </div>
<?
}

if ($num_avvisi>0) {
    ?>
    <div id="tabs-avvisi">
      <table class="elenco" style="width:100%">
        <thead style="display:none;"><tr><td></td><td></td></tr></thead>
          <tbody>
            <?
            while ($record = $risultato_avvisi->fetch(PDO::FETCH_ASSOC)) {
              $codice     = $record["codice"];
              $titolo     = $record["titolo"];
              $data     = mysql2date($record["data"]);
              $testo      = strip_tags($record["testo"]);
              $href = "/gare/avvisi/dettaglio.php?cod=".$codice;
              ?>
              <tr id="<? echo $codice ?>">
                <td width="10"><strong><? echo $data ?></strong></td><td><strong><a href="<? echo $href ?>" title="<? echo $titolo ?>"><? echo $titolo;
 ?> - Gara <? echo $record["id"] . ": " . $record["oggetto"] ?></a></strong><br><? echo substr($testo,0,255);
 ?>...</td>
              </tr>
        <?}?>
        </tbody>
    </table>
    <div class="clear"></div>
    </div>
<?}

if ($num_news>0) {
  ?>
  <div id="tabs-news">
  <table class="elenco" style="width:100%">
    <thead style="display:none;"><tr><td></td><td></td></tr></thead>
      <tbody>
        <?
        while ($record = $risultato_news->fetch(PDO::FETCH_ASSOC)) {
          $codice     = $record["codice"];
          $titolo     = strtoupper($record["titolo"]);
          $data     = mysql2date($record["data"]);
          $testo      = strip_tags($record["testo"]);
          $href = "/news/dettaglio.php?cod=".$codice;
          ?>
          <tr>
          <td><strong><? echo $data ?></strong></td><td><strong><a href="<? echo $href ?>" title="<? echo $record["titolo"] ?>"><? echo $record["titolo"]; ?></a></strong><br>
            <? echo substr($testo,0,255); ?>...
            </td>
        </tr>
        <?}?>
        </tbody>
    </table>
    <div class="clear"></div>
    </div>
    <?
}

if($num_catalogo>0){
    ?>
    <div id="tabs-catalogo">
    <table class="elenco" width="100%">
        <thead><tr><td>Denominazione</td><td>CPV</td><td>Prezzo</td><td>Fornitore</td></tr></thead>
    <tbody>
    <? while($record_catalogo = $risultato_catalogo->fetch(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><strong><a href="/catalogo/id<? echo $record_catalogo["codice"] ?>-dettaglio"?><?
                    echo $record_catalogo["denominazione"]?></a></strong><br><?
                    echo substr($record_catalogo["descrizione"], 0, 300)?></td>
            <td><? echo $record_catalogo["cpv"]?></td>
            <td width="100">&euro; <?
                    echo number_format($record_catalogo["prezzo"], 2, ",", ".");?></td>
                    <td><?
                    echo $record_catalogo["ragione_sociale"]?></td>
                    </tr>
                    <?
                }
                ?>
    </tbody>
    </table>
    <div class="clear"></div>
    </div>
        <?}
    }
}
    ?>
    <script>
    $("#tabs").tabs();
    $("#maschera").tabs();

    if ($(".cerca_cpv_simple").length > 0) {
        $(".cerca_cpv_simple").autocomplete({
            source: function(request, response) {
                $.ajax({
                url: "/moduli/cpv_simple.php",
                dataType: "json",
                data: {
                    term : request.term
                },
                success: function(data) {
                    response(data);
                }
                });
            },
            minLength: 2,
            search  : function(){$(this).addClass('working'); $("#codice_cpv").val('');},
            open    : function(){$(this).removeClass('working');},
            select: function(e, ui) {
                e.preventDefault(); // <--- Prevent the value from being inserted.
                $(this).val(ui.item.label);
                $("#codice_cpv").val(ui.item.value);
                $(this).focus();
            },
            focus: function(e, ui) {
                e.preventDefault(); // <--- Prevent the value from being inserted.
            }
        }).data("ui-autocomplete")._renderItem = function( ul, item ) {
            return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> " + item.label).appendTo( ul );
        }
    }

    if ($(".cerca_cpv_simple2").length > 0) {
        $(".cerca_cpv_simple2").autocomplete({
            source: function(request, response) {
                $.ajax({
                url: "/moduli/cpv_simple.php",
                dataType: "json",
                data: {
                    term : request.term
                },
                success: function(data) {
                    response(data);
                }
                });
            },
            minLength: 2,
            search  : function(){$(this).addClass('working'); $("#codice_cpv_catalogo").val('');},
            open    : function(){$(this).removeClass('working');},
            select: function(e, ui) {
                e.preventDefault(); // <--- Prevent the value from being inserted.
                $(this).val(ui.item.label);
                $("#codice_cpv_catalogo").val(ui.item.value);
                $(this).focus();
            },
            focus: function(e, ui) {
                e.preventDefault(); // <--- Prevent the value from being inserted.
            }
        }).data("ui-autocomplete")._renderItem = function( ul, item ) {
            return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> " + item.label).appendTo( ul );
        }
    }

    if ($(".cerca_operatori").length > 0) {
        $(".cerca_operatori").autocomplete({
            source: function(request, response) {
                $.ajax({
                url: "operatori.php",
                dataType: "json",
                data: {
                    term : request.term
                },
                success: function(data) {
                    response(data);
                }
                });
            },
            minLength: 3,
            search  : function(){$(this).addClass('working'); $("#codice_operatore").val('');},
            open    : function(){$(this).removeClass('working');},
            select: function(e, ui) {
                e.preventDefault(); // <--- Prevent the value from being inserted.
                $(this).val(ui.item.label);
                $("#codice_operatore").val(ui.item.codice_operatore);
                $(this).focus();
            },
            focus: function(e, ui) {
                e.preventDefault(); // <--- Prevent the value from being inserted.
            }
        }).data("ui-autocomplete")._renderItem = function( ul, item ) {
            return $( "<li id='val"+ item.value +"'>" ).append("<a><strong>" + item.value + "</strong> " + item.label).appendTo( ul );
        }
    }
  </script>
</div>
<?
include_once($root."/layout/bottom.php");
?>
