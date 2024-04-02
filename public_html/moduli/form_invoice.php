<? if (!empty($callBackFunction)) { ?>
  <script type="text/javascript" src="/js/resumable.js"></script>
  <script type="text/javascript" src="/moduli/resumable-invoice.js"></script>

    <form action="/moduli/readInvoice.php" id="invoice_form" method="post" rel="validate">
      <input type="hidden" id="filechunk" name="filechunk">
      <input type="hidden" id="callBackFunction" name="callBackFunction" value="<?= $callBackFunction ?>">
      <div class="scegli_file"><span class="fa fa-code"></span> Importa dati da fattura elettronica</div>
          <script>
            var invoice = "";
            var uploader = (function($){
            return (new ResumableUploader($('.scegli_file')));
            })(jQuery);
          </script>
          <div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
      </form><br>
<? } else { ?>
  <h3 class="ui-state-error">CallBackFunction non definita</h3>
<? } ?>
