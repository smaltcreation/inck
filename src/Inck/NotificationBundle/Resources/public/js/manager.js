$(document).ready(function(){
    var link = $('.notifications-popover-toggle');

    link.click(function(e) {
        e.preventDefault();
    });

    link.popover({
        html: true,
        placement: 'bottom',
        content: function() {
            return $.ajax({
                url: Routing.generate('inck_notification_manager_popover'),
                dataType: 'html',
                async: false
            }).responseText;
        }
    });
});
