$(function() {
    $('input[type=file]').bootstrapFileInput();

    $('a[data-role=needs-confirm], button[data-role=needs-confirm]').click(function() {
        var confirm_text = $(this).data('confirm-text');
        return confirm(confirm_text);
    });

    $('form.attachment_upload input[type=file]').change(function() {
        var form = $(this).closest('form');
        var form_data = new FormData(form[0]);
        var file_upload_status = $('.file_upload_status');
        var file_upload_progress = $('.file_upload_progress');
        var progress_bar = file_upload_progress.find('.progress-bar');

        file_upload_status.removeClass('text-success text-danger').text('');
        progress_bar.css('width', 0);
        file_upload_progress.show();

        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            success: function() {
                file_upload_progress.hide();
                file_upload_status.addClass('text-success').text('File successfully uploaded');
            },
            error: function() {
                file_upload_progress.hide();
                file_upload_status.addClass('text-danger').text('File upload error');
            },
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if(myXhr.upload) {
                    myXhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            progress_bar.css('width', (e.loaded / e.total * 100) + '%');
                        }
                    }, false);
                }
                return myXhr;
            }
        });
    });
});
