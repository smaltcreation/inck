$(document).ready(function(){
    $('.btn-article-send').click(function() {
        var btn = $(this);
        swal({
            title: "Êtes-vous sûr ?",
            text: "Vous ne pourrez pas remodifier votre article, il sera envoyé à la modération !",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d3e397",
            confirmButtonText: "Oui, envoyer mon article !",
            cancelButtonText: "Annuler",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: Routing.generate('inck_article_article_send', {
                        id: btn.attr('data-article-id'),
                        slug: btn.attr('data-article-slug')
                    }),
                    dataType: 'json'
                }).done( function(data){
                    if('success' in data && data.success == false) {
                        swal("Erreur !", data.message, "error");
                    }
                    else {
                        swal("Envoyé !", "Votre article a été envoyé à la modération avec succès !", "success");
                        btn.closest('tr').hide(400, function() {
                            $(this).remove();
                        });
                    }
                });
            } else {
                swal("Annulé", "Votre article n'a pas été envoyé !", "error");
            }
        });
    });

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
