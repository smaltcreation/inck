$(document).ready(function(){
    var server = Clank.connect(_CLANK_URI);

    server.on("socket/connect", function(session){
        //session is an Autobahn JS WAMP session.

        console.log("Successfully Connected!");
        console.log(session);
    });

    server.on("socket/disconnect", function(error){
        //error provides us with some insight into the disconnection: error.reason and error.code

        console.log("Disconnected for " + error.reason + " with code " + error.code);
    });
});