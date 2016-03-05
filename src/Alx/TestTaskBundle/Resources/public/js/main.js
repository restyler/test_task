$(function() {
    $('a[data-role=needs-confirm], button[data-role=needs-confirm]').click(function() {
        var confirm_text = $(this).data('confirm-text');
        return confirm(confirm_text);
    });
});
