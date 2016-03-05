var attachments;
var attachments_view;

$(function() {
    attachments = new Attachments;
    attachments_view = new AttachmentsView({
        collection: attachments,
        el: '#attachments'
    });
    attachments.fetch({reset: true});

    $('input[type=file]').bootstrapFileInput();

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
                attachments.fetch();
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

    $('#attachments').sortable({
        update: function(e, ui) {
            ui.item.trigger('sorted', [ui.item.index()]);
        }
    });
    $('#attachments').disableSelection();
});

var Attachment = Backbone.Model.extend({});

var Attachments = Backbone.Collection.extend({
    model: Attachment,
    url: location.toString().replace('edit', 'attachments')
});

var AttachmentView = Marionette.ItemView.extend({
    tagName: 'tr',
    template: '#template-attachment',
    ui: {
        delete_form: '.delete_attachment'
    },
    events: {
        'submit @ui.delete_form': 'deleteAttachment',
        'sorted' : 'resort'
    },
    deleteAttachment: function() {
        if (!confirm('Are you sure you want to remove this attachment?')) {
            return false;
        }
        var form_data = new FormData(this.ui.delete_form[0]);
        $.ajax({
            url: this.ui.delete_form.attr('action'),
            type: 'post',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            success: function() {
                attachments.fetch();
            },
            error: function() {
                alert('Something may gone wrong');
            }
        });
        return false;
    },
    resort: function(e, index) {
        $.ajax({
            url: this.model.get('path') + '/reorder',
            type: 'post',
            data: {
                '_method': 'PUT',
                'attachment[order]': index
            },
            cache: false,
            success: function() {

            },
            error: function() {
                alert('Something may gone wrong');
            }
        });
    }
});

var AttachmentsView = Marionette.CollectionView.extend({
    childView: AttachmentView,
    collectionEvents: {
        reset: 'render'
    }
});
