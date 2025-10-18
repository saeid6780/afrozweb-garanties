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
    public function enqueue_styles() {

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

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

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

    }

    public function settings_menu ()
    {
        // Add Menu
        add_menu_page(
            __( 'Books List', AFROZWEB_GARANTY_SLUG ),
            __( 'Books List', AFROZWEB_GARANTY_SLUG ),
            'manage_options',
            AFROZWEB_GARANTY_SLUG,
            [
                $this,
                'afrozweb_garanties_list_content'
            ], 'dashicons-sos', '5'
        );
    }

    public function afrozweb_garanties_list_content ()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-books-list-table.php';
        //Create an instance of our package class...
        $list_table = new Afrozweb_Garanty_List_Table();
        //Fetch, prepare, sort, and filter our data...
        $list_table->prepare_items();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e( 'Books', AFROZWEB_GARANTY_SLUG ) ?></h1>
            <hr class="wp-header-end">
            <?php $list_table->views(); ?>
            <form id="appointments-form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST[ 'page' ] ?>"/>
                <?php $list_table->search_box( __( 'Search Books', AFROZWEB_GARANTY_SLUG ), 'search' ); ?>
                <?php $list_table->display(); ?>
            </form>
        </div>
        <?php
    }
}
