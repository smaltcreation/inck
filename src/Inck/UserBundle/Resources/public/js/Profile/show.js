$(document).ready(function(){
    $('.btn-article-publish').click(function() {
        var btn = $(this);
        swal({
            title: "Êtes-vous sûr ?",
            text: "Votre article va être publié.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d3e397",
            confirmButtonText: "Oui, publier mon article !",
            cancelButtonText: "Annuler",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (isConfirm) {
                location.href = Routing.generate('inck_article_article_publish', {
                    id: btn.attr('data-article-id'),
                    slug: btn.attr('data-article-slug')
                })
            } else {
                swal("Annulé", "Votre article n'a pas été publié !", "error");
            }
        });
    });

    $('.btn-article-delete').click(function() {
        var btn = $(this);
        swal({
            title: "Êtes-vous sûr ?",
            text: "Cet article sera supprimé définitevement, vous ne pourrez pas le récupérer !",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f15645",
            confirmButtonText: "Oui, supprimer mon article !",
            cancelButtonText: "Annuler",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: Routing.generate('inck_article_article_delete', {
                        id: btn.attr('data-article-id'),
                        slug: btn.attr('data-article-slug')
                    }),
                    dataType: 'json'
                }).done(function(data) {
                    swal("Supprimé !", data.message, "success");
                    btn.closest('tr').hide(400, function() {
                        $(this).remove();
                    })
                }).fail(function(jqXHR) {
                    try{
                        var data = JSON.parse(jqXHR.responseText);
                        swal("Erreur !", data.message, "error");
                    }catch(e){
                        swal("Erreur !", "Une erreur est survenue lors de la suppression ! Merci de réessayer plus tard.", "error");
                    }
                })
            } else {
                swal("Annulé", "Votre article n'a pas été supprimé !", "error");
            }
        });
    });

    $('.btn-bookshelf-delete').click(function() {
        var btn = $(this);
        swal({
            title: "Êtes-vous sûr ?",
            text: "Cette étagère sera supprimée définitevement, vous ne pourrez pas le récupérer !",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f15645",
            confirmButtonText: "Supprimer cette étagère !",
            cancelButtonText: "Annuler",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    method: "DELETE",
                    url: Routing.generate('inck_article_bookshelf_delete', {
                        id: btn.attr('data-bookshelf-id')
                    }),
                    dataType: 'json'
                }).done(function(data) {
                    swal("Supprimé !", data.message, "success");
                    btn.closest('tr').hide(400, function() {
                        $(this).remove();
                    })
                }).fail(function(jqXHR) {
                    try{
                        var data = JSON.parse(jqXHR.responseText);
                        swal("Erreur !", data.message, "error");
                    }catch(e){
                        swal("Erreur !", "Une erreur est survenue lors de la suppression ! Merci de réessayer plus tard.", "error");
                    }
                })
            } else {
                swal("Annulé", "L'étagère n'a pas été supprimé !", "error");
            }
        });
    });

    $('#content').on('click', '.btn-modal', function(e) {
        e.preventDefault();

        $('#article-preview').modal({
            remote: $(this).attr('href')
        });
    }).on('click', '.bookshelf-modal', function(e) {
        e.preventDefault();

        $('#bookshelf-preview').modal({
            remote: $(this).attr('href')
        });
    });

    $('body').on('hidden.bs.modal', '#article-preview', function() {
        $(this).removeData('bs.modal');
    });
});
