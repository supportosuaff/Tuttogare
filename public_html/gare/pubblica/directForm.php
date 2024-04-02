<?
  if (isset($record)) {
    include_once($root."/inc/oeManager.class.php");
    $bind = array();
    $bind[":codice_gara"] = $record["codice"];
    $sql_cpv = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice_gara ORDER BY codice";
    $risultato_cpv = $pdo->bindAndExec($sql_cpv,$bind);
    if ($risultato_cpv->rowCount()>0) {
      $cpv = array();
      while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
        $cpv[] = $rec_cpv["codice"];
      }
      $_POST["oeManager"]["cpv"] = implode(";",$cpv);
    }

    $bind = array();
    $bind[":codice_gara"] = $record["codice"];
    $sql_soa = "SELECT * FROM b_qualificazione_lavori WHERE codice_gara = :codice_gara AND tipo = 'P'";
    $risultato_soa = $pdo->bindAndExec($sql_soa,$bind);
    if ($risultato_soa->rowCount()>0) {
      $rec_soa = $risultato_soa->fetch(PDO::FETCH_ASSOC);
      $_POST["oeManager"]["categoria_soa"] = $rec_soa["codice_categoria"];
      $sql_classifiche = "SELECT * FROM b_classifiche_soa WHERE ATTIVO = 'S'";
      $ris_classifiche = $pdo->query($sql_classifiche);
      if ($ris_classifiche->rowCount()>0) {
        while($rec_classifica = $ris_classifiche->fetch(PDO::FETCH_ASSOC)) {
          if (($rec_classifica["massimo"] == 0)||($rec_classifica["massimo"]*1.20) >= $rec_soa["importo_base"]) {
            $_POST["oeManager"]["classifica_soa"] = $rec_classifica["codice"];
            $_POST["oeManager"]["classifica_only_selected"] = false;
            break;
          }
        }
      }
    }

    $bind = array();
    $bind[":codice_gara"] = $record["codice"];
    $sql_progettazione = "SELECT * FROM b_qualificazione_progettazione WHERE codice_gara = :codice_gara ";
    $risultato_progettazione = $pdo->bindAndExec($sql_progettazione,$bind);
    if ($risultato_progettazione->rowCount()>0) {
      $rec_progettazione = $risultato_progettazione->fetch(PDO::FETCH_ASSOC);
      $_POST["oeManager"]["categoria_progettazione"] = $rec_progettazione["codice_categoria"];
    }

    if (!empty($record["tipo_elenco"]) && !empty($record["codice_elenco"])) {
      $_POST["oeManager"]["elenco"] = $record["tipo_elenco"] . "-" . $record["codice_elenco"];
    }

    ?>
    <button onClick="$('#filtro').toggle('fast'); return false" style="width:100%; padding:10px; background-color:#F60" class="submit">
    <span class="fa fa-filter"></span> Filtri
    </button>
    <div class="box" id="filtro">
      <? oeManager::printFilterForm() ?>
      <button class="submit_big" onClick="oeManagerFilters = $('.oeManagerInput').serializeArray(); elenco.draw(); return false;"><span class="fa fa-filter"></span> Applica filtro</button>
      <br>
    </div>
    <input type="hidden" id="indirizzi" name="indirizzi" title="Destinatari" value="">
    <table style="text-align:center; width:100%; font-size:0.8em" id="oe">
      <thead>
        <tr>
          <th width="10">ID</th>
          <th>Ragione Sociale</th>
          <th width="10">Tipo</th>
          <th width="100">Partita IVA</th>
          <th width="150">Richiesta</th>
          <th width="150">Abilitazione</th>
          <th width="10">Inviti</th>
          <th width="10">Inviti<br><?= date("Y") ?></th>
          <th width="10"></th>
          <th width="10">Invita<br><input id="invia_all" type="image" src="/img/add.png" onClick="triggerClick = true; elenco.page.len(-1).draw(); return false" width="24" title="Invia una comunicazione a tutti"></th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
    <script>
      var oeManagerFilters = $(".oeManagerInput").serializeArray();
      var triggerClick = false
      var elenco = $("#oe").DataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "/js/dataTables.Italian.json"
        },
        "order": [[ 6, "ASC" ]],
        "ajax": {
          "url": "/gare/pubblica/directDataSource.php",
          "method": "POST",
          "data": function (d) {
            d.oeManager = oeManagerFilters;
            d.codice_gara = "<?= $record["codice"] ?>";
            return d;
          }
        },
        "drawCallback": function( settings ) {
          if (triggerClick) {
           $('.invita').trigger('click');
           triggerClick = false;
          }
          check_invitati();
        },
        "pageLength": 50,
        "lengthMenu": [
            [5, 10, 25, 50, 100, 200, -1],
            [5, 10, 25, 50, 100, 200, "Tutti"]
        ]
      });

      function invitato(codice) {
  			invitati = $("#indirizzi").val().split(";");
  			if ($.inArray(codice,invitati)==-1) {
  				invitati.push(codice);
  				$("#indirizzi").val(invitati.join(";"));
  				$("#invia_"+codice).removeClass("btn-warning").addClass("btn-primary").html('<span class="fa fa-check"></span> Selezionato');
					infoOE = $("#invia_"+codice).parent().parent().parent().children();
					row = "<tr id='invitato-"+codice+"'><td>" + infoOE[3].innerHTML + "</td><td>" + infoOE[1].innerHTML + "</td>";
					row += "<td><button class='btn-danger btn-round' onClick='invitato(\""+codice+"\"); return false;'><span class='fa fa-remove'></span></button></td></tr>";
					$("#table-invitati").append(row)
					$("#anteprima-invitati").slideDown();
  			} else {
  				index = $.inArray(codice,invitati);
  				invitati.splice(index,1);
  				$("#indirizzi").val(invitati.join(";"));
  				$("#invia_"+codice).removeClass("btn-primary").addClass("btn-warning").html('<span class="fa fa-plus"></span> Invita');
					$("#invitato-"+codice).remove();
  			}
				return false;
  		}


      function check_invitati() {
				invitati = $("#indirizzi").val().split(";");
				invitati.forEach(function(codice) {
					$("#invia_"+codice).removeClass("btn-warning").addClass("btn-primary").html('<span class="fa fa-check"></span> Selezionato');
        });
				return false;
      }

  	</script>
    <?
  }
?>
