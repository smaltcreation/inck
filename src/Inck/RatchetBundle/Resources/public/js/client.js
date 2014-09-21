window.server.connection = new WebSocket(window.server.uri);

window.server.call = function(method, message){
    message.method = method;
    message = JSON.stringify(message);
    window.server.connection.send(message);
};

window.server.connection.onmessage = function(message){
    var data = JSON.parse(message.data);
    $(document).trigger('inck.' + data.method, data.options);
};