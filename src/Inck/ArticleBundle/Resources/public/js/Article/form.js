$(document).ready(function(){
    var toggleArticleHeader = $('.toggle-article-header');
    toggleArticleHeader.click(function(){
        $('#article-header').stop().slideToggle('slow');
    });
    // Gestion des tags
    var input = $("#inck_articlebundle_article_tags");

    input.select2({
        tags: [],
        tokenSeparators: [",", ";"],
        minimumInputLength: 1,
        maximumSelectionSize: 10,
        width: 'container',
        initSelection : function(element, callback){
            var tags = input.val().split(',');
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
            if(query.term == '') return;
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
            return { id: term, text: term };
        }
    });
});

// Gestion des brouillons automatiques
var draftInterval = setInterval(saveDraft, 30000);
var draftErrors = 0;

// Tente d'enregistrer un brouillon
function saveDraft(){
    for(var i in CKEDITOR.instances){
        CKEDITOR.instances[i].updateElement();
    }

    var form = $('#content').find('form[data-article-id]:first');
    var articleId = form.attr('data-article-id');
    var data = new FormData(form[0]);
    var url = null, callback = false;

    if(articleId){
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
        if(callback){
            changeType(form, result);
        }
    }).error(function(){
        draftErrors++;
        if(draftErrors >= 3){
            clearInterval(draftInterval);
        }
    });
}

function changeType(form, result){
    if(result.valid){
        form.attr({
            'data-article-id': result.id,
            'action': Routing.generate('inck_article_article_edit', {
                id: result.id
            })
        });
    }
}