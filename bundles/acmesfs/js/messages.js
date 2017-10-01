/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function destroyMessage(button){
    $($(button).parent()).fadeOut(500);
    var element = $(button).parent().parent();
    $(element).slideUp(1000, function(){
        $(this).remove();
    });
}

function messageIncrement(){
    ++messageCount;
}

var messageCount = 0;
function showMessage(content, parent, class_style, icon){
    messageIncrement();
    var iconIMG = "";
    if(class_style == ""){
        class_style = "info";
    }
    if(icon != ""){
        iconIMG = '<img src="../img_icons/' + icon + '.png" align="top"/> ';
    }
    
    var message = ''
    + '<div id="message_' + messageCount + '" class="sty-box-message-' + class_style + '" style="display:none">'
    + '<div id="message_content_' + messageCount + '" class="sty-box-padding-10" style="display:none">'
    + iconIMG
    + '<div onclick="destroyMessage(this)" style="float:right" class="closeButton"></div>'
    + content
    + '</div>'
    + '</div>'
    + '';

    document.getElementById(parent).innerHTML = message;
    
    $('#message_content_' + messageCount).fadeIn(2000);
    var element = $('#message_' + messageCount);
    $(element).slideDown(1000);
}