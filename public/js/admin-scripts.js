jQuery(document).ready(function($) {

    $(".representative-select2").select2({
        placeholder: "یک نماینده را جستجو و انتخاب کنید...",
        width: "100%"
    });

    $('#installation_date').persianDatepicker({
        calendar:{
            persian: {
                leapYearMode: 'astronomical'
            }
        },
        format: 'YYYY/MM/DD',
        initialValue: false,
        initialValueType: 'persian',
        altField: '#installation_date_alt',
        autoClose: true
    });

    // انتخابگرهای عناصر فرم
    var productTypeSelect = $('#product_type');
    var yearsInput = $('#warranty_period_years');

    /**
     * تابعی برای به‌روزرسانی مقدار سال گارانتی بر اساس محصول انتخاب شده.
     */
    function updateWarrantyYears() {
        // دریافت مقدار (value) محصول انتخاب شده
        var selectedProduct = productTypeSelect.val();

        // بررسی اینکه آیا محصول انتخاب شده یکی از دو نوع ۱۰ ساله است یا خیر
        if (selectedProduct === 'simple_4mm' || selectedProduct === 'foil_4mm') {
            // اگر بود، مقدار را روی 10 تنظیم کن
            yearsInput.val(10);
        }
        // در غیر این صورت، اگر یک محصول انتخاب شده بود (خالی نبود)
        else if (selectedProduct !== '') {
            // مقدار را روی 7 تنظیم کن
            yearsInput.val(7);
        }
        // اگر کاربر گزینه "انتخاب کنید..." را انتخاب کند، هیچ کاری انجام نمی‌دهیم
        // و مقدار قبلی باقی می‌ماند.
    }

    // 1. اجرای تابع در هنگام تغییر مقدار دراپ‌داون نوع محصول
    productTypeSelect.on('change', function() {
        updateWarrantyYears();
    });

    // 2. اجرای تابع در هنگام بارگذاری اولیه صفحه
    // این کار برای حالت "ویرایش" ضروری است که ممکن است یک محصول از قبل انتخاب شده باشد.
    updateWarrantyYears();

});