<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="warranty-search-wrapper">
    <form id="warranty-search-form">
        <div class="search-form-inner">
            <div class="form-group-phone">
                <input type="tel" id="customer-phone-search" name="phone" placeholder="<?php esc_attr_e( 'شماره تماس خود را وارد کنید (مثال: 09123456789)', AFROZWEB_GARANTY_SLUG ); ?>" required>
            </div>
            <div class="form-group-submit">
                <button type="submit" id="warranty-search-btn"><?php esc_html_e( 'بررسی گارانتی', AFROZWEB_GARANTY_SLUG ); ?></button>
            </div>
        </div>
    </form>

    <div id="warranty-search-messages" class="search-messages"></div>
    <div id="warranty-search-results" class="search-results">
        <!-- نتایج ایجکس در اینجا نمایش داده می‌شود -->
    </div>
</div>