$(document).ready(function(){
    var manager = $('#entities-manager');

    manager.find('.panel').each(function(){
        update(Routing.generate('inck_core_admin_paginator', {
            alias: $(this).attr('data-alias'),
            page: 1
        }), $(this).find('.panel-data'));
    });

    manager.on('click', 'ul.pagination a', function(e){
        e.preventDefault();
        update($(this).attr('href'), $(this).closest('.panel-data'));
    });

    function update(url, panel) {
        $.ajax({
            url: url,
            dataType: 'html'
        }).done(function(data){
            panel.html(data);
        });
    }

    $('#content').on('click', '.btn-modal', function(e) {
        e.preventDefault();

        $('#article-preview').modal({
            remote: $(this).attr('href')
        });
    });

    $('body').on('hidden.bs.modal', '#article-preview', function() {
        $(this).removeData('bs.modal');
    });
});