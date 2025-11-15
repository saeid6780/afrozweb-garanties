jQuery(document).ready(function($) {
    var form = $('#warranty-submission-form');
    var messagesDiv = $('#form-messages');
    var submitButton = $('#submit-warranty-btn');
    var originalButtonText = submitButton.text();

    form.on('submit', function(e) {
        e.preventDefault();

        // 1. پاک کردن خطاهای قبلی و نمایش حالت لودینگ
        $('.error-message').text('');
        messagesDiv.empty().removeClass('success-message error-message');
        submitButton.prop('disabled', true).text(warranty_form_ajax.loading_text);

        // 2. جمع‌آوری داده‌های فرم
        var formData = form.serialize();
        // اضافه کردن اکشن و nonce به داده‌ها
        formData += '&action=submit_warranty_form&nonce=' + warranty_form_ajax.nonce;

        // 3. ارسال درخواست ایجکس
        $.ajax({
            type: 'POST',
            url: warranty_form_ajax.ajax_url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // 4. در صورت موفقیت
                    messagesDiv.addClass('success-message').html('<p>' + response.data.message + '</p>');
                    form[0].reset(); // ریست کردن فرم
                    $('html, body').animate({ scrollTop: form.offset().top - 100 }, 500); // اسکرول به بالای فرم
                } else {
                    // 5. در صورت وجود خطاهای اعتبارسنجی
                    messagesDiv.addClass('error-message').html('<p>' + 'لطفاً خطاهای فرم را برطرف کنید.' + '</p>');
                    if (response.data.errors) {
                        $.each(response.data.errors, function(key, value) {
                            $('#' + key).siblings('.error-message').text(value);
                        });
                    }
                    if (response.data.message) { // خطای عمومی
                        messagesDiv.append('<p>' + response.data.message + '</p>');
                    }
                }
            },
            error: function() {
                // 6. در صورت بروز خطای سرور
                messagesDiv.addClass('error-message').html('<p>' + 'خطای غیرمنتظره‌ای رخ داد. لطفاً با پشتیبانی تماس بگیرید.' + '</p>');
            },
            complete: function() {
                // 7. بازگرداندن دکمه به حالت اولیه
                submitButton.prop('disabled', false).text(originalButtonText);
            }
        });
    });


});