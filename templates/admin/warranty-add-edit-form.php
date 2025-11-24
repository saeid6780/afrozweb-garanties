<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// اگر ID اشتباه بود، پیام خطا
if ( $warranty_id_from_url > 0 && ! $warranty ) {
    echo '<div class="wrap"><h1>' . esc_html__( 'خطا', AFROZWEB_GARANTY_SLUG ) . '</h1><p>' . esc_html__( 'گارانتی مورد نظر یافت نشد.', AFROZWEB_GARANTY_SLUG ) . '</p></div>';
    return;
}

// تعیین عنوان صفحه
$page_title = $warranty ? sprintf( esc_html__( 'ویرایش گارانتی (ID: %d)', AFROZWEB_GARANTY_SLUG ), $warranty->id ) : esc_html__( 'افزودن گارانتی جدید', AFROZWEB_GARANTY_SLUG );
?>

<div class="wrap">
    <h1>
        <?php echo $page_title; ?>
        <!-- [بخش جدید] افزودن دکمه چاپ فقط در حالت ویرایش -->
        <?php if ( $warranty ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=warranty-print&warranty_id=' . $warranty->id ) ); ?>"
               class="page-title-action"
               target="_blank">
                <?php esc_html_e( 'چاپ گارانتی', AFROZWEB_GARANTY_SLUG ); ?>
            </a>
        <?php endif; ?>
    </h1>

    <?php if ( ! empty( $message ) ) : ?>
        <div class="notice <?php echo esc_attr( $notice_class ); ?> is-dismissible">
            <p><?php echo $message; // پیام‌ها از قبل ترجمه شده‌اند ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <?php wp_nonce_field( 'save_warranty_action', 'submit_warranty_nonce' ); ?>
        <?php if ( $warranty ) : ?>
            <input type="hidden" name="warranty_id" value="<?php echo esc_attr( $warranty->id ); ?>" />
            <input type="hidden" name="current_status" value="<?php echo esc_attr( $warranty->status ); ?>" />
        <?php endif;?>

        <table class="form-table" role="presentation">
            <tbody>
            <tr class="form-field">
                <th scope="row"><label for="representative_id"><?php esc_html_e( 'نماینده ثبت کننده', AFROZWEB_GARANTY_SLUG ); ?> <span class="description">(<?php esc_html_e( 'ضروری', AFROZWEB_GARANTY_SLUG ); ?>)</span></label></th>
                <td>
                    <select name="representative_id" id="representative_id" class="representative-select2" required>
                        <option value=""></option> <!-- Option خالی برای placeholder -->
                        <?php if ( ! empty( $representatives ) ) : ?>
                            <?php foreach ( $representatives as $rep ) : ?>
                                <option value="<?php echo esc_attr( $rep->ID ); ?>" <?php selected( $warranty->representative_id ?? '', $rep->ID ); ?>>
                                    <?php echo esc_html( $rep->display_name ); ?> (ID: <?php echo esc_html( $rep->ID ); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
            <!-- اطلاعات مشتری -->
            <tr class="form-field">
                <th scope="row"><label for="customer_name"><?php esc_html_e( 'نام مشتری', AFROZWEB_GARANTY_SLUG ); ?> <span class="description">(<?php esc_html_e( 'ضروری', AFROZWEB_GARANTY_SLUG ); ?>)</span></label></th>
                <td><input name="customer_name" type="text" id="customer_name" value="<?php echo esc_attr( $warranty->customer_name ?? '' ); ?>" class="regular-text" required></td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="customer_phone"><?php esc_html_e( 'شماره تماس مشتری', AFROZWEB_GARANTY_SLUG ); ?> <span class="description">(<?php esc_html_e( 'ضروری', AFROZWEB_GARANTY_SLUG ); ?>)</span></label></th>
                <td><input name="customer_phone" type="text" id="customer_phone" value="<?php echo esc_attr( $warranty->customer_phone ?? '' ); ?>" class="regular-text" required></td>
            </tr>

            <!-- اطلاعات پروژه -->
            <tr class="form-field">
                <th scope="row"><label for="project_address"><?php esc_html_e( 'آدرس محل اجرا', AFROZWEB_GARANTY_SLUG ); ?> <span class="description">(<?php esc_html_e( 'ضروری', AFROZWEB_GARANTY_SLUG ); ?>)</span></label></th>
                <td><textarea name="project_address" id="project_address" rows="4" class="large-text" required><?php echo esc_textarea( $warranty->project_address ?? '' ); ?></textarea></td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="installed_area"><?php esc_html_e( 'متراژ نصب شده (متر مربع)', AFROZWEB_GARANTY_SLUG ); ?></label></th>
                <td><input name="installed_area" type="number" step="0.01" id="installed_area" value="<?php echo esc_attr( $warranty->installed_area ?? '' ); ?>" class="small-text"></td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="project_postal_code"><?php esc_html_e( 'کد پستی محل اجرا', AFROZWEB_GARANTY_SLUG ); ?></label></th>
                <td><input name="project_postal_code" type="text" id="project_postal_code" value="<?php echo esc_attr( $warranty->project_postal_code ?? '' ); ?>" class="regular-text"></td>
            </tr>

            <!-- اطلاعات نصاب -->
            <tr class="form-field">
                <th scope="row"><label for="installer_name"><?php esc_html_e( 'نام نصاب', AFROZWEB_GARANTY_SLUG ); ?></label></th>
                <td><input name="installer_name" type="text" id="installer_name" value="<?php echo esc_attr( $warranty->installer_name ?? '' ); ?>" class="regular-text"></td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="installer_phone"><?php esc_html_e( 'شماره تماس نصاب', AFROZWEB_GARANTY_SLUG ); ?> <span class="description">(<?php esc_html_e( 'ضروری', AFROZWEB_GARANTY_SLUG ); ?>)</span></label></th>
                <td><input name="installer_phone" type="text" id="installer_phone" value="<?php echo esc_attr( $warranty->installer_phone ?? '' ); ?>" class="regular-text" required></td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="installer_national_id"><?php esc_html_e( 'کد ملی نصاب', AFROZWEB_GARANTY_SLUG ); ?></label></th>
                <td><input name="installer_national_id" type="text" id="installer_national_id" value="<?php echo esc_attr( $warranty->installer_national_id ?? '' ); ?>" class="regular-text"></td>
            </tr>

            <!-- جزئیات گارانتی -->
            <tr class="form-field">
                <th scope="row"><label for="warranty_number"><?php esc_html_e( 'شماره گارانتی', AFROZWEB_GARANTY_SLUG ); ?></label></th>
                <td><input name="warranty_number" type="text" id="warranty_number" value="<?php echo esc_attr( $warranty->warranty_number ?? '' ); ?>" class="regular-text"></td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="product_type"><?php esc_html_e( 'نوع محصول استفاده شده', AFROZWEB_GARANTY_SLUG ); ?> <span class="description">(<?php esc_html_e( 'ضروری', AFROZWEB_GARANTY_SLUG ); ?>)</span></label></th>
                <td>
                    <?php
                    $products = [
                        'simple_4mm'   => __( 'ایزوگام ساده ۴ میلیمتر', AFROZWEB_GARANTY_SLUG ),
                        'foil_4mm'     => __( 'ایزوگام فویل دار ۴ میلیمتر', AFROZWEB_GARANTY_SLUG ),
                        'simple_3.5mm' => __( 'ایزوگام ساده ۳.۵ میلیمتر', AFROZWEB_GARANTY_SLUG ),
                        'foil_3.5mm'   => __( 'ایزوگام فویل دار ۳.۵ میلیمتر', AFROZWEB_GARANTY_SLUG ),
                        'isoepoxy'     => __( 'ایزواپوکسی', AFROZWEB_GARANTY_SLUG ),
                        'isopolymer'   => __( 'ایزوپلیمر', AFROZWEB_GARANTY_SLUG )
                    ];
                    ?>
                    <select name="product_type" id="product_type" required>
                        <option value=""><?php esc_html_e( 'یک محصول را انتخاب کنید...', AFROZWEB_GARANTY_SLUG ); ?></option>
                        <?php foreach ($products as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected( $warranty->product_type ?? '', $key ); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <?php
            $installation_date = $warranty->installation_date;
            $installation_date_timestamp = strtotime( $installation_date ) ;
            $installation_date_alt = $installation_date_timestamp * 1000;
            ?>
            <tr class="form-field">
                <th scope="row"><label for="installation_date"><?php esc_html_e( 'تاریخ نصب', AFROZWEB_GARANTY_SLUG ); ?> <span class="description">(<?php esc_html_e( 'ضروری', AFROZWEB_GARANTY_SLUG ); ?>)</span></label></th>
                <td>
                    <input name="installation_date" type="text" id="installation_date" value="<?php echo date_i18n('Y/m/d', $installation_date_timestamp ) ?>" required>
                    <input name="installation_date_alt" type="hidden" id="installation_date_alt" value="<?php echo $installation_date_alt ?>" required>
                </td>
            </tr>

            <?php
            $default_warranty_period_years = 7;
            $ten_years_types = [
                'simple_4mm',
                'foil_4mm'
            ];
            if ( empty( $warranty->warranty_period_years ) && ! empty( $warranty->product_type ) and in_array( $warranty->product_type, $ten_years_types ) )
                $default_warranty_period_years = 10;
            ?>
            <tr class="form-field">
                <th scope="row"><label for="warranty_period_years"><?php esc_html_e( 'مدت گارانتی', AFROZWEB_GARANTY_SLUG ); ?></label></th>
                <td>
                    <input name="warranty_period_years" type="number" id="warranty_period_years" value="<?php echo esc_attr( $warranty->warranty_period_years ?? $default_warranty_period_years ); ?>" class="small-text"> <?php esc_html_e( 'سال', AFROZWEB_GARANTY_SLUG ); ?>
                    <input name="warranty_period_months" type="number" id="warranty_period_months" value="<?php echo esc_attr( $warranty->warranty_period_months ?? '0' ); ?>" class="small-text"> <?php esc_html_e( 'ماه', AFROZWEB_GARANTY_SLUG ); ?>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="status"><?php esc_html_e( 'وضعیت گارانتی', AFROZWEB_GARANTY_SLUG ); ?></label></th>
                <td>
                    <select name="status" id="status">
                        <option value="pending_approval" <?php selected( $warranty->status ?? 'pending_approval', 'pending_approval' ); ?>><?php esc_html_e( 'در انتظار تایید', AFROZWEB_GARANTY_SLUG ); ?></option>
                        <option value="approved" <?php selected( $warranty->status ?? '', 'approved' ); ?>><?php esc_html_e( 'تایید شده', AFROZWEB_GARANTY_SLUG ); ?></option>
                        <option value="expired" <?php selected( $warranty->status ?? '', 'expired' ); ?>><?php esc_html_e( 'منقضی شده', AFROZWEB_GARANTY_SLUG ); ?></option>
                    </select>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="project_description"><?php esc_html_e( 'توضیحات پروژه', AFROZWEB_GARANTY_SLUG ); ?></label></th>
                <td><textarea name="project_description" id="project_description" rows="5" class="large-text"><?php echo esc_textarea( $warranty->project_description ?? '' ); ?></textarea></td>
            </tr>
            </tbody>
        </table>

        <?php
        $submit_button_text = $warranty ? __( 'به‌روزرسانی گارانتی', AFROZWEB_GARANTY_SLUG ) : __( 'ذخیره گارانتی', AFROZWEB_GARANTY_SLUG );
        submit_button( $submit_button_text, 'primary', 'submit', true );
        ?>
    </form>
</div>