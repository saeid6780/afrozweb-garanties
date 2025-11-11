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

        // 3. بررسی تایید شدن حساب نمایندگی
        $corresponded_post_id = get_user_meta( $user->ID, 'corresponded_post_id', true );
        if ( ( empty( $corresponded_post_id ) || 'publish' !== get_post_status( $corresponded_post_id ) ) && ! in_array( 'administrator', (array) $user->roles ) ) {
            return '<div class="warranty-form-notice notice-warning"><p>' . esc_html__( 'حساب نمایندگی شما هنوز توسط مدیر تایید نشده است. پس از تایید، می‌توانید گارانتی‌های خود را ثبت کنید.', AFROZWEB_GARANTY_SLUG ) . '</p></div>';
        }

        // 4. اگر تمام شرایط برقرار بود، اسکریپت‌ها و استایل‌ها را فراخوانی کن
        wp_enqueue_style( 'warranty-frontend-form-style' );
        wp_enqueue_script( 'warranty-frontend-form-script' );

        wp_enqueue_script('jquery');
        wp_enqueue_style( 'persian-datepicker' );
        wp_enqueue_script( 'persian-date' );
        wp_enqueue_script( 'persian-datepicker' );

        // افزودن اسکریپت inline برای فعال‌سازی Datepicker
        $inline_script = "
        (function($){
            $(document).ready(function(){
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
            });
        })(jQuery);
    ";
        wp_add_inline_script( 'persian-datepicker', $inline_script, 'after' );

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

        // 2. اعتبارسنجی سمت سرور
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
        if ( ! empty( $_POST['installation_date_alt'] ) ) {
            // تبدیل timestamp میلی‌ثانیه‌ای به ثانیه
            $timestamp_ms = intval($_POST['installation_date_alt']);
            $timestamp = $timestamp_ms / 1000;

            // تبدیل به فرمت DATE (فقط تاریخ)
            $installation_date = gmdate('Y-m-d', $timestamp);
            $installation_date_standard = new DateTime( $installation_date );
            $today = new DateTime();
            if ( $installation_date_standard > $today ) {
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
            'installation_date'     => $installation_date,
            'warranty_period_years' => isset($_POST['warranty_period_years']) ? absint( $_POST['warranty_period_years'] ) : 10, // مقدار پیش‌فرض
            'warranty_period_months'=> isset($_POST['warranty_period_months']) ? absint( $_POST['warranty_period_months'] ) : 0,
            'project_description'   => sanitize_textarea_field( $_POST['project_description'] ),
            'status'                => 'pending_approval',
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

    public function warranty_list_shortcode_handler()
    {
        if ( ! is_user_logged_in() ) {
            return '<p class="warranty-list-error">' . esc_html__( 'برای مشاهده لیست گارانتی‌ها باید وارد حساب کاربری خود شوید.', AFROZWEB_GARANTY_SLUG ) . '</p>';
        }

        $user = wp_get_current_user();
        $is_admin = in_array( 'administrator', (array) $user->roles );
        $is_representative = in_array( 'representative', (array) $user->roles );

        // 2. فقط برای ادمین یا نماینده
        if ( ! $is_admin && ! $is_representative ) {
            return '<p class="warranty-list-error">' . esc_html__( 'شما دسترسی لازم برای مشاهده این لیست را ندارید.', AFROZWEB_GARANTY_SLUG ) . '</p>';
        }

        // 3. بررسی تایید شدن حساب نمایندگی (برای نقش نماینده)
        if ( $is_representative ) {

            $corresponded_post_id = get_user_meta( $user->ID, 'corresponded_post_id', true );
            if ( empty( $corresponded_post_id ) || 'publish' !== get_post_status( $corresponded_post_id ) ) {
                return '<div class="warranty-form-notice notice-warning"><p>' . esc_html__( 'حساب نمایندگی شما هنوز توسط مدیر تایید نشده است.', AFROZWEB_GARANTY_SLUG ) . '</p></div>';
            }
        }

        // 4. فراخوانی استایل‌ها و اسکریپت‌ها
        wp_enqueue_style( 'warranty-frontend-list-style' );
        wp_enqueue_script( 'warranty-frontend-list-script' );

        // 5. رندر کردن view اولیه
        ob_start();
        include $this->get_template_part( 'warranty-list-view' );
        return ob_get_clean();
    }

    public function handle_warranty_list_ajax()
    {
        check_ajax_referer( 'warranty_list_nonce', 'nonce' );

        global $wpdb;
        $table_name = $wpdb->prefix . 'warranties';
        $user = wp_get_current_user();

        $per_page = 10;
        $page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
        $offset = ( $page - 1 ) * $per_page;

        $where_clauses = [];
        $query_params = [];

        // اگر کاربر ادمین نیست، فقط گارانتی‌های خودش را ببیند
        if ( ! in_array( 'administrator', (array) $user->roles ) ) {
            $where_clauses[] = "representative_id = %d";
            $query_params[] = $user->ID;
        }

        $where_sql = ! empty( $where_clauses ) ? 'WHERE ' . implode( ' AND ', $where_clauses ) : '';

        // کوئری برای شمارش کل آیتم‌ها
        $total_items_query = "SELECT COUNT(id) FROM {$table_name} {$where_sql}";
        $total_items = $wpdb->get_var( $wpdb->prepare( $total_items_query, $query_params ) );

        // کوئری برای واکشی داده‌های صفحه فعلی
        $data_query = "SELECT * FROM {$table_name} {$where_sql} ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $query_params_with_pagination = array_merge( $query_params, [$per_page, $offset] );
        $warranties = $wpdb->get_results( $wpdb->prepare( $data_query, $query_params_with_pagination ) );

        // ساخت HTML خروجی
        ob_start();
        if ( $warranties ) {
            $start_row_num = $offset + 1;
            foreach ( $warranties as $index => $warranty ) {
                ?>
                <tr data-id="<?php echo esc_attr( $warranty->id ); ?>">
                    <td data-label="<?php esc_attr_e( 'ردیف', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo esc_html( $start_row_num + $index ); ?></td>
                    <td data-label="<?php esc_attr_e( 'شماره گارانتی', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->warranty_number ) ? '-' : esc_html( $warranty->warranty_number ); ?></td>
                    <td data-label="<?php esc_attr_e( 'نام نصاب', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->installer_name ) ? '-' : esc_html( $warranty->installer_name ); ?></td>
                    <td data-label="<?php esc_attr_e( 'نام مشتری', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->customer_name ) ? '-' : esc_html( $warranty->customer_name ); ?></td>
                    <td data-label="<?php esc_attr_e( 'تماس مشتری', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->customer_phone ) ? '-' : esc_html( $warranty->customer_phone ); ?></td>
                    <td data-label="<?php esc_attr_e( 'تاریخ نصب', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->installation_date ) ? '-' : esc_html( $warranty->installation_date ); ?></td>
                    <td data-label="<?php esc_attr_e( 'تاریخ انقضا', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->expiration_date ) ? '-' : esc_html( $warranty->expiration_date ); ?></td>
                    <td data-label="<?php esc_attr_e( 'وضعیت', AFROZWEB_GARANTY_SLUG ); ?>">
                    <span class="status-badge status-<?php echo esc_attr( $warranty->status ); ?>">
                        <?php echo esc_html( $this->get_warranty_status_label( $warranty->status ) ); ?>
                    </span>
                    </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="8" class="no-results"><?php esc_html_e( 'هیچ گارانتی برای نمایش یافت نشد.', AFROZWEB_GARANTY_SLUG ); ?></td>
            </tr>
            <?php
        }
        $table_rows_html = ob_get_clean();

        // ساخت HTML صفحه‌بندی
        $total_pages = ceil( $total_items / $per_page );
        $pagination_html = '';
        if ( $total_pages > 1 ) {
            $pagination_html = paginate_links( [
                'base'      => '#', // Prevent page reload
                'format'    => '?paged=%#%',
                'current'   => $page,
                'total'     => $total_pages,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'type'      => 'plain'
            ] );
        }

        wp_send_json_success( [
            'table_rows' => $table_rows_html,
            'pagination' => $pagination_html,
        ] );

        wp_die();
    }

    public function warranty_search_shortcode_handler()
    {
        wp_enqueue_style( 'warranty-frontend-search-style' );
        wp_enqueue_script( 'warranty-frontend-search-script' );

        // رندر کردن view
        ob_start();
        include $this->get_template_part( 'warranty-search-view' );
        return ob_get_clean();
    }

    public function handle_warranty_search_ajax(){
        check_ajax_referer( 'warranty_search_nonce', 'nonce' );

        $phone_number = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';

        // 1. تبدیل اعداد فارسی به انگلیسی
        $phone_number = $this->convert_persian_numbers_to_english( $phone_number );

        // 2. اعتبارسنجی شماره تماس
        if ( ! preg_match( '/^09\d{9}$/', $phone_number ) ) {
            wp_send_json_error( [ 'message' => __( 'لطفاً یک شماره تماس معتبر ۱۱ رقمی که با 09 شروع می‌شود وارد کنید.', AFROZWEB_GARANTY_SLUG ) ] );
        }

        global $wpdb;
        $warranties_table = $wpdb->prefix . 'warranties';
        $users_table = $wpdb->users;

        // 3. جستجو در دیتابیس
        $query = $wpdb->prepare(
            "SELECT w.*, u.display_name as representative_name 
         FROM {$warranties_table} w
         LEFT JOIN {$users_table} u ON w.representative_id = u.ID
         WHERE w.customer_phone = %s
         ORDER BY w.created_at DESC",
            $phone_number
        );
        $results = $wpdb->get_results( $query );

        // 4. بررسی نتیجه
        if ( empty( $results ) ) {
            wp_send_json_error( [ 'message' => __( 'هیچ گارانتی با این شماره تماس یافت نشد.', AFROZWEB_GARANTY_SLUG ) ] );
        }

        // 5. ساخت HTML جدول نتایج
        ob_start();
        ?>
        <div class="table-wrapper">
            <table class="warranty-list-table">
                <thead>
                <tr>
                    <th><?php esc_html_e( 'ردیف', AFROZWEB_GARANTY_SLUG ); ?></th>
                    <th><?php esc_html_e( 'شماره گارانتی', AFROZWEB_GARANTY_SLUG ); ?></th>
                    <th><?php esc_html_e( 'نماینده', AFROZWEB_GARANTY_SLUG ); ?></th>
                    <th><?php esc_html_e( 'نصاب', AFROZWEB_GARANTY_SLUG ); ?></th>
                    <th><?php esc_html_e( 'آدرس', AFROZWEB_GARANTY_SLUG ); ?></th>
                    <th><?php esc_html_e( 'تاریخ نصب', AFROZWEB_GARANTY_SLUG ); ?></th>
                    <th><?php esc_html_e( 'زمان باقیمانده', AFROZWEB_GARANTY_SLUG ); ?></th>
                    <th><?php esc_html_e( 'وضعیت', AFROZWEB_GARANTY_SLUG ); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $results as $index => $warranty ) : ?>
                    <tr>
                        <td data-label="<?php esc_attr_e( 'ردیف', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo $index + 1; ?></td>
                        <td data-label="<?php esc_attr_e( 'شماره گارانتی', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->warranty_number ) ? '-' : esc_html( $warranty->warranty_number ); ?></td>
                        <td data-label="<?php esc_attr_e( 'نماینده', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->representative_name ) ? '-' : esc_html( $warranty->representative_name ); ?></td>
                        <td data-label="<?php esc_attr_e( 'نصاب', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->installer_name ) ? '-' : esc_html( $warranty->installer_name ); ?></td>
                        <td data-label="<?php esc_attr_e( 'آدرس', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->project_address ) ? '-' : esc_html( $warranty->project_address ); ?></td>
                        <td data-label="<?php esc_attr_e( 'تاریخ نصب', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo empty( $warranty->installation_date ) ? '-' : esc_html( $warranty->installation_date ); ?></td>
                        <td data-label="<?php esc_attr_e( 'زمان باقیمانده', AFROZWEB_GARANTY_SLUG ); ?>"><?php echo $this->calculate_time_until_expiration( $warranty->expiration_date ); ?></td>
                        <td data-label="<?php esc_attr_e( 'وضعیت', AFROZWEB_GARANTY_SLUG ); ?>">
                        <span class="status-badge status-<?php echo esc_attr( $warranty->status ); ?>">
                            <?php echo esc_html( $this->get_warranty_status_label( $warranty->status ) ); ?>
                        </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        $table_html = ob_get_clean();

        wp_send_json_success( [ 'table_html' => $table_html ] );
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

    public function get_warranty_status_label( $status_key ) {
        $statuses = [
            'approved'         => __( 'تایید شده', AFROZWEB_GARANTY_SLUG ),
            'pending_approval' => __( 'در انتظار تایید', AFROZWEB_GARANTY_SLUG ),
            'expired'          => __( 'منقضی شده', AFROZWEB_GARANTY_SLUG ),
        ];
        return $statuses[ $status_key ] ?? ucfirst( str_replace( '_', ' ', $status_key ) );
    }

    /**
     * تابع کمکی برای تبدیل اعداد فارسی و عربی به انگلیسی.
     */
    public function convert_persian_numbers_to_english($string) {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = range(0, 9);
        return str_replace($persian, $english, str_replace($arabic, $english, $string));
    }

    /**
     * تابع کمکی برای محاسبه زمان باقیمانده تا انقضا.
     */
    public function calculate_time_until_expiration( $expiration_date_str ) {
        $expiration_date = new DateTime( $expiration_date_str );
        $now = new DateTime();

        if ( $now > $expiration_date ) {
            return __( 'منقضی شده', AFROZWEB_GARANTY_SLUG );
        }

        $interval = $now->diff( $expiration_date );

        $parts = [];
        if ( $interval->y > 0 ) $parts[] = sprintf( _n( '%d سال', '%d سال', $interval->y, AFROZWEB_GARANTY_SLUG ), $interval->y );
        if ( $interval->m > 0 ) $parts[] = sprintf( _n( '%d ماه', '%d ماه', $interval->m, AFROZWEB_GARANTY_SLUG ), $interval->m );
        if ( $interval->d > 0 ) $parts[] = sprintf( _n( '%d روز', '%d روز', $interval->d, AFROZWEB_GARANTY_SLUG ), $interval->d );

        return empty($parts) ? __( 'امروز منقضی می‌شود', AFROZWEB_GARANTY_SLUG ) : implode( ' و ', $parts );
    }
}
