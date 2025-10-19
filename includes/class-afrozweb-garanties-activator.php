<?php

/**
 * Fired during plugin activation
 *
 * @link       https://linkedin.com/in/saeid-sadigh-zadeh-8861688a
 * @since      1.0.0
 *
 * @package    Afrozweb_Garanties
 * @subpackage Afrozweb_Garanties/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Afrozweb_Garanties
 * @subpackage Afrozweb_Garanties/includes
 * @author     saeid6780 <saeid6780sz@gmail.com>
 */
class Afrozweb_Garanties_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        self::create_warranties_table();
	}

    public static function create_warranties_table()
    {
        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $table_name      = $wpdb->prefix . "warranties";
        $charset_collate = $wpdb->get_charset_collate();
        $sql             = "CREATE TABLE IF NOT EXISTS $table_name (
                            -- ستون‌های اصلی
                            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                            customer_name VARCHAR(255) NOT NULL,
                            customer_phone VARCHAR(20) NOT NULL,
                            installed_area DECIMAL(10, 2) NULL,
                            project_address TEXT NOT NULL,
                            project_postal_code VARCHAR(20) NULL,
                            installer_name VARCHAR(255) NULL,
                            installer_phone VARCHAR(20) NOT NULL,
                            installer_national_id VARCHAR(20) NULL,
                            warranty_number VARCHAR(100) NOT NULL,
                            product_type VARCHAR(100) NOT NULL,
                        
                            -- ستون‌های مربوط به تاریخ و مدت گارانتی
                            installation_date DATE NOT NULL,
                            warranty_period_years TINYINT UNSIGNED NULL,
                            warranty_period_months TINYINT UNSIGNED NULL,
                            expiration_date DATE NULL,
                        
                            -- ستون‌های داده‌های اضافی
                            project_images TEXT NULL, -- ذخیره آرایه لینک‌های تصویر در قالب JSON
                            project_description LONGTEXT NULL,
                            extra_data LONGTEXT NULL, -- برای داده‌های آینده در قالب JSON
                        
                            -- ستون‌های مدیریتی و ارتباطی
                            status VARCHAR(50) NOT NULL DEFAULT 'pending_approval',
                            representative_id BIGINT(20) UNSIGNED NOT NULL, -- نام اصلاح شده برای اشاره به ID کاربر نماینده
                            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        
                            -- کلید اصلی و کلیدهای یکتا
                            PRIMARY KEY (id),
                            UNIQUE KEY warranty_number_unique (warranty_number),
                        
                            -- ایندکس‌ها برای بهینه‌سازی سرعت کوئری‌ها
                            KEY representative_id_index (representative_id), -- ایندکس برای ستون نماینده
                            KEY status_index (status) -- ایندکس برای ستون وضعیت
                        
                        ) $charset_collate;";

        dbDelta( $sql );
    }
}
