$(document).ready(function(){
    $('.btn-subscribe').click(function(){
        subscribe($(this));
    });

    function subscribe(clickedButton){
        var entityAlias         = getEntityAlias(clickedButton),
            entityId            = getEntityId(clickedButton),
            clickedIconClass    = getIconClass(clickedButton),
            icon                = getButtonIcon(clickedButton),
            text                = getButtonText(clickedButton);

        clickedButton.prop('disabled', true);
        icon.attr('class', 'fa fa-circle-o-notch fa-spin');

        session.call("subscription/save", [entityAlias, entityId]).then(function(){
                resetButtonState(clickedButton, clickedIconClass);
                clickedButton.toggleClass('subscribed');
                if(clickedButton.hasClass('subscribed')){
                    icon.attr('class', 'fa fa-check');
                    text.text('Vous êtes abonné');
                } else {
                    icon.attr('class', 'fa fa-thumb-tack');
                    text.text('S\'abonner');
                }
            }, function(error, description){
                console.log("RPC Error", error, description);
                resetButtonState(clickedButton, clickedIconClass);
                $('#subscription-error').remove();
            }
        );
    }

    function getEntityAlias(button){
        return button.attr('data-entity-alias');
    }

    function getEntityId(button){
        return button.attr('data-entity-id');
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