<?php
/**
 * Template for printing a single warranty.
 *
 * @var stdClass $warranty The warranty object.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// لیست محصولات برای نمایش نام فارسی
$products = [
    'simple_4mm'   => __( 'ایزوگام ساده ۴ میلیمتر', AFROZWEB_GARANTY_SLUG ),
    'foil_4mm'     => __( 'ایزوگام فویل دار ۴ میلیمتر', AFROZWEB_GARANTY_SLUG ),
    'simple_3.5mm' => __( 'ایزوگام ساده ۳.۵ میلیمتر', AFROZWEB_GARANTY_SLUG ),
    'foil_3.5mm'   => __( 'ایزوگام فویل دار ۳.۵ میلیمتر', AFROZWEB_GARANTY_SLUG ),
    'isoepoxy'     => __( 'ایزواپوکسی', AFROZWEB_GARANTY_SLUG ),
    'isopolymer'   => __( 'ایزوپلیمر', AFROZWEB_GARANTY_SLUG )
];
$product_name = $products[$warranty->product_type] ?? $warranty->product_type;
$statuses = [
    'approved'         => __( 'تایید شده', AFROZWEB_GARANTY_SLUG ),
    'pending_approval' => __( 'در انتظار تایید', AFROZWEB_GARANTY_SLUG ),
    'expired'          => __( 'منقضی شده', AFROZWEB_GARANTY_SLUG ),
];

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php printf( esc_html__( 'چاپ گارانتی #%s', AFROZWEB_GARANTY_SLUG ), $warranty->warranty_number ); ?></title>
    <style>
        body { font-family: sans-serif; direction: rtl; background-color: #f0f0f1; margin: 0; padding: 20px; }
        .print-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border: 1px solid #ccc; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .print-header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .print-header h1 { margin: 0; }
        .print-table { width: 100%; border-collapse: collapse; }
        .print-table th, .print-table td { border: 1px solid #ddd; padding: 10px; text-align: right; }
        .print-table th { background-color: #f9f9f9; width: 30%; }
        .print-footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
        .print-button { display: inline-block; padding: 10px 20px; background: #2271b1; color: #fff; text-decoration: none; border-radius: 3px; margin-top: 20px; }

        /* استایل‌های مخصوص زمان چاپ */
        @media print {
            body { background: #fff; padding: 0;-webkit-print-color-adjust: exact; /* برای چاپ رنگ‌های پس‌زمینه در Chrome/Safari */
                print-color-adjust: exact; /* استاندارد جدید */ }
            .print-container { border: none; box-shadow: none; margin: 0; max-width: 100%; }
            .print-controls { display: none; }
        }
    </style>
</head>
<body>
<div class="print-container">
    <div class="print-header">
        <h1><?php printf( esc_html__( 'برگه گارانتی محصول - شماره: %s', AFROZWEB_GARANTY_SLUG ), $warranty->warranty_number ); ?></h1>
    </div>

    <table class="print-table">
        <tr><th colspan="2" style="text-align: center; background: #eef;">اطلاعات مشتری و پروژه</th></tr>
        <tr><th><?php _e('نام مشتری:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( $warranty->customer_name ); ?></td></tr>
        <tr><th><?php _e('شماره تماس مشتری:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( $warranty->customer_phone ); ?></td></tr>
        <tr><th><?php _e('آدرس محل اجرا:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo nl2br( esc_html( $warranty->project_address ) ); ?></td></tr>
        <tr><th><?php _e('کد پستی:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( $warranty->project_postal_code ); ?></td></tr>
        <tr><th><?php _e('متراژ نصب:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( $warranty->installed_area ); ?> متر مربع</td></tr>

        <tr><th colspan="2" style="text-align: center; background: #eef;">اطلاعات نصاب</th></tr>
        <tr><th><?php _e('نام نصاب:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( $warranty->installer_name ); ?></td></tr>
        <tr><th><?php _e('شماره تماس نصاب:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( $warranty->installer_phone ); ?></td></tr>
        <tr><th><?php _e('کد ملی نصاب:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( $warranty->installer_national_id ); ?></td></tr>

        <tr><th colspan="2" style="text-align: center; background: #eef;">جزئیات گارانتی</th></tr>
        <tr><th><?php _e('نماینده ثبت کننده:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( $warranty->representative_name ); ?></td></tr>
        <tr><th><?php _e('نوع محصول:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( $product_name ); ?></td></tr>
        <tr><th><?php _e('تاریخ نصب:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( date_i18n( 'Y/m/d', strtotime( $warranty->installation_date ) ) ); ?></td></tr>
        <tr><th><?php _e('مدت گارانتی:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php printf( '%d سال و %d ماه', $warranty->warranty_period_years, $warranty->warranty_period_months ); ?></td></tr>
        <tr><th><?php _e('تاریخ انقضا:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( date_i18n( 'Y/m/d', strtotime( $warranty->expiration_date ) ) ); ?></td></tr>
        <tr><th><?php _e('وضعیت:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo esc_html( $statuses[ $warranty->status ] ?? ucfirst( str_replace( '_', ' ', $warranty->status ) ) ); ?></td></tr>
        <tr><th><?php _e('توضیحات:', AFROZWEB_GARANTY_SLUG); ?></th><td><?php echo nl2br( esc_html( $warranty->project_description ) ); ?></td></tr>
    </table>

    <div class="print-footer">

    </div>

    <div class="print-controls" style="text-align: center;">
        <a href="javascript:window.print()" class="print-button"><?php _e( 'چاپ', AFROZWEB_GARANTY_SLUG ); ?></a>
    </div>
</div>
</body>
</html>