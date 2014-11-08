$(document).ready(function(){
    var notifications = [];
    var notificationDisplayed = false;

    $(document).bind('notification.received', function(e, data){
        notifications.push(data.html);

        if(!notificationDisplayed) {
            displaySubscriberNotification();
        }
    });

    function displaySubscriberNotification(){
        if (notifications.length == 0) {
            return false;
        }

        notificationDisplayed = true;
        var message = notifications.shift();

        var notification = new NotificationFx({
            message: message,
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
    }
});