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
        self::create_books_table();
	}

    public static function create_books_table()
    {
        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $table_name      = $wpdb->prefix . "books";
        $charset_collate = $wpdb->get_charset_collate();
        $sql             = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            published_year INT NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta( $sql );
    }
}
