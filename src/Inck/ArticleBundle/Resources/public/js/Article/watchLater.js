$(document).ready(function(){
    var content = $('#content');
    content.on('click', '.btn-watch-later', function(){
        watchLater($(this));
    });

    content.on('click', '.btn-unwatch-later', function(){
        watchLater($(this));
        $(this).closest( ".row").hide('slow');
    });

    function watchLater(clickedButton){
        var id                  = getArticleId(clickedButton),
            clickedIconClass    = getIconClass(clickedButton),
            icon                = getButtonIcon(clickedButton),
            text                = getButtonText(clickedButton);

        clickedButton.prop('disabled', true);
        icon.attr('class', 'fa fa-circle-o-notch fa-spin');

        $.ajax({
            url: Routing.generate('inck_article_article_watchLater', {
                id: id
            }),
            dataType: 'json'
        }).done(function(){
            resetButtonState(clickedButton, clickedIconClass);
            clickedButton.toggleClass('watch-later');
            if(clickedButton.hasClass('watch-later')) {
                icon.attr('class', 'fa fa-check');
                text.text('Ajouté à la liste');
            }
            else {
                icon.attr('class', 'fa fa-clock-o');
                text.text('À regarder plus tard');
            }
        }).error(function(result){
            // Error
            resetButtonState(clickedButton, clickedIconClass);
            $('#article-error').remove();
            var modal = $(result.responseJSON.modal);
            modal.appendTo('body').modal();
        })
        ;
    }

    function getArticleId(button){
        return button
            .attr('data-id')
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