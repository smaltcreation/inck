$(document).ready(function(){
    $('.notifications-popover-toggle').popover({
        html: true,
        placement: 'bottom',
        trigger: 'hover',
        content: function() {
            return $.ajax({
                url: Routing.generate('inck_notification_manager_history', {
                    page: 1
                }),
                dataType: 'html',
                async: false
            }).responseText;
        }
    });
});
