<?
  if (isset($record_concorso)) {
    ?>
    <form id="verifica_firma" action="/concorsi/verifyPrivate.php" method="post">
      <input type="hidden" name="codice_concorso" value="<? echo $record_concorso["codice"] ?>">
      <input type="hidden" name="private_key" id="private" rel="S;0;0;A" title="Chiave privata">
    </form>
    <div class="box" style="text-align:center; font-size:2em; padding:100px 0;">
      <strong>Verifica chiave privata</strong><br>
      <p style="font-size:0.8em">
        Prima di procedere alla pubblicazione si richiede la verifica di ricezione della chiave privata del concorso.<br><br>
        <strong>Caricare la chiave privata</strong><br>
        <input type="file" id="chiave" title="Chiave privata" rel="S;0;0;F" /><br><br>
        <strong>Si invitano le SS.VV. a conservare la Chiave Privata fino al termine della procedura di gara. In assenza della Chiave non sar&agrave; possibile procedere con l'apertura delle Buste.</strong><br>
        <small>N.B. La chiave sar&agrave; utilizzata solo allo scopo di verificarne la corretta ricezione e non sarà in alcun modo memorizzata sul server</small>
      </p>
      <script>
        if (window.File && window.FileReader && window.FileList && window.Blob) {
          function handleFileSelect(evt) {
            $("#file").parent().addClass('working');
            var file = evt.target.files[0];
            var r = new FileReader();
            r.onload = function (e) {
              var contents = e.target.result;
              $("#private").val(contents);
              $("#chiave").parent().removeClass('working');
              $("#verifica_firma").submit();
            }
            r.readAsBinaryString(file);
          }
          document.getElementById("chiave").addEventListener('change', handleFileSelect, false);
        } else {
          corpo_alert = '<div style="text-align:center; font-weight:bold">Il tuo browser non supporta la procedura di invio.<br>';
          corpo_alert += 'Si consiglia di aggiornare il browser in uso o di utilizzare uno dei seguenti';
          corpo_alert += '<table width="100%"><tr>';
          corpo_alert += '<td style="text-align:center; width:50%;"><a target="_blank" title="Sito esterno" href="http://www.google.it/intl/it/chrome/browser/">';
          corpo_alert += '<img src="/img/chrome.png" alt="Google Chrome"><br>Google Chrome';
          corpo_alert += '</a></td>';
          corpo_alert += '<td style="text-align:center; width:50%;"><a target="_blank" title="Sito esterno" href="http://www.mozilla.org/it/firefox/new/">';
          corpo_alert += '<img src="/img/firefox.png" alt="Firefox"><br>Firefox';
          corpo_alert += '</a></td>';
          corpo_alert += '</tr>';
          corpo_alert += '</table></div>';
          jalert(corpo_alert);
          $('#buste').after(corpo_alert).remove();
        }
      </script>
    </div>
  <?
  }
?>