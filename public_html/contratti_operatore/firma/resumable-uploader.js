/*
 *
 * This is the boring glue for the Resumable.js-based uploader.
 *
 * For the interesting stuff, see
 * http://www.23developer.com/opensource
 * http://github.com/23/resumable.js
 *
 * Steffen Tiedemann Christensen, steffen@23company.com
 *
 */

(function(window, document, $, undefined)
 {
    window.ResumableUploader = function(browseTarget) {
     var $this = this;
     // Bootstrap parameters and clear HTML
     this.browseTarget = browseTarget;
     this.dropTarget = "";
     // Defaults
     this.fallbackUrl = '';
     // Properties
     this.resumable = null;
     this.progress = 0;
     this.progressPercent = 0;
     this.files = {};
     this.fileCount = 0;

     // Initialization routines
     this.newResumable = function(){
       // Build the uploader application
       this.resumable = new Resumable({
           chunkSize:3*1024*1024,
           maxFileSize:this.maxFileSize*1024*1024*1024,
           maxFiles:1,
           simultaneousUploads:1,
           target:'/allegati/chunk.php',
           prioritizeFirstAndLastChunk:true,
           throttleProgressCallbacks:1,
         });
       if(!this.resumable.support) {
        corpo_alert = '<div style="text-align:center; font-weight:bold">Il tuo browser non supporta la procedura di upload dei file.<br>';
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
				$("#modulo_partecipazione").html(corpo_alert);
       }
       this.resumable.assignBrowse(this.browseTarget);
       this.resumable.assignDrop(this.dropTarget);

       this.resumable.on('fileError', function(file, message){
          $("#"+file.uniqueIdentifier).find(".progress_bar").removeClass("progress_bar").addClass("error_bar");
       });
      this.resumable.on('fileSuccess', function(file, message){
        $("#"+file.uniqueIdentifier).find(".progress_bar").removeClass("progress_bar").addClass("complete_bar");
      });
      this.resumable.on('fileProgress', function(file){
        progress = Math.floor(file.progress() * 100);
        $("#"+file.uniqueIdentifier).find(".progress_bar").css("width",progress+"%");
      });
      this.resumable.on('complete', function(){
   		  $("#progress_bar").find(".progress_bar").removeClass("progress_bar").addClass("complete_bar");
			  $("#upload_contratto").submit();
      });
      this.resumable.on('progress',function() {
        $("#progress_bar").show();
        progress = Math.floor(this.progress() * 100);
        $("#progress_bar").find(".progress_bar").css("width",progress+"%");
      });
      this.resumable.on('pause', function(file){});
      this.resumable.on('fileRetry', function(file){});
      this.resumable.on('fileAdded', function(file){
        var allReg = /\.(p7m|P7M|pdf|PDF)$/;	// espressione regolare per il controllo dei file allegati
        if (allReg.test(file.fileName)) {
          $this.browseTarget.slideUp();
          html = "<div id='#" + file.uniqueIdentifier + "' class='modulo_partecipazione'>";
          html+= "<h2 style='text-align:center'>" + file.fileName + "<br><img src=\"/img/indicator.gif\" alt=\"Loading\" id=\"checksum\"></h2><br>";
          html+= "<div id='label_md5' style='text-align:center;display;none; font-weight:normal'><h3  style='text-align:center;'></h3><br>";
          html+= "L'hash MD5 viene utilizzato dalla piattaforma per controllare l'integrità del file inviato</div>";
          html+= "</div>";
          $("#filechunk").val(file.fileName);
          var blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice,
          spark_chunkSize = 2097152,
          spark_chunks = Math.ceil(file.file.size / spark_chunkSize),
          spark_currentChunk = 0,
          spark = new SparkMD5.ArrayBuffer(),
          frOnload = function(e) {
            spark.append(e.target.result);                 // append array buffer
            spark_currentChunk++;
            if (spark_currentChunk < spark_chunks) {
              loadNext();
            }
            else {
             $("#md5_file").val(spark.end()); // compute hash
             $("#label_md5").slideDown();
             $("h3", "#label_md5").html("Hash MD5: <strong>" +  $("#md5_file").val() + "</strong>"); // compute hash
            //  $("#invio").slideDown();
             $("#checksum").remove();
            }
          },
          frOnerror = function () {
            console.warn("oops, something went wrong.");
          };

          function loadNext() {
            var fileReader = new FileReader();
            fileReader.onload = frOnload;
            fileReader.onerror = frOnerror;

            var start = spark_currentChunk * spark_chunkSize,
            end = ((start + spark_chunkSize) >= file.file.size) ? file.file.size : start + spark_chunkSize;
            fileReader.readAsArrayBuffer(blobSlice.call(file.file, start, end));
          };

          loadNext();
          $this.browseTarget.after(html);
        } else {
          jalert("<div style=\"text-align:center\">Impossibile caricaricare il file<br><br><strong>" + file.fileName + "</strong></br><br>La tipologia pu&ograve; solo essere: P7M o PDF</div>");
		      $this.resumable.file.cancel();
        }
      });
    }
    this.newResumable();
    return this;
  }
})(window, window.document, jQuery);
