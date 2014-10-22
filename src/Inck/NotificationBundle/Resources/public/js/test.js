$(document).ready(function() {
    var displayed = false;
    $('h1').click(function() {
        if(displayed) {
            return false;
        }

        displayed = true;
        var notification = new NotificationFx({
            message: '<div class="ns-thumb"><img src="images/tests/user.png" style="width: 64px;"></div><div class="ns-content"><p><a href="#">Nicolas Rouvière</a> s\'est abonné à vous.</p></div>',
            layout: 'other',
            ttl: 6000,
            effect: 'thumbslider',
            type: 'notice',
            onClose: function(){
                displayed = false;
            }
        });

        notification.show();
    });
});
