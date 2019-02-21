var $ = jQuery;

//init

$(document).ready(function () {

    $("#wpma-select-post-type").change(function () {

        $('#div-append-loader').append('<div id="loader"></div>');

        clear_results();

        if ($('#wpma-select-post-type').val()) {
            var data = {
                'action': 'wpma_get_posts_by_post_type',
                'post-type': $('#wpma-select-post-type').val(),
            };
            jQuery.ajax({
                url: wpma_vars.ajax_url,
                type: 'post',
                data: data,
                success: function (response) {
                    if (response.success) {
                        call_render_posts(response.data.posts);
                        $('#div-append-loader *').remove();
                    } else {
                        console.error(response);
                        $('#div-append-loader *').remove();
                    }
                },// end success;
                error: function (error) {
                    console.error(error);
                }// end error;
            }); // end ajax;
        }

    });// end change();


    $("#wpma-select-posts").change(function () {

        $('#div-append-loader').append('<div id="loader"></div>');

        if ($('#wpma-select-posts').val()) {
            var data = {
                'action': 'wpma_get_var_dump_post',
                'post': $('#wpma-select-posts').val(),
            };
            jQuery.ajax({
                url: wpma_vars.ajax_url,
                type: 'post',
                data: data,
                success: function (response) {
                    if (response.success) {
                        call_render_var_dump(response.data.var_dump);
                        call_render_json(response.data.json);
                        call_render_prettyjson(response.data.json_pretty);

                        call_render_status(response.data.id);

                        $('#div-append-loader *').remove();
                    } else {
                        console.error(response);
                        $('#div-append-loader *').remove();
                    }
                },// end success;
                error: function (error) {
                    console.error(error);
                }// end error;
            }); // end ajax;
        }

    });// end change();
});

function call_render_posts(posts) {
    //clear
    $('#wpma-select-posts option').remove();

    //insert
    for(var post of posts) {
        $('#wpma-select-posts').append("<option value=" + post.ID + ">" + post.post_name + "</option>");
    };

}

function call_render_var_dump(obj){
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }

    $('#wpma-ta-vardump').html(out);
}

function call_render_json(text){
    $('#wpma-ta-json').html(text);
}

function call_render_prettyjson(text){
    $('#wpma-ta-prettyjson').html(text);
}

function clear_results(){
    call_render_var_dump({});
    call_render_json("");
    call_render_prettyjson("");

    clear_status();
}

function clear_status(){
    $('#wpma-list-status *').remove();
}

function call_render_status(id){

    var actions = ["wpma_has_meta", "wpma_already_publish"];

    clear_status();

    for(action of actions) {
        status_post_by_id(id, action);
    }

}

function status_post_by_id(id, action){

    if(!id || !action){
        return "";
    }

    $('#div-append-loader-status').append('<div id="loader"></div>');

    var has_name = action.substring(5) + "()";

    var data = {
        'action': ''+ action +'',
        'id': ''+ id +'',
    };
    jQuery.ajax({
        url: wpma_vars.ajax_url,
        type: 'post',
        data: data,
        success: function (response) {
            if (response.success) {
                if(response.data.boolean == "true") {
                    $('#wpma-list-status').append("<li class='wpma-each-status green'>"+ has_name +"</li>");
                } else {
                    $('#wpma-list-status').append("<li class='wpma-each-status red'>"+ has_name +"</li>");
                }
                $('#div-append-loader-status *').remove();
            } else {
                console.error(response);
                $('#div-append-loader-status *').remove();
            }
        },// end success;
        error: function (error) {
            console.error(error);
        }// end error;
    }); // end ajax;
}