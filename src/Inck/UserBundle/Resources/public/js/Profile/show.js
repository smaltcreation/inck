$(document).ready(function(){
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
