<?php
/*
 * Table rendering
 */

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class OF_Taxonomy_List_Table extends WP_List_Table
{
    private $taxonomy_data = [];

    function __construct()
    {
        global $status, $page;
        parent::__construct([
            'singular' => 'wp_list_of_taxonomy', //Singular label
            'plural' => 'wp_list_of_taxonomies', //plural label, also this well be one of the table css class
            'ajax' => false, //We won't support Ajax for this table
        ]);
    }

    function get_columns()
    {
        $columns = [
            'name' => 'Name',
            'label' => 'Label',
            'posttypes' => 'Post Types',
        ];
        return $columns;
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->items = $this->taxonomy_data;
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'name':
            case 'label':
            case 'posttypes':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }
}

class OF_Post_Type_Table extends WP_List_Table
{
    private $post_types = [];

    function __construct()
    {
        global $status, $page;
        parent::__construct([
            'singular' => 'wp_list_of_post_type', //Singular label
            'plural' => 'wp_list_of_post_types', //plural label, also this well be one of the table css class
            'ajax' => false, //We won't support Ajax for this table
        ]);

        $args = ['public' => true];
        $output = 'object'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types_objs = get_post_types($args, $output, $operator);

        if ($post_types_objs) {
            $counter = 0;

            foreach ($post_types_objs as $post_type) {
                if ($post_type->name != 'attachment') {
                    $tempobject = [
                        'ID' => $counter,
                        'name' => $post_type->name,
                        'label' => $post_type->labels->name,
                    ];

                    $this->post_types[] = $tempobject;
                }
            }
        }
    }

    function get_columns()
    {
        $columns = [
            'name' => 'Name',
            'label' => 'Label',
        ];
        return $columns;
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->items = $this->post_types;
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'name':
            case 'label':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }
}

class OF_Variable_List_Table extends WP_List_Table
{
    private $taxonomy_data = [];

    function __construct()
    {
        parent::__construct([
            'singular' => 'wp_list_of_variable', //Singular label
            'plural' => 'wp_list_of_variables', //plural label, also this well be one of the table css class
            'ajax' => false, //We won't support Ajax for this table
        ]);

        //var_dump($taxonomies['post_tag']['labels']['all_items']); - all items should be used in the drop downs
        $counter = 0;
        $args = [
            'public' => true,
        ];
        $output = 'names'; // or objects
        $taxonomies = get_taxonomies($args, $output);
        $fulltaxonomylist = implode(',', $taxonomies);

        $this->taxonomy_data[] = [
            'info' =>
                "Exemple utilisant toutes vos taxonomies publiques (copier-coller !) :<pre><code class='string'>[searchandfilter]</code></pre>",
        ];
        $counter++;
    }

    function get_columns()
    {
        $columns = [
            'info' => 'shortcodes',
        ];
        return $columns;
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->items = $this->taxonomy_data;
    }
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'name':
            case 'defaultval':
            case 'options':
            case 'info':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }
}

?>
