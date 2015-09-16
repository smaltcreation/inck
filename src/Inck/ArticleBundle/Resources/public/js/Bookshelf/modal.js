/**
 * Created by jitrixis on 14/09/2015.
 */
$("form[name='bookshelf']").submit(function (event) {
    event.preventDefault();
    add2bookshelf();
});
$(".btn-bookshelf-remove").click(function (event) {
    event.preventDefault();
    remove2bookshelf(event.currentTarget);
});


function add2bookshelf(){
    var data = a2bData();
    if(!data){
        a2bError(data, "Les données sont incorrects !");
        return false;
    }
    $.ajax({
        method: "PUT",
        url: Routing.generate('inck_article_bookshelf_article', {
            articleId: data.article,
            bookshelfId: data.bookshelf
        }),
        dataType: 'json'
    }).done(function() {
        a2bSuccess(data);
    }).fail(function(jqXHR) {
        try{
            var resp = JSON.parse(jqXHR.responseText);
            a2bError(data, resp.message);
        }catch(e){
            a2bError(data, "Une erreur est survenue, réessayez plus tard !");
        }
    })
}

function remove2bookshelf(child){
    var data = r2bData(child);
    if(!data){
        r2bError(data, child, "Les données sont incorrects !");
        return false;
    }
    $.ajax({
        method: "DELETE",
        url: Routing.generate('inck_article_bookshelf_article', {
            articleId: data.article,
            bookshelfId: data.bookshelf
        }),
        dataType: 'json'
    }).done(function() {
        r2bSuccess(data, child);
    }).fail(function(jqXHR) {
        try{
            var resp = JSON.parse(jqXHR.responseText);
            r2bError(data, child, resp.message);
        }catch(e){
            r2bError(data, child, "Une erreur est survenue, réessayez plus tard !");
        }
    })
}

/**
 * Get add 2 bookshelf data from selectors
 * @returns {{article, bookshelf: (*|jQuery)}}
 */
function a2bData(){
    var data = {};

    data['article'] = $("#bookshelf-title").data("article-id");
    data['bookshelf'] = $("#bookshelfs-list option[value='"+$("#bookshelf-title").val()+"']").data("bookshelf-id");
    data['bookshelfTitle'] = $("#bookshelf-title").val();

    if(!data.article || !data.bookshelf){
        return false;
    }
    return data;
}

/**
 * Get remove to bookshelf data from seletors
 */
function r2bData(child){
    var data = {};

    data['article'] = $(child).parent("table").data("article-id");
    data['bookshelf'] = $(child).parent("tr").data("bookshelf-id");

    if(!data.article || !data.bookshelf){
        return false;
    }
    return data;
}

function a2bSuccess(data){
    var html = '<tr class="bookshelf-current-list" data-bookshelf-id="'+data.bookshelf+'"><td>';
    html += '<i class="fa fa-book"></i>'+data.bookshelfTitle;
    html += '<a href="#" role="button" class="btn btn-default btn-xs pull-right btn-bookshelf-remove" data-toggle="tooltip" data-placement="right" title="Remove"><span aria-hidden="true">&times;</span></a>';
    html += '</td></tr>';

    $("#bookshelf-current tbody").append(html);

    $('.bookshelf-current-list[data-bookshelf-id="'+data.bookshelf+'"] .btn-bookshelf-remove').click(function (event) {
        event.preventDefault();
        remove2bookshelf(event.currentTarget);
    });

    $("#bookshelf-title").val("");
}

function a2bError(data, message){
    $("#bookshelf-error").remove();
    $("#bookshelf-title").parent("div").addClass('has-error');
    $("#bookshelf-title").parent("div").insertAfter('<span class="help-block" id="bookshelf-error"><ul class="list-unstyled"><li><span class="fa fa-ban"></span>'+message+'</li></ul></span>');
    $("#bookshelf-title").val("");
}

function r2bSuccess(data, child){
    $(child).parent("tr").hide(400, function() {
        $(this).remove();
    });
}

function r2bError(data, child, message){
    //TODO: don't know what to do
}