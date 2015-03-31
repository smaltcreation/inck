$(document).ready(function() {
    var notifications = [];
    var displayingNotification = false;

    var loading = $('<div>')
        .attr('id', 'notifications-loading')
        .css('display', 'none');

    $('body').append(loading);

    $(window.server).bind('notification.received', function(e, data){
        notifications.push(data);

        if (!displayingNotification) {
            displayNotification();
        }
    });

    function displayNotification() {
        if (notifications.length == 0) {
            return false;
        }

        displayingNotification = true;
        var data = notifications.shift();

        loading.append(data.html).waitForImages(function(){
            loading.empty();

            var notification = new NotificationFx({
                message: data.html,
                layout: 'other',
                ttl: 6000,
                effect: 'thumbslider',
                type: 'notice',
                onClose: function() {
                    setTimeout(function(){
                        displayingNotification = false;
                        displayNotification();
                    }, 1000);
                }
            });

            notification.show();
            window.server.call('notification.displayed', {
                id: data.id,
                date: new Date().toLocaleString()
            });
        });
    }
});
