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
   window.ResumableUploader = function(browseTarget, dropTarget, progressContainer, online, id) {
	  if (typeof id == "undefined") id = "";
     var $this = this;
     // Bootstrap parameters and clear HTML
     this.browseTarget = browseTarget;
     this.dropTarget = dropTarget;
     this.progressContainer = progressContainer;
     this.progressContainer.hide();

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
						$("#div_allegati"+id).html(corpo_alert);
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
   		   	$("#progress_bar"+id).find(".progress_bar").removeClass("progress_bar").addClass("complete_bar");
			$("#submit_allegati"+id).show();
		});
	   this.resumable.on('progress',function() {
		   progress = Math.floor(this.progress() * 100);
		   $("#progress_bar"+id).find(".progress_bar").css("width",progress+"%");
		});
       this.resumable.on('pause', function(file){});
       this.resumable.on('fileRetry', function(file){});
       this.resumable.on('fileAdded', function(file){
		   var allReg = /\.(jpg|jpeg|png|gif|doc|docx|xlsx|xls|pdf|zip|rar|ods|odt|csv|rtf|p7m|xml|mp4|MP4|P7M|JPG|JPEG|PNG|GIF|DOC|DOCX|XLSX|XLS|PDF|ZIP|RAR|ODS|ODT|XML|CSV|RTF)$/;	// espressione regolare per il controllo dei file allegati
		   if (allReg.test(file.fileName)) {
	         	$("#progress_bar"+id).find(".complete_bar").addClass("progress_bar").removeClass("complete_bar");
				$("#submit_allegati"+id).hide();
				$.ajax({
					type: "POST",
					 url: "/allegati/tr_form.php",
					 dataType: "html",
					 data: "uniqueIdentifier="+file.uniqueIdentifier+"&filename="+file.fileName+"&online="+online,
					 success: function(script) {
						$("#list"+id).append(script);
						f_ready();
					}
				});
	           $this.progressContainer.show();
    	       $this.resumable.upload();
		   } else {
			  jalert("<div style=\"text-align:center\">Impossibile caricaricare il file<br><br><strong>" + file.fileName + "</strong></br><br>La tipologia pu&ograve; solo essere: P7M | JPG | JPEG | PNG | GIF | DOC | DOCX | XLSX | XLS | PDF | ZIP | RAR | ODS | ODT | RTF | XML | CSV</div>");
		    $this.resumable.file.cancel();
		   }
         });
     }


     this.newResumable();
     return this;
   }
 })(window, window.document, jQuery);
