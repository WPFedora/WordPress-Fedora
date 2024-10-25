jQuery(document).ready(function ($) {
    // Function to update the character count
    function updateCharacterCount(inputId, counterId) {
        const inputElement = $('#' + inputId);
        const counterElement = $('#' + counterId);

        inputElement.on('input', function () {
            counterElement.text(inputElement.val().length);
        });
    }

    // Initialize character count updates
    updateCharacterCount('wp_fedora_meta_title', 'meta_title_count');
    updateCharacterCount('wp_fedora_meta_description', 'meta_description_count');
    updateCharacterCount('wp_fedora_og_meta_title', 'og_meta_title_count');
    updateCharacterCount('wp_fedora_og_meta_description', 'og_meta_description_count');
});
