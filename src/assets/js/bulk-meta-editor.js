jQuery(document).ready(function($) {
    // Trigger save when the meta title or description field changes
    $('.meta-title-field, .meta-description-field').on('change', function() {
        let postID = $(this).data('post-id');
        let metaTitle = $('.meta-title-field[data-post-id="' + postID + '"]').val();
        let metaDescription = $('.meta-description-field[data-post-id="' + postID + '"]').val();

        // AJAX request to save meta data
        $.ajax({
            url: bulkMetaEditor.ajax_url,
            type: 'POST',
            data: {
                action: 'bulk_meta_editor_save_meta',
                post_id: postID,
                meta_title: metaTitle,
                meta_description: metaDescription,
                nonce: bulkMetaEditor.nonce
            },
            success: function(response) {
                if (!response.success) {
                    console.log('Failed to update meta for post ID: ' + postID);
                }
            },
            error: function() {
                console.log('AJAX error: Failed to save meta.');
            }
        });
    });
});