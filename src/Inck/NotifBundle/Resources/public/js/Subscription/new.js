$(document).ready(function(){
    $('.btn-subscribe').click(function(){
        subscribe($(this));
    });

    function subscribe(clickedButton){
        return false;
        var entityName          = getEntityName(clickedButton),
            entityId            = getEntityId(clickedButton),
            clickedIconClass    = getIconClass(clickedButton),
            icon                = getButtonIcon(clickedButton),
            text                = getButtonText(clickedButton);

        clickedButton.prop('disabled', true);
        icon.attr('class', 'fa fa-circle-o-notch fa-spin');

        $.ajax({
            url: Routing.generate('inck_notif_subscription_new', {
                entity: entityName,
                id: entityId
            }),
            dataType: 'json'
        }).done(function(){
            resetButtonState(clickedButton, clickedIconClass);
            clickedButton.toggleClass('subscribed');
            if(clickedButton.hasClass('subscribed')) {
                icon.attr('class', 'fa fa-check');
                text.text('Vous êtes abonné');
            }
            else {
                icon.attr('class', 'fa fa-thumb-tack');
                text.text('S\'abonner');
            }
        }).error(function(result){
            // Error
            resetButtonState(clickedButton, clickedIconClass);
            $('#subscription-error').remove();
            var modal = $(result.responseJSON.modal);
            modal.appendTo('body').modal();
        });
    }

    function getEntityName(button){
        return button.attr('data-entity');
    }

    function getEntityId(button){
        return button.attr('data-id');
    }

    function getIconClass(button){
        return getButtonIcon(button).attr('class');
    }

    function getButtonIcon(button){
        return button.find('i:first');
    }

    function getButtonText(button){
        return button.find('.btn-text:first');
    }

    function resetButtonState(button, iconClass){
        getButtonIcon(button).attr('class', iconClass);
        button.prop('disabled', false);
    }
});