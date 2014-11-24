$(document).ready(function(){
    var notifications = [];
    var notificationDisplayed = false;
    var loading = $('<div>')
        .attr('id', 'notifications-loading')
        .css('display', 'none');

    $('body').append(loading);

    $(document).bind('notification.received', function(e, data){
        console.log(data);
        notifications.push(data);

        if(!notificationDisplayed) {
            displaySubscriberNotification();
        }
    });

    function displaySubscriberNotification(){
        if (notifications.length == 0) {
            return false;
        }

        notificationDisplayed = true;
        var data = notifications.shift();

        loading.append(data.html).waitForImages(function(){
            loading.empty();

            var notification = new NotificationFx({
                message: data.html,
                layout: 'other',
                ttl: 6000,
                effect: 'thumbslider',
                type: 'notice',
                onClose: function(){
                    notificationDisplayed = false;
                    displaySubscriberNotification();
                }
            });

            notification.show();
            window.server.call('notification.displayed', {
                id: data.id,
                date: new Date()
            });
        });
    }
});
