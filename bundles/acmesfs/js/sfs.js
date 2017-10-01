/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var Utils = {
    showBootstrapNotify: function(parent, type, title, desc) {
        var date = new Date();
        var str = '<div style="display:none" class="alert alert-' + type + '" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><i>{time}</i> - <strong>{main}</strong> {desc}</div>';
        str = str.replace("{main}", title).replace("{desc}", desc).replace("{time}", date.toLocaleTimeString());
        $(parent).find(".alert:last").remove();
        var alert = $(str)
        $(parent).prepend(alert);
        $(alert).slideDown();
    },
    toggleBetweenElements: function(element1, element2, state_first) {
        if (state_first !== undefined) {
            if (state_first) {
                $(element1).show();
                $(element2).hide();
            } else {
                $(element1).hide();
                $(element2).show();
            }
        } else {
            if ($(element1).css("display") === "block") {
                $(element1).hide();
                $(element2).show();
            } else {
                $(element1).show();
                $(element2).hide();
            }
        }
    }
};
