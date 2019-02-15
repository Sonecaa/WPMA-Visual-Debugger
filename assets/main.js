var $ = jQuery;

//init
$(document).ready(function () {

    $("#wpma-select-post-type").change(function () {

        console.log($('#wpma-select-post-type').val());

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
                        console.log(response.data.posts);
                        call_render_posts(response.data.posts);
                    } else {
                        console.error(response);
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
    console.log("..");
    console.log(posts);
    for(var post in posts) {
      
        $('#wpma-select-posts').append("<option value=" + post.ID + ">" + post + "</option>");
    };
    
}