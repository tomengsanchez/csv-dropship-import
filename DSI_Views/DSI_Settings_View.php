<?php

class Events_List_Table extends WP_List_Table
{
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular'  => 'wp_list_event',
            'plural'    => 'wp_list_events',
            'ajax'      => false
        ));
    }

    function column_default($item, $column_name)
    {
        switch($column_name) {
            case 'start_date':
            case 'end_date':
            case 'status':
                return ucfirst($item[$column_name]);
            default:
                return print_r($item,true);
        }
    }

    function column_title($item)
    {
        $actions = array(
            'edit'          => '<a href="'.MvcRouter::admin_url(array('controller' => 'events', 'action' => 'edit', 'id' => $item['id'])).'">Edit</a>',
            'registrants'   => '<a href="'.MvcRouter::admin_url(array('controller' => 'events', 'action' => 'registrants', 'id' => $item['id'])).'">Registrants</a>',
            'export'        => '<a href="' . admin_url() . 'downloads/registrants?id=' . $item['id'] . '">Export CSV</a>',
        );

        if ($item['status'] === 'new') {
            $actions['cancel'] = '<a href="'.MvcRouter::admin_url(array('controller' => 'events', 'action' => 'cancel', 'id' => $item['id'])).'">Cancel</a>';
        } else {
            $actions['uncancel'] = '<a href="'.MvcRouter::admin_url(array('controller' => 'events', 'action' => 'uncancel', 'id' => $item['id'])).'">Uncancel</a>';
        }

        return sprintf(
            '%1$s %3$s',
            $item['title'],
            $item['id'],
            $this->row_actions($actions)
        );
    }

    function column_start_date($item)
    {
        return date('F jS, Y h:i a', strtotime($item['start_date']));
    }

    function column_end_date($item)
    {
        return date('F jS, Y h:i a', strtotime($item['end_date']));
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id']
        );
    }

    function get_columns()
    {
        $columns = array(
            'cb'            => '<input type="checkbox">',
            'title'         => 'Title',
            'start_date'    => 'Start Date',
            'end_date'      => 'End Date',
            'status'        => 'Status',
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'title'         => array('title', false),
            'start_date'    => array('start_date', false),
            'end_date'  => array('end_date', false),
            'status'        => array('status', false),
        );

        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'cancel'    => 'Cancel Events',
            'delete'    => 'Delete',
        );

        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;

        if ('delete' === $this->current_action()) {
            foreach ($_GET['wp_list_event'] as $event) {
                // $wpdb->delete($wpdb->prefix.'atb_events', array('id' => $event));
            }
        }

        if ('cancel' === $this->current_action()) {
            // blah blah
        }
    }

    function custom_bulk_admin_notices()
    {
        echo 'Hello.';
    }

    function prepare_items()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atb_events';
        $per_page = 100;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'title';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        $this->set_pagination_args(array(
            'total_items'   => $total_items,
            'per_page'      => $per_page,
            'total_pages'   => ceil($total_items / $per_page),
        ));
    }
}

$new = new Events_List_Table();
 ?>