jQuery(document).ready(function ($) {
    $('#upload_profile_image_button').on('click', function (e) {
        e.preventDefault();

        var mediaUploader;

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Profile Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#profile_image').val(attachment.url);
            $('#profile_image_preview').attr('src', attachment.url);
        });

        mediaUploader.open();
    });
});
