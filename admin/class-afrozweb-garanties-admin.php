<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://linkedin.com/in/saeid-sadigh-zadeh-8861688a
 * @since      1.0.0
 *
 * @package    Afrozweb_Garanties
 * @subpackage Afrozweb_Garanties/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Afrozweb_Garanties
 * @subpackage Afrozweb_Garanties/admin
 * @author     saeid6780 <saeid6780sz@gmail.com>
 */
class Afrozweb_Garanties_Admin {

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

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles( $hook ) {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Afrozweb_Garanties_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Afrozweb_Garanties_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        // اطمینان از اینکه اسکریپت‌ها فقط در صفحه پلاگin ما بارگذاری شوند
        if ( ! str_contains( $hook, 'warranty-management-add-new' ) ) {
            return;
        }

        wp_enqueue_style( 'awg-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
        wp_enqueue_style(
            'persian-datepicker',
            AFROZWEB_GARANTY_URL . 'public/css/persian-datepicker.min.css',
            '1.0.0',
        );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts( $hook ) {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Afrozweb_Garanties_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Afrozweb_Garanties_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        // اطمینان از اینکه اسکریپت‌ها فقط در صفحه پلاگin ما بارگذاری شوند
        if ( ! str_contains( $hook, 'warranty-management-add-new' ) ) {
            return;
        }

        wp_deregister_script('jquery');
        wp_register_script('jquery', AFROZWEB_GARANTY_URL . 'public/js/jquery.js', [], null, true);
        wp_enqueue_script('jquery');

        wp_enqueue_script( 'awg-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array( 'jquery' ) );

        wp_enqueue_script(
            'persian-date',
            AFROZWEB_GARANTY_URL . 'public/js/persian-date.min.js',
            [ 'jquery' ],
            '1.0.0',
            true // در فوتر لود شود

        );

        wp_enqueue_script(
            'persian-datepicker',
            AFROZWEB_GARANTY_URL . 'public/js/persian-datepicker.min.js',
            [ 'jquery','persian-date' ],
            '1.0.0',
            true // در فوتر لود شود
        );

        wp_enqueue_script(
            'afrozweb-garanties-admin',
            AFROZWEB_GARANTY_URL . 'public/js/admin-scripts.js',
            [ 'persian-datepicker','awg-select2' ],
            '1.0.0',
            true // در فوتر لود شود
        );

    }

    public function settings_menu ()
    {
        // منوی اصلی
        add_menu_page(
            __( 'Warranties', AFROZWEB_GARANTY_SLUG ),          // عنوان صفحه (Page Title)
            __( 'گارانتی‌ها', AFROZWEB_GARANTY_SLUG ),          // عنوان منو (Menu Title)
            'manage_options',                                   // سطح دسترسی مورد نیاز
            'warranty-management-list',                         // اسلاگ (Slug) منو
            [ $this, 'garanties_list_content' ],                    // تابع نمایش محتوای صفحه لیست
            'dashicons-shield-alt',                             // آیکون منو
            25                                                  // موقعیت منو در پیشخوان
        );

        // زیرمنوی "لیست گارانتی‌ها"
        add_submenu_page(
            'warranty-management-list',                         // اسلاگ منوی والد
            __( 'لیست گارانتی‌ها', AFROZWEB_GARANTY_SLUG ),     // عنوان صفحه
            __( 'لیست گارانتی‌ها', AFROZWEB_GARANTY_SLUG ),     // عنوان منو
            'manage_options',                                   // سطح دسترسی
            'warranty-management-list',                         // اسلاگ این منو (مشابه والد برای صفحه پیش‌فرض)
            [ $this, 'garanties_list_content' ]                     // تابع نمایش محتوا
        );

        // زیرمنوی "افزودن گارانتی"
        add_submenu_page(
            'warranty-management-list',                         // اسلاگ منوی والد
            __( 'افزودن گارانتی جدید', AFROZWEB_GARANTY_SLUG ), // عنوان صفحه
            __( 'افزودن جدید', AFROZWEB_GARANTY_SLUG ),         // عنوان منو
            'manage_options',                                   // سطح دسترسی
            'warranty-management-add-new',                      // اسلاگ این منو
            [ $this, 'warranty_management_add_edit_page' ]                 // تابع نمایش محتوای صفحه افزودن/ویرایش
        );

        $print_page_hook = add_submenu_page(
            null,                                       // Parent Slug (null for no menu)
            __( 'چاپ گارانتی', AFROZWEB_GARANTY_SLUG ),    // Page Title
            __( 'چاپ گارانتی', AFROZWEB_GARANTY_SLUG ),    // Menu Title
            'manage_options',                           // Capability
            'warranty-print',                           // Menu Slug (برای استفاده در URL)
            [ $this, 'warranty_management_print_page_callback' ]  // تابع نمایش محتوا
        );
        add_action( "load-{$print_page_hook}", [ $this, 'warranty_management_load_print_page_screen' ] );
    }

    function warranty_management_load_print_page_screen() {
        // 1. بررسی امنیت و دریافت ID گارانتی از URL
        if ( ! isset( $_GET['warranty_id'] ) ) {
            wp_die( esc_html__( 'شناسه گارانتی مشخص نشده است.', AFROZWEB_GARANTY_SLUG ) );
        }
        $warranty_id = absint( $_GET['warranty_id'] );

        // 2. واکشی اطلاعات کامل گارانتی
        global $wpdb;
        $warranty = $wpdb->get_row( $wpdb->prepare(
            "SELECT w.*, u.display_name as representative_name 
         FROM {$wpdb->prefix}warranties w
         LEFT JOIN {$wpdb->users} u ON w.representative_id = u.ID
         WHERE w.id = %d",
            $warranty_id
        ) );

        // 3. اگر گارانتی یافت نشد، خطا نمایش بده
        if ( ! $warranty ) {
            wp_die( esc_html__( 'گارانتی با این شناسه یافت نشد.', AFROZWEB_GARANTY_SLUG ) );
        }

        // 4. رندر کردن فایل View
        // ما تمام HTML، از جمله تگ‌های <html> و <head> را در فایل view خود مدیریت می‌کنیم.
        require_once AFROZWEB_GARANTY_BASE . 'templates/admin/warranty-print-page.php';

        // 5. **مهم‌ترین بخش:** اجرای اسکریپت را در همینجا متوقف کن!
        exit;
    }

    function warranty_management_print_page_callback() {
        // 1. بررسی امنیت و دریافت ID گارانتی از URL
        /*if ( ! isset( $_GET['warranty_id'] ) ) {
            wp_die( esc_html__( 'شناسه گارانتی مشخص نشده است.', AFROZWEB_GARANTY_SLUG ) );
        }
        $warranty_id = absint( $_GET['warranty_id'] );

        // 2. واکشی اطلاعات کامل گارانتی به همراه نام نماینده
        global $wpdb;
        $warranty = $wpdb->get_row( $wpdb->prepare(
            "SELECT w.*, u.display_name as representative_name 
         FROM {$wpdb->prefix}warranties w
         LEFT JOIN {$wpdb->users} u ON w.representative_id = u.ID
         WHERE w.id = %d",
            $warranty_id
        ) );

        // 3. اگر گارانتی یافت نشد، خطا نمایش بده
        if ( ! $warranty ) {
            wp_die( esc_html__( 'گارانتی با این شناسه یافت نشد.', AFROZWEB_GARANTY_SLUG ) );
        }

        // 4. رندر کردن فایل View و پاس دادن اطلاعات به آن
        require_once AFROZWEB_GARANTY_BASE . 'templates/admin/warranty-print-page.php';*/
    }

    public function warranty_management_handle_form_submission() {
        // فقط در صفحه افزودن/ویرایش پلاگin ما اجرا شود
        if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'warranty-management-add-new' ) {
            return;
        }
        $data = [];

        // بررسی می‌کنیم که فرم با استفاده از nonce امن ما ارسال شده باشد
        if ( isset( $_POST['submit_warranty_nonce'] ) && wp_verify_nonce( $_POST['submit_warranty_nonce'], 'save_warranty_action' ) ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'warranties';

            if ( ! empty( $_POST[ 'installation_date_alt' ] ) ) {
                // تبدیل timestamp میلی‌ثانیه‌ای به ثانیه
                $timestamp_ms = intval($_POST['installation_date_alt']);
                $timestamp = $timestamp_ms / 1000;

                // تبدیل به فرمت DATE (فقط تاریخ)
                $installation_date = gmdate('Y-m-d', $timestamp);
            }else
                $installation_date = gmdate( 'Y-m-d', time() );

            // 1. آماده‌سازی داده‌ها برای ذخیره در دیتابیس (با فیلد جدید نماینده)
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
                'warranty_period_years' => absint( $_POST['warranty_period_years'] ),
                'warranty_period_months'=> absint( $_POST['warranty_period_months'] ),
                'project_description'   => sanitize_textarea_field( $_POST['project_description'] ),
                'status'                => sanitize_text_field( $_POST['status'] ),
                'representative_id'     => absint( $_POST['representative_id'] ), // <-- اصلاح کلیدی
            ];

            // 2. محاسبه تاریخ انقضا
            if ( ! empty( $data['installation_date'] ) ) {
                $installation_date_obj = new DateTime( $data['installation_date'] );
                $installation_date_obj->modify( '+' . $data['warranty_period_years'] . ' years' );
                $installation_date_obj->modify( '+' . $data['warranty_period_months'] . ' months' );
                $data['expiration_date'] = $installation_date_obj->format( 'Y-m-d' );
            }

            // 3. تشخیص حالت افزودن یا ویرایش
            $warranty_id = isset( $_POST['warranty_id'] ) ? absint( $_POST['warranty_id'] ) : 0;
            $redirect_url = '';

            // --- [بخش جدید] منطق ارسال SMS ---
            $should_send_sms = false; // به طور پیش‌فرض SMS ارسال نمی‌شود

            if ( $warranty_id > 0 ) {
                // --- حالت ویرایش ---
                $new_status = $data['status'];
                $old_status = isset( $_POST['current_status'] ) ? sanitize_text_field( $_POST['current_status'] ) : '';

                // شرط اصلی: فقط زمانی SMS ارسال شود که وضعیت برای اولین بار به 'approved' تغییر می‌کند
                if ( $new_status === 'approved' && $old_status !== 'approved' ) {
                    $should_send_sms = true;
                }
            } else {
                // --- حالت افزودن جدید ---
                // اگر ادمین از همان ابتدا یک گارانتی را با وضعیت 'approved' ایجاد کرد
                if ( $data['status'] === 'approved' ) {
                    $should_send_sms = true;
                }
            }

            if ( $warranty_id > 0 ) {
                // حالت به‌روزرسانی (Update)
                $result = $wpdb->update( $table_name, $data, [ 'id' => $warranty_id ] );
                $message_code = ( $result !== false ) ? '2' : '3'; // 2=موفق, 3=خطا
                $redirect_url = admin_url( 'admin.php?page=warranty-management-add-new&id=' . $warranty_id . '&message=' . $message_code );
            } else {
                // حالت افزودن (Insert)
                $result = $wpdb->insert( $table_name, $data );
                if ( $result ) {
                    $new_id = $wpdb->insert_id;
                    $redirect_url = admin_url( 'admin.php?page=warranty-management-add-new&id=' . $new_id . '&message=1' ); // 1=ایجاد موفق
                } else {
                    $redirect_url = admin_url( 'admin.php?page=warranty-management-add-new&message=4' ); // 4=خطا در ایجاد
                }
            }

            // --- اجرای تابع ارسال SMS در صورت نیاز ---
            if ( $should_send_sms && $result !== false ) {
                // فراخوانی تابع سفارشی شما برای ارسال SMS
                $this->send_approval_sms_notification( $data );
            }

            // ریدایرکت به همراه پارامترها
            wp_safe_redirect( $redirect_url );
            exit;
        }
    }

    public function send_approval_sms_notification( $warranty_data ) {
        // اطمینان از اینکه ID معتبر است
        if ( empty( $warranty_data ) ) {
            return;
        }

        // 2. دریافت شماره تماس نماینده از جدول کاربران
        $representative_data = get_user_by( 'id', $warranty_data[ 'representative_id' ] );
        $representative_phone = $representative_data->user_login;
        $representative_name = $representative_data->user_nicename;

        // 3. استخراج شماره تماس مشتری
        $customer_phone = $warranty_data[ 'customer_phone' ];
        $warranty_number = empty( $warranty_data[ 'warranty_number' ] ) ? '-' : $warranty_data[ 'warranty_number' ];

        // 4. آماده‌سازی متن پیام‌ها
        $customer_message = sprintf(
            "%s عزیز، گارانتی محصول شما با شماره %s فعال شد. زمان باقی ماند گارانتی خود را از وب سایت ایزوگام شرق میتوانید مشاهد کنید.",
            $warranty_data[ 'customer_name' ],
            $warranty_number,
        );
        $sms_to_customer = $this->send_sms_to_number( $customer_phone, $customer_message );

        if ( isset( $sms_to_customer[ 'error' ] ) )
            error_log( "Error: SMS to Customer ($customer_phone): $customer_message" );
        else
            error_log( "SMS to Customer ($customer_phone): $customer_message" );

        $representative_message = sprintf(
            "%s عزیز، گارانتی شماره %s برای مشتری %s با موفقیت فعال شد.",
            $representative_name,
            $warranty_number,
            $warranty_data[ 'customer_name' ]
        );
        $sms_to_representative = $this->send_sms_to_number( $representative_phone, $representative_message );

        if ( isset( $sms_to_representative[ 'error' ] ) )
            error_log( "Error: SMS to representative ($customer_phone): $customer_message" );
        else
            error_log( "SMS to representative ($customer_phone): $customer_message" );

        error_log( "SMS to Representative ($representative_phone): $representative_message" );
    }

    public function warranty_management_add_edit_page()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'warranties';
        $message = '';
        $notice_class = '';

        // واکشی پیام‌ها از URL
        if ( isset( $_GET['message'] ) ) {
            switch ( $_GET['message'] ) {
                case '1':
                    $message = __( 'گارانتی با موفقیت ایجاد شد. اکنون می‌توانید به ویرایش ادامه دهید.', AFROZWEB_GARANTY_SLUG );
                    $notice_class = 'notice-success';
                    break;
                case '2':
                    $message = __( 'گارانتی با موفقیت به‌روزرسانی شد.', AFROZWEB_GARANTY_SLUG );
                    $notice_class = 'notice-success';
                    break;
                case '3':
                    $message = __( 'خطایی هنگام به‌روزرسانی گارانتی رخ داد.', AFROZWEB_GARANTY_SLUG );
                    $notice_class = 'notice-error';
                    break;
                case '4':
                    $message = __( 'خطایی هنگام ایجاد گارانتی رخ داد.', AFROZWEB_GARANTY_SLUG );
                    $notice_class = 'notice-error';
                    break;
            }
        }

        $warranty = null;
        $warranty_id_from_url = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
        if ( $warranty_id_from_url > 0 ) {
            $warranty = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $warranty_id_from_url ) );
        }

        // دریافت لیست کاربران با نقش "representative" برای ارسال به فرم
        $representatives = get_users( [ 'role' => 'representative', 'fields' => [ 'ID', 'display_name' ] ] );

        // رندر کردن HTML فرم

        // --- بخش سوم: رندر کردن HTML فرم ---
        require_once AFROZWEB_GARANTY_BASE . 'templates/admin/warranty-add-edit-form.php';
    }

    public function garanties_list_content ()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-garanties-list-table.php';
        //Create an instance of our package class...
        $list_table = new Afrozweb_Garanty_List_Table();
        //Fetch, prepare, sort, and filter our data...
        $list_table->prepare_items();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'لیست گارانتی‌ها', AFROZWEB_GARANTY_SLUG ); ?></h1>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=warranty-management-add-new' ) ); ?>" class="page-title-action">
                <?php esc_html_e( 'افزودن جدید', AFROZWEB_GARANTY_SLUG ); ?>
            </a>
            <hr class="wp-header-end">
            <?php
            // نمایش پیام‌های بازگشتی از عملیات حذف
            if ( isset( $_GET['deleted'] ) && $_GET['deleted'] > 0 ) {
                $count = intval($_GET['deleted']);
                $message = sprintf(
                    _n(
                        '%d warranty has been successfully deleted.',
                        '%d warranties have been successfully deleted.',
                        $count,
                        AFROZWEB_GARANTY_SLUG
                    ),
                    $count
                );
                echo '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';
            }
            ?>
            <?php $list_table->views(); ?>
            <form id="appointments-form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST[ 'page' ] ?>"/>
                <?php
                $list_table->search_box( __( 'جستجوی گارانتی', AFROZWEB_GARANTY_SLUG ), 'warranty_search' );
                $list_table->display();
                ?>
            </form>
        </div>
        <?php
    }

    public function send_sms_to_number( $number, $message )
    {
        if ( empty( $number ) || empty( $message ) )
            return false;

        $recipient = $this->sanitize_mobile( $number );

        if ( ! $recipient ){
            error_log( 'Wrong mobile number format' );
            return false;
        }

        $username = "isogumeshargh";
        $password = "ish@2709_6736";
        $lineNumber = "20008040";

        $wsdl = "http://payamkotah.ir/service.asmx?WSDL";
        $options = [
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
        ];

        try {
            $client = new SoapClient($wsdl, $options);

            $params = [
                'Username' => $username,
                'Password' => $password,
                'LineNumber' => $lineNumber,
                'Mobile' => $recipient,
                'Message' => $message,
                'FlashSend' => false,
                'WapPushSend' => false,
                'SendWithBusinessName' => false,
            ];

            $reference = 0;
            $params['Refrence'] = &$reference;
            $client->__soapCall("FastSend", [ $params ] );

            if ($reference > 0) {
                return [ 'success' => 'پیام با موفقیت ارسال شد.' ];
            } else {
                return [ 'success' => 'پیام با موفقیت ارسال شد.' ];
            }
        } catch (Exception $e) {
            return [ 'error' => 'پیام ارسال نشد.' ];
        }
    }

    public function sanitize_mobile ( $mobile )
    {
        $mobile = $this->convert_numbers( $mobile );

        return preg_match( '/^09[0-9]{9}$/', $mobile ) ? $mobile : false;
    }

    function convert_numbers ( $string, $to_persian = false )
    {
        $string  = sanitize_text_field( $string );
        $persian = [ '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ];
        $english = [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ];

        return $to_persian ? str_replace( $english, $persian, $string ) : str_replace( $persian, $english, $string );
    }
}
