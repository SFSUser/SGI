function hide(id){
    var element = document.getElementById(id);
    element.style.display = "none";
}
function show(id){
    var element = document.getElementById(id);
    element.style.display = "block";
}
function hideShow(id){
    var element = document.getElementById(id);
    if(element.style.display == "block"){
        element.style.display = "none";
    }else{
        element.style.display = "block";
    }
}