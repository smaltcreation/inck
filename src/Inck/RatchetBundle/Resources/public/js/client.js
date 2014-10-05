window.server.connection = new WebSocket(window.server.uri);

window.server.call = function(method, parameters){
    if(parameters === undefined) {
        parameters = null;
    }

    message = JSON.stringify({
        method: method,
        parameters: parameters
    });
    window.server.connection.send(message);
};

window.server.connection.onmessage = function(message){
    var data = JSON.parse(message.data);
    $(document).trigger('inck.' + data.method, data.options);
};
