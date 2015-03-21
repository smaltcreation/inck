$(document).ready(function(){
    var modal = $('#subscriptions-manager');

    modal.on('show.bs.modal', function() {
        modal.find('.tab-pane').each(function() {
            update(Routing.generate('inck_subscription_manager_paginator', {
                alias: $(this).attr('data-alias'),
                page: 1
            }), $(this));
        });
    });

    modal.on('click', 'ul.pagination a', function(e) {
        e.preventDefault();
        update($(this).attr('href'), $(this).closest('.tab-pane'));
    });

    function update(url, tab) {
        $.ajax({
            url: url,
            dataType: 'html'
        }).done(function(data){
            tab.html(data);
        });
    }
});
