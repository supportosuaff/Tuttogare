// JavaScript Document

function sendArt80Request(codice_operatore) {
    if (confirm('Vuoi inoltrare una nuova richiesta di verifica Art.80 per l\'operatore?')) {
        window.location.href = "/verifica-art-80/edit.php?codice=0&codice_operatore="+codice_operatore;
    }
}

function setMetadati(codice,tabella) {
  if($('#div_metadati').length < 1) {
    $('body').append('<div id="div_metadati" style="display:none;"></div>');
  }
  $.ajax({
    url: '/allegati/metadati.php',
    type: 'POST',
    dataType: 'html',
    data: {codice: codice, tabella: tabella},
    beforeSend: function(e) {
      $('#wait').fadeIn();
    }
  })
  .done(function(html_response) {
    $('#div_metadati').html(html_response);
    $('#div_metadati').dialog({
        title: 'Info Metadati',
        modal: true,
        width: '70%',
        position: 'top',
        create: function() {
            $(this).css("maxHeight", "100%");
        }
    });
    $('.metadati-xml > * > tr:even').removeClass('odd even').addClass("even");
    $('.metadati-xml > * > tr:odd').removeClass('odd even').addClass("odd");
    f_ready();
  })
  .fail(function() {
    jalert('Si è verificato un errore. Si prega di riprovare!<br>Se il problema persiste contattare l&#39;helpdesk tecnico.');
  })
  .always(function() {
    $('#wait').fadeOut();
  });
}

function sanitize_string(string) {
  string = string.replace(/[^a-zA-Z0-9\.\/_|+ -]/g,'');
  string = string.toLowerCase();
  string =  string.replace(/[\/_|+ -]+/g,'-');
  string =  string.replace('..','.');
  return string;
}

function switchAllegato(codice) {
  if (typeof codice != "undefined") {
    if (codice > 0) {
      $.ajax({
        url: '/allegati/switch.php',
        type: 'POST',
        dataType: 'script',
        data: {codice: codice},
        beforeSend: function(e) {
          $('#wait').fadeIn();
        }
      })
      .done(function(script) {
        script;
      })
      .always(function() {
        $('#wait').fadeOut();
      });
    }
  }
}

window.jalert = function(message, fallback) {
    $('<div></div>').attr({
        title: 'Attenzione'
    }).html(message).dialog({
        dialogClass: 'ui-state-error',
        buttons: {
            OK: function() {
                $(this).dialog('close');
            }
        },
        close: function() {
            $(this).remove();
        },
        draggable: true,
        modal: true,
        resizable: false,
        width: 'auto',
        position: ['center', 100],
    });
};
window.jconfirm = function(message, callback) {
    $("<div></div>").attr({
        title: 'Attenzione'
    }).html(message).dialog({
        position: ['center', 100],
        buttons: {
            "OK": function() {
                $(this).dialog('close');
                callback();
                return true;
            },
            "Annulla": function() {
                $(this).dialog('close');
                return false;
            }
        },
        close: function() {
            $(this).remove();
        },
        draggable: false,
        modal: true,
        resizable: false,
        width: 'auto'
    });
};
var toolbar_full = [
    ['Source'],
    ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
    ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'],
    ['Link', 'Unlink', 'Anchor'],
    ['Format', 'FontSize'],
    ['TextColor', 'BGColor'],
    ['Table'],
    ['Image'],
    ['Find', 'Replace', '-', 'SelectAll', '-', 'SpellChecker', 'Scayt'],
    ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', ],
    ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
];
var config_full = {
    toolbar_fulltoolbar: toolbar_full,
    toolbar: 'fulltoolbar',
    enterMode: CKEDITOR.ENTER_BR,
    allowedContent: ['table [width,class,style]', 'tr', 'td [colspan,rowspan,width]', 'th [colspan,rowspan,width]', 'strong', 'img', 'ul', 'ol', 'li', 'u', 'b', 'i', 'p', 'h1', 'h2', 'h3', 'h4', 'a']
};
var toolbar_models = [
    ['Source'],
    ['Preview'],
    ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
    ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'],
    ['Link', 'Unlink', 'Anchor'],
    ['Format', 'FontSize'],
    ['TextColor', 'BGColor'],
    ['Find', 'Replace', '-', 'SelectAll', '-', 'SpellChecker', 'Scayt'],
    ['Table'],
    ['Image', 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'],
    ['Maximize']
];
var config_models = {
    toolbar_modelstoolbar: toolbar_models,
    toolbar: 'modelstoolbar',
    //htmlEncodeOutput: false,
    //entities: false,
    enterMode: CKEDITOR.ENTER_BR,
    //	allowedContent : ['img','table [width,class]','tr','td [colspan,width]','th [colspan,width]','strong','ul','ol','li','u','b','i','p','h1','h2','h3','h4','div [style]','a'],
    height: '600px',
};
var toolbar_simple = [
    ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
];
var config_simple = {
    toolbar_simpletoolbar: toolbar_simple,
    toolbar: 'simpletoolbar',
    enterMode: CKEDITOR.ENTER_BR,
    allowedContent: false
};
String.prototype.padRight = function(l, c) {
    return this + Array(l - this.length + 1).join(c || " ");
};

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? ',' + x[1] : ',00';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    x2 = x2.padRight(3, "0");
    return x1 + x2;
}

function versione(codice, titolo) {
    $("#wait").show();
    $("<div><iframe onload='$(\"#wait\").hide();' style='width:100%;height:100%' frameborder='0' src='/moduli/anteprima.php?codice=" + codice + "&anteprima=1'></iframe></div>").dialog({
        modal: true,
        width: 800,
        height: 600,
        title: titolo,
        buttons: {
            "Ripristina Versione": function() {
                $("<div></div>").load('/moduli/anteprima.php?codice=' + codice, function() {
                    $("#corpo").val($(this).html());
                    $(".ui-dialog-content").dialog("close");
                    $(".ui-dialog-content:not([id])").remove();
                });
            }
        }
    });
}

function open_allegati(id) {
  if (typeof id == "undefined") id = "";
  $('#div_allegati' + id).dialog({
      title: 'Upload allegato',
      modal: true,
      width: '50%',
      position: 'top',
      create: function() {
          $(this).css("maxHeight", "100%");
      }
  });
  f_ready();
}
var destinatari = new Array();
var id_inserimento = 0;

function aggiungi_destinario(codice, email) {
    if (destinatari.length === 0) {
        $(".invia_comunicazione").attr("src", "/img/add.png");
    }
    if ($.inArray(codice, destinatari) == -1) {
        destinatari.push(codice);
        $("#destinatari").append("<li rel='" + codice + "'>" + email + "</li>");
        $("#indirizzi").val(destinatari.join(";"));
        f_ready();
        $("#invia_" + codice).attr("src", "/img/added.png");
    }
}

function annulla_comunicazione() {
    destinatari = new Array();
    $("#destinatari").html("");
    $("#indirizzi").val("");
    $("#cod_allegati").val("");
    $("#tab_allegati").html("");
    $("#list_allegati tr").slideDown();
    $("#oggetto_comunicazione").val("");
    $("#corpo_comunicazione").val("");
    $("#comunicazione").slideUp("fast");
    $(".invia_comunicazione").attr("src", "/img/newsletter.png");
}

function aggiungi(url, target, param) {
    id_inserimento++;
    param = typeof param !== 'undefined' ? param : {};
    data = {id:"i_"+ id_inserimento, target: target, param: param};
    $.ajax({
        type: "POST",
        url: url,
        dataType: "html",
        data: data,
        async: false,
        success: function(script) {
          $(target).append(script);
          // $('html,body').animate({scrollTop: ($(target).offset().top + $(target).height()) - 200 }, 500);
        }
    });
    f_ready();
    etichette_testo();
}

function visualizza_cpv_disponibili() {
    $('#list_all').dialog({
        title: 'Categorie disponibili',
        modal: true,
        width: '90%'
    });
}

function check_categorie() {
    str_categorie = $("#cpv").val();
    categorie = str_categorie.trim().split(";");
    for (i = 0; i < categorie.length; i++) {
        $("#all_" + categorie[i]).addClass("selezionato").hide();
    }
}

function carica_categorie(codice, lista, url) {
    data = "codice=" + codice + "&lista=" + lista;
    if ($("#" + lista + "_" + codice).children(".children").html() === "") {
        $.ajax({
            type: "GET",
            url: url,
            dataType: "html",
            data: data,
            async: false,
            success: function(script) {
                if (script != "") {
                    $("#" + lista + "_" + codice).children(".children").html(script);
                    $("#" + lista + "_" + codice).children(".children").slideDown();
                    $("#espandi_" + codice).attr("src", "/img/contrai.png");
                    f_ready();
                }
            }
        });
    } else {
        $("#" + lista + "_" + codice).children(".children").slideUp(function() {
            $(this).html("");
        });
        $("#espandi_" + codice).attr("src", "/img/espandi.png");
    }
    if ($("#cpv").val() !== '') check_categorie();
}

function categoria(codice, lista, url) {
    var str_categorie = $("#cpv").val();
    str_categorie = str_categorie.trim();
    var categorie = new Array();
    categorie = str_categorie.split(";");
    if (lista == "all") {
        categorie.push(codice);
        data = "codice=" + codice + "&lista=in";
        $.ajax({
            type: "POST",
            url: url,
            dataType: "html",
            data: data,
            async: false,
            success: function(script) {
                if (script !== "") {
                    $("#list_in").prepend(script);
                    $("#all_" + codice).addClass("selezionato").hide();
                    var categorie_attuali = new Array();
                    categorie_attuali = str_categorie.split(";");
                    for (i = 0; i < categorie_attuali.length; i++) {
                        if ((codice.length < categorie_attuali[i].length) && (categorie_attuali[i].indexOf(codice) == 0)) {
                            $("#in" + "_" + categorie_attuali[i]).slideUp(function() {
                                $(this).remove();
                            });
                            $("#all_" + categorie_attuali[i]).addClass("selezionato").slideDown();
                            var pos = $.inArray(categorie_attuali[i], categorie);
                            categorie.splice(pos, 1);
                        }
                    }
                    f_ready();
                }
            }
        });
    } else {
        var pos = $.inArray(codice, categorie);
        categorie.splice(pos, 1);
        $("#in_" + codice).remove();
        $("#all_" + codice).removeClass("selezionato").show();
    }
    str_categorie = categorie.join(";");
    $("#cpv").val(str_categorie).trigger("change");
}

function check_altro(el) {
  if (typeof el.data("input") !== "undefined") {
    input = el.data("input");
    if ($(input).length > 0) {
      input = $(input);
      if (el.val() == -1) {
        input.attr("rel","S;1;0;A");
        input.slideDown("fast");
      } else {
        input.attr("rel","N;1;0;A");
        input.slideUp("fast");
      }
    }
  }
}


function update_comuni(id_sorgente, id_destinazione, valore) {
    provincia = $("#" + id_sorgente).val();
    valore = typeof valore !== 'undefined' ? valore : "";
    data = "provincia=" + provincia;
    $.ajax({
        type: "POST",
        url: '/inc/cf/comuni.php',
        dataType: "html",
        data: data,
        success: function(script) {
            $("#" + id_destinazione).html(script);
            $("#" + id_destinazione).val(valore);
        }
    });
}

function calcola_cf(nome, cognome, cnascita, dnascita, sesso, pnascita, id_destinazione) {
    errore = false;
    if (typeof nome === 'undefined' || nome === "") errore = true;
    if (typeof cognome === 'undefined' || cognome === "") errore = true;
    if (typeof cnascita === 'undefined' || cnascita === "") errore = true;
    if (typeof dnascita === 'undefined' || dnascita === "") errore = true;
    if (typeof sesso === 'undefined' || sesso === "") errore = true;
    if (typeof pnascita === 'undefined' || pnascita === "") errore = true;
    if (typeof id_destinazione === 'undefined' || id_destinazione === "") errore = true;
    if (!errore) {
        data = "nome=" + nome + "&cognome=" + cognome + "&luogo=" + cnascita + "&dnascita=" + dnascita + "&sesso=" + sesso + "&prov=" + pnascita;
        $("#" + id_destinazione).after("<img src=\"/img/loading.gif\" height=\"15\" alt=\"attendere\" id=\"" + id_destinazione + "_load_cf\">");
        $.ajax({
            type: "GET",
            url: "/inc/cf/calcolo.php",
            data: data,
            dataType: "html",
            success: function(script) {
                $("#" + id_destinazione).val(script);
                $("#" + id_destinazione + "_load_cf").remove();
            },
            error: function() {
                $("#" + id_destinazione + "_load_cf").remove();
                jalert("Errore, si prega di riprovare...");
            }
        });
    } else {
        jalert("Inserisci i tuoi dati anagrafici");
    }
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k).toFixed(prec);
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
var map;

function initialize() {
    var latlng = new google.maps.LatLng(40.47832491219327, 17.930030822753906);
    var myOptions = {
        zoom: 8,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.HYBRID
    };
    document.getElementById("big").style.height = '460px';
    map = new google.maps.Map(document.getElementById("big"), myOptions);
}

function initialize_dettaglio() {
    var latlng = new google.maps.LatLng(40.47832491219327, 17.930030822753906);
    var myOptions = {
        zoom: 11,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.HYBRID
    };
    document.getElementById("map").style.height = '460px';
    map = new google.maps.Map(document.getElementById("map"), myOptions);
}

function exportDOC() {
    if ($("#corpo").length > 0) {
        for (var instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        data = $("#editor :input").serialize();
        window.open('/moduli/exportDOC.php?' + data, "_blank");
    }
}

function exportPDF() {
    if ($("#corpo").length > 0) {
        for (var instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        $("#exp_corpo").val($("#corpo").val());
        $("#exp_file_title").val($("#file_title").val());
        $("#exp_orientamento").val($("#orientamento").val());
        $("#exp_formato").val($("#formato").val());
        $("#exportPDF").submit();
        // window.open('/moduli/exportPDF.php?'+data,"_blank");
    }
}

function aggiorna_allegati() {
    if ($(".allegato").length > 0) {
        $.ajax({
            type: "POST",
            url: '/allegati/aggiorna_allegati.php',
            dataType: "html",
            success: function(script) {
                $(".allegato").html(script);
                $(".allegato").each(function() {
                    id = "";
                    id = "#" + $(this).attr("name").replace("[", "_");
                    id = id.replace("]", "");
                    valore = $(id).val();
                    $(this).val(valore);
                });
            }
        });
    }
}

function recupera_password() {
    $("#recupera_password").dialog({
        position: ["center", 50],
        modal: true,
        show: {
            effect: 'drop',
            direction: "up"
        },
        buttons: [{
            text: "Recupera",
            click: function() {
                $("#recupero").submit();
            }
        }]
    });
    $("#recupera_password").show();
}

function etichette_testo() {
    // Inserisce il titolo dei tag input nel placeholder
    $("* :input").each(function() {
        $(this).attr("placeholder", $(this).attr("title"));
    });
}

function suggest_password(id) {
    $(id).load("/moduli/random_password.php");
}

function change_password() {
    var checked = $("#edit_password").prop('checked');
    if (checked) {
        if ($("#check_password").length>0) {
          rel = "S;8;16;P;check_password;=";
          $("#check_password").attr("rel","S;8;16;P");
          $("#check_password").removeAttr("disabled");
        } else {
          rel = "S;8;16;P";
        }
        $("#password").removeAttr("disabled");
        $("#password").attr("rel", rel);
    } else {
        if ($("#check_password").length>0) {
          $("#check_password").attr("rel","N;8;16;P");
          $("#check_password").attr("disabled", true);
        }
        rel = "N;8;16;A";
        $("#password").attr("disabled", true);
        $("#password").removeClass("ui-state-error");
        $("#password").attr("rel", rel);
    }
}

function logout() {
    $.ajax({
        url: '/logout.php',
        success: function(data) {
            window.location.href='/index.php';
        }
    });
}

function elimina(codice, modulo) {
    msg = "Stai per eliminare l'elemento. Confermi?";

    function conferma_elimina() {
        $.ajax({
            type: "POST",
            url: "/" + modulo + "/delete.php",
            data: "codice=" + codice + "&modulo=" + modulo,
            dataType: "script",
            success: function(script) {
                script;
            },
            error: function() {
                //	alert("Errore, si prega di riprovare...");
            }
        });
    }
    jconfirm(msg, conferma_elimina);
    return false;
}

function disabilita(codice, modulo) {
    msg = "Confermi l'operazione?";

    function conferma_disabilita() {
        $.ajax({
            type: "POST",
            url: "/" + modulo + "/disable.php",
            data: "codice=" + codice + "&modulo=" + modulo,
            dataType: "script",
            success: function(script) {
                script;
            },
            error: function() {
                //	alert("Errore, si prega di riprovare...");
            }
        });
    }
    jconfirm(msg, conferma_disabilita);
}

function send_newsletter(codice, modulo) {
    msg = "Confermi l'operazione?";
    invia = true;
    if (confirm(msg)) {
        if ($("#testo_newsletter").html() == "Inviata") {
            msg = "L'articolo è già stato inviato.\rVuoi procedere comunque?";
            if (!confirm(msg)) {
                invia = false;
            }
        }
        if (invia) {
            $.ajax({
                type: "POST",
                url: "/" + modulo + "/newsletter.php",
                data: "codice=" + codice + "&modulo=" + modulo,
                dataType: "script",
                success: function(script) {
                    script;
                },
                error: function() {
                    //	alert("Errore, si prega di riprovare...");
                }
            });
        }
    }
}

function trascina_file() {
    $("#drop").bind("dragenter", function(event) {
        return false;
    }).bind("dragover", function(event) {
        return false;
    }).bind("drop", function(event) {
        event.stopPropagation();
        event.preventDefault();
        for (i = 0; i < event.originalEvent.dataTransfer.files.length; i++) {
            var file = event.originalEvent.dataTransfer.files[i];
            var imgReg = /\.(gif|jpg|jpeg|png)$/;
            if (imgReg.test(file.name)) {
                rand = Math.floor((Math.random() * 10000) + 1000);
                $("#stato").append("<div id=\"file" + rand + "\"><img src=\"/img/loading.gif\" align=\"middle\">" + file.name + "</div>");
                $.ajax({
                    url: 'upload.php',
                    async: true,
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    processData: false,
                    data: file,
                    dataType: "script",
                    success: function(data) {
                        data;
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader("X-File-Name", file.name);
                        xhr.setRequestHeader("DIV-ID", "file" + rand);
                        xhr.setRequestHeader("Cache-Control", "no-cache");
                    }
                });
            } else {
                jalert("Impossibile caricare il file:" + file.name);
            }
        }
    });
}

function check_upload() {
    var filename = $('#allegato_upload').val();
    var allReg = /\.(jpg|jpeg|png|gif|doc|xls|pdf|zip|rar|ods|odt|rtf|JPG|JPEG|PNG|GIF|DOC|XLS|PDF|ZIP|RAR|ODS|ODT|rtf)$/;
    if ((allReg.test(filename)) && ($("#titolo_upload").val() !== "")) {
        $("#gif_load_allegato", parent.document).css("display", "inline");
    }
}

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function fill_relazioni(input, ambito) {
    var relazioni = $(input).val();
    if (relazioni !== undefined) {
        relazioni = relazioni.split(";");
    }
    $(ambito).find(".bt_relazione").each(function() {
        $(this).attr("checked", false);
        codice = $(this).attr("name");
        if ($.inArray(codice, relazioni) != -1) {
            if ($(this).is("button")) {
                $(this).addClass("attivo");
            } else {
                $(this).attr("checked", true);
            }
        }
    });
}

function ruota(classe, ritardo) {
    var InfiniteRotator = {
        init: function() {
            //initial fade-in time (in milliseconds)
            var initialFadeIn = 0;
            //interval between items (in milliseconds)
            var itemInterval = 2000 + ritardo;
            //cross-fade time (in milliseconds)
            var fadeTime = 1000;
            //count number of items
            var numberOfItems = $('.' + classe).length;
            //set current item
            var currentItem = 0;
            //show first item
            $('.' + classe).eq(currentItem).fadeIn(initialFadeIn);
            if (numberOfItems > 1) {
                //loop through the items
                var infiniteLoop = setInterval(function() {
                    $('.' + classe).eq(currentItem).fadeOut(fadeTime);
                    if (currentItem == numberOfItems - 1) {
                        currentItem = 0;
                    } else {
                        currentItem++;
                    }
                    $('.' + classe).eq(currentItem).fadeIn(fadeTime);
                }, itemInterval);
            }
        }
    };
    InfiniteRotator.init();
}

function check_error_tabs() {
    $('.ui-tabs-nav a').removeClass("ui-state-error");
    $(".ui-tabs-panel").each(function(index, element) {
        if ($(".ui-state-error", this).length > 0) {
            $('.ui-tabs-nav a[href="#' + $(this).attr("id") + '"]').addClass("ui-state-error");
        }
    });
}

function toTimestamp(string) {
    array_string = string.split(" ");
    if (array_string.length == 1) array_string[1] = "00:00";
    time_array = array_string[1].split(":");
    date_array = array_string[0].split("/");
    var datum = new Date(Date.UTC(date_array[2], date_array[1] - 1, date_array[0], time_array[0], time_array[1], 00));
    return datum.getTime() / 1000;
}

function completeVat(vat, country, destinazione) {
    data = "countryCode=" + country + "&vatNumber=" + vat;
    $.ajax({
        type: "POST",
        url: "/complete_vat.php",
        data: data,
        async: false,
        dataType: "html",
        success: function(script) {
            if (script != "") {
                $("#" + destinazione).val(script);
            }
        }
    });
}

function valida(input) {
    var msg = "";
    input.removeClass("ui-state-error");
    var label_id = input.attr("id");
    if((typeof label_id === "undefined" || label_id.length < 1) && (typeof input.attr("name") !== "undefined")) { label_id = input.attr("name").replace(/\]/g,'_').replace(/\[/g,'_').replace(/\:/g,'_').replace(/\$/g,'_').replace(/\'/g,'_'); }
    $("#alert_" + label_id).remove();
    $("#note_" + label_id).remove();
    var errore = false;
    var controlla = true;
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,12})?$/; // espressione regolare per il controllo del formato della mail
    var dataReg = /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/; // espressione regolare per il controllo del formato della data
    var timeReg = /^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/; // espressione regolare per il controllo del formato della data
    var dateTimeReg = /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}\s\d([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
    var imgReg = /\.(gif|jpg|jpeg|png)$/; // espressione regolare per il controllo dei file immagine
    var allReg = /\.(jpg|jpeg|png|gif|doc|xls|pdf|zip|rar|ods|odt|rtf|JPG|JPEG|PNG|GIF|DOC|XLS|PDF|ZIP|RAR|ODS|ODT|RTF)$/; // espressione regolare per il controllo dei file allegati
    var allPReg = /\.(p7m|pdf|zip|P7M|PDF|ZIP)$/; // espressione regolare per il controllo dei file allegati
    var SignReg = /\.(p7m|P7M)$/; // espressione regolare per il controllo dei file allegati
    var csvReg = /\.(csv)$/; // espressione regolare per il controllo dei file allegati
    var videoReg = /^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?(?=.*v=((\w|-){11}))(?:\S+)?$/; // espressione regolare per il controllo dei video Youtube
    var urlReg = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/; // espressione regolare per il controllo dei link
  var cfReg = /^([a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9a-zA-Z]{3}[a-zA-Z]|[0-9]{9,13}|[0-9]{8}[a-zA-Z]{1}|[a-zA-Z]{1}[0-9]{7}[a-zA-Z]{1}|[0-9]{7}[a-zA-Z]{1,2})$/;
    var ibanReg = /^[a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16}$/;
    // var pivaReg = /^[0-9]{11}$/;
    var pivaReg = /^((AT)?U[0-9]{8}|(BE)?0[0-9]{9}|(BG)?[0-9]{9,10}|(HR)?[0-9]{11}|(CY)?[0-9]{8}L|(CZ)?[0-9]{8,10}|(DE)?[0-9]{9}|(DK)?[0-9]{8}|(EE)?[0-9]{9}|(EL|GR)?[0-9]{9}|(ES)?[0-9A-Z][0-9]{7}[0-9A-Z]|(FI)?[0-9]{8}|(FR)?[0-9A-Z]{2}[0-9]{9}|(GB)?([0-9]{9}([0-9]{3})?|[A-Z]{2}[0-9]{3})|(HU)?[0-9]{8}|(IE)?[0-9]S[0-9]{5}L|(IT)?[0-9]{11}|(LT)?([0-9]{9}|[0-9]{12})|(LU)?[0-9]{8}|(LV)?[0-9]{11}|(MT)?[0-9]{8}|(NL)?[0-9]{9}B[0-9]{2}|(PL)?[0-9]{10}|(PT)?[0-9]{9}|(RO)?[0-9]{2,10}|(SE)?[0-9]{12}|(SI)?[0-9]{8}|(SK)?[0-9]{1})$/;
    //	acquisizione dei controlli da effettuare sul campo specificati nella proprietà rel del singolo input
    //	se tale proprietà non è stata inserita il controllo sul campo non sarà effettuato.
    var controlli_str = input.attr("rel");
    if(input.is(':disabled') || input.attr('disabled') || input.prop('disabled')) controlli_str = null;
    if (controlli_str != null) {
        if (input.val() == null) input.val("");
        if (!$.isArray(input.val())) input.val($.trim(input.val()));
        if (input.val() == null) {
            msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere valorizzato</li>";
            errore = true;
        } else {
            var controlli = controlli_str.split(";");
            if (controlli.length >= 4) {
                var obbligatorio = controlli[0];
                var lunghezza_minima = controlli[1];
                var lunghezza_massima = controlli[2];
                var tipo = controlli[3];
                if (typeof controlli[4] !== 'undefined' && controlli[4] != '') {
                    if (controlli[4].slice(-3) == "php") {
                        data = "valore=" + input.val() + "&codice=" + $("#codice").val();
                        input.after("<img id='load_" + label_id + "' src='/img/loading.gif' alt='attendere'>");
                        $.ajax({
                            type: "POST",
                            url: controlli[4],
                            data: data,
                            dataType: "html",
                            async: false,
                            success: function(script) {
                                if (script != "") {
                                    msg = msg + "<li>" + script + "</li>";
                                    errore = true;
                                }
                            },
                            error: function() {
                                msg = msg + "<li>Errore nella validazione" + "</li>";
                                errore = true;
                            }
                        });
                        $("#load_" + label_id).remove();
                    } else {
                        var valore = input.val();
                        var riferimento = controlli[4];
                        var operatore = "=";
                        if (riferimento.indexOf(" ") === -1) {
                            if ($("#" + controlli[4]).length > 0) riferimento = $("#" + controlli[4]).val();
                        }
                        riferimento_echo = riferimento;
                        if ((tipo == "D" || tipo == "T" || tipo == "DT") && typeof riferimento != 'undefined') {
                            riferimento = toTimestamp(riferimento);
                            valore = toTimestamp(valore);
                        } else if (tipo == "checked" && riferimento == "group_validate" && typeof riferimento != 'undefined') {
                            var controlla = false;
                            if(obbligatorio == 'S') {
                                if (input.find("input:checked").length == 0) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> è obbligatorio</li>";
                                    errore = true;
                                }
                            }
                        } else if (tipo == "checked" && riferimento == "group_one" && typeof riferimento != 'undefined') {
                            var controlla = false;
                            if (obbligatorio == "S" && input.find("input:checked").length != 1) {
                                msg = msg + "<li><strong>" + input.attr("title") + "</strong> errore nella compilazione</li>";
                                errore = true;
                            } else if (input.find("input:checked").length > 1) {
                                msg = msg + "<li><strong>" + input.attr("title") + "</strong> errore nella compilazione</li>";
                                errore = true;
                            }
                        } else if (tipo == "N") {
                            valore = parseFloat(valore);
                            riferimento = parseFloat(riferimento);
                        } else if (tipo == "P") {
                          riferimento_echo = "a conferma"
                        }
                        if (typeof controlli[5] !== 'undefined' && controlli[5] != '') operatore = controlli[5];
                        //alert('valore:' + valore + " - riferimento:" + riferimento);
                        if (controlla) {
                            switch (operatore) {
                                case "=":
                                    if (valore != riferimento) {
                                        msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere uguale " + riferimento_echo + "</li>";
                                        errore = true
                                    }
                                    break;
                                case ">":
                                    if (valore <= riferimento) {
                                        msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere maggiore di " + riferimento_echo + "</li>";
                                        errore = true
                                    }
                                    break;
                                case ">=":
                                    if (valore < riferimento) {
                                        msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere maggiore o uguale a " + riferimento_echo + "</li>";
                                        errore = true
                                    }
                                    break;
                                case "<":
                                    if (valore >= riferimento) {
                                        msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere minore di " + riferimento_echo + "</li>";
                                        errore = true
                                    }
                                    break;
                                case "<=":
                                    if (valore > riferimento) {
                                        msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere minore o uguale a " + riferimento_echo + "</li>";
                                        errore = true
                                    }
                                    break;
                                case "!=":
                                    if (valore == riferimento) {
                                        msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere diverso da " + riferimento_echo + "</li>";
                                        errore = true
                                    }
                                    break;
                            }
                        }
                    }
                }
                if (controlla) {
                    if ((input.val() === "") && (obbligatorio == "S")) { // Controllo sul valore del campo
                        msg = msg + "<li><strong>" + input.attr("title") + "</strong> è obbligatorio</li>";
                        errore = true;
                    }
                    if (input.val() != "") {
                        if (input.val().length < lunghezza_minima) { //	Controlla il numero di caratteri del campo
                            msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere lungo almeno " + lunghezza_minima + " caratteri</li>";
                            errore = true;
                        }
                        if ((lunghezza_massima > 0) && (input.val().length > lunghezza_massima)) { //	Controlla il numero di caratteri del campo
                            msg = msg + "<li><strong>" + input.attr("title") + "</strong> non deve essere piu lungo di " + lunghezza_massima + " caratteri</li>";
                            errore = true;
                        }
                        switch (tipo) {
                            case "ARRAY":
                                if (!$.isArray(input.val())) {
                                    errore = true;
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere una scelta multipla</li>";
                                }
                                break;
                            case "N":
                                input.val(input.val().replace(",", "."));
                                if (!isNumeric(input.val())) {
                                    errore = true;
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un numero</li>";
                                }
                                break;
                            case "E":
                                if (!emailReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un indirizzo e-mail valido</li>";
                                    errore = true;
                                }
                                break;
                            case "D":
                                if (!dataReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere una data valida</li>";
                                    errore = true;
                                }
                                break;
                            case "T":
                                if (!timeReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un'ora valida</li>";
                                    errore = true;
                                }
                                break;
                            case "DT":
                                if (!dateTimeReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere una data-ora valida</li>";
                                    errore = true;
                                }
                                break;
                            case "I":
                                if (!imgReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> può solo essere di tipo: JPG | GIF | PNG</li>";
                                    errore = true;
                                }
                                break;
                            case "F":
                                if (!allReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> può solo essere di tipo: JPG | GIF | PNG | DOC | XLS | PDF | ZIP | RAR</li>";
                                    errore = true;
                                }
                                break;
                            case "FP":
                                if (!allPReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> può solo essere di tipo: P7M | PDF | ZIP </li>";
                                    errore = true;
                                }
                                break;
                            case "FS":
                                if (!SignReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un file firmato di tipo: P7M </li>";
                                    errore = true;
                                }
                                break;
                            case "CSV":
                                if (!csvReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> può solo essere di tipo: CSV</li>";
                                    errore = true;
                                }
                                break;
                            case "YV":
                                if (!videoReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un link valido ad un video di youtube</li>";
                                    errore = true;
                                }
                                break;
                            case "CF":
                                if (!cfReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un codice fiscale valido</li>";
                                    errore = true;
                                }
                                break;
                            case "IB":
                                if (!ibanReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un codice iban valido</li>";
                                    errore = true;
                                }
                                break;
                            case "L":
                                if (!urlReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un link valido</li>";
                                    errore = true;
                                }
                                break;
                            case "Checked":
                                var checked = input.prop("checked");
                                if (!checked && obbligatorio == "S") {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere selezionato</li>";
                                    errore = true;
                                }
                                break;
                            case "2D":
                                input.val(input.val().replace(",", "."));
                                if (!isNumeric(input.val())) {
                                    errore = true;
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un numero</li>";
                                } else {
                                    var integer_part = Math.floor(input.val());
                                    if (input.val() == integer_part) {
                                        input.val(integer_part + '.00');
                                    } else {
                                        var splitted_number = input.val().split('.');
                                        if(splitted_number[1].length <= 1) {
                                            input.val(input.val() + '0');
                                        } else if (splitted_number[1].length > 2) {
                                            input.val(integer_part + '.' + splitted_number[1].substring(0,2));
                                        }
                                    }
                                }
                                break;
                            case "P": {
                              var hasUpperCase = /[A-Z]/.test(input.val());
                              var hasLowerCase = /[a-z]/.test(input.val());
                              var hasNumbers = /\d/.test(input.val());
                              var hasNonalphas = /\W/.test(input.val());
                              var hasEcommerciale = /\&/.test(input.val());
                              if (hasUpperCase + hasLowerCase + hasNumbers + hasNonalphas < 4 || hasEcommerciale > 0)
                              msg = msg + ("<li><strong>" + input.attr("title") + "</strong> deve contenere almeno una lettera maiuscola, una lettera minuscola, un numero ed un simbolo (escluso il simbolo &)</li>");
                              break;
                            }
                            case "PI":
                                if (!pivaReg.test(input.val())) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un numero di partita iva valido</li>";
                                    errore = true;
                                }
                                /* stato = 'IT';
													if ($("#"+input.attr("opt")).length > 0) stato = $("#"+input.attr("opt")).val();
													data = "countryCode=" + stato + "&vatNumber=" + piva;
													$.ajax({
														type: "POST",
														url: "/verifica_vat.php",
														data: data,
														async:false,
														dataType: "html",
														success: function(script)
														 {
															if (script != "") {
																msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un numero di partita iva valido</li>";
																errore = true;
															}
														  },
														error: function()
														  {
																msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un numero di partita iva valido</li>";
																errore = true;
														  }
													}); */
                                break;
                            case "PICF":
                                errore_cf = false;
                                errore_piva = false;
                                if (!cfReg.test(input.val())) {
                                    errore_cf = true;
                                }
                                if (errore_cf) {
                                    errore_cf = false;
                                    /* piva = input.val();
													stato = 'IT';
													if ($("#"+input.attr("opt")).length > 0) stato = $("#"+input.attr("opt")).val();
													data = "countryCode=" + stato + "&vatNumber=" + piva;
													input.addClass('working');
													$.ajax({
														type: "POST",
														url: "/verifica_vat.php",
														data: data,
														async:false,
														dataType: "html",
														success: function(script)
														 {
															if (script != "") {
																errore_piva = true;
															}
														  },
														error: function()
														  {
																errore_piva = true;
														  }
													}); */
                                    if (!pivaReg.test(input.val())) {
                                        errore_piva = true;
                                        msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un numero di partita iva valido</li>";
                                        errore = true;
                                    }
                                }
                                //          input.removeClass('working');
                                if (errore_cf || errore_piva) {
                                    msg = msg + "<li><strong>" + input.attr("title") + "</strong> deve essere un numero di partita iva valido o un codice fiscale valido</li>";
                                    errore = true;
                                }
                                break;
                        }
                    }
                }
            }
        }
    }
    if (msg != "") {
        input.addClass("ui-state-error");
        input.after("<div id=\"note_" + label_id + "\" class='note_errore'><img id=\"alert_" + label_id + "\" src=\"/img/alert.png\" width=\"15\" style=\";vertical-align:middle; margin-right:5px;\"><strong>" + $("<div></div").html(msg).text() + "</strong></div>");
    }
    check_error_tabs();
    return msg;
}

function prova_configurazione(id) {
    if (typeof id == 'undefined') id = "impostazioni_pec";
    data = $("#" + id + " :input").serialize();
    $.ajax({
        type: "POST",
        url: "/moduli/test_invio.php",
        data: data,
        dataType: "html",
        success: function(script) {
            jalert(script);
        },
        error: function() {
            jalert("Errore, si prega di riprovare...");
        }
    });
}

function f_ready() {

    $(".completeVat").change(function() {
        if (typeof $(this).attr("dest") != 'undefined') {
            country = 'IT';
            if ($("#" + $(this).attr("opt")).length > 0) country = $("#" + $(this).attr("opt")).val();
            completeVat($(this).val(), country, $(this).attr("dest"));
        }
    });
    $(".sortable").sortable({
        handle: ".handle",
        start: function(e, ui) {
            $(".ckeditor_models").each(function() {
                $(this).ckeditor().editor.destroy();
            });
        },
        stop: function(e, ui) {
            $.map($(this).find('div, tr'), function(el) {
                $("input.ordinamento", el).val($(el).index());
            });
            $(".ckeditor_models").ckeditor(config_models);
        }
    });
    // $( ".sortable" ).disableSelection();
    $(".rimuovi_colore").click(function() {
        td = $(this).parent().prev();
        $(td.attr('object')).css(td.attr('property'), "");
        td.css("background", "url(/img/transparent.png)");
        $("#" + td.attr('rel')).val('');
        return false;
    });
    $('.color_selector').colorpicker({
        inline: false,
        alpha: false,
        colorFormat: 'HEX',
        closeOnOutside: true,
        select: function(event, color) {
            id = $(this).attr("rel");
            $("#" + id).val(color.formatted);
            $($(this).attr("object")).css($(this).attr("property"), "#" + color.formatted + " !important");
            $(this).css("background", "#" + color.formatted);
        }
    });
    $("#destinatari li").unbind("click");
    $("#destinatari li").click(function() {
        var pos = $.inArray($(this).attr("rel"), destinatari);
        if (pos != -1) {
            destinatari.splice(pos, 1)
            $(this).remove();
            $("#invia_" + $(this).attr("rel")).attr("src", "/img/add.png");
            $("#indirizzi").val(destinatari.join(";"));
        }
    });
    $("select").not(".dataTables_wrapper select").chosen({
        width: '100%',
        search_contains: true
    });
    $(".ckeditor_full").ckeditor(config_full);
    $(".ckeditor_models").ckeditor(config_models);
    $(".ckeditor_simple").ckeditor(config_simple);
    if (typeof escapeHtmlEntities == 'undefined') {
        escapeHtmlEntities = function(text) {
            return text.replace(/[\u00A0-\u2666<>\&]/g, function(c) {
                return '&' + (escapeHtmlEntities.entityTable[c.charCodeAt(0)] || '#' + c.charCodeAt(0)) + ';';
            });
        };
        // all HTML4 entities as defined here: http://www.w3.org/TR/html4/sgml/entities.html
        // added: amp, lt, gt, quot and apos
        escapeHtmlEntities.entityTable = {
            34: 'quot',
            38: 'amp',
            39: 'apos',
            60: 'lt',
            62: 'gt',
            160: 'nbsp',
            161: 'iexcl',
            162: 'cent',
            163: 'pound',
            164: 'curren',
            165: 'yen',
            166: 'brvbar',
            167: 'sect',
            168: 'uml',
            169: 'copy',
            170: 'ordf',
            171: 'laquo',
            172: 'not',
            173: 'shy',
            174: 'reg',
            175: 'macr',
            176: 'deg',
            177: 'plusmn',
            178: 'sup2',
            179: 'sup3',
            180: 'acute',
            181: 'micro',
            182: 'para',
            183: 'middot',
            184: 'cedil',
            185: 'sup1',
            186: 'ordm',
            187: 'raquo',
            188: 'frac14',
            189: 'frac12',
            190: 'frac34',
            191: 'iquest',
            192: 'Agrave',
            193: 'Aacute',
            194: 'Acirc',
            195: 'Atilde',
            196: 'Auml',
            197: 'Aring',
            198: 'AElig',
            199: 'Ccedil',
            200: 'Egrave',
            201: 'Eacute',
            202: 'Ecirc',
            203: 'Euml',
            204: 'Igrave',
            205: 'Iacute',
            206: 'Icirc',
            207: 'Iuml',
            208: 'ETH',
            209: 'Ntilde',
            210: 'Ograve',
            211: 'Oacute',
            212: 'Ocirc',
            213: 'Otilde',
            214: 'Ouml',
            215: 'times',
            216: 'Oslash',
            217: 'Ugrave',
            218: 'Uacute',
            219: 'Ucirc',
            220: 'Uuml',
            221: 'Yacute',
            222: 'THORN',
            223: 'szlig',
            224: 'agrave',
            225: 'aacute',
            226: 'acirc',
            227: 'atilde',
            228: 'auml',
            229: 'aring',
            230: 'aelig',
            231: 'ccedil',
            232: 'egrave',
            233: 'eacute',
            234: 'ecirc',
            235: 'euml',
            236: 'igrave',
            237: 'iacute',
            238: 'icirc',
            239: 'iuml',
            240: 'eth',
            241: 'ntilde',
            242: 'ograve',
            243: 'oacute',
            244: 'ocirc',
            245: 'otilde',
            246: 'ouml',
            247: 'divide',
            248: 'oslash',
            249: 'ugrave',
            250: 'uacute',
            251: 'ucirc',
            252: 'uuml',
            253: 'yacute',
            254: 'thorn',
            255: 'yuml',
            402: 'fnof',
            913: 'Alpha',
            914: 'Beta',
            915: 'Gamma',
            916: 'Delta',
            917: 'Epsilon',
            918: 'Zeta',
            919: 'Eta',
            920: 'Theta',
            921: 'Iota',
            922: 'Kappa',
            923: 'Lambda',
            924: 'Mu',
            925: 'Nu',
            926: 'Xi',
            927: 'Omicron',
            928: 'Pi',
            929: 'Rho',
            931: 'Sigma',
            932: 'Tau',
            933: 'Upsilon',
            934: 'Phi',
            935: 'Chi',
            936: 'Psi',
            937: 'Omega',
            945: 'alpha',
            946: 'beta',
            947: 'gamma',
            948: 'delta',
            949: 'epsilon',
            950: 'zeta',
            951: 'eta',
            952: 'theta',
            953: 'iota',
            954: 'kappa',
            955: 'lambda',
            956: 'mu',
            957: 'nu',
            958: 'xi',
            959: 'omicron',
            960: 'pi',
            961: 'rho',
            962: 'sigmaf',
            963: 'sigma',
            964: 'tau',
            965: 'upsilon',
            966: 'phi',
            967: 'chi',
            968: 'psi',
            969: 'omega',
            977: 'thetasym',
            978: 'upsih',
            982: 'piv',
            8226: 'bull',
            8230: 'hellip',
            8242: 'prime',
            8243: 'Prime',
            8254: 'oline',
            8260: 'frasl',
            8472: 'weierp',
            8465: 'image',
            8476: 'real',
            8482: 'trade',
            8501: 'alefsym',
            8592: 'larr',
            8593: 'uarr',
            8594: 'rarr',
            8595: 'darr',
            8596: 'harr',
            8629: 'crarr',
            8656: 'lArr',
            8657: 'uArr',
            8658: 'rArr',
            8659: 'dArr',
            8660: 'hArr',
            8704: 'forall',
            8706: 'part',
            8707: 'exist',
            8709: 'empty',
            8711: 'nabla',
            8712: 'isin',
            8713: 'notin',
            8715: 'ni',
            8719: 'prod',
            8721: 'sum',
            8722: 'minus',
            8727: 'lowast',
            8730: 'radic',
            8733: 'prop',
            8734: 'infin',
            8736: 'ang',
            8743: 'and',
            8744: 'or',
            8745: 'cap',
            8746: 'cup',
            8747: 'int',
            8756: 'there4',
            8764: 'sim',
            8773: 'cong',
            8776: 'asymp',
            8800: 'ne',
            8801: 'equiv',
            8804: 'le',
            8805: 'ge',
            8834: 'sub',
            8835: 'sup',
            8836: 'nsub',
            8838: 'sube',
            8839: 'supe',
            8853: 'oplus',
            8855: 'otimes',
            8869: 'perp',
            8901: 'sdot',
            8968: 'lceil',
            8969: 'rceil',
            8970: 'lfloor',
            8971: 'rfloor',
            9001: 'lang',
            9002: 'rang',
            9674: 'loz',
            9824: 'spades',
            9827: 'clubs',
            9829: 'hearts',
            9830: 'diams',
            338: 'OElig',
            339: 'oelig',
            352: 'Scaron',
            353: 'scaron',
            376: 'Yuml',
            710: 'circ',
            732: 'tilde',
            8194: 'ensp',
            8195: 'emsp',
            8201: 'thinsp',
            8204: 'zwnj',
            8205: 'zwj',
            8206: 'lrm',
            8207: 'rlm',
            8211: 'ndash',
            8212: 'mdash',
            8216: 'lsquo',
            8217: 'rsquo',
            8218: 'sbquo',
            8220: 'ldquo',
            8221: 'rdquo',
            8222: 'bdquo',
            8224: 'dagger',
            8225: 'Dagger',
            8240: 'permil',
            8249: 'lsaquo',
            8250: 'rsaquo',
            8364: 'euro'
        };
    }
    var items = $('#list_menu li.first_level').get();
    items.sort(function(a, b) {
        var keyA = $(a).attr("ord");
        var keyB = $(b).attr("ord");
        if (keyA < keyB) return -1;
        if (keyA > keyB) return 1;
        return 0;
    });
    var ul = $('#list_menu');
    $.each(items, function(i, li) {
        ul.append(li);
    });
    /* $("tbody > tr:even").removeClass("odd").addClass("even");
    $("tbody > tr:odd").removeClass("even").addClass("odd"); */
    $("table > * > tr:even").not(".elenco, .triangular, .calendar").removeClass("odd").addClass("even");
    $("table > * > tr:odd").not(".elenco, .triangular, .calendar").removeClass("even").addClass("odd");
    // Funzione per l'utilizzo di trim() su Internet Explorer
    if (typeof String.prototype.trim !== 'function') {
        String.prototype.trim = function() {
            return this.replace(/^\s+|\s+$/g, '');
        }
    }
    // Inizio funzione espressioni regolari case insensitive
    // NEW selector
    jQuery.expr[':'].Contains = function(a, i, m) {
        return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };
    // OVERWRITES old selecor
    jQuery.expr[':'].contains = function(a, i, m) {
        return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };
    // Fine funzione espressioni regolari case insensitive
    $("#newsletter_email").focus(function() {
        $("#div_newsletter_privacy").slideDown();
    });
    $("#password").passStrength({
        shortPass: "corta", //optional
        badPass: "debole", //optional
        goodPass: "buona", //optional
        strongPass: "ottima", //optional
        userid: "#email"
    });
    //	Funzione per nascondere i div vuoti generati durante l'inclusione dei vari moduli
    if ($("#moduli .padding").length > 0) {
        $('.padding', '#moduli').each(function() {
            html = $(this).html();
            html = html.trim();
            if (html == "") {
                $(this).parent().remove();
            }
        });
        if ($("#moduli .padding").length > 0) {
            $("#destra").children().appendTo("#barra_sx");
            $("#destra").remove();
            $("#list_menu").slideUp();
            $("#show_menu").slideDown();
        }
    }
    etichette_testo();
    /*
			Funzione per la modifica delle relazioni
			La funzione si applica al cambiamento di valore
			per gli elementi aventi classe bt_relazione.

			Nella proprietà rel si indicherà l'id dell'input di riferimento
			per l'aggiornamento delle relazioni
			Nell'input sarà presente una stringa con i codici degli articoli
			relazionati divisi dal punto e virgola

			Nella proprietà name si indicherà il codice dell'articolo da
			inserire/eliminare dalle relazioni

			es. <input type="checkbox" rel="#cod_allegati" name="1">

			*/
    $("input.bt_relazione").change(function() {
        var relazioni_str = $($(this).attr("rel")).val();
        relazioni_str = relazioni_str.trim();
        var relazioni = new Array();
        relazioni = relazioni_str.split(";");
        var checked = $(this).prop("checked");
        var codice = $(this).attr("name");
        if (checked) {
            relazioni.push(codice);
        } else {
            var pos = $.inArray(codice, relazioni);
            relazioni.splice(pos, 1);
        }
        relazioni_str = relazioni.join(";");
        $($(this).attr("rel")).val(relazioni_str);
    });
    $("button.bt_relazione").unbind("click");
    $("button.bt_relazione").click(function() {
        var relazioni_str = $($(this).attr("rel")).val();
        relazioni_str = relazioni_str.trim();
        var relazioni = new Array();
        relazioni = relazioni_str.split(";");
        var checked = $(this).hasClass("attivo");
        var codice = $(this).attr("name");
        if (!checked) {
            relazioni.push(codice);
            $(this).addClass("attivo");
        } else {
            var pos = $.inArray(codice, relazioni);
            relazioni.splice(pos, 1);
            $(this).removeClass("attivo");
        }
        relazioni_str = relazioni.join(";");
        $($(this).attr("rel")).val(relazioni_str);
        return false;
    });
    $('.datepick').datetimepicker({
        lang: 'it',
        timepicker: false,
        format: 'd/m/Y',
        dayOfWeekStart: 1,
        scrollInput: false,
        closeOnDateSelect: true
    });
    $('.datepick').each(function() {
        title = $(this).attr('title');
        title += " Formato (dd/mm/yyyy)"; 
        $(this).attr('title',title);
    });
    $('.timepick').datetimepicker({
        lang: 'it',
        datepicker: false,
        format: 'H:i',
        step: '30',
        minTime: '07:00',
        maxTime: '22:00',
        dayOfWeekStart: 1,
        scrollInput: false,
        closeOnDateSelect: true
    });
    $('.timepick').each(function() {
        title = $(this).attr('title');
        title += " Formato (hh:mm)"; 
        $(this).attr('title',title);
    });
    $('.datetimepick').datetimepicker({
        lang: 'it',
        startDate: 'today',
        step: 15,
        format: 'd/m/Y H:i',
        minTime: '07:00',
        maxTime: '22:00',
        dayOfWeekStart: 1,
        scrollInput: false,
        closeOnDateSelect: true
    });
    $('.datetimepick').each(function() {
        title = $(this).attr('title');
        title += " Formato (dd/mm/yyyy hh:mm)"; 
        $(this).attr('title',title);
    });
    $('.datetimepick_today').datetimepicker({
        lang: 'it',
        step: 15,
        startDate: 0,
        format: 'd/m/Y H:i',
        minTime: '07:00',
        maxTime: '22:00',
        dayOfWeekStart: 1,
        scrollInput: false,
        closeOnDateSelect: true
    });
    $('.datetimepick_today').each(function() {
        title = $(this).attr('title');
        title += " Formato (dd/mm/yyyy hh:mm)"; 
        $(this).attr('title',title);
    });
    $('.datepick_today').datetimepicker({
        lang: 'it',
        startDate: 0,
        timepicker: false,
        format: 'd/m/Y',
        scrollInput: false,
        closeOnDateSelect: true
    });
    $('.datepick_today').each(function() {
        title = $(this).attr('title');
        title += " Formato (dd/mm/yyyy)"; 
        $(this).attr('title',title);
    });
    $("input:text,input:password,select,textarea").focusout(function() {
        if ($(this).val() != "") {
            $(this).removeClass("ui-state-error");
        }
    });
    $(".titolo_div").click(function() {
        if ($(this).parent().find(".contenuto_div").css("display") == 'none') {
            //	$(".contenuto_div").slideUp();
            $(this).parent().find(".contenuto_div").slideDown();
        } else {
            $(this).parent().find(".contenuto_div").slideUp();
        }
    });
    // Funzione per il controllo del submit di tutti i form nel sito
    //		La funzione svuoterà tutti i campi vuoti in cui è stato inserito il titolo da utilizzare come etichetta
    //		inoltre validerà il form stesso prima di effettuare il submit.
    //		Inserire la proprietà rel="validate" nel form per effettuare il controllo dei campi
    //		Inserire la proprietà target se si vuole effettuare un submit classico
    //			Non inserire tale proprietà per effettuare un submit tramite ajax
    //		Inserire la proprietà rel nel tag per effettuare il controllo sul campo
    //		Sintassi: rel="Obbligatorio;Lunghezza minima;Lunghezza Massima;Tipo" es. rel= "S;3;10;A"
    //		Obbligatorio = 'S' qualsiasi altro valore sarà considerato non obbligatorio
    //		Lunghezza minima = Numerico; 0 nessun limite
    //		Lunghezza Massima	= Numerico; 0 nessun limite
    // 		Tipo = A: Alfanumerico N: Numerico E:email D: Data F: File I:Immagine YV: Linl a video di youtube
    //		L: Link
    //				Checked: Selezionato (Riferito a checkbox)
    $("form").unbind("submit");
    $("form :input").unbind("focusout");
    $("form :input").focusout(function() {
        msg = valida($(this));
    });
    CKEDITOR.replaceAll('ckeditor_schema');
    $(".onchange").change(function() {
        $(this).parents("form").submit();
    });
    $("form").submit(function() {
        var controllo = false;
        if (($(this).attr("rel") !== null) && ($(this).attr("rel") == "validate")) {
            controllo = true;
        }
        var msg = "";
        // Aggiorna il contenuto di eventuali istanze di CKEditor
        for (var instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        // Analizza tutti i campi del database
        $(this).find(":input, .valida").each(function() {
            // Se la proprieta rel è stata impostata su validate per il form passa al controllo
            if (controllo) {
                msg += valida($(this));
            }
        });
        check_error_tabs();
        if (msg != "") {
            etichette_testo();
            jalert("<ul>" + msg + "</ul>");
            $("#wait").fadeOut('fast');
            return false;
        } else {
            $("#wait").show();
            if ($(this).attr("target") == undefined) {
                $(this).find("input").not(".ckeditor").each(function() {
                    $(this).val(escapeHtmlEntities($(this).val()));
                });
                var data = $(this).serialize(); //	Creazione di una stringa con il valore dei vari input del form da utilizzare per il submit ajax
                $(this).find("input").not(".ckeditor").each(function() {
                    $(this).val();
                    var stringa = $(this).val();
                    var valore = $('<div />').html(stringa).text();
                    $(this).val(valore);
                });
                etichette_testo();
                $.ajax({
                    type: "POST",
                    url: $(this).attr("action"),
                    data: data,
                    dataType: "script",
                    async: true,
                    success: function(script) {
                        script;
                        $("#wait").fadeOut('fast');
                    },
                    error: function() {
                        jalert("Errore, si prega di riprovare...");
                        $("#wait").fadeOut('fast');
                    }
                });
                return false;
            }
            if ($(this).attr("target") != "_self") $("#wait").fadeOut('fast');
        }
    });
}

/*
function launchLiveChat(url,name,email) {
    if ($('df-messenger:visible').length > 0 || $(".rocketchat-widget").length == 0) {
        $('df-messenger').hide();
        (function(w, d, s, u) {
            w.RocketChat = function(c) { w.RocketChat._.push(c) };
            w.RocketChat._ = [];
            w.RocketChat.url = u; 
            var h = d.getElementsByTagName(s)[0], j = d.createElement(s);
            j.async = true; 
            j.src = url+'/livechat/rocketchat-livechat.min.js?_=201903270000';
            h.parentNode.insertBefore(j, h);
            if (name != "" && email != "") {
                RocketChat(function() {
                    this.registerGuest({
                        name: name,
                        email: email
                    });
                });
            }
            RocketChat(function() {
                this.onChatStarted(function() {
                    document.cookie = "rcLivechat=enabled";
                });
            });
            RocketChat(function() {
                this.maximizeWidget();
            })
            RocketChat(function() {
                this.onChatEnded(function() {
                    $('df-messenger').show();
                    document.cookie = "rcLivechat=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                });
            });
        })(window, document, 'script', url+'/livechat?version=2.0.0');
    } else if ($(".rocketchat-widget").length > 0) {
        $('df-messenger').hide();
        RocketChat(function() {
            this.maximizeWidget();
        })
    }
}
*/
$(document).ready(function() {
  $.fn.dataTable.moment("DD/MM/YYYY HH:mm:ss");

  $.extend(true, $.fn.dataTable.defaults, {
      "language": {
          "url": "/js/dataTables.Italian.json"
      }
  });

  /* var auto_refresh = setInterval(function() {
      update_badges();
  }, 60000); */
  f_ready();
  $(".geocomplete").each(function() {
      classe = "." + $(this).attr("riferimento");
      $(this).geocomplete({
          details: classe,
          detailsAttribute: "data-geo",
          map: false,
          componentRestrictions: {
              country: "it"
          }
      }).bind("geocode:result", function(event, result){
        $('select').trigger('change').trigger("chosen:updated");
      });
  });
  if ($(".cerca_cpv").length > 0) {
      $(".cerca_cpv").autocomplete({
          source: function(request, response) {
              $.ajax({
                  url: "/moduli/cpv.php",
                  dataType: "json",
                  data: {
                      term: request.term,
                      esclusioni: $("#cpv").val()
                  },
                  success: function(data) {
                      response(data);
                  }
              });
          },
          minLength: 3,
          search: function() {
              $(this).addClass('working');
          },
          open: function() {
              $(this).removeClass('working');
          },
          select: function(e, ui) {
              e.preventDefault(); // <--- Prevent the value from being inserted.
              categoria(ui.item.label, $(this).attr("rel"), $(this).attr("url"));
              $(this).focus();
          },
          focus: function(e, ui) {
              e.preventDefault(); // <--- Prevent the value from being inserted.
          }
      }).data("ui-autocomplete")._renderItem = function(ul, item) {
          return $("<li id='val" + item.label + "'>").append("<a><strong>" + item.label + " " + item.value + "</strong></a>").appendTo(ul);
      }
  }
  if ($(".elenco").length > 0) {
    $(".elenco").dataTable({
      "pageLength": 25,
      "order": [],
      "lengthMenu": [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 50, "Tutti"]
      ]
    });
  }
  $("#wait").fadeOut('fast');
});
