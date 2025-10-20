jQuery(document).ready(function($) {
    var container = $('#warranty-list-container');
    var tableBody = container.find('tbody');
    var paginationContainer = $('#warranty-list-pagination');

    // تابع برای بارگذاری داده‌ها
    function loadWarranties(page) {
        // نمایش حالت لودینگ
        tableBody.html('<tr><td colspan="8">' + warranty_list_ajax.loading_html + '</td></tr>');
        paginationContainer.empty();

        // ارسال درخواست ایجکس
        $.ajax({
            type: 'POST',
            url: warranty_list_ajax.ajax_url,
            data: {
                action: 'get_warranty_list',
                nonce: warranty_list_ajax.nonce,
                page: page
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    tableBody.html(response.data.table_rows);
                    paginationContainer.html(response.data.pagination);
                } else {
                    tableBody.html('<tr><td colspan="8">خطایی رخ داد. لطفاً صفحه را دوباره بارگذاری کنید.</td></tr>');
                }
            },
            error: function() {
                tableBody.html('<tr><td colspan="8">خطای سرور. لطفاً با پشتیبانی تماس بگیرید.</td></tr>');
            }
        });
    }

    // بارگذاری اولیه داده‌ها برای صفحه اول
    loadWarranties(1);

    // مدیریت کلیک روی لینک‌های صفحه‌بندی
    paginationContainer.on('click', 'a.page-numbers', function(e) {
        e.preventDefault();

        var pageUrl = $(this).attr('href');
        var pageNum = 1;

        // استخراج شماره صفحه از URL
        if (pageUrl.includes('paged=')) {
            pageNum = pageUrl.split('paged=')[1].split('&')[0];
        } else {
            // برای لینک‌های prev/next که paged ندارند
            var currentPage = parseInt(paginationContainer.find('.current').text());
            if ($(this).hasClass('prev')) {
                pageNum = currentPage - 1;
            } else if ($(this).hasClass('next')) {
                pageNum = currentPage + 1;
            }
        }

        loadWarranties(parseInt(pageNum));
    });
});