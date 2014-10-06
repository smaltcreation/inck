window.server.connection = new WebSocket(window.server.uri);

window.server.call = function(method, parameters){
    if(parameters === undefined) {
        parameters = null;
    }

    window.server.connection.send(JSON.stringify({
        method: method,
        parameters: parameters
    }));
};

window.server.connection.onmessage = function(message){
    var data = JSON.parse(message.data);
    $(document).trigger(data.method, data.parameters);
};
