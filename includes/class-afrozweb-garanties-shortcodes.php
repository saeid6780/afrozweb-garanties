<?php

/**
 * Register all plugin shortcodes
 *
 * @link       https://linkedin.com/in/saeid-sadigh-zadeh-8861688a
 * @since      1.0.0
 *
 * @package    Afrozweb_Garanties
 * @subpackage Afrozweb_Garanties/includes
 */

/**
 * Register all plugin shortcodes
 *
 * @package    Afrozweb_Garanties
 * @subpackage Afrozweb_Garanties/includes
 * @author     saeid6780 <saeid6780sz@gmail.com>
 */
class Afrozweb_Garanties_Shortcodes {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    protected $wpdb;

    private $table;

    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     * @since    1.0.0
     */

    public function __construct( $plugin_name, $version ) {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'books';
        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Render shortcode [book_list]
     * attributes:
     *  - primary_color (optional)
     *  - secondary_color (optional)
     */
    public function warranty_submission_shortcode_handler( $atts ) {

        // 1. فقط برای کاربران لاگین شده
        if ( ! is_user_logged_in() ) {
            return '<p class="warranty-form-error">' . esc_html__( 'برای دسترسی به این فرم باید وارد حساب کاربری خود شوید.', AFROZWEB_GARANTY_SLUG ) . '</p>';
        }

        $user = wp_get_current_user();
        // 2. فقط برای کاربران با نقش 'representative'
        if ( ! in_array( 'representative', (array) $user->roles ) && ! in_array( 'administrator', (array) $user->roles ) ) {
            return '<p class="warranty-form-error">' . esc_html__( 'شما دسترسی لازم برای مشاهده این فرم را ندارید.', AFROZWEB_GARANTY_SLUG ) . '</p>';
        }

        // 3. بررسی تایید شدن حساب نمایندگی (نیازمندی شماره ۱)
        $corresponded_post_id = get_user_meta( $user->ID, 'corresponded_post_id', true );
        if ( empty( $corresponded_post_id ) || 'publish' !== get_post_status( $corresponded_post_id ) ) {
            return '<div class="warranty-form-notice notice-warning"><p>' . esc_html__( 'حساب نمایندگی شما هنوز توسط مدیر تایید نشده است. پس از تایید، می‌توانید گارانتی‌های خود را ثبت کنید.', AFROZWEB_GARANTY_SLUG ) . '</p></div>';
        }

        // 4. اگر تمام شرایط برقرار بود، اسکریپت‌ها و استایل‌ها را فراخوانی کن (نیازمندی شماره ۶)
        wp_enqueue_style( 'warranty-frontend-form-style' );
        wp_enqueue_script( 'warranty-frontend-form-script' );

        // 5. رندر کردن فرم از یک فایل view مجزا
        ob_start();
        include $this->get_template_part( 'warranty-submission-form-view' );
        return ob_get_clean();
    }

    public function handle_warranty_submission_ajax()
    {
        // 1. بررسی امنیت (Nonce)
        check_ajax_referer( 'warranty_form_nonce', 'nonce' );

        $errors = [];

        // 2. اعتبارسنجی سمت سرور (نیازمندی شماره ۲)
        $required_fields = ['customer_name', 'customer_phone', 'project_address', 'installer_phone', 'warranty_number', 'product_type', 'installation_date'];
        foreach ($required_fields as $field) {
            if ( empty( $_POST[$field] ) ) {
                $errors[$field] = __( 'این فیلد ضروری است.', AFROZWEB_GARANTY_SLUG );
            }
        }

        // اعتبارسنجی شماره تماس مشتری
        if ( ! empty( $_POST['customer_phone'] ) && ! preg_match( '/^09\d{9}$/', $_POST['customer_phone'] ) ) {
            $errors['customer_phone'] = __( 'فرمت شماره تماس مشتری صحیح نیست (مثال: 09123456789).', AFROZWEB_GARANTY_SLUG );
        }

        // اعتبارسنجی شماره تماس نصاب
        if ( ! empty( $_POST['installer_phone'] ) && ! preg_match( '/^09\d{9}$/', $_POST['installer_phone'] ) ) {
            $errors['installer_phone'] = __( 'فرمت شماره تماس نصاب صحیح نیست.', AFROZWEB_GARANTY_SLUG );
        }

        // اعتبارسنجی کد پستی
        if ( ! empty( $_POST['project_postal_code'] ) && ! preg_match( '/^\d{10}$/', $_POST['project_postal_code'] ) ) {
            $errors['project_postal_code'] = __( 'کد پستی باید ۱۰ رقمی باشد.', AFROZWEB_GARANTY_SLUG );
        }

        // اعتبارسنجی تاریخ نصب (نباید در آینده باشد)
        if ( ! empty( $_POST['installation_date'] ) ) {
            $installation_date = new DateTime( $_POST['installation_date'] );
            $today = new DateTime();
            if ( $installation_date > $today ) {
                $errors['installation_date'] = __( 'تاریخ نصب نمی‌تواند در آینده باشد.', AFROZWEB_GARANTY_SLUG );
            }
        }

        // اعتبارسنجی یکتا بودن شماره گارانتی
        if ( ! empty( $_POST['warranty_number'] ) ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'warranties';
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table_name WHERE warranty_number = %s", sanitize_text_field( $_POST['warranty_number'] ) ) );
            if ( $exists ) {
                $errors['warranty_number'] = __( 'این شماره گارانتی قبلاً ثبت شده است.', AFROZWEB_GARANTY_SLUG );
            }
        }

        // 3. اگر خطایی وجود داشت، آن را برگردان
        if ( ! empty( $errors ) ) {
            wp_send_json_error( [ 'errors' => $errors ] );
        }

        // 4. اگر همه چیز صحیح بود، داده‌ها را در دیتابیس ذخیره کن
        $data = [
            'customer_name'         => sanitize_text_field( $_POST['customer_name'] ),
            'customer_phone'        => sanitize_text_field( $_POST['customer_phone'] ),
            'installed_area'        => isset($_POST['installed_area']) ? floatval( $_POST['installed_area'] ) : null,
            'project_address'       => sanitize_textarea_field( $_POST['project_address'] ),
            'project_postal_code'   => sanitize_text_field( $_POST['project_postal_code'] ),
            'installer_name'        => sanitize_text_field( $_POST['installer_name'] ),
            'installer_phone'       => sanitize_text_field( $_POST['installer_phone'] ),
            'installer_national_id' => sanitize_text_field( $_POST['installer_national_id'] ),
            'warranty_number'       => sanitize_text_field( $_POST['warranty_number'] ),
            'product_type'          => sanitize_text_field( $_POST['product_type'] ),
            'installation_date'     => sanitize_text_field( $_POST['installation_date'] ),
            'warranty_period_years' => isset($_POST['warranty_period_years']) ? absint( $_POST['warranty_period_years'] ) : 10, // مقدار پیش‌فرض
            'warranty_period_months'=> isset($_POST['warranty_period_months']) ? absint( $_POST['warranty_period_months'] ) : 0,
            'project_description'   => sanitize_textarea_field( $_POST['project_description'] ),
            'status'                => 'pending_approval', // نیازمندی شماره ۴
            'representative_id'     => get_current_user_id(),
        ];

        // محاسبه تاریخ انقضا
        $install_date = new DateTime( $data['installation_date'] );
        $install_date->modify( '+' . $data['warranty_period_years'] . ' years' );
        $install_date->modify( '+' . $data['warranty_period_months'] . ' months' );
        $data['expiration_date'] = $install_date->format( 'Y-m-d' );

        $result = $wpdb->insert( $table_name, $data );

        if ( $result ) {
            wp_send_json_success( [ 'message' => __( 'گارانتی شما با موفقیت ثبت شد و پس از بررسی توسط مدیر، تایید خواهد شد.', AFROZWEB_GARANTY_SLUG ) ] );
        } else {
            wp_send_json_error( [ 'message' => __( 'خطایی در پایگاه داده رخ داد. لطفاً دوباره تلاش کنید.', AFROZWEB_GARANTY_SLUG ) ] );
        }

        wp_die();
    }

    public function get_template_part ( $template )
    {
        $located       = '';
        $template_slug = rtrim( $template, '.php' );
        $template      = $template_slug . '.php';

        if ( $template )
        {
            if ( file_exists( get_stylesheet_directory() . '/afrozweb-garanties/' . $template ) )
                $located = get_stylesheet_directory() . '/afrozweb-garanties/' . $template;
            else if ( file_exists( get_template_directory() . '/afrozweb-garanties/' . $template ) )
                $located = get_template_directory() . '/afrozweb-garanties/' . $template;
            else if ( file_exists( AFROZWEB_GARANTY_TEMPLATE . 'public/' . $template ) )
                $located = AFROZWEB_GARANTY_TEMPLATE . 'public/' . $template;
        }

        return $located;
    }

}
