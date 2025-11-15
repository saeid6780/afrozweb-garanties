<?php

if ( ! class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class Afrozweb_Garanty_List_Table extends \WP_List_Table {

    private $table_name;

    /**
     * سازنده کلاس. تنظیمات اولیه و نام جدول را مشخص می‌کند.
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'warranties';

        parent::__construct( [
            'singular' => __( 'Warranty', AFROZWEB_GARANTY_SLUG ), // نام تکین آیتم
            'plural'   => __( 'Warranties', AFROZWEB_GARANTY_SLUG ),// نام جمع آیتم
            'ajax'     => false // این جدول از ایجکس استفاده نمی‌کند
        ] );
    }

    /**
     * تعریف ستون‌های جدول.
     * @return array آرایه‌ای از ستون‌ها
     */
    public function get_columns() {
        return [
            'cb'                  => '<input type="checkbox" />',
            'warranty_number'     => __( 'شماره گارانتی', AFROZWEB_GARANTY_SLUG ),
            'installer_name'      => __( 'نام نصاب', AFROZWEB_GARANTY_SLUG ),
            'installation_date'   => __( 'تاریخ نصب', AFROZWEB_GARANTY_SLUG ),
            'expiration_date'     => __( 'تاریخ انقضا', AFROZWEB_GARANTY_SLUG ),
            'representative_name' => __( 'نام نماینده', AFROZWEB_GARANTY_SLUG ),
            'status'              => __( 'وضعیت', AFROZWEB_GARANTY_SLUG ), // <-- ستون جدید
        ];
    }

    /**
     * تعریف ستون‌های قابل مرتب‌سازی.
     * @return array
     */
    protected function get_sortable_columns() {
        return [
            'warranty_number'     => [ 'warranty_number', false ],
            'installer_name'      => [ 'installer_name', false ],
            'installation_date'   => [ 'installation_date', false ],
            'expiration_date'     => [ 'expiration_date', false ],
            'representative_name' => [ 'representative_name', false ],
            'status'              => [ 'status', false ],
        ];
    }

    /**
     * تعریف تب‌های فیلتر بر اساس وضعیت (Status).
     */
    protected function get_views() {
        global $wpdb;
        $current_status = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : 'all';
        $base_url = admin_url( 'admin.php?page=warranty-management-list' );

        // شمارش تعداد گارانتی‌ها در هر وضعیت
        $counts = (array) $wpdb->get_results( "
            SELECT status, COUNT(id) as count 
            FROM {$this->table_name} 
            GROUP BY status
        ", OBJECT_K );

        $total_count = array_sum( array_column( $counts, 'count' ) );
        $approved_count = $counts['approved']->count ?? 0;
        $pending_count = $counts['pending_approval']->count ?? 0;
        $expired_count = $counts['expired']->count ?? 0;

        $views = [
            'all' => sprintf( '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
                esc_url( $base_url ),
                $current_status === 'all' ? 'current' : '',
                __( 'همه', AFROZWEB_GARANTY_SLUG ),
                $total_count
            ),
            'approved' => sprintf( '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
                esc_url( add_query_arg( 'status', 'approved', $base_url ) ),
                $current_status === 'approved' ? 'current' : '',
                __( 'تایید شده', AFROZWEB_GARANTY_SLUG ),
                $approved_count
            ),
            'pending_approval' => sprintf( '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
                esc_url( add_query_arg( 'status', 'pending_approval', $base_url ) ),
                $current_status === 'pending_approval' ? 'current' : '',
                __( 'در انتظار تایید', AFROZWEB_GARANTY_SLUG ),
                $pending_count
            ),
            'expired' => sprintf( '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
                esc_url( add_query_arg( 'status', 'expired', $base_url ) ),
                $current_status === 'expired' ? 'current' : '',
                __( 'منقضی شده', AFROZWEB_GARANTY_SLUG ),
                $expired_count
            ),
        ];

        return $views;
    }

    /**
     * تعریف عملیات‌های گروهی (Bulk Actions).
     * @return array
     */
    protected function get_bulk_actions() {
        return [
            'bulk_delete' => __( 'حذف', AFROZWEB_GARANTY_SLUG )
        ];
    }

    /**
     * متد اصلی برای آماده‌سازی آیتم‌ها جهت نمایش.
     * این متد داده‌ها را از دیتابیس واکشی، فیلتر، جستجو و صفحه‌بندی می‌کند.
     */
    public function prepare_items() {
        global $wpdb;

        // ابتدا Bulk Action را پردازش می‌کنیم
        $this->process_bulk_action();

        $this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns(), 'warranty_number' ];

        $per_page     = $this->get_items_per_page( 'warranties_per_page', 20 );
        $current_page = $this->get_pagenum();
        $offset       = ( $current_page - 1 ) * $per_page;

        // --- ساخت کوئری ---
        $where_clauses = [];
        $query_params = [];

        // 1. فیلتر بر اساس وضعیت
        if ( isset( $_GET['status'] ) && ! empty( $_GET['status'] ) && $_GET['status'] !== 'all' ) {
            $where_clauses[] = "w.status = %s";
            $query_params[] = sanitize_key( $_GET['status'] );
        }

        // 2. فیلتر بر اساس جستجو [اصلاح شده]
        if ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) {
            $search_term = '%' . $wpdb->esc_like( stripslashes( $_REQUEST['s'] ) ) . '%';
            $where_clauses[] = $wpdb->prepare("(
                w.id LIKE %s OR 
                w.customer_name LIKE %s OR w.customer_phone LIKE %s OR 
                w.project_postal_code LIKE %s OR w.installer_name LIKE %s OR 
                w.installer_phone LIKE %s OR w.installer_national_id LIKE %s OR 
                w.warranty_number LIKE %s OR u.display_name LIKE %s OR 
                u.user_login LIKE %s)",
                // 10 بار تکرار متغیر برای 10 placeholder
                $search_term, $search_term, $search_term, $search_term, $search_term,
                $search_term, $search_term, $search_term, $search_term, $search_term
            );
        }

        $where_sql = ! empty( $where_clauses ) ? 'WHERE ' . implode( ' AND ', $where_clauses ) : '';

        // --- کوئری شمارش کل آیتم‌ها [اصلاح شده] ---
        $total_items_query = "SELECT COUNT(w.id) FROM {$this->table_name} w LEFT JOIN {$wpdb->users} u ON w.representative_id = u.ID {$where_sql}";
        $total_items = $wpdb->get_var( $wpdb->prepare( $total_items_query, $query_params ) );

        // --- کوئری اصلی برای واکشی داده‌ها [اصلاح شده] ---
        $orderby = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : 'created_at';
        $order   = isset( $_GET['order'] ) ? strtoupper( sanitize_key( $_GET['order'] ) ) : 'DESC';

        // لیست سفید برای ستون‌های قابل مرتب‌سازی
        $valid_orderby_cols = [ 'warranty_number', 'installer_name', 'installation_date', 'expiration_date', 'status' ];
        if ( in_array( $orderby, $valid_orderby_cols ) ) {
            $orderby_sql = 'w.' . $orderby;
        } elseif ( $orderby === 'representative_name' ) {
            $orderby_sql = 'u.display_name';
        } else {
            $orderby_sql = 'w.created_at'; // پیش‌فرض
        }

        $data_query = "SELECT w.*, u.display_name as representative_name FROM {$this->table_name} w 
                       LEFT JOIN {$wpdb->users} u ON w.representative_id = u.ID
                       {$where_sql}
                       ORDER BY {$orderby_sql} {$order}
                       LIMIT %d OFFSET %d";

        // افزودن پارامترهای صفحه‌بندی به انتهای آرایه پارامترها
        $query_params_with_pagination = array_merge( $query_params, [$per_page, $offset] );

        $this->items = $wpdb->get_results( $wpdb->prepare( $data_query, $query_params_with_pagination ) );

        $this->set_pagination_args( [
            'total_items' => $total_items,
            'per_page'    => $per_page
        ] );
    }

    public function process_bulk_action() {
        global $wpdb;

        // بخش 1: مدیریت حذف تکی (Single Delete)
        // current_action() پارامتر 'action' از URL را می‌خواند.
        if ( 'delete' === $this->current_action() ) {
            // بررسی Nonce برای امنیت
            if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'warranty_delete_nonce' ) ) {
                wp_die( 'Security check failed!' );
            }

            // دریافت ID و حذف از دیتابیس
            $id = absint( $_GET['warranty'] );
            $wpdb->delete( $this->table_name, [ 'id' => $id ], [ '%d' ] );

            // ریدایرکت با پیام موفقیت
            $redirect_url = add_query_arg( 'deleted', 1, remove_query_arg( [ 'action', 'warranty', '_wpnonce' ], wp_get_referer() ) );
            wp_safe_redirect( $redirect_url );
            exit;
        }

        // بخش 2: مدیریت حذف گروهی (Bulk Delete)
        // current_action() همچنین اکشن انتخاب شده از دراپ‌داون bulk action را می‌خواند.
        if ( 'bulk_delete' === $this->current_action() ) {

            if ( ! empty( $_POST['warranty_ids'] ) ) {
                $ids = array_map( 'absint', $_POST['warranty_ids'] );
                $count = count($ids);
                $ids_placeholder = implode( ',', array_fill( 0, $count, '%d' ) );
                $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->table_name} WHERE id IN ($ids_placeholder)", $ids ) );

                // ریدایرکت با پیام موفقیت
                $redirect_url = add_query_arg( 'deleted', $count, remove_query_arg( ['action', 'action2', '_wpnonce', 'warranty_ids'], wp_get_referer() ) );
                wp_safe_redirect( $redirect_url );
                exit;
            }
        }
    }

    /**
     * رندر کردن محتوای هر ستون.
     */
    protected function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'installer_name':
            case 'representative_name':
                return esc_html( $item->$column_name );
            case 'installation_date':
            case 'expiration_date':
                return date_i18n( 'Y/m/d', strtotime( $item->$column_name ) );
            case 'status':
                return $this->get_status_label( $item->status );
            default:
                return print_r( $item, true );
        }
    }

    /**
     * رندر کردن ستون Checkbox برای Bulk Actions.
     */
    protected function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="warranty_ids[]" value="%d" />', $item->id );
    }

    /**
     * رندر کردن ستون "شماره گارانتی" و افزودن لینک‌های Edit/Delete.
     */
    protected function column_warranty_number( $item ) {
        $edit_url = admin_url( 'admin.php?page=warranty-management-add-new&id=' . $item->id );
        $delete_nonce = wp_create_nonce( 'warranty_delete_nonce' );
        $delete_url = admin_url( 'admin.php?page=warranty-management-list&action=delete&warranty=' . $item->id . '&_wpnonce=' . $delete_nonce );

        $title = '<strong>' . esc_html( $item->warranty_number ) . '</strong>';

        $actions = [
            'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'ویرایش', AFROZWEB_GARANTY_SLUG ) ),
            'delete' => sprintf( '<a href="%s" style="color:#a00;" onclick="return confirm(\'%s\')">%s</a>',
                esc_url( $delete_url ),
                esc_js( __( 'آیا از حذف این گارانتی اطمینان دارید؟', AFROZWEB_GARANTY_SLUG ) ),
                __( 'حذف', AFROZWEB_GARANTY_SLUG )
            )
        ];

        return $title . $this->row_actions( $actions );
    }

    private function get_status_label( $status_key ) {
        $statuses = [
            'approved'         => __( 'تایید شده', AFROZWEB_GARANTY_SLUG ),
            'pending_approval' => __( 'در انتظار تایید', AFROZWEB_GARANTY_SLUG ),
            'expired'          => __( 'منقضی شده', AFROZWEB_GARANTY_SLUG ),
        ];
        return $statuses[ $status_key ] ?? ucfirst( str_replace( '_', ' ', $status_key ) );
    }

}
