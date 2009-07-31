/* 
 * DooPHP demos
 */
var fontsize = 190;
function smaller(){
    fontsize *=0.9
    $("body").css("font-size", fontsize+'%');
}
function larger(){
    fontsize *=1.1
    $("body").css("font-size", fontsize+'%');
}


