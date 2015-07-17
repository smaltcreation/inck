$(document).ready(function() {
    // Champs du formulaire
    var fields = {
        tags: $('#inck_articlebundle_article_tags'),
        content: $('#inck_articlebundle_article_content')
    };

    // Contenu de l'article
    fields.content.summernote({
        height: 600
    });

    // Gestion des tags
    fields.tags.select2({
        tags: [],
        tokenSeparators: [",", ";"],
        minimumInputLength: 1,
        maximumSelectionSize: 10,
        width: 'container',
        initSelection : function(element, callback){
            var tags = fields.tags.val().split(',');
            var data = [];

            $.each(tags, function(key, value){
                data.push({
                    id: value,
                    text: value
                });
            });

            callback(data);
        },
        query: function(query){
            if(query.term == '') {
                return;
            }

            $.ajax({
                url: Routing.generate('inck_article_tag_autocomplete', {
                    mode: 'name',
                    name: query.term
                }),
                dataType: 'json'
            }).done(function(data){
                query.callback(data);
            });
        },
        createSearchChoice: function(term){
            return {
                id: term,
                text: term
            };
        }
    });

    // Publier l'article
    $('#inck_articlebundle_article_actions_publish').click(function(e) {
        console.log('toto');
        e.preventDefault();

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
                $('#inck-article-article-form').submit();
            } else {
                swal("Annulé", "Votre article n'a pas été publié !", "error");
            }
        });
    });

    // Reset
    $('#inck_articlebundle_article_actions_cancel').click(function(){
        $('.select2-container').select2('data', null);
        $('.singleupload-buttonbar').find('.cancel:first').click();
        $('#inck_articlebundle_article_language').val('fr').select2('val', 'fr');
        $('#inck_articlebundle_article_content').code('');
    });
});

// Gestion des brouillons automatiques
var draftInterval = setInterval(saveDraft, 30000);
var draftErrors = 0;

// Tente d'enregistrer un brouillon
function saveDraft() {
    var content = $('#inck_articlebundle_article_content');
    content.val(content .code());

    var form = $('#content').find('form[data-article-id]:first');
    var articleId = form.attr('data-article-id');
    var data = new FormData(form[0]);
    var url = null, callback = false;

    if(articleId) {
        url = Routing.generate('inck_article_draft_edit', {
            id: articleId
        });
    } else {
        url = Routing.generate('inck_article_draft_new');
        callback = true;
    }

    $.ajax({
        url: url,
        type: 'POST',
        processData: false,
        contentType: false,
        data: data,
        dataType: 'json'
    }).done(function(result){
        if(callback) {
            changeType(form, result);
        }
    }).error(function(){
        draftErrors++;
        if(draftErrors >= 3){
            clearInterval(draftInterval);
        }
    });
}

function changeType(form, result) {
    if(result.valid){
        form.attr({
            'data-article-id': result.id,
            'action': Routing.generate('inck_article_article_edit', {
                id: result.id,
                slug: result.slug
            })
        });
    }
}
