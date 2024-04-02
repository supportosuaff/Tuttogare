<?
  include_once '../../../config.php';
  include_once "{$root}/layout/top.php";
  if(empty($_SESSION["codice_utente"]) || !check_permessi("scadenzario/gestione",$_SESSION["codice_utente"])) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  } else {
    $codice_ente = 0;
    if (isset($_SESSION["ente"])) {
      $codice_ente = !empty($_SESSION["record_utente"]["codice_ente"]) ? $_SESSION["record_utente"]["codice_ente"] : $_SESSION["ente"]["codice"];
    }
    ?>
    <style media="screen">
      table > * > tr > th {
        text-align: left;
        font-weight: bold;
        color: #000 !important;
      }
    </style>
    <link rel="stylesheet" href="/contratti/css.css" media="screen" title="Button Style">
    <h1>GESTIONE SCADENZE</h1>
    <a href="edit.php" title="Inserisci nuovo promemoria">
			<div class="add_new" style="margin-bottom:20px !important;">
				<i class="fa fa-plus-circle fa-3x" style="color:#28b311"></i><br />
        Crea nuovo promemoria
      </div>
		</a>
    <div class="box" style="margin-bottom:20px !important;">
      <table id="elenco_promemoria" style="width:100%">
        <thead>
          <tr>
            <th colspan="7" style="vertical-align:middle"><input type="checkbox" onchange="update_table()" id="old">Visualizza promemoria passati</th>
          </tr>
          <tr>
            <th width="10">#</th>
            <th width="160">Scadenza</th>
            <th>Titolo</th>
            <th width="10"></th>
            <th width="10"></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <script type="text/javascript">
        var old = 0;
        function update_table() {
          old = 0;
          if($('#old').is(':checked')) old = 1;
          table.ajax.reload();
        }
        var table = $('#elenco_promemoria').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": {
              "url": "table.php",
              "data": function(d) {
                d.old = old;
              }
            }
        });
      </script>
    </div>
    <a href="edit.php" title="Inserisci nuovo promemoria">
			<div class="add_new" style="margin-bottom:20px !important;">
				<i class="fa fa-plus-circle fa-3x" style="color:#28b311"></i><br />
        Crea nuovo promemoria
      </div>
		</a>
    <?
  }
  include_once "{$root}/layout/bottom.php";
?>
