var bookNameFinished = true;
$('#bookName').typeahead({
    source: function(query, process){
        if(!bookNameFinished) {
            return;
        }
        bookNameFinished = false;
        var type = $("#bookType").val();
        $.get("/admin/api/api.php",{action:"getbooks",query:query,type:type},function(result){
            $("#show").html(result);
            process(eval(result));
            bookNameFinished = true;
  		});
    },
    updater: function(item) {
        var array = item.split("_");
        $.get("/admin/api/api.php",{action:"getBorrowerInfo",query:array[0]},function(result){
            $("#personName").val(result);
        });
        return item;
    }
});

var personNameFinished = true;
$('#personName').typeahead({
    source: function(query, process){
        if(!personNameFinished) {
            return;
        }
        personNameFinished = false;
        var type = $("#personType").val();
        $.get("/admin/api/api.php",{action:"getpersons",query:query,type:type},function(result){
            $("#show").html(result);
            process(eval(result));
            personNameFinished = true;
        });
    }
});