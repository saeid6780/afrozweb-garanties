jQuery(document).ready(function($) {
    var form = $('#warranty-search-form');
    var messagesDiv = $('#warranty-search-messages');
    var resultsDiv = $('#warranty-search-results');
    var submitButton = $('#warranty-search-btn');
    var originalButtonText = submitButton.text();

    form.on('submit', function(e) {
        e.preventDefault();

        // پاک کردن نتایج و پیام‌های قبلی
        messagesDiv.empty().removeClass('success-message error-message').hide();
        resultsDiv.empty().hide();
        submitButton.prop('disabled', true).text(warranty_search_ajax.loading_text);

        var formData = {
            action: 'search_warranties_by_phone',
            nonce: warranty_search_ajax.nonce,
            phone: $('#customer-phone-search').val()
        };

        $.ajax({
            type: 'POST',
            url: warranty_search_ajax.ajax_url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // نمایش جدول نتایج
                    resultsDiv.html(response.data.table_html).show();
                } else {
                    // نمایش پیام خطا
                    messagesDiv.addClass('error-message').html('<p>' + response.data.message + '</p>').show();
                }
            },
            error: function() {
                messagesDiv.addClass('error-message').html('<p>خطای سرور. لطفاً دوباره تلاش کنید.</p>').show();
            },
            complete: function() {
                submitButton.prop('disabled', false).text(originalButtonText);
            }
        });
    });
});