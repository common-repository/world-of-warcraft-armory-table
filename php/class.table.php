<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if(!class_exists('wow_armory_table_list'))
{
    class wow_armory_table_list extends WP_List_Table {

        function __construct(){
            global $status, $page;

            parent::__construct( array(
                'singular'  => 'character',
                'plural'    => 'characters',
                'ajax'      => false
            ) );

        }

        function column_default($item, $column_name){
            switch($column_name){
                case 'name':
                case 'lvl':
                case 'rank':
                case 'ingroup':
                case 'classname':
                case 'rankname':
                    return $item[$column_name];
                default:
                    return print_r($item,true);
            }
        }

        function column_name($item){

            $sPage = sanitize_text_field(trim($_GET['page']));
            $actions = array(
                /*'edit_groups'      => sprintf('<a href="?page=wow-armory-table-groups&characterId=%s&manage=false">'.__("Add Group", "wowarmorytable-plugin").'</a>',$item['id']),
                'manage_groups'      => sprintf('<a href="?page=wow-armory-table-groups&characterId=%s&manage=true">'.__("Manage Groups", "wowarmorytable-plugin").'</a>',$item['id']),*/
                'delete'    => sprintf('<a href="?page=%s&action=%s&movie=%s">'.__("Delete", "wowarmorytable-plugin").'</a>',$sPage,'delete',$item['id']),
            );

            return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
                /*$1%s*/ $item['name'],
                /*$2%s*/ $item['id'],
                /*$3%s*/ $this->row_actions($actions)
            );
        }

        function column_cb($item){
            return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /*$1%s*/ $this->_args['singular'],
                /*$2%s*/ $item['id']
            );
        }

        function get_bulk_actions() {
            $actions = array(
                'delete'    => __("Delete", "wowarmorytable-plugin"),
                'addgroups' => __("Add Group", "wowarmorytable-plugin")
            );
            return $actions;
        }

        function prepare_items() {
            global $wpdb;

            $per_page = 25;

            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();

            $this->_column_headers = array($columns, $hidden, $sortable);

            $this->process_bulk_action();

            $tablename = $wpdb->base_prefix . 'wow_armory_table_chars';
            $classtable = $wpdb->base_prefix . 'wow_armory_table_classes';
            //$grouptable = $wpdb->base_prefix .  'wow_armory_table_groups';
            //$groupmapping = $wpdb->base_prefix . 'wow_armory_table_groups_mapping';

            $data = $wpdb->get_results("SELECT $tablename.*, $classtable.name AS classname FROM $tablename, $classtable WHERE ".$tablename.".class = ".$classtable.".map", ARRAY_A);

            /*$checkdata = $wpdb->get_results("
            SELECT c.*, g.*, cl.name AS classname, gr.name AS groupname
                FROM $tablename c
                    LEFT JOIN $groupmapping g ON c.id = g.characterId
                    LEFT JOIN $classtable cl ON c.class = cl.map
                    LEFT JOIN $grouptable gr ON g.groupId = gr.id");*/

            /*echo "<pre>";
            print_r($data);
            echo "</pre>";*/

            $options = get_option('wow_arsenal_table_options');
            $ranks = $options['ranks'];

            for($i = 0;$i < count($data); $i++)
                $data[$i]['rankname'] = (!empty($ranks[$data[$i]['rank']])) ? $ranks[$data[$i]['rank']] : $data[$i]['rank'];

            function usort_reorder($a,$b){

                $sOrder = (!empty($_GET['order']) && ($_GET['order'] == 'asc' || $_GET['order'] == 'desc')) ? $_GET['order'] : 'asc';
                $sOrderBy = (!empty($_GET['orderby']) && in_array($_GET['orderby'], array('name','class', 'lvl','rank', 'class'))) ? $_GET['orderby'] : 'name';

                $result = strcmp($a[$sOrderBy], $b[$sOrderBy]);

                return ($sOrder==='asc') ? $result : -$result;
            }

            usort($data, 'usort_reorder');

            $current_page = $this->get_pagenum();

            $total_items = count($data);

            $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

            $this->items = $data;

            $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil($total_items/$per_page)
            ) );


        }

        function get_columns(){
            $columns = array(
                'cb'        => '<input type="checkbox" />',
                'name'     => __("Name", "wowarmorytable-plugin"),
                'classname' => __("Class", "wowarmorytable-plugin"),
                'lvl'    => __("Level", "wowarmorytable-plugin"),
                'rankname'  => __("Rank", "wowarmorytable-plugin")
            );
            return $columns;
        }

        function get_sortable_columns() {
            $sortable_columns = array(
                'name'     => array('name',false),
                'classname' => array('class', false),
                'lvl'    => array('lvl',false),
                'rankname'  => array('rank',false)

            );
            return $sortable_columns;
        }

        function process_bulk_action() {
            if(!isset($_GET['page']) || $_GET['page'] != 'wow-armory-table')
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

            if( 'delete'===$this->current_action() ) {
                global $wpdb;
                $table = $wpdb->base_prefix . "wow_armory_table_chars";

                $characterIds = $_GET['character'];
                array_map('intval', $characterIds);
                foreach($characterIds as $Ids)
                {
                    if ( $Ids > 0 )
                        $wpdb->query("DELETE FROM $table WHERE id = $Ids");
                }
            }
        }

    }
}
?>