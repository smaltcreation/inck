$(document).ready(function(){
    $('.btn-subscribe').click(function(){
        subscribe($(this));
    });

    $(document).bind('inck.subscription.saved', function(e, data){
        subscriptionSaved(data.id);
    });
});

function subscribe(button){
    saveState(button);
    getButtonIcon(button).attr('class', 'fa fa-circle-o-notch fa-spin');
    window.server.call('subscription.save', getEntityData(button));
}

function saveState(button){
    button.prop('disabled', true);
    button.data('previousState', {
        iconClass: getButtonIcon(button).attr('class'),
        text: getButtonText(button).text()
    });
}

function getButtonIcon(button){
    return button.find('i:first');
}

function getEntityData(button){
    return {
        entityAlias: button.attr('data-entity-alias'),
        entityId: button.attr('data-entity-id'),
        id: button.attr('id')
    }
}

function getButtonText(button){
    return button.find('span.btn-text:first');
}

function resetButtonState(button){
    button.prop('disabled', false);
    var data = button.data('previousState');
    getButtonIcon(button).attr('class', data.iconClass);
    getButtonText(button).text(data.text);
}

function subscriptionSaved(id){
    var button = $('#' + id);
    var icon = getButtonIcon(button);
    var text = getButtonText(button);

    resetButtonState(button);
    button.toggleClass('subscribed');

    if(button.hasClass('subscribed')){
        icon.attr('class', 'fa fa-check');
        text.text('Vous êtes abonné');
    } else {
        icon.attr('class', 'fa fa-thumb-tack');
        text.text('S\'abonner');
    }
}

function subscriptionError(button){
    resetButtonState(button, button);
    $('#subscription-error').remove();
}