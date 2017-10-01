
function trackEvent(cat1, cat2, cat3){
    if(typeof(ga) !== "undefined"){
        ga('send', 'event', cat1, cat2, cat3);
    }
    console.log("GaEvent: " + cat1 + ", " + cat2 + ", " + cat3);
}

function removeEntityElement(url, entity, index, element, redir) {
    $.ajax({
        url: url,
        data: {
            entity: entity,
            id: index
        },
        success: function(result) {
            if ($(element).size() <= 0) {
                location.href = redir;
            }
            $(element).fadeOut(500, function() {
                $(this).remove();
            });
        }
    });
}

function ItemCollection() {
    this.values = {};
    this.index = 0;
    this.input = null;
    this.repeat_values = false;
}

ItemCollection.prototype.genIndex = function() {
    while (true) {
        ++this.index;
        if (this.existsIndex(this.index + "")) {
            continue;
        }
        return this.index;
    }
};
ItemCollection.prototype.saveInput = function() {
    $(this.input).val(this.getJSON());
};
ItemCollection.prototype.loadInput = function() {
    this.parseJSON($(this.input).val());
};
ItemCollection.prototype.setInput = function(element) {
    this.input = $(element);
    this.loadInput();
};
ItemCollection.prototype.parseJSON = function(string) {
    if (string == "" || string == null)
        return;
    this.values = JSON.parse(string);
};
ItemCollection.prototype.getJSON = function() {
    return JSON.stringify(this.values);
};
ItemCollection.prototype.exists = function(value) {
    for (var x in this.values) {
        if (this.values[x] == value) {
            return x;
        }
    }
    return 0;
};
ItemCollection.prototype.existsIndex = function(index) {
    return this.values[index] != null;
};
ItemCollection.prototype.addOn = function(index, value) {
    this.values[index] = value;
    this.saveInput();
    return index;
};
ItemCollection.prototype.add = function(value) {
    if (!this.repeat_values) {
        if (this.exists(value) > 0) {
            return 0;
        }
    }
    this.values[this.genIndex()] = value;
    this.saveInput();
    return this.index;
};
ItemCollection.prototype.removeIndex = function(index) {
    delete(this.values[index]);
    this.saveInput();
};
ItemCollection.prototype.removeValue = function(value) {
    var index = this.exists(value);
    if (index > 0) {
        this.removeIndex(index);
    }
};
ItemCollection.prototype.get = function(index) {
    return this.values[index];
};
ItemCollection.prototype.getValues = function() {
    return this.values;
};
ItemCollection.prototype.clear = function() {
    this.values = {};
    this.saveInput();
};

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function stripTags(html)
{
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
}

function isIE(v) {
    var r = RegExp('msie' + (!isNaN(v) ? ('\\s' + v) : ''), 'i');
    return r.test(navigator.userAgent);
}

function containsArrayValue(array, value) {
    for (var x in array) {
        if (array[x] == value) {
            return true;
        }
    }
    return false;
}
function replaceMulti(text, assoc_array) {
    for (var x in assoc_array) {
        text = replaceAll(text, x, assoc_array[x]);
    }
    return text;
}
function checkEmail(email) {
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (filter.test(email)) {
        return true;
    }
    return false;
}

function maskValue(mask, value) {
    if (value == "" || value == null) {
        return "";
    }
    return replaceAll(mask, "%s", value);
}
$(function() {
    jQuery.fn.forceNumericOnly = function()
    {
        return this.each(function()
        {
            $(this).keydown(function(e)
            {
                var key = e.charCode || e.keyCode || 0;
                // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
                // home, end, period, and numpad decimal
                return (
                        key == 8 ||
                        key == 9 ||
                        key == 46 ||
                        key == 110 ||
                        key == 190 ||
                        (key >= 35 && key <= 40) ||
                        (key >= 48 && key <= 57) ||
                        (key >= 96 && key <= 105));
            });
        });
    };
});
(function($) {
    $.fn.goTo = function() {
        $('html, body').animate({
            scrollTop: $(this).offset().top + 'px'
        }, 'fast');
        return this; // for chaining...
    }
})(jQuery);
(function($) {
    $.fn.ocultable = function(selector, clickable) {
        $(this).each(function() {
            if(selector === undefined){
                selector = "div:last";
            }
            var child = $(this).children(selector);
            $(child).css("display", "none");
            
            var show_an = function() {
                $(child).stop();
                $(child).slideDown();
            };
            var hide_an = function() {
                $(child).stop();
                $(child).slideUp();
            };
            
            if(clickable !== undefined && clickable){
                var context = this;
                
                $(this).click(function(e){       
                    //console.log(this);       
                    //console.log(e);
                    if($(child).find(e.target).size() > 0){
                        return false;
                    }
                    //if(e.target === this) return; // only continue if the target itself has been clicked
                    $(child).slideToggle();
                });
            } else {
                $(this).mouseenter(show_an);
                $(this).mouseleave(hide_an);
            }
        });
    }
})(jQuery);

var NotifyInterface = {
    container: "",
    max_items: 10,
    timeout: 1000,
    current_index: -1,
    owner: "user",
    type: "time",
    builder: function(data) {
        //console.log("preparing to attach");
        var vars = JSON.parse(data);
        var template = vars["template"];
        var html = proccessTemplate("#template_" + template, vars);
        var tmp_element = document.createElement("div");
        var container = $(NotifyInterface.container);
        $(tmp_element).css("display", "none");
        $(tmp_element).html(html);
        $(tmp_element).prependTo(container);
        $(tmp_element).slideDown();

        while ($(container).children().size() > NotifyInterface.max_items) {
            $(container).children().last().remove();
        }
        console.log("Attached notify succes");
    },
    prepareNotify: function(owner, type, container, max_items) {
        this.owner = owner;
        this.type = type;
        this.container = container;
        this.max_items = max_items;
        //this.current_index = index;
        //this.builder_function = builder_function;
    },
    runBucle: function(timeout) {
        this.timeout = timeout;
        this.internalBucle();
    },
    internalBucle: function() {
        NotifyInterface.getLastNotify();
        setTimeout(NotifyInterface.internalBucle, NotifyInterface.timeout);
    },
    getLastNotify: function() {
        var params = "ajax=notify&owner=" + this.owner + "&types=" + this.type + "&last=" + this.current_index + "&max_items=" + this.max_items;
        //console.log("Asking server: " + params);
        executeCall("", params, function(data) {
            switch (getResultValue(data)) {
                case 1:
                    var last_index = getAjaxParsedValue(data, "last_index");
                    NotifyInterface.current_index = parseInt(last_index);
                    var dat = getAjaxParsedValue(data, "data");
                    dat = JSON.parse(dat);
                    //console.log("Last index: " + last_index);
                    for (var x in dat) {
                        //console.log(dat[x]);
                        NotifyInterface.builder(dat[x]);
                    }
                    break;
                case -666:
                    if (data.lenght > 10) {
                        console.log("Data error: ");
                    }
                    break;
            }
        });
    }
};
/*
 function mark(file) {
 console.log("File uploaded: " + file.upload.links.original);
 }
 function test() {
 ImgurUploader.setAndUpload("imagenes_progress", "label_desc", mark, true);
 }
 */


function replaceParamsAndRedirect(params) {
    redirectTo(replaceAndGetParams(params));
}

function replaceAndGetParams(params) {

    var search = location.search;
    var search2 = "";
    var host = location.origin;
    var path = location.pathname;
    var hash = location.hash;

    for (var param in params) {
        if (params[param] != null && params[param] != "") {
            search2 += param + "=" + encodeURIComponent(params[param]) + "&";
        }
    }
    search = replaceAll(search, "?", "");
    search = search.split("&");
    for (var param in search) {
        var splited = search[param].split("=");

        if (splited.length > 1) {
            var isin = false;
            for (var param in params) {
                if (splited[0] == param) {
                    isin = true;
                }
            }
            if (!isin && (splited[1] != "")) {
                search2 += splited[0] + "=" + splited[1] + "&";
            }
        }
    }

    var final_url = "?" + search2 + hash;
    //console.log(final_url);
    return final_url;
}

var ImageTool = {
    flag: "yes",
    xsize: 0,
    ysize: 0,
    boundx: 0,
    boundy: 0,
    x: 0,
    y: 0,
    w: 50,
    h: 50,
    current_prev: null,
    checkCoords: function() {
        if (parseInt($('#w').val())) {
            return true;
        }
        alert('Please select a crop region then press submit.');
        return false;
    },
    updateCoords: function(c) {
        //alert("updateCoords:" + this.flag);
        ImageTool.x = c.x;
        ImageTool.y = c.y;
        ImageTool.w = c.w;
        ImageTool.h = c.h;
        /*
         $('#x').val(ImageTool.x);
         $('#y').val(ImageTool.y);
         $('#w').val(ImageTool.w);
         $('#h').val(ImageTool.h);
         */
        //alert("uopdate jcrop: " + ImageTool.x + "-" + ImageTool.y + "-" + ImageTool.w + "-" + ImageTool.h);
    },
    saveJcrop: function(input) {
        //alert("saveJcrop:" + this.flag);
        var data = JSON.parse($(input).val());

        //alert("saveJcrop jcrop: " + ImageTool.x + "-" + ImageTool.y + "-" + ImageTool.w + "-" + ImageTool.h);
        data["upload"]["jcrop"] = {
            "x": ImageTool.x, //$(current_prev).css("margin-left"),
            "y": ImageTool.y, //$(current_prev).css("margin-top"),
            "w": ImageTool.w, //$(current_prev).width(),
            "h": ImageTool.h, //$(current_prev).height()
            "style": $(ImageTool.current_prev).attr("style")//$(current_prev).height()
        };
        $(input).val(JSON.stringify(data));
    },
    resetJcrop: function(input) {
        //alert("resetJcrop:" + this.flag);
        var data = JSON.parse($(input).val());

        if (ImageTool.current_prev != null) {
            $(ImageTool.current_prev).attr("style", data.upload.jcrop.style);
        }

        $(input).val(JSON.stringify(data));
    },
    updatePreview: function(c) {
        //alert("updatePreview:" + ImageTool.flag);
        if (ImageTool.current_prev == null) {
            return;
        }
        if (parseInt(c.w) > 0) {
            if (ImageTool.xsize <= 0)
                return;
            if (ImageTool.ysize <= 0)
                return;

            var rx = ImageTool.xsize / c.w;
            var ry = ImageTool.ysize / c.h;

            //alert(ImageTool.boundx);
            $(ImageTool.current_prev).css({
                width: Math.round(rx * ImageTool.boundx) + 'px',
                height: Math.round(ry * ImageTool.boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
        }
    },
    /*
     setPreview: function(x, y, w, h, id) {
     current_prev = document.getElementById(id);
     this.updatePreview({
     "x": x,
     "y": y,
     "w": w,
     "h": h
     });
     },*/
    showJcrop: function(input) {
        //alert(this.flag);
        var jcrop = document.createElement("img");
        var input = document.getElementById(input);
        var data = JSON.parse($(input).val());
        var url = data.upload.links.original;

        if (url == "") {
            return;
        }
        this.current_prev = document.getElementById("image_preview_" + $(input).attr("id"));
        var prev_container = document.getElementById("prev_container_" + $(input).attr("id"));

        this.xsize = $(prev_container).width();
        this.ysize = $(prev_container).height();

        $(jcrop).attr("src", url);
        $(jcrop).appendTo("body");
        $(jcrop).dialog({
            title: "Recortar imagen",
            width: "auto",
            height: "auto",
            resizable: false,
            create: function() {
                $(this).css("maxHeight", 400);
                $(this).css("maxWidth", 400);
            },
            buttons: {
                "send": {
                    text: 'Recortar',
                    "class": 'save',
                    click: function() {
                        ImageTool.saveJcrop(input);
                        $(jcrop).dialog("close");
                    }
                },
                "cancel": {
                    text: 'Cancelar',
                    "class": 'cancel',
                    click: function() {
                        ImageTool.resetJcrop(input);
                        $(jcrop).dialog("close");
                    }
                }
            }
        });

        var x = 0;
        var y = 0;
        var w = 100;
        var h = 100;
        if (data.upload.jcrop != null) {
            x = parseInt(data.upload.jcrop.x);
            y = parseInt(data.upload.jcrop.y);
            w = parseInt(data.upload.jcrop.w);
            h = parseInt(data.upload.jcrop.h);
        }
        w += x;
        h += y;
        //alert("Load jcrop: " + x + "-" + y + "-" + w + "-" + h);
        $(jcrop).Jcrop({
            aspectRatio: 1,
            onChange: this.updatePreview,
            onSelect: this.updateCoords,
            setSelect: [x, y, w, h] //[ 60, 70, 540, 330 ]
        }, function() {
            // Use the API to get the real image size
            var bounds = this.getBounds();
            ImageTool.boundx = bounds[0];
            ImageTool.boundy = bounds[1];
            $(ImageTool.current_prev).width("auto");
            $(ImageTool.current_prev).height("auto");
            // Store the API in the jcrop_api variable
            //jcrop_api = this;

            // Move the preview into the jcrop container for css positioning
            //$preview.appendTo(jcrop_api.ui.holder);
        });
    },
    inputImageSelectURL: function(input) {
        //alert("inputImageSelectURL:" + this.flag);
        var square = document.getElementById("image_preview_" + input);
        var input = document.getElementById(input);

        var data = null;
        var url_old = "";
        try {
            data = JSON.parse($(input).val());
            url_old = data.upload.links.original;
        } catch (ex) {
        }

        var url = prompt("Ingrese la URL de la imagen:", url_old);
        if (url == null || url_old == url) {
            return;
        }

        data = {"upload": {"links": {"original": url}}};
        $(input).val(JSON.stringify(data));
        if (url != "") {
            $(square).attr("src", url);
            this.showJcrop($(input).attr("id"));
        } else {
            $(square).attr("src", "null");
        }
    },
    inputImageSelectFile: function(input) {
        //alert("inputImageSelectFile:" + this.flag);
        var square = document.getElementById("image_preview_" + input);
        var input = document.getElementById(input);
        ImgurUploader.setAndUpload("progress_" + $(input).attr("id"), null, function(val) {
            $(input).val(JSON.stringify(val));
            $(square).attr("src", val.upload.links.original);
            ImageTool.showJcrop($(input).attr("id"));
        }, false);
    }
};

var ImgurUploader = {
    count: 0,
    max: 0,
    multiple: false,
    progressBar: null,
    labelDescription: null,
    targetOnLoad: null,
    set: function(progressBar, labelDescription, targetOnLoad, multiple) {
        this.labelDescription = document.getElementById(labelDescription);
        this.progressBar = document.getElementById(progressBar);
        this.targetOnLoad = targetOnLoad;
        this.multiple = multiple;
    },
    setAndUpload: function(progressBar, labelDescription, targetOnLoad, multiple) {
        this.set(progressBar, labelDescription, targetOnLoad, multiple);
        this.upload();
    },
    upload: function() {
        var file = document.createElement("input");
        $(file).css("display", "none");
        $(file).attr("type", "file");
        if (this.multiple) {
            $(file).attr("multiple", "");
        }
        $(file).attr("name", "filesToUpload[]");
        $(file).appendTo("body");
        $(file).attr("onchange", "ImgurUploader.uploadMulti(this.files)");
        file.click();
    },
    uploadMulti: function(files) {
        this.count = 0;
        this.max = 0;
        for (var count in files) {
            this.uploadOne(files[count]);
        }
    },
    uploadOne: function(file) {
        var fd = new FormData();

        if (file.type == null || !file || !file.type.match(/image.*/)) {
            return;
        }
        ++this.max;
        $(ImgurUploader.progressBar).val(0);
        console.log("Prepare to upload: " + file.name);

        // wrote about it: https://hacks.mozilla.org/2011/01/how-to-develop-a-html5-image-uploader/
        fd.append("image", file); // Append the file
        fd.append("key", "6528448c258cff474ca9701c5bab6927"); // Get your own key http://api.imgur.com/
        var xhr = new XMLHttpRequest(); // Create the XHR (Cross-Domain XHR FTW!!!) Thank you sooooo much imgur.com
        xhr.upload.onprogress = function(D) {
            var porcentaje = D.loaded / D.total;
            $(ImgurUploader.labelDescription).html("Subiendo imagen [" + ImgurUploader.count + "/" + ImgurUploader.max + "]: " + file.name + " (" + Math.round(porcentaje * 100) + "%)");
            $(ImgurUploader.progressBar).val(porcentaje);
        }; //.closure(this);
        xhr.open("POST", "http://api.imgur.com/2/upload.json"); // Boooom!
        xhr.onload = function() {
            console.log("Se ha subido una imagen.");
            ++ImgurUploader.count;
            var values = JSON.parse(xhr.responseText);
            if (ImgurUploader.count >= ImgurUploader.max) {
                if (ImgurUploader.count > 1) {
                    $(ImgurUploader.labelDescription).html("Se han subido " + ImgurUploader.count + " imagenes.");
                } else {
                    $(ImgurUploader.labelDescription).html("Se ha subido la imagen.");
                }
            }
            if (ImgurUploader.targetOnLoad != null) {
                ImgurUploader.targetOnLoad(values);
            }
        };
        xhr.send(fd);
    }
};

function checkRequiredInputsTo(message_parent) {
    var ok = true;
    $("input, textarea, select").each(function() {
        if ($(this).attr("required") != null) {
            if ($(this).attr("name") != null) {
                if (getInputValue(this) == "") {
                    $("#label_" + $(this).attr("name")).removeClass("sty-label-green");
                    $("#label_" + $(this).attr("name")).addClass("sty-label-red");
                    showMessage('El campo <a class="sty-label-blue" href="javascript:$(\'#label_' + $(this).attr("name") + '\').goTo();">' + $(this).attr("name") + "</a> no debe quedar en blanco.", message_parent, "warn", "");
                    ok = false;
                } else {
                    $("#label_" + $(this).attr("name")).removeClass("sty-label-red");
                    $("#label_" + $(this).attr("name")).addClass("sty-label-green");
                }
            }
        }
    });
    return ok;
}
function checkRequiredInputs() {
    return checkRequiredInputsTo("submit_message");
}

function setElementDisableState(element, state) {
    if (!state) {
        $(element).addClass("sty-ui-disabled");
    } else {
        $(element).removeClass("sty-ui-disabled");
    }
}

function redirectToSection(section) {
    redirectTo(section + replaceAndGetParams());
}
function getHashParameter(name) {
    return getJavascriptParams()[name];
}
function getJavascriptParams() {
    var hash = location.hash.replace("#", "");
    var vars = {};
    hash = hash.split("&");
    for (var x in hash) {
        var values = hash[x].split("=");
        var name = values[0];
        var value = "";
        if (typeof (values[1] != 'undefined')) {
            value = values[1];
        }
        //alert(x + "\n" + name + "-" + value);
        vars[name] = value;
    }
    return vars;
}
function setNumElementInner(element, value) {
    var num = $(element).html();
    if (isNaN(num)) {
        num = 0;
    }
    num = parseInt(num);
    num += value;
    $(element).html(num);
}
function decrementElementInner(element) {
    var num = $(element).html();
    if (isNaN(num)) {
        num = 0;
    }
    num = parseInt(num);
    --num;
    $(element).html(num);
}
function getSign(element) {
    var num = $(element).html();
    if (isNaN(num)) {
        num = 0;
    }
    num = parseInt(num);
    if (num > 0) {
        $(element).html("+" + num);
        return;
    }
}
function paintSignElement(element) {
    var num = $(element).html();
    if (isNaN(num)) {
        num = 0;
    }
    num = parseInt(num);
    if (num > 0) {
        $(element).css("color", "green");
    }
    if (num < 0) {
        $(element).css("color", "red");
    }
    if (num == 0) {
        $(element).css("color", "gray");
    }
}
function getInputValue(element) {
    var id = $(element).attr("name");
    var val = $(element).val();
    if ($(element).attr("type") == "radio") {
        val = $('input[name=' + id + ']:checked').val();
    }
    if ($(element).attr("type") == "checkbox") {
        val = ($('input[name=' + id + ']:checked').val() != null) ? "true" : "false";
    }
    return val;
}
function getDataParams() {
    var data = "none=0";
    $("input, select, textarea").each(function() {
        var id = $(this).attr("name");
        var nosendempty = $(this).attr("nosendempty") != null;
        var val = getInputValue(this);
        if (id != null && ((nosendempty && (val != "" && val != null)) || !nosendempty)) {
            val = encodeURIComponent(val);
            data += "&" + id + "=" + val;
            //var notify = $('input[name=notify]:checked').val();
        }
    });
    return data;
}
function contains(text, find) {
    return (text.indexOf(find) != -1);
}
function setCaptcha(container) {
    executeCall("../utils/captcha.php", "", function(data) {
        $(container).html(data);
    });
}
function proccessTemplate(template, params) {
    var templateTXT = $(template).html();
    for (x in params) {
            
        var templateTXTR = replaceAll(templateTXT, "{" + x + "}", params[x]);
        if (templateTXTR === templateTXT) {
            templateTXTR = replaceAll(templateTXT, "< % = " + x + " % >", params[x]);
        }
        if (templateTXTR === templateTXT) {
            templateTXTR = replaceAll(templateTXT, "<%=" + x + "%>", params[x]);
        }
        if (templateTXTR === templateTXT) {
            templateTXTR = replaceAll(templateTXT, "&lt;%=" + x + "%&gt;", params[x]);
        }
        templateTXT = templateTXTR;
    }
    return templateTXT;
}
function proccessTemplateTo(element, template, params) {
    $(element).html(proccessTemplate(template, params));
}

function replaceAll(text, find, replace) {
    return text.split(find).join(replace);
}

function removeElement(element) {
    $(element).fadeOut(500, function() {
        $(this).remove();
    });
}

function addElement(element) {
    $(element).fadeIn(500);
}


function getResultMessage(data) {
    return getAjaxParsedValue(data, "message");
}

function getResultValue(data) {
    return parseData(getAjaxParsedValue(data, "result"));
}

function parseData(data) {
    if (isNaN(data)) {
        return -666;
    }
    return parseInt(data);
}

function getAjaxParsedValue(data, ident) {
    var segments = data.split("<[@]>");
    for (x in segments) {
        var pair = segments[x].split("<=@=>");
        var id = pair[0];
        var value = pair[1];
        if (id.indexOf(ident) != -1) {
            return value;
        }
    }
    return "-666";
}
function setSelectValue(select, title) {
    var selected = false;
    $(select).find("option:contains('" + values + "')").each(function() {
        if ($(this).text() == title) {
            $(this).attr("selected", "selected");
            selected = true;
        }
    });
    if (!selected) {
        addSelectValue(select, title, title);
    }
}
function setSelectValueByValue(select, value) {
    var selected = false;
    $(select).children("option").each(function() {
        if ($(this).val() == value) {
            $(this).attr("selected", "selected");
            selected = true;
        }
    });
    if (!selected) {
        addSelectValue(select, value, value);
    }
}
function addSelectValue(select, value, title) {
    var item = document.createElement("option");
    item.value = value;
    $(item).html(title);
    $(select).append(item);
}
function reloadPage() {
    window.location.reload();
}
function redirectTo(url) {
    window.location.href = url;
}
function redirect() {
    window.location.href = "../home/";
}

function executeCall(url, params, target) {
    $.ajax({
        type: 'POST',
        url: url,
        data: params,
        success: target
    });
}

function getMisc(target) {
    $.ajax({
        url: "../empresas/get_misc.php",
        success: target
    });
}

function setSeccions(loadSeccion) {
    var seccion = document.getElementById('seccion');
    var subseccion = document.getElementById('subseccion');

    subseccion.disabled = true;

    getMisc(function(data) {
        eval(data);
        setTimeout("eval('" + data + "')", 0);
        var seccion = document.getElementById('seccion');
        var subseccion = document.getElementById('subseccion');

        for (y in seccions_subseccions) {
            seccion.innerHTML += "<option value=\"" + y + "\">" + y + "</option>";
        }

        $("#input_find").autocomplete({
            source: all_elements
        });

        if (loadSeccion != "") {
            setSelectValue("#seccion", loadSeccion);
        }
    });
}

function setSubSeccion(loadSubseccion)
{
    getMisc(function(data) {
        eval(data);
        setTimeout("eval('" + data + "')", 0);
        var seccion = document.getElementById('seccion');
        var subseccion = document.getElementById('subseccion');

        subseccion.disabled = true;
        var selSecc = seccion.options[seccion.selectedIndex].text;
        if (seccions_subseccions[selSecc])
        {
            var txt = '<option value="-1">Seleccione subsección</option>';
            var subs = seccions_subseccions[selSecc].split('|');
            for (i in subs) {
                txt += '<option value="' + subs[i] + '">' + subs[i] + '</option>';
            }
            subseccion.innerHTML = txt;
            subseccion.disabled = false;
        }

        if (loadSubseccion != "") {
            setSelectValue("#subseccion", loadSubseccion);
        }
    });
}