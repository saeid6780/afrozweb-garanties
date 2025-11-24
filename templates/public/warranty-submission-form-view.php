<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="warranty-form-wrapper">
    <form id="warranty-submission-form" method="post">

        <div id="form-messages" class="form-messages"></div>

        <div class="form-row">
            <div class="form-group">
                <label for="customer_name"><?php esc_html_e( 'نام مشتری', AFROZWEB_GARANTY_SLUG ); ?> <span class="required">*</span></label>
                <input type="text" id="customer_name" name="customer_name" required>
                <small class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="customer_phone"><?php esc_html_e( 'شماره تماس مشتری', AFROZWEB_GARANTY_SLUG ); ?> <span class="required">*</span></label>
                <input type="tel" id="customer_phone" name="customer_phone" required>
                <small class="error-message"></small>
            </div>
        </div>

        <div class="form-group">
            <label for="project_address"><?php esc_html_e( 'آدرس محل اجرا', AFROZWEB_GARANTY_SLUG ); ?> <span class="required">*</span></label>
            <textarea id="project_address" name="project_address" rows="3" required></textarea>
            <small class="error-message"></small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="installed_area"><?php esc_html_e( 'متراژ نصب شده (متر مربع)', AFROZWEB_GARANTY_SLUG ); ?></label>
                <input type="number" step="0.01" id="installed_area" name="installed_area">
                <small class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="project_postal_code"><?php esc_html_e( 'کد پستی محل اجرا', AFROZWEB_GARANTY_SLUG ); ?></label>
                <input type="text" id="project_postal_code" name="project_postal_code">
                <small class="error-message"></small>
            </div>
        </div>

        <hr>

        <div class="form-row">
            <div class="form-group">
                <label for="installer_name"><?php esc_html_e( 'نام نصاب', AFROZWEB_GARANTY_SLUG ); ?></label>
                <input type="text" id="installer_name" name="installer_name">
                <small class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="installer_phone"><?php esc_html_e( 'شماره تماس نصاب', AFROZWEB_GARANTY_SLUG ); ?> <span class="required">*</span></label>
                <input type="tel" id="installer_phone" name="installer_phone" required>
                <small class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="installer_national_id"><?php esc_html_e( 'کد ملی نصاب', AFROZWEB_GARANTY_SLUG ); ?></label>
                <input type="text" id="installer_national_id" name="installer_national_id">
                <small class="error-message"></small>
            </div>
        </div>

        <hr>

        <div class="form-row">
            <div class="form-group">
                <label for="warranty_number"><?php esc_html_e( 'شماره گارانتی', AFROZWEB_GARANTY_SLUG ); ?></label>
                <input type="text" id="warranty_number" name="warranty_number">
                <small class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="product_type"><?php esc_html_e( 'نوع محصول', AFROZWEB_GARANTY_SLUG ); ?> <span class="required">*</span></label>
                <select id="product_type" name="product_type" required>
                    <option value=""><?php esc_html_e( 'انتخاب کنید...', AFROZWEB_GARANTY_SLUG ); ?></option>
                    <option value="simple_4mm"><?php esc_html_e( 'ایزوگام ساده ۴ میلیمتر', AFROZWEB_GARANTY_SLUG ); ?></option>
                    <option value="foil_4mm"><?php esc_html_e( 'ایزوگام فویل دار ۴ میلیمتر', AFROZWEB_GARANTY_SLUG ); ?></option>
                    <option value="simple_3.5mm"><?php esc_html_e( 'ایزوگام ساده ۳.۵ میلیمتر', AFROZWEB_GARANTY_SLUG ); ?></option>
                    <option value="foil_3.5mm"><?php esc_html_e( 'ایزوگام فویل دار ۳.۵ میلیمتر', AFROZWEB_GARANTY_SLUG ); ?></option>
                    <option value="isoepoxy"><?php esc_html_e( 'ایزواپوکسی', AFROZWEB_GARANTY_SLUG ); ?></option>
                    <option value="isopolymer"><?php esc_html_e( 'ایزوپلیمر', AFROZWEB_GARANTY_SLUG ); ?></option>
                </select>
                <small class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="installation_date"><?php esc_html_e( 'تاریخ نصب', AFROZWEB_GARANTY_SLUG ); ?> <span class="required">*</span></label>
                <input type="text" id="installation_date" name="installation_date" required>
                <input type="hidden" id="installation_date_alt" name="installation_date_alt">
                <small class="error-message"></small>
            </div>
        </div>

        <div class="form-group">
            <label for="project_description"><?php esc_html_e( 'توضیحات پروژه', AFROZWEB_GARANTY_SLUG ); ?></label>
            <textarea id="project_description" name="project_description" rows="4"></textarea>
            <small class="error-message"></small>
        </div>

        <div class="form-group">
            <button type="submit" id="submit-warranty-btn"><?php esc_html_e( 'ثبت گارانتی', AFROZWEB_GARANTY_SLUG ); ?></button>
        </div>
    </form>
</div>