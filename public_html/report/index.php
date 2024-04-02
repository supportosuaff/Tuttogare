<?
  include_once("../../config.php");
  include_once($root."/layout/top.php");
  $edit = false;
  if (isset($_SESSION["codice_utente"]) && isset($ente)) {
    $edit = check_permessi("report",$_SESSION["codice_utente"]);
    if (!$edit) {
      echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
      die();
    }
  } else {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  }
?>
<h1>Attività</h1>
<?
if ($edit) {
$ente = $ente;
?>
<link rel="stylesheet" type="text/css" href="dataTables/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="dataTables/css/dataTables.tableTools.css">
<script src="/js/highcharts.js" type="text/javascript"></script>
<script src="/js/exporting.js" type="text/javascript"></script>
<table class="box" width="100%">
  <thead>
<tr>
  <th colspan="4"><strong>Filtro ricerca</strong></th>
</tr>
  </thead>
<tbody>
<tr>
<td class="etichetta">Data d'inizio:</td><td><input name="startDate" type="text" id="startDate" style="width:30%"></td>
<td class="etichetta">Data di fine:</td><td><input name="endDate" type="text" id="endDate" style="width:30%"></td>
</tr>
<? if($ente["tipo"]=="SUA"){ ?>
<tr>
  <td class="etichetta">
Ente:</td><td colspan="3"><select name="ente" id="ente">
        <option value="0">Tutte</option>
        <option value='<? echo $ente['codice']?>'> <? echo $ente['denominazione'] ?></option>
        <?
        $sql_enti = "SELECT codice, denominazione FROM b_enti WHERE sua = :codice_ente ";
        $ris_enti = $pdo->bindAndExec($sql_enti,array(":codice_ente"=>$ente["codice"]));
        if($ris_enti->rowCount()>0)
          while($rec_enti = $ris_enti->fetch(PDO::FETCH_ASSOC))
            echo "<option value='".$rec_enti['codice']."'>".$rec_enti['denominazione']."</option>";
        ?>
      </select>
    </td>
</tr>
<? } ?>
<tr>
  <td class="etichetta">Tipologia:</td><td>
    <div id="cell_tipo">
    <select name="tipologia" id="tipologia">
                  <option value="0">Tutte</option>
                  <?
                  $sql = "SELECT codice, tipologia FROM b_tipologie WHERE attivo = 'S'";
                  $ris_tipologie = $pdo->query($sql);
                  if($ris_tipologie->rowCount()>0)
                    while($tipo = $ris_tipologie->fetch(PDO::FETCH_ASSOC))
                      echo "<option value='".$tipo['codice']."'>".$tipo['tipologia']."</option>";
                  ?>
                  </select>
  </div>
                </td>
    <td class="etichetta">Stato gara:</td><td>
      <div id="cell_stato">
  <select name="stato" id="stato">
                  <option value="0">Tutte</option>
                  <option value="1">Pubblicata</option>
                  <option value="2">Annullata</option>
                  <option value="3">Deserta</option>
                  <option value="5">Aggiudicazione Provvisoria</option>
                  <option value="4">Conclusa</option>
                  </select>
                </div>
    </td>
  </tr>
  <tr>
  <td class="etichetta">
    Tipo report:</td><td><select name="tipo" id="tipo">
                      <option value="0">Gare pubblicate</option>
                      <option value="1">Importi a base d'asta</option>
                      <option value="2">Percentuali per tipologia</option>
                      <option value="3">Importi per tipologia</option>
                      <? if($ente["tipo"]=="SUA") {?><option value="4">Importi per ente</option><? } ?>
                      <? if($ente["tipo"]=="SUA") {?><option value="5">Gare per ente</option><? } ?>
                      <? if($ente["tipo"]=="SUA") {?><option value="6">Percentuali gare per ente</option><? } ?>
                      <option value="7">Incremento importi</option>
                      <option value="8">Incremento gare</option>
                      <!-- <?// if($ente["tipo"]=="SUA") {?><option value="9">Gare per enti e tipologia</option><? // } ?> -->
                  </select>
  </td>
  <td colspan="2">
    <div align="center">
      <input type="button" id="submit_graph" value="Elabora">
      <!-- <input type="button" id="submit_table" value="Vedi Gare"> -->
    </div>
  </td>
</tr>
</tbody>
</table>
    <div id="txtHint">
      <style>
      .right {text-align:right}
      </style>
      <div style='float: right;'><img src='/img/view.png' id='detailsTable' name='detailsTable' alt="Visualizza la tabella" title="Visualizza la tabella"/></div><br/><br/>
      <div id="container" style="width=100%;"></div><br/>
      <div id="tableResult" style="width=90%">
      </div>
    </div>
    <script>
    $(function () {
      var report = new Array();
      $("#startDate").datepicker({
          defaultDate: '01/01/'+(new Date().getFullYear()),
          changeMonth: true,
          changeYear: true
      });
      $("#endDate").datepicker({
        defaultDate: new Date(),
        changeMonth: true,
        changeYear: true
      });
      $('#startDate').datepicker("setDate", new Date(new Date().getFullYear(),00,01) );
      $('#endDate').datepicker("setDate", new Date(new Date().getFullYear(),11,31) );

      $('#detailsTable').on('click',function(){
        $('#tableResult').toggle('showHide');
      });

      // $('#detailsTable').hide();
      // $('#tableResult').hide();

      $('#tipo').on("change",function(){
        var code = $(this).val();
        var optionSua=["4","5","6"];
        var optionTipologia = ["2","3"];
        if(optionSua.indexOf(code)!="-1") {
          $('#ente').val("0");
          $('#ente').prop("disabled", true).trigger("chosen:updated");;
          $('#ente option[value="0"]').attr("selected","selected");
        }else{
          $('#ente').prop("disabled", false).trigger("chosen:updated");;
        }
        if(optionTipologia.indexOf(code)!="-1"){
          $('#tipologia').val("0");
          $('#tipologia').prop("disabled", true).trigger("chosen:updated");;
          $('#tipologia option[value="0"]').attr("selected","selected");
        }else{
          $('#tipologia').prop("disabled", false).trigger("chosen:updated");;
        }
      });

      $("#submit_graph").on('click',function(){
        $("#container").height("400px");
        var dataInizio = $("#startDate").datepicker("getDate");
        var dataFine = $("#endDate").datepicker("getDate");
        report["ente"] = $("#ente option:selected").val();
        setTipologia($("#tipo").val());
        setStato($("#stato").val());
        report["tipologia"] = $("#tipologia").val();
        report["dataInizio"] = $.datepicker.formatDate('yy-mm-dd', dataInizio);
        report["dataFine"] = $.datepicker.formatDate('yy-mm-dd', dataFine);
        report["titolo"] = $("#tipo option:selected").text();
        reportGare(report);
        if($("#tipo").val()==0) viewTable(report);
        // $('#detailsTable').show();
        // $('#tableResult').show();
      });

      function reportGare(report){
        if(report["graph"]=='pie'){
        $.ajax({
            url: report["url"],
            type: 'GET',
            data: {'startDate':report["dataInizio"], "endDate":report["dataFine"],"tipologia":report["tipologia"],"stato":report["stato"],"ente":report["ente"]},
            success: function(data) {
              data = JSON.parse(data);
              $("#tableResult").html("<br/><div style='float: right;'><a href='/report/exportArray.php'><img src='/img/opendata.png' id='opendata' name='opendata'/></a></div><table id='tableGare'><thead><tr><th>"+report["x"]+"</th><th>"+report["y"]+"</th><th>Importi di gara</th></tr></thead><tbody></tbody></table>");
              var table = $("#tableGare").dataTable({
                'dom': 'T<"clear">lfrtip',
                "bDestroy": true,
                "aaData":data,
                "buttons": [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                "aoColumnDefs": [
                  { "sType": "string", "aTargets": [0] },
                  { "sType": "numeric", "aTargets": [1], "sClass":"right" },
                  { "sType": "numeric", "aTargets": [2], "sClass":"right"  }
                ]
              });
              $('#container').highcharts({
                  chart: {
                        type: 'pie'
                    },
                    title: {
                        text: report["titolo"]
                    },
                    tooltip: {
                        pointFormat: report["format"]
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            dataLabels: {
                                 enabled: false
                            },
                            showInLegend: true
                      }
                    },
                    series: [{
                        data: data,
                        name: report["titolo"]
                    }]
                  });
            },
            error: function(e) {
              console.log(e.message);
            }
          });
      } else {
        var options = ["0","1"];
        $.ajax({
          url: report["url"],
          type: 'GET',
          data: {'startDate':report["dataInizio"], "endDate":report["dataFine"],"tipologia":report["tipologia"], "stato":report["stato"],"ente":report["ente"]},
          success: function(data) {
            data = JSON.parse(data);
            var categories = [];
            $.each(data,function(){
              //console.log(this[0]);
              categories.push(this[0]);
            })
            // console.log(data);
            var ente = $("#ente option:selected").text();
            $('#container').highcharts({
                chart: {
                      type: report["graph"]
                  },
                  title: {
                      text: report["titolo"]
                  },
                  xAxis: {
                      title:{
                        text: report["x"]
                      },
                      categories: categories
                  },
                  yAxis: {
                      title: {
                          text: report["y"]
                      }
                  },
                  tooltip: {
                      pointFormat: report["format"]
                  },
                  plotOptions: {
                      pie: {
                          allowPointSelect: true,
                          cursor: 'pointer',
                          dataLabels: {
                              enabled: true,
                              format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                              style: {
                                  color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                              }
                          }
                      }
                  },
                  series: [{
                      data: data,
                      name: report["titolo"]
                  }],
                  legend:[{
                    enabled: true
                  }]
                });
                if(options.indexOf($("#tipo").val())=="-1"){
                  $("#tableResult").html("<br/><div style='float: right;'><a href='/report/exportArray.php'><img src='/img/opendata.png' id='opendata' name='opendata'/></a></div><table id='tableGare'><thead><tr><th>"+report["x"]+"</th><th>"+report["y"]+"</th><th>"+report["z"]+"</th></tr></thead><tbody></tbody></table>");
                  var table = $("#tableGare").dataTable({
                    "dom": 'T<"clear">lfrtip',
                    "buttons": [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],            
                    "bDestroy": true,
                    "aaData":data,
                    "aoColumnDefs": [
                      { "sType": "string", "aTargets": [0] },
                      { "sType": "numeric", "aTargets": [1], "sClass":"right" },
                      { "sType": "numeric", "aTargets": [2], "sClass":"right" }
                    ]
                  });
                }
          },
          error: function(e) {
            console.log(e.message);
          }
        });
      }
    }

    function viewTable(report){
      $.ajax({
        url: "viewTable.php",
        type: 'GET',
        data: {'startDate':report["dataInizio"], "endDate":report["dataFine"],"tipologia":report["tipologia"], "stato":report["stato"],"ente":report["ente"]},
        success: function(data) {
            $('#tableResult').html(data);
            $('#tabellaTotale').dataTable();
            //f_ready();
        },
        error: function(e) {
          console.log(e.message);
        }
      });
    }

    function setStato(stato){
      switch(stato){
        case '0':
          report["stato"] = "0";
          break;
        case '1':
          report["stato"] = "1";
          break;
        case '2':
          report["stato"] = "2";
          break;
        case '3':
          report["stato"] = "3";
          break;
        case '4':
          report["stato"] = "4";
          break;
        case '5':
          report["stato"] = "5";
        break;
      }
    }
    function setTipologia(tipo){
      switch(tipo){
        case '0':
          report["url"] = 'getGare.php';
          report["graph"] = 'column';
          report["x"] = 'Mese di riferimento';
          report["y"] = 'Numero di gare';
          report["z"] = 'Numero di Gare';
          report["format"] = "{series.name}: <b>{point.y}</b>";
          break;
        case '1':
          report["url"] = 'getImporti.php';
          report["graph"] = 'bar';
          report["x"] = 'Mese di riferimento';
          report["y"] = 'Importi di gara';
          report["z"] = 'Numero di Gare';
          report["format"] = "{series.name}: € <b>{point.y} </b>";
          break;
        case '2':
          report["url"] = 'percTipologia.php';
          report["graph"] = 'pie';
          report["x"] = "Tipologia";
          report["y"] = "Percentuale"
          report["z"] = 'Numero di Gare';
          report["format"] = "{series.name}: <b>{point.y}%</b>";
          break;
        case '3':
          report["url"] = 'getImportiTipo.php';
          report["graph"] = 'bar';
          report["x"] = 'Tipologia';
          report["y"] = 'Importi di gara';
          report["z"] = 'Numero di Gare';
          report["format"] = "{series.name}: € <b>{point.y}</b>";
          break;
        case '4':
          report["url"] = 'getImportiEnte.php';
          report["graph"] = 'bar';
          report["x"] = 'Ente beneficiario';
          report["y"] = 'Importi di gara';
          report["z"] = 'Numero di Gare';
          report["format"] = "{series.name}: € <b>{point.y}</b>";
          break;
        case '5':
          report["url"] = 'getGareEnte.php';
          report["graph"] = 'pie';
          report["x"] = "Ente beneficiario";
          report["y"] = "Numero di gare";
          report["z"] = 'Numero di Gare';
          report["format"] = "{series.name}: <b>{point.y}<b>";
          break;
        case '6':
          report["url"] = 'percGareEnte.php';
          report["graph"] = 'pie';
          report["x"] = "Ente beneficiario";
          report["y"] = "Percentuale gare";
          report["z"] = 'Numero di Gare';
          report["format"] = "{series.name}: <b>{point.y}%</b>";
          break;
        case '7':
          report["url"] = 'getImportiCrescita.php';
          report["graph"] = '';
          report["x"] = 'Mese di riferimento';
          report["y"] = 'Importi di gara';
          report["z"] = 'Numero di Gare';
          report["format"] = "{series.name}: € <b>{point.y}</b>";
          break;
        case '8':
          report["url"] = 'getGareCrescita.php';
          report["graph"] = '';
          report["x"] = 'Mese di riferimento';
          report["y"] = 'Gare trattate';
          report["z"] = 'Importi di gara';
          report["format"] = "{series.name}: <b>{point.y}</b>";
          break;
        // case '9':
        //   report["url"] = 'getGareTipoEnte.php';
        //   report["graph"] = 'column';
        //   report["x"] = 'Mese di riferimento';
        //   report["y"] = 'Gare trattate';
        //   report["format"] = "{series.name}: <b>{point.y}</b>";
        //   break;
      }
    }
  });

    </script>
  <? } ?>
<?
include_once($root."/layout/bottom.php");
?>
