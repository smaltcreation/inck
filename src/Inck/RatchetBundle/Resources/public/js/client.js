(function(window) {
    function InckRatchetServer(uri) {
        this.uri = uri;
        this.connection = null;
        this.timeout = null;
        this.messages = [];

        this.connect();
    }

    InckRatchetServer.prototype.connect = function() {
        this.connection = new WebSocket(this.uri);
        var self = this;

        this.connection.onopen = function() {
            clearTimeout(self.timeout);

            while (self.messages.length != 0) {
                var message = self.messages.shift();
                self.connection.send(message);
            }
        };

        this.connection.onclose = function() {
            this.timeout = setTimeout(function() {
                self.connect();
            }, 10000);
        };

        this.connection.onmessage = function(message) {
            var data = JSON.parse(message.data);
            $(self).trigger(data.method, data.parameters);
        };

        this.connection.onerror = function(error) {
            console.log(error);
        };
    };

    InckRatchetServer.prototype.call = function(method, parameters) {
        if (parameters == undefined) {
            parameters = null;
        }

        var message = JSON.stringify({
            method: method,
            parameters: parameters
        });

        if (this.connection == null || this.connection.readyState != 1) {
            this.messages.push(message);
        } else {
            this.connection.send(message);
        }
    };

    window.InckRatchetServer = InckRatchetServer;
})(window);
