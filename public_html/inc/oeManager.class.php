<?
  class oeManager {

      public $cpv;
      public $categoria_soa;
      public $classifica_soa;
      public $classifica_only_selected;
      public $categoria_progettazione;
      public $codice_elenco;
      public $tipo_elenco;
      public $stato;
      public $regione;
      public $provincia;
      public $citta;
      public $conteggio_rotazione;
      public $showInviti; // visualizza il numero di inviti ricevuti
      public $showAffidamenti;
      public $showDataRichiesta; // visualizza la data di richiesta di abilitazione all'iniziativa - Valido solo in presenza di codice_elenco e tipo_elenco
      public $showDataAbilitazione; // visualizza la data di abilitazione all'iniziativa - Valido solo in presenza di codice_elenco e tipo_elenco
      public $extend;
      public $order;
      private $list;
      private $ente;
      private $sua;
      private $db;
      private $admittedOrderKeys;

      public function __construct() {
        global $pdo;
        $this->db = $pdo;
        $this->cpv = "";
        $this->categoria_soa = "";
        $this->classifica_soa = "";
        $this->classifica_only_selected = true;
        $this->categoria_progettazione = "";
        $this->conteggio_rotazione = "";
        $this->codice_elenco = "";
        $this->tipo_elenco = "";
        $this->stato = "";
        $this->regione = "";
        $this->provincia = "";
        $this->citta = "";
        $this->extend = "N";
        $this->showInviti = false;
        $this->showAffidamenti = false;
        $this->showDataRichiesta = false;
        $this->showDataAbilitazione = false;
        $this->admittedOrderKeys = array("id_interno","ragione_sociale","cognome","nome","codice_fiscale_impresa","partita_iva","tipo","timestamp_richiesta","timestamp_abilitazione","timestamp_iscrizione","feedback");
        $this->order = array("ragione_sociale"=>"ASC");
      }

      public function getList() {
        $this->list = false;
        if ($this->extend == "S") {
          if (!check_permessi("albo/extended",$_SESSION["codice_utente"])) {
            $this->extend = "N";
          } else {
            $this->order = array("ragione_sociale"=>"ASC");
            $this->showAffidamenti = false;
            $this->showInviti = false;
            $this->showDataRichiesta = false;
            $this->showDataAbilitazione = false;
          }
        } else {
          $this->extend = "N";
        }
        if (!empty($_SESSION["ente"]["codice"])) {
          $this->ente = $_SESSION["ente"]["codice"];
          $this->sua = $_SESSION["ente"]["sua"];
          $bind = array();
          $strsql  = "SELECT b_operatori_economici.ragione_sociale, b_operatori_economici.partita_iva, 
                      b_operatori_economici.indirizzo_legale,
                      b_operatori_economici.citta_legale,
                      b_operatori_economici.provincia_legale,
                      b_utenti.codice AS codice, b_utenti.pec, b_utenti.nome, b_utenti.cognome, b_utenti.telefono,
                      b_gruppi.gruppo AS tipo, b_operatori_economici.codice_fiscale_impresa, b_operatori_economici.codice as codice_operatore,
                      r_enti_operatori.timestamp AS timestamp_iscrizione, r_enti_operatori.feedback ";
          if ($this->extend == "N") {
            $strsql .= ", r_enti_operatori.id_interno ";
          } else {
            $strsql .= ", NULL AS id_interno ";
          }
          if (!empty($this->tipo_elenco) && !empty($this->codice_elenco)) {
            if ($this->showDataRichiesta) $strsql .= ", list_partecipanti.timestamp_richiesta ";
            if ($this->showDataAbilitazione) $strsql .= ", list_partecipanti.timestamp_abilitazione ";
          }
          $strsql  .= "FROM b_utenti
                       JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
                       JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente
                       JOIN r_enti_operatori ON r_enti_operatori.cod_utente = b_utenti.codice ";
          if ($this->extend == "S") $strsql  .= " JOIN b_enti ON b_enti.codice = r_enti_operatori.cod_ente ";
          if (!empty($this->cpv)) $strsql .= "JOIN r_cpv_operatori ON b_operatori_economici.codice = r_cpv_operatori.codice_operatore ";
          if (!empty($this->categoria_soa) || (!empty($this->classifica_soa))) $strsql .= " JOIN b_certificazioni_soa ON b_operatori_economici.codice = b_certificazioni_soa.codice_operatore ";
          if (!empty($this->categoria_progettazione)) $strsql .= " JOIN b_esperienze_progettazione ON b_operatori_economici.codice = b_esperienze_progettazione.codice_operatore ";
          if (!empty($this->tipo_elenco)) {
            switch($this->tipo_elenco) {
              case "albo":
               $strsql .= "JOIN r_partecipanti_albo AS list_partecipanti ON b_operatori_economici.codice = list_partecipanti.codice_operatore
                           JOIN b_bandi_albo AS bando_riferimento ON list_partecipanti.codice_bando = bando_riferimento.codice ";
              break;
              case "mercato":
               $strsql .= "JOIN r_partecipanti_me AS list_partecipanti ON b_operatori_economici.codice = list_partecipanti.codice_operatore
                           JOIN b_bandi_mercato AS bando_riferimento ON list_partecipanti.codice_bando = bando_riferimento.codice ";
              break;
              case "sda":
               $strsql .= "JOIN r_partecipanti_sda AS list_partecipanti ON b_operatori_economici.codice = list_partecipanti.codice_operatore
                           JOIN b_bandi_sda AS bando_riferimento ON list_partecipanti.codice_bando = bando_riferimento.codice ";
              break;
            }
          }
          $strsql .= "WHERE b_gruppi.gerarchia > 2 AND b_utenti.attivo = 'S' ";
          if (!empty($this->stato)) { $bind[":stato"] = $this->stato; $strsql .= " AND b_operatori_economici.stato_operativa = :stato "; }
      		if (!empty($this->regione)) { $bind[":regione"] = $this->regione; $strsql .= " AND b_operatori_economici.regione_operativa = :regione "; }
      		if (!empty($this->provincia)) { $bind[":provincia"] = $this->provincia; $strsql .= " AND b_operatori_economici.provincia_operativa = :provincia "; }
      		if (!empty($this->citta)) { $bind[":citta"] = $this->citta; $strsql .= " AND b_operatori_economici.citta_operativa = :citta "; }
          if (!empty($this->tipo_elenco) && !empty($this->codice_elenco)) {
            $bind[":codice_bando"] = $this->codice_elenco;
            $strsql .=" AND list_partecipanti.codice_bando = :codice_bando AND list_partecipanti.ammesso = 'S' ";
          } else if (!empty($this->tipo_elenco) && empty($this->codice_elenco)) {
            $bind[":codice_ente"]=$this->ente;
            $bind[":codice_sua"]=$this->sua;
            $strsql .= " AND (r_enti_operatori.cod_ente = :codice_ente OR r_enti_operatori.cod_ente = :codice_sua) ";
            $strsql .=" AND list_partecipanti.ammesso = 'S'
                        AND (bando_riferimento.codice_gestore = :codice_ente OR bando_riferimento.codice_gestore = :codice_sua)
                        AND (bando_riferimento.tipologia = 'F' OR bando_riferimento.tipologia = 'P') ";
          } else {
            $bind[":codice_ente"]=$this->ente;
            $bind[":codice_sua"]=$this->sua;
            $strsql .= " AND ((r_enti_operatori.cod_ente = :codice_ente OR r_enti_operatori.cod_ente = :codice_sua) ";
            if ($this->extend=="S") $strsql .= " OR (b_enti.ambienteTest = 'N') ";
            // var_dump($this->extend);
            $strsql .= ")";
          }

          if (!empty($this->cpv)) {
    				$strsql .= " AND (";
    				$categorie = explode(";",$this->cpv);
    					$n_cat = 0;
    					foreach($categorie as $codice) {
    						$n_cat++;
    						if ($codice != "") {
    							$bind[":cat_".$n_cat] = $codice;
    							$strsql .= "(r_cpv_operatori.codice = :cat_".$n_cat ." ";
    							if (strlen($codice)>2) {
    								$diff = strlen($codice) - 2;
    								$n_sub_cat = 0;
    								for($i=1;$i<=$diff;$i++) {
    									$n_sub_cat++;
    									$bind[":cat_".$n_cat."_".$n_sub_cat] = substr($codice,0,$i*-1);
    									$strsql .= "OR r_cpv_operatori.codice = :cat_".$n_cat."_".$n_sub_cat . " ";
    								}
    							}
    							$strsql.=") OR ";
    						}
    					}
    				$strsql = substr($strsql,0,-4);
    				$strsql .= ")";
      		}
          if (!empty($this->categoria_soa)) {
    				$bind[":soa"] = $this->categoria_soa;
    				$strsql .= " AND b_certificazioni_soa.codice_categoria = :soa";
    			}
    			if (!empty($this->categoria_progettazione)) {
    				$bind[":progettazione"] = $this->categoria_progettazione;
    				$strsql .= " AND b_esperienze_progettazione.codice_categoria = :progettazione";
    			}
    			if (!empty($this->classifica_soa)) {
    				$bind[":classifica"] = $this->classifica_soa;
    				if (!$this->classifica_only_selected) {
    					$strsql .= " AND b_certificazioni_soa.codice_classifica >= :classifica ";
    				} else {
    					$strsql .= " AND b_certificazioni_soa.codice_classifica = :classifica ";
    				}
    			}
    		$strsql .= " GROUP BY b_utenti.codice " ;

        $inviti_sort_key = "";
        $inviti_sort_available = array("timestamp_richiesta","timestamp_abilitazione","ragione_sociale");

        $aggiudicato_sort_key = "";
        $aggiudicato_sort_available = array("timestamp_richiesta","timestamp_abilitazione","ragione_sociale");

        if (!empty($this->order) && is_array($this->order)) {
          $order = array();
          foreach($this->order AS $key => $value) {
            if (empty($inviti_sort_key) && in_array($key, $inviti_sort_available) !== false) $inviti_sort_key = $key;
            if (empty($aggiudicato_sort_key) && in_array($key, $aggiudicato_sort_available) !== false) $aggiudicato_sort_key = $key;
            if (in_array($key, $this->admittedOrderKeys)) {
              if ($value == "ASC") {
                $order[] = $key . " ASC";
              } else if ($value == "DESC") {
                $order[] = $key . " DESC";
              }
            }
          }
          if (count($order) > 0) {
            $order = implode(",", $order);
            $strsql .= "ORDER BY " . $order;
          }
        }
        $risultati = $this->db->bindAndExec($strsql,$bind);
        if ($risultati->rowCount() > 0) {
          if ($risultati->rowCount() > 2000) ini_set('memory_limit','-1');
          $this->list = $risultati->fetchAll(PDO::FETCH_ASSOC);
          
          if ($this->showInviti) $this->addInviti();

          $sort_inviti_key = "";
          if (!empty($this->order["inviti"])) {
            $sort_inviti_key = "inviti";
            $sort_inviti_dir = $this->order["inviti"];
          } else if (!empty($this->order["inviti_anno"])) {
            $sort_inviti_key = "inviti_anno";
            $sort_inviti_dir = $this->order["inviti_anno"];
          }

          if (!empty($sort_inviti_key)) {
            $sort_inviti_dir = ($sort_inviti_dir == "DESC") ? SORT_DESC : SORT_ASC;
            if (!empty($inviti_sort_key) && isset($this->list[0][$inviti_sort_key])) {
              $this->list = array_orderby($this->list,$sort_inviti_key,$sort_inviti_dir,$inviti_sort_key,SORT_ASC);
            } else {
              $this->list = array_orderby($this->list,$sort_inviti_key,$sort_inviti_dir);
            }
          }

          if ($this->showAffidamenti) $this->addAffidamenti();

          $sort_aggiudicato_key = "";
          if (!empty($this->order["aggiudicato"])) {
            $sort_aggiudicato_key = "aggiudicato";
            $sort_aggiudicato_dir = $this->order["aggiudicato"];
          } else if (!empty($this->order["aggiudicato_anno"])) {
            $sort_aggiudicato_key = "aggiudicato_anno";
            $sort_aggiudicato_dir = $this->order["aggiudicato_anno"];
          }
          if (!empty($sort_aggiudicato_key)) {
            $sort_aggiudicato_dir = ($sort_aggiudicato_dir == "DESC") ? SORT_DESC : SORT_ASC;
            if (!empty($aggiudicato_sort_key) && isset($this->list[0][$aggiudicato_sort_key])) {
              $this->list = array_orderby($this->list,$sort_aggiudicato_key,$sort_aggiudicato_dir,$aggiudicato_sort_key,SORT_ASC);
            } else {
              $this->list = array_orderby($this->list,$sort_aggiudicato_key,$sort_aggiudicato_dir);
            }
          }
        }
      }
      return $this->list;
    }

    private function addInviti() {
      if (!empty($this->list)) {
        if (count($this->list) < 1000) {
          $bind = array();
          $bind[":codice_ente"] = $this->ente;
          $sql_filtro = " SELECT r_inviti_gare.* FROM r_inviti_gare JOIN b_gare ON r_inviti_gare.codice_gara = b_gare.codice ";
          if (!empty($this->conteggio_rotazione)) {
            if (!empty($this->categoria_soa) && strpos($this->conteggio_rotazione,"soa") !== false) {
              $sql_filtro .= " JOIN b_qualificazione_lavori ON b_gare.codice = b_qualificazione_lavori.codice_gara ";
            } else if (!empty($this->cpv) && strpos($this->conteggio_rotazione,"cpv") !== false) {
              $sql_filtro .= " JOIN r_cpv_gare ON b_gare.codice = r_cpv_gare.codice_gara ";
            } else if (!empty($this->categoria_progettazione) && strpos($this->conteggio_rotazione,"progettazione") !== false) {
              $sql_filtro .= " JOIN b_qualificazione_progettazione ON b_gare.codice = b_qualificazione_progettazione.codice_gara ";
            }
          }
          $sql_filtro .= " WHERE r_inviti_gare.codice_utente = :codice AND b_gare.codice_gestore = :codice_ente ";
          if (!empty($this->codice_elenco)) {
            $bind[":codice_elenco"] = $this->codice_elenco;
            $bind[":tipo_elenco"] = $this->tipo_elenco;
            $sql_filtro .= " AND b_gare.codice_elenco = :codice_elenco AND b_gare.tipo_elenco = :tipo_elenco ";
          }
          if (!empty($this->conteggio_rotazione)) {
            if (!empty($this->categoria_soa) && strpos($this->conteggio_rotazione,"soa") !== false) {
              $bind[":soa"] = $this->categoria_soa;
              $sql_filtro .= " AND b_qualificazione_lavori.codice_categoria = :soa AND tipo = 'P' ";
              if (!empty($this->classifica_soa) && $this->conteggio_rotazione == "soa_classifica") {
                // $bind[":classifica"] = $this->classifica_soa;
                $sql = "SELECT * FROM b_classifiche_soa WHERE codice = :classifica AND attivo = 'S' ";
                $ris_class = $this->db->bindAndExec($sql,array(":classifica"=>$this->classifica_soa));
                if ($ris_class->rowCount() > 0)
                  $class = $ris_class->fetch(PDO::FETCH_ASSOC);
                  $bind[":limite_minimo"] = $class["minimo"];
                  $sql_filtro .= " AND (b_qualificazione_lavori.importo_base + b_qualificazione_lavori.importo_oneri) >= :limite_minimo ";
                if ($class["massimo"] > 0) {
                  $bind[":limite_massimo"] = $class["massimo"];
                  $sql_filtro .= " AND (b_qualificazione_lavori.importo_base + b_qualificazione_lavori.importo_oneri) <= :limite_massimo ";
                }
              }
            } else if (!empty($this->cpv) && strpos($this->conteggio_rotazione,"cpv") !== false) {
              $cpv_length = substr($this->conteggio_rotazione, -1);
              if (is_numeric($cpv_length)) {
                $sql_filtro .= " AND (";
                $categorie = explode(";",$this->cpv);
                $n_cat = 0;
                foreach($categorie as $codice) {
                  $n_cat++;
                  if ($codice != "") {
                    $codice = substr($codice,0,$cpv_length);
                    $bind[":cat_".$n_cat] = $codice . '%';
                    $sql_filtro .= "(r_cpv_gare.codice LIKE :cat_".$n_cat ." ";
                    $sql_filtro.=") OR ";
                  }
                }
                $sql_filtro = substr($sql_filtro,0,-4);
                $sql_filtro .= ")";
              }
            } else if (!empty($this->categoria_progettazione) && strpos($this->conteggio_rotazione,"progettazione") !== false) {
              $bind[":progettazione"] = $this->categoria_progettazione;
              $sql_filtro .= " AND b_qualificazione_progettazione.codice_categoria = :progettazione ";
            }
          }


          $sth_inviti_generali = $this->db->prepare($sql_filtro . " GROUP BY r_inviti_gare.codice_gara ");
          $sql_filtro .= " AND (YEAR(b_gare.data_pubblicazione) = " . date("Y") . " OR (b_gare.data_pubblicazione IS NULL AND YEAR(b_gare.data_scadenza) = " . date("Y") . "))";
          $sth_inviti_anno = $this->db->prepare($sql_filtro . " GROUP BY r_inviti_gare.codice_gara ");
          foreach($this->list AS $key => $operatore) {
            $bind[":codice"] = $operatore["codice"];

            $operatore["inviti"] = 0;
            $operatore["inviti_anno"] = 0;
            $operatore["inviti_categoria"] = 0;

            $sth_inviti_generali->execute($bind);
            if ($sth_inviti_generali->rowCount() > 0) $operatore["inviti"] = $sth_inviti_generali->rowCount();
            $sth_inviti_anno->execute($bind);
            if ($sth_inviti_anno->rowCount() > 0) $operatore["inviti_anno"] = $sth_inviti_anno->rowCount();

            $this->list[$key] = $operatore;
          }
        } else {
          foreach($this->list AS $key => $operatore) {
            $operatore["inviti"] = "n/a";
            $operatore["inviti_anno"] = "n/a";
            $operatore["inviti_categoria"] = "n/a";
            $this->list[$key] = $operatore;
          }
        }
      }
    }


    private function addAffidamenti() {
      if (!empty($this->list)) {
        if (count($this->list) < 1000) {
          $bind_agg = array(":codice_ente"=>$this->ente);

          $sql = "SELECT b_gare.ribasso FROM r_partecipanti JOIN b_gare ON r_partecipanti.codice_gara = b_gare.codice
                  WHERE r_partecipanti.codice_utente = :codice_utente AND b_gare.codice_gestore = :codice_ente
                  AND r_partecipanti.primo = 'S' AND b_gare.ribasso > 0 AND b_gare.procedura = 11 "; // procedura 11 affidamento diretto

          $sth_agg = $this->db->prepare($sql);

          $sql .= " AND YEAR(b_gare.data_pubblicazione) = " . date("Y");
          $sth_agg_anno = $this->db->prepare($sql);


          foreach($this->list AS $key => $operatore) {
            $operatore["aggiudicato"] = 0;
            $operatore["aggiudicato_anno"] = 0;
            $bind_agg[":codice_utente"] = $operatore["codice"];
            $sth_agg->execute($bind_agg);
            if ($sth_agg->rowCount() > 0) {
              while($record = $sth_agg->fetch(PDO::FETCH_ASSOC)) $operatore["aggiudicato"] += $record["ribasso"];
            }
            $sth_agg_anno->execute($bind_agg);
            if ($sth_agg_anno->rowCount() > 0) {
              while($record = $sth_agg_anno->fetch(PDO::FETCH_ASSOC)) $operatore["aggiudicato_anno"] += $record["ribasso"];
            }
            $this->list[$key] = $operatore;
          }
        } else {
          foreach($this->list AS $key => $operatore) {
            $operatore["aggiudicato"] = "n/a";
            $operatore["aggiudicato_anno"] = "n/a";
            $this->list[$key] = $operatore;
          }
        }
      }
    }

    public static function printFilterForm($showInvitiFilters=true) {
      $return = false;
      global $pdo;
      global $root;
      if (!empty($_SESSION["ente"]["codice"])) {
        $return = true;
        $ente = $_SESSION["ente"]["codice"];
        $sua = $_SESSION["ente"]["sua"];
        ?>
        <div style="float:left; width:44%">
          <strong>Sede operativa</strong>
          <table style="width:100%">
            <tr>
              <td width="30%">Stato</td>
              <td>
                <select class="oeManagerInput" name="oeManager[stato]" id="oeManager-stato">
                  <option value="">Tutti</option>
                  <?
                  $bind = array(":codice_ente"=>$ente);
                  $sql =  "SELECT b_operatori_economici.stato_operativa as stato FROM b_operatori_economici JOIN b_utenti ON
                        b_operatori_economici.codice_utente = b_utenti.codice JOIN r_enti_operatori ON
                        b_utenti.codice = r_enti_operatori.cod_utente WHERE b_utenti.attivo = 'S' AND
                        (r_enti_operatori.cod_ente = :codice_ente ";
                  if (!empty($sua)) {
                      $bind[":codice_sua"] = $sua;
                      $sql.=" OR r_enti_operatori.cod_ente = :codice_sua";
                  }
                  $sql .= ") GROUP BY b_operatori_economici.stato_operativa
                        ORDER BY b_operatori_economici.stato_operativa ";
                  $ris = $pdo->bindAndExec($sql,$bind);
                  if ($ris->rowCount()>0) {
                    $stati_UE = getStatiUE();
                    while($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
                      if (!empty($stati_UE[$rec["stato"]])) { ?><option value="<?= $rec["stato"] ?>"><? echo $stati_UE[$rec["stato"]] ?></option><? }
                    }
                  }
                ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>Regione</td>
              <td>
                <select class="oeManagerInput" name="oeManager[regione]" id="oeManager-regione">
                  <option value="">Tutte</option>
                  <?
                    $sql =  "SELECT b_operatori_economici.regione_operativa as regione FROM b_operatori_economici JOIN b_utenti ON
                          b_operatori_economici.codice_utente = b_utenti.codice JOIN r_enti_operatori ON
                          b_utenti.codice = r_enti_operatori.cod_utente WHERE b_utenti.attivo = 'S' AND
                          (r_enti_operatori.cod_ente = :codice_ente ";
                          if (!empty($sua)) {
                              $bind[":codice_sua"] = $sua;
                              $sql.=" OR r_enti_operatori.cod_ente = :codice_sua";
                          }
                          $sql .= ")
                          GROUP BY b_operatori_economici.regione_operativa
                          ORDER BY b_operatori_economici.regione_operativa ";
                    $ris = $pdo->bindAndExec($sql,$bind);
                    if ($ris->rowCount()>0) {
                      while($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
                        ?><option><? echo $rec["regione"] ?></option><?
                      }
                    }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>Provincia</td>
              <td>
                <select class="oeManagerInput" name="oeManager[provincia]" id="oeManager-provincia">
                  <option value="">Tutte</option>
                  <?
                    $sql =  "SELECT b_operatori_economici.provincia_operativa as provincia FROM b_operatori_economici JOIN b_utenti ON
                          b_operatori_economici.codice_utente = b_utenti.codice JOIN r_enti_operatori ON
                          b_utenti.codice = r_enti_operatori.cod_utente WHERE b_utenti.attivo = 'S' AND
                          (r_enti_operatori.cod_ente = :codice_ente ";
                          if (!empty($sua)) {
                              $bind[":codice_sua"] = $sua;
                              $sql.=" OR r_enti_operatori.cod_ente = :codice_sua";
                          }
                          $sql .= ")
                          GROUP BY b_operatori_economici.provincia_operativa
                          ORDER BY b_operatori_economici.provincia_operativa ";
                    $ris = $pdo->bindAndExec($sql,$bind);
                    if ($ris->rowCount()>0) {
                      $province = getProvinceIT();
                      while($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
                        $label = $rec["provincia"];
                        foreach($province AS $provincia) if ($provincia["sigla"] == $rec["provincia"]) $label = ucfirst(strtolower($provincia["provincia"]));
                        ?><option value="<?= $rec["provincia"] ?>"><? echo $label ?></option><?
                      }
                    }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>Citta</td>
              <td>
                <select class="oeManagerInput" name="oeManager[citta]" id="oeManager-citta">
                  <option value="">Tutti</option>
                  <?
                    $sql =  "SELECT b_operatori_economici.citta_operativa as citta FROM b_operatori_economici JOIN b_utenti ON
                          b_operatori_economici.codice_utente = b_utenti.codice JOIN r_enti_operatori ON
                          b_utenti.codice = r_enti_operatori.cod_utente WHERE b_utenti.attivo = 'S' AND
                          (r_enti_operatori.cod_ente = :codice_ente ";
                          if (!empty($sua)) {
                              $bind[":codice_sua"] = $sua;
                              $sql.=" OR r_enti_operatori.cod_ente = :codice_sua";
                          }
                          $sql .= ")
                          GROUP BY b_operatori_economici.citta_operativa
                          ORDER BY b_operatori_economici.citta_operativa ";
                    $ris = $pdo->bindAndExec($sql,$bind);
                    if ($ris->rowCount()>0) {
                      while($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
                        ?><option><? echo $rec["citta"] ?></option><?
                      }
                    }
                  ?>
                </select>
              </td>
            </tr>
          </table>
          <script>
            <? if (!empty($_POST["oeManager"]["stato"])) { ?>$("#oeManager-stato").val('<? echo $_POST["oeManager"]["stato"] ?>');<? } ?>
            <? if (!empty($_POST["oeManager"]["regione"])) { ?>$("#oeManager-regione").val('<? echo $_POST["oeManager"]["regione"] ?>');<? } ?>
            <? if (!empty($_POST["oeManager"]["provincia"])) { ?>$("#oeManager-provincia").val('<? echo $_POST["oeManager"]["provincia"] ?>');<? } ?>
            <? if (!empty($_POST["oeManager"]["citta"])) { ?>$("#oeManager-citta").val('<? echo $_POST["oeManager"]["citta"] ?>');<? } ?>
          </script>
        </div>
        <div style="float:right; width:55%">
          <strong>Categorie</strong><br>
          <input type="text" class="cerca_cpv" rel="all" url="/albo/filtro_categorie/categoria.php" title="Cerca..." style="width:70%">
          <input type="button" class="submit" value="Scegli da lista" onClick="visualizza_cpv_disponibili();return false" style="width:26%">
          <div id="list_in" style="max-height:250px; overflow: auto;" style="text-align:left;">
            <?
              if (!empty($_POST["oeManager"]["cpv"])) {
                $categorie_filtro = explode(";",$_POST["oeManager"]["cpv"]);
                $categorie_filtro = array_filter($categorie_filtro);
                $i = 0;
                foreach($categorie_filtro as $categoria_filtro) {
                  if ($categoria_filtro != "") {
                    $bind = array();
                    $bind[":cat"] = $categoria_filtro;
                    $sql_filtro = "SELECT * FROM b_cpv WHERE codice = :cat";
                    $ris_filtro = $pdo->bindAndExec($sql_filtro,$bind);
                    if ($ris_filtro->rowCount()>0) {
                      $lista = "in";
                      $rec_categorie = $ris_filtro->fetch(PDO::FETCH_ASSOC);
                      include($root."/albo/filtro_categorie/categoria.php");
                    }
                  }
                }
              }
            ?>
          </div>
          <div id="list_all" style="display:none">
          <?
            $sql_categorie = "SELECT * FROM b_cpv WHERE LENGTH(codice)=2 ORDER BY codice";
            $ris_categorie = $pdo->query($sql_categorie);
            if ($ris_categorie->rowCount()>0) {
              $lista = "all";
              while($rec_categorie=$ris_categorie->fetch(PDO::FETCH_ASSOC)) {
                 include($root."/albo/filtro_categorie/categoria.php");
              }
            }
          ?>
          </div>
          <input type="hidden" class="oeManagerInput" id="cpv" name="oeManager[cpv]" value="<? if (!empty($_POST["oeManager"]["cpv"])) echo $_POST["oeManager"]["cpv"] ?>">
          <table style="table-layout:fixed" width="100%">
            <tr>
              <td class="etichetta">Categoria SOA</td>
              <td>
                <select class="oeManagerInput" title="Categoria SOA" name="oeManager[categoria_soa]" id="oeManager-categoria_soa">
                  <option value="">Nessuna</option>
                  <?
                    $sql_soa = "SELECT * FROM b_categorie_soa WHERE attivo = 'S' ORDER BY codice";
                    $ris_elenco_soa = $pdo->query($sql_soa);
                    if ($ris_elenco_soa->rowCount()>0) {
                      while($oggetto_soa = $ris_elenco_soa->fetch(PDO::FETCH_ASSOC)) {
                      ?>
                        <option value="<? echo $oggetto_soa["codice"] ?>"><strong><? echo $oggetto_soa["id"] . "</strong> - " . $oggetto_soa["descrizione"] ?></option>
                      <?
                      }
                    }
                ?>
                </select>
                <? if (!empty($_POST["oeManager"]["categoria_soa"])) { ?>
                  <script>
                    $("#oeManager-categoria_soa").val('<? echo $_POST["oeManager"]["categoria_soa"] ?>');
                  </script>
                <? } ?>
              </td>
            </tr>
            <tr>
              <td class="etichetta">Classifica</td>
              <td>
                <select title="Classifica SOA" class="oeManagerInput" name="oeManager[classifica_soa]" id="oeManager-classifica_soa">
                  <option value="">Nessuna</option>
                  <?
                    $sql_soa = "SELECT * FROM b_classifiche_soa WHERE attivo = 'S' ORDER BY codice";
                    $ris_elenco_soa = $pdo->query($sql_soa);
                    if ($ris_elenco_soa->rowCount()>0) {
                      while($oggetto_soa = $ris_elenco_soa->fetch(PDO::FETCH_ASSOC)) {
                      ?>
                        <option value="<? echo $oggetto_soa["codice"] ?>"><strong><? echo $oggetto_soa["id"] . " - " . $oggetto_soa["minimo"] . " - " . $oggetto_soa["massimo"] ?></option>
                      <?
                      }
                    }
                  ?>
                </select>
                <? if (!empty($_POST["oeManager"]["classifica_soa"])) { ?>
                <script>
                  $("#oeManager-classifica_soa").val('<? echo $_POST["oeManager"]["classifica_soa"] ?>');
                </script>
                <? } ?>
                <br>
                <input type="checkbox" class="oeManagerInput" name="oeManager[classifica_only_selected]" id="oeManager-classifica_only_selected" <? if (isset($_POST["oeManager"]["classifica_only_selected"])) echo "checked"; ?>> Includi classifiche superiori
              </td>
            </tr>
            <tr>
              <td class="etichetta">Categoria Progettazione</td>
              <td>
                <select title="Categoria Progettazione" class="oeManagerInput" name="oeManager[categoria_progettazione]" id="oeManager-categoria_progettazione">
                  <option value="">Nessuna</option>
                  <?
                    $sql_progettazione = "SELECT * FROM b_categorie_progettazione WHERE attivo = 'S' ORDER BY codice";
                    $ris_elenco_progettazione = $pdo->query($sql_progettazione);
                    if ($ris_elenco_progettazione->rowCount()>0) {
                      while($oggetto_progettazione = $ris_elenco_progettazione->fetch(PDO::FETCH_ASSOC)) {
                      ?>
                        <option value="<? echo $oggetto_progettazione["codice"] ?>"><strong><? echo $oggetto_progettazione["id"] . "</strong> - " .  $oggetto_progettazione["destinazione"] ." - " . $oggetto_progettazione["descrizione"] ?></option>
                      <?
                      }
                    }
                  ?>
                </select>
                <? if (!empty($_POST["oeManager"]["categoria_progettazione"])) { ?>
                  <script>
                    $("#oeManager-categoria_progettazione").val('<? echo $_POST["oeManager"]["categoria_progettazione"] ?>');
                  </script>
                <? } ?>
              </td>
            </tr>
            <?
            if ($showInvitiFilters) {
              ?>
              <tr>
                <td class="etichetta">Conteggio inviti</td>
                <td>
                  <select title="Conteggio rotazione" class="oeManagerInput" name="oeManager[conteggio_rotazione]" id="oeManager-conteggio_rotazione">
                    <option value="">Generale</option>
                    <option value="cpv_2">CPV 2 cifre (es. 03)</option>
                    <option value="cpv_3">CPV 3 cifre (es. 031)</option>
                    <option value="cpv_4">CPV 4 cifre (es. 0311)</option>
                    <option value="soa">SOA</option>
                    <option value="soa_classifica">SOA e classifica</option>
                    <option value="progettazione">Progettazione</option>
                  </select>
                  <? if (!empty($_POST["oeManager"]["conteggio_rotazione"])) { ?>
                    <script>
                      $("#oeManager-conteggio_rotazione").val('<? echo $_POST["oeManager"]["conteggio_rotazione"] ?>');
                    </script>
                  <? } ?>
                </td>
              </tr>
              <?
            }
            ?>
          </table>
        </div>
        <div class="clear"></div>
        <div>
          <table width="100%">
            <?
              $elenchi = array("albo"=>"Elenco fornitori","sda"=>"SDA","mercato"=>"Mercato Elettronico");
              ?>
              <tr>
                <td class="etichetta">Elenco di riferimento</td>
                <td>
                  <select name="oeManager[elenco]" class="oeManagerInput" title="Elenco di riferimento" id="oeManager-elenco">
                    <option value="">Nessuno</option>
                    <option value="albo-0">Qualsiasi elenco dei fornitori</option>
                    <?
                    $bind = array(":codice_ente"=>$ente,":codice_sua"=>$sua);
                    foreach ($elenchi AS $elenco => $etichetta) {
                      $sql_bando  = "SELECT b_enti.denominazione, b_bandi_{$elenco}.*
                                     FROM b_bandi_{$elenco} JOIN b_enti ON b_bandi_{$elenco}.codice_gestore = b_enti.codice
                                     WHERE (b_bandi_{$elenco}.codice_gestore = :codice_ente OR b_bandi_{$elenco}.codice_ente = :codice_sua)
                                     ORDER BY " ;
                      if ($elenco == "albo") {
                        $sql_bando .= "b_bandi_{$elenco}.manifestazione_interesse ASC, ";
                      }
                      $sql_bando .= "b_bandi_{$elenco}.oggetto, b_bandi_{$elenco}.codice DESC";
                      $risultato_bando = $pdo->bindAndExec($sql_bando,$bind);
                      if ($risultato_bando->rowCount()>0) {
                        $change = false;
                        ?>
                        <optgroup label="<?= $etichetta ?>">
                        <?
                        while($bando = $risultato_bando->fetch(PDO::FETCH_ASSOC)) {
                          if (isset($bando["manifestazione_interesse"])) {
                            if ($bando["manifestazione_interesse"] == "S") {
                              $etichetta = "Indagine di mercato";
                              if (!$change) {
                                $change = true;
                                ?>
                                </optgroup>
                                <?
                                ?>
                                <optgroup label="<?= $etichetta ?>">
                                <?
                              }
                              
                            } else {
                              $etichetta = "Elenco fornitori";
                            }
                          }
                          ?>
                          <option value="<?= $elenco ?>-<?= $bando["codice"] ?>">
                            <?= (!empty($bando["id"])) ? "ID {$bando["id"]} - " : "" ?> <?= $bando["oggetto"] ?> - Scadenza: <?= mysql2date($bando["data_scadenza"]) ?>
                            <?
                              if ($bando["codice_gestore"] != $_SESSION["ente"]["codice"]) {
                                echo " - Gestore: " . $bando["denominazione"];
                              }
                            ?>
                          </option>
                          <?
                        }
                        ?>
                        </optgroup>
                        <?
                      }
                    }
                    ?>
                  </select>
                </td>
              </tr>
              <? if (!empty($_POST["oeManager"]["elenco"])) { ?>
              <script>
                $("#oeManager-elenco").val('<? echo $_POST["oeManager"]["elenco"] ?>');
              </script>
              <? } ?>
          </table>
          <? if (check_permessi("albo/extended",$_SESSION["codice_utente"])) { ?>
            <table width="100%">
              <tr>
                <td class="etichetta">Estendi la ricerca a tutti gli Operatori economici iscritti alla piattaforma TUTTO GARE</td>
                <td width="1">
                  <select name="oeManager[extend]" class="oeManagerInput" title="Estendere ricerca" id="oeManager-extend">
                    <option value="">No</option>
                    <option value="S">Si</option>
                  </select>
                </td>
              </tr>
            </table>
            <? if (!empty($_POST["oeManager"]["extend"])) { ?>
              <script>
                $("#oeManager-extend").val('<? echo $_POST["oeManager"]["extend"] ?>');
              </script>
            <? } ?>
          <? } ?>
        </div>
        <?
      }
      return $return;
    }
  }
