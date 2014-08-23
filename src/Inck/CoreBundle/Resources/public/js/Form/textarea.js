function textarea_max_length(badge){
    var input = badge.prev('textarea');

    $(document).ready(function(){
        badge.addClass('alert-success').tooltip({
            container: 'body',
            placement: 'left',
            title: 'Caractères restants'
        });

        var parent = input.closest('div.form-group');
        if(parent.hasClass('has-error')){
            var help = parent.find('ul.help-block:first');
            var top = badge.position().top - 5;

            badge.css({
                bottom: 'auto',
                top: top - help.outerHeight()
            });
        }

        textarea_max_length_update_value(input, badge);
    });

    input.bind('input propertychange', function(){
        textarea_max_length_update_value(input, badge);
    });
}

function textarea_max_length_update_value(input, badge){
    var length = input.val().length;
    var max = input.attr('maxlength');
    var value = max - length;

    badge.text(value);

    var stepDanger = max / 100 * 5;
    var stepWarning = max / 100 * 15;

    if(value > stepWarning){
        badge.attr('class', 'badge alert-success');
    } else if(value <= stepWarning && value > stepDanger){
        badge.attr('class', 'badge alert-warning');
    } else if(value <= stepDanger) {
        badge.attr('class', 'badge alert-danger');
    }
}

$(document).ready(function(){
    $('textarea + span.badge').each(function(){
        textarea_max_length($(this));
    });
});