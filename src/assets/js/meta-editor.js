jQuery(document).ready(function ($) {
    // Function to update the character count
    function updateCharacterCount(inputId, counterId) {
        const inputElement = $('#' + inputId);
        const counterElement = $('#' + counterId);

        inputElement.on('input', function () {
            counterElement.text(inputElement.val().length);
        });
    }

    // Initialize character count updates for posts/pages/CPTs
    updateCharacterCount('wp_fedora_meta_title', 'meta_title_count');
    updateCharacterCount('wp_fedora_meta_description', 'meta_description_count');
    updateCharacterCount('wp_fedora_og_meta_title', 'og_meta_title_count');
    updateCharacterCount('wp_fedora_og_meta_description', 'og_meta_description_count');

    // For taxonomies (categories/tags)
    updateCharacterCount('wp_fedora_meta_title_tax', 'meta_title_count_tax');
    updateCharacterCount('wp_fedora_meta_description_tax', 'meta_description_count_tax');

    // For author archives
    updateCharacterCount('wp_fedora_meta_title_author', 'meta_title_count_author');
    updateCharacterCount('wp_fedora_meta_description_author', 'meta_description_count_author');
});
