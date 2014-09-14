var server      = null;
var connected   = false;
var session     = null;

$(document).ready(function(){
    server = Clank.connect(_CLANK_URI);

    server.on('socket/connect', function(s){
        connected   = true;
        session     = s;
    });

    server.on('socket/disconnect', function(){
        connected   = false;
        session     = null;
    });
});