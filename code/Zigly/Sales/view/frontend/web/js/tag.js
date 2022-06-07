define(['jquery'],function($) {
    'use strict';

    return function (config) {
        $(document).ready(function() {
            $("input[name='star_rating']").change(function(){
                $('#star_rating-error').remove()
                var reviewurl = config.reviewurl;
                var starrating = $("[name='star_rating']:checked").val();
                $.ajax({
                    url: reviewurl,
                    method: 'POST',
                    data: {'starrating': starrating},
                    showLoader: true,
                    success: function (response) {
                        if (!response.success) {
                            $('.review-field-Tags').empty()
                        } else {
                            let $tagHtml = '<label >'+response.show_text+'</label>'
                            $tagHtml += '<div class="control">'
                            $.each(response.tag, function(index, value) {
                                $tagHtml += '<div class="feedback-tags">'
                                $tagHtml += '<input type="checkbox" class="checkbox" id="'+value.tag_name+'" name="tag_name[]" value="'+value.tag_name+'">'
                                $tagHtml += '<label for="'+value.tag_name+'">'+value.tag_name+'</label>'
                                $tagHtml += '</div>'
                            });
                            /*$tagHtml += '</div>'
                            $tagHtml += '<div class="admin__field-control">'
                            $tagHtml += '<textarea name="description" id="description" title="Write Feedback (optional)" class="input-text" placeholder="Write Feedback (optional)" rows="4" cols="15"></textarea>'
                            $tagHtml += '</div>'*/
                            $('.review-field-Tags').html($tagHtml)
                        }
                    }
                });
            });
        });
    }
});