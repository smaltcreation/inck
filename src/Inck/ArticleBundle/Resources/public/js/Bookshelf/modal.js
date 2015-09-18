/**
 * Created by jitrixis on 14/09/2015.
 */
$("form[name='bookshelf']").submit(function (event) {
    event.preventDefault();
    BookshelfController.add.exec(event);
});
$("#bookshelf-current").click(function (event) {
    event.preventDefault();
    BookshelfController.remove.exec(event);
});

var BookshelfController = {
    /**
     * Manage get article infos
     */
    "get":{
        /**
         * Exec get action
         * @param event
         */
        "exec": function(target){
            var data = this.data(target);
            if(data)
                this.request(data);
            else
                BookshelfController.handle.error("Incorrect input");
        },
        /**
         * Get data infos
         * @param elem
         * @returns {*}
         */
        "data": function (elem){
            var data = {};

            data['article'] = $(elem).data("article-id");

            if(!data.article)
                return false;
            return data;
        },
        /**
         * Launch AJAX Request
         * @param data
         * @returns {boolean}
         */
        "request": function (data){
            var me = this;
            $.ajax({
                method: "GET",
                url: Routing.generate('inck_article_bookshelf_article_get', {
                    id: data.article
                }),
                dataType: 'json',
                async: false
            }).done(function(jqJSON) {
                me.compound(data, jqJSON.bookshelfs);
                $("#bookshelf-modal").modal('show');
            }).fail(function(jqXHR) {
                me.flush();
                try{
                    BookshelfController.handle.error(JSON.parse(jqXHR.responseText).message);
                }catch(e){
                    BookshelfController.handle.error("Une erreur est survenue, réessayez plus tard !");
                }
            });
        },
        /**
         * Reload data from another handler
         * @param data
         */
        "reload": function (data){
            if(data.hasOwnProperty("article"))
                this.request(data);
        },
        /**
         * Compound modal
         * @param data
         * @param json
         */
        "compound": function (data, json){
            BookshelfController.handle.components.add.input.set(data.article);
            BookshelfController.handle.components.add.datalist.reset();
            BookshelfController.handle.components.remove.ul.set(data.article);
            BookshelfController.handle.components.remove.li.reset();
            json.forEach(function (elem) {
                if(elem.hasArticle){
                    BookshelfController.handle.components.remove.li.set(elem.id, elem.title);
                }else{
                    BookshelfController.handle.components.add.datalist.set(elem.id, elem.title);
                }
            });
            BookshelfController.handle.components.add.datalist.uniq();
        },
        "flush": function () {
            BookshelfController.handle.components.add.input.reset();
            BookshelfController.handle.components.add.datalist.reset();
            BookshelfController.handle.components.remove.ul.reset();
            BookshelfController.handle.components.remove.li.reset();
        }
    },
    /**
     * Manage add to bookshelf
     */
    "add":{
        /**
         * Exec an add action
         */
        "exec": function(event){
            var data = this.data();
            if(data)
                this.request(data);
            else
                BookshelfController.handle.error("Incorrect input");
        },
        /**
         * Get input data
         * @returns {*}
         */
        "data": function (){
            var data = {};

            data['article'] = $("#bookshelf-title").data("article-id");
            data['bookshelf'] = $("#bookshelfs-list option[value='"+$("#bookshelf-title").val()+"']").data("bookshelf-id");

            if(!data.article || !data.bookshelf)
                return false;
            return data;
        },
        /**
         * Launch AJAX Request
         * @param data
         * @returns {boolean}
         */
        "request": function (data){
            $.ajax({
                method: "PUT",
                url: Routing.generate('inck_article_bookshelf_article_add', {
                    article_id: data.article,
                    bookshelf_id: data.bookshelf
                }),
                dataType: 'json'
            }).done(function(jqJSON) {
                BookshelfController.get.reload(data);
                BookshelfController.handle.success(jqJSON.message);
                $("#bookshelf-title").val("");
            }).fail(function(jqXHR) {
                try{
                    BookshelfController.handle.error(JSON.parse(jqXHR.responseText).message);
                }catch(e){
                    BookshelfController.handle.error("Une erreur est survenue, réessayez plus tard !");
                }
            });
        }
    },
    /**
     * Manage remove to bookshelf
     */
    "remove":{
        /**
         * Exec remove action
         * @param event
         */
        "exec": function(event){
            var data = this.data(event.target);
            if(data)
                this.request(data);
            else
                BookshelfController.handle.error("Incorrect input");
        },
        /**
         * Get data infos
         * @param elem
         */
        "data": function (elem){
            var data = {};

            data['article'] = $(elem).closest("ul").data("article-id");
            data['bookshelf'] = $(elem).data("bookshelf-id");

            if(!data.article || !data.bookshelf){
                return false;
            }
            return data;
        },
        /**
         * Launch AJAX Request
         * @param data
         * @returns {boolean}
         */
        "request": function (data){
            $.ajax({
                method: "DELETE",
                url: Routing.generate('inck_article_bookshelf_article_remove', {
                    article_id: data.article,
                    bookshelf_id: data.bookshelf
                }),
                dataType: 'json',
                async: false
            }).done(function(jqJSON) {
                BookshelfController.get.reload(data);
                BookshelfController.handle.success(jqJSON.message);
            }).fail(function(jqXHR) {
                try{
                    BookshelfController.handle.error(JSON.parse(jqXHR.responseText).message);
                }catch(e){
                    BookshelfController.handle.error("Une erreur est survenue, réessayez plus tard !");
                }
            });
        }
    },
    /**
     * Handle components and alerts
     */
    "handle":{
        "success": function (message){
            this.components.alert.formGroup("success");
            this.components.alert.li("success", "check", message);
        },
        "error": function (message){
            this.components.alert.formGroup("error");
            this.components.alert.li("danger", "ban", message);
        },
        "components": {
            "add":{
                "input": {
                    "set":function (aid){
                        $("#bookshelf-title").data("article-id", aid);
                    },
                    "reset":function (){
                        $("#bookshelfs-list").data("article-id", "");
                    }
                },
                "datalist":{
                    "set":function (bid, bit){
                        var html = $("<option></option>").attr("value", bit).data("bookshelf-id", bid);
                        $("#bookshelfs-list").append(html);
                    },
                    "reset":function (){
                        $("#bookshelfs-list").empty();
                    },
                    "uniq":function (){
                        var list = [];
                        $("#bookshelfs-list option").each(function(index, elem){
                            list.push($(elem).attr("value"));
                        });
                        list = $.unique(list);
                        for(var index in list) {
                            if(list.hasOwnProperty(index)) {
                                var selector = $("#bookshelfs-list option[value='" + list[index] + "']");
                                if(selector.length > 1) {
                                    selector.each(function (index, elem) {
                                        $(elem).attr("value", $(elem).attr("value") + " (" + index + ")")
                                    });
                                }
                            }
                        }
                    }
                }
            },
            "remove": {
                "ul": {
                    "set":function (aid){
                        $("#bookshelf-current").data("article-id", aid);
                    },
                    "reset":function (){
                        $("#bookshelf-current").data("article-id", "");
                    }
                },
                "li": {
                    "set":function (bid, bit){
                        var html = $("<li></li>").addClass("bookshelf-current-list").data("bookshelf-id", bid).html(bit);
                        $("#bookshelf-current").append(html);
                    },
                    "reset":function (){
                        $("#bookshelf-current").empty();
                    }
                }
            },
            "alert":{
                "formGroup": function (state) {
                    $("form[name='bookshelf'] .form-group").addClass('has-'+state).delay(2000).queue(function(n){$(this).removeClass('has-'+state);n();})
                },
                "li":function (state, icon, message){
                    var html = $("<li></li>").addClass("text-"+state);
                    var fa = $("<i></i>").addClass("fa").addClass("fa-"+icon);
                    $(html).append(fa);
                    $(html).append("&nbsp;"+message);
                    $(html).css("opacity", 0);

                    $(html)
                        .appendTo("form[name='bookshelf'] .help-block ul")
                        .animate({
                            opacity: 1
                        }, 400)
                        .delay(2000).queue(function(){
                            $(this).remove();
                        });
                }
            }
        }
    }
};