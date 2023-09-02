<?php
/*
Plugin Name: Search Listing
Plugin URI: 
Description: Search and Filtering system for Pages, Posts, Categories and Taxonomies
Author: Zakaria Hamdi
Author URI: 
Version: 0.0.1
Text Domain: search and filter
License: GPLv2
*/

if (!defined('SEARCHANDFILTER_THEME_DIR')) {
    define(
        'SEARCHANDFILTER_THEME_DIR',
        ABSPATH . 'wp-content/themes/' . get_template()
    );
}

if (!defined('SEARCHANDFILTER_PLUGIN_NAME')) {
    define(
        'SEARCHANDFILTER_PLUGIN_NAME',
        trim(dirname(plugin_basename(__FILE__)), '/')
    );
}

if (!defined('SEARCHANDFILTER_PLUGIN_DIR')) {
    define(
        'SEARCHANDFILTER_PLUGIN_DIR',
        WP_PLUGIN_DIR . '/' . SEARCHANDFILTER_PLUGIN_NAME
    );
}

if (!defined('SEARCHANDFILTER_PLUGIN_URL')) {
    define(
        'SEARCHANDFILTER_PLUGIN_URL',
        WP_PLUGIN_URL . '/' . SEARCHANDFILTER_PLUGIN_NAME
    );
}

if (!defined('SEARCHANDFILTER_BASENAME')) {
    define('SEARCHANDFILTER_BASENAME', plugin_basename(__FILE__));
}

if (!defined('SEARCHANDFILTER_VERSION_KEY')) {
    define('SEARCHANDFILTER_VERSION_KEY', 'searchandfilter_version');
}

function wpdocs_theme_name_scripts()
{
    wp_enqueue_style(
        'leaflet',
        'https://unpkg.com/leaflet@1.3.1/dist/leaflet.css'
    );
    wp_enqueue_style(
        'markercluster',
        'https://unpkg.com/leaflet.markercluster@1.3.0/dist/MarkerCluster.css'
    );
    wp_enqueue_style(
        'Defaultmarkercluster',
        'https://unpkg.com/leaflet.markercluster@1.3.0/dist/MarkerCluster.Default.css'
    );
    wp_enqueue_style(
        'leafletMarkerCluster',
        'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/MarkerCluster.css'
    );

    wp_enqueue_script(
        'wpdocs-markercluster',
        'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js'
    );
    wp_enqueue_script(
        'wpdocs-markercluster',
        'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/leaflet.markercluster.js'
    );

    // Enqueue my scripts.
    wp_enqueue_script(
        'wpdocs-cookie',
        'https://cdnjs.cloudflare.com/ajax/libs/js-cookie/2.1.4/js.cookie.min.js'
    );

    wp_enqueue_script(
        'leaflet-markercluster',
        'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/leaflet.markercluster.js'
    );
    wp_enqueue_script(
        'mapsjs',
        'https://maps.googleapis.com/maps/api/js?key=AIzaSyDQIbsz2wFeL42Dp9KaL4o4cJKJu4r8Tvg&libraries=places',
        'jquery',
        '',
        false
    );
    wp_enqueue_script(
        'Mapbox',
        plugin_dir_url(__FILE__) . '/js/mapbox.js',
        'jquery',
        '',
        true
    );
    wp_enqueue_script(
        'Mapbox-leaflet',
        plugin_dir_url(__FILE__) . '/js/leaflet.markercluster.js',
        'jquery',
        '',
        true
    );
    wp_enqueue_script(
        'leaflet-google',
        plugin_dir_url(__FILE__) . '/js/leafleft-google.js',
        'jquery',
        '',
        true
    );
    wp_enqueue_style(
        'Slick-css',
        plugin_dir_url(__FILE__) . '/assets/lib/slick/slick.css'
    );
    wp_enqueue_style(
        'Slick-theme',
        plugin_dir_url(__FILE__) . '/assets/lib/slick/slick-theme.css'
    );
    wp_enqueue_style(
        'css-prettyphoto',
        plugin_dir_url(__FILE__) . '/assets/css/prettyphoto.css'
    );
    wp_enqueue_script(
        'socialshare',
        plugin_dir_url(__FILE__) . '/assets/js/social-share.js',
        'jquery',
        '',
        true
    );
    wp_enqueue_script(
        'jquery-prettyPhoto',
        plugin_dir_url(__FILE__) . '/assets/js/jquery.prettyPhoto.js',
        'jquery',
        '',
        true
    );
    wp_enqueue_script(
        'bootstrap-rating',
        plugin_dir_url(__FILE__) . '/assets/js/bootstrap-rating.js',
        'jquery',
        '',
        true
    );
    wp_enqueue_script(
        'Slick',
        plugin_dir_url(__FILE__) . '/assets/lib/slick/slick.min.js',
        'jquery',
        '',
        true
    );
    wp_enqueue_script(
        'nicescroll',
        plugin_dir_url(__FILE__) . '/assets/js/jquery.nicescroll.min.js',
        'jquery',
        '',
        true
    );
    wp_enqueue_script(
        'wpdocs-jquery',
        'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'
    );
}
add_action('wp_enqueue_scripts', 'wpdocs_theme_name_scripts', 0);

// plugin activation hook

// callback function to create table In DB
function search_activation_function()
{
    global $wpdb;

    $mytable =
        'CREATE TABLE `' .
        search_table() .
        '` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `name` varchar(100) NOT NULL,
                            `slug` varchar(50) NOT NULL,
							`status` int(11) NOT NULL DEFAULT "1",
							`ctp_name` varchar(50) NOT NULL,
							`status_cpt` int(11) NOT NULL DEFAULT "1",
                            `created_at` DATETIME,
							`order` int(11) NOT NULL,
                            PRIMARY KEY (`id`)
                          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($mytable);

    $args = [
        'public' => true,
        '_builtin' => false,
    ];

    $output = 'names'; // or objects
    $operator = 'and'; // 'and' or 'or'

    $taxonomies = array_values(get_taxonomies($args, $output, $operator));
    $date = date('d-m-y h:i:s');
    $post_types = array_values(get_post_types($args));

    // Insert data into table
    $compteur = 0;

    foreach ($taxonomies as $key => $value) {
        $sqlInsertData =
            'INSERT INTO `' .
            search_table() .
            "` (`name`, `slug`,`status`, `ctp_name`,`status_cpt`, `created_at`, `order`)VALUES 
                                ('$taxonomies[$key]' ,'$taxonomies[$key]',0,'$post_types[$key]',0,'$date',$compteur)";
        $wpdb->query($sqlInsertData);
        $compteur++;
    }

}
register_activation_hook(__FILE__, 'search_activation_function');

/**
 * Deactivation hook.
 */
/*function pluginprefix_deactivate() {
    // region Terms
    global $wpdb;
    $db_table_name = $wpdb->prefix . 'options'; // table name
    $sqll = "DELETE FROM $db_table_name WHERE `option_name` LIKE 'tax_%'";
    
    $rslt = $wpdb->query($sqll);

}
register_deactivation_hook( __FILE__, 'pluginprefix_deactivate' );*/

function search_table()
{
    global $wpdb;
    return $wpdb->prefix . 'searchListing';
}

/** Remove plugin */

function search_remove_database()
{
    global $wpdb;
    $db_table_name = $wpdb->prefix . 'searchListing'; // table name
    $sql = "DROP TABLE IF EXISTS $db_table_name";



    $rslt = $wpdb->query($sql);
}

register_deactivation_hook(__FILE__, 'search_remove_database');

/** Register default Taxonomy */

function create_listing_taxonomies()
{
    
    $labels = [
        'name' => _x('Categories', 'taxonomy general name', 'textdomain'),
        'singular_name' => _x(
            'Category',
            'taxonomy singular name',
            'textdomain'
        ),
        'menu_name' => _x('Categories', 'admin menu'),
        'search_items' => __('Search Category', 'textdomain'),
        'popular_items' => __('Popular Category', 'textdomain'),
        'all_items' => __('All Categories', 'textdomain'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Categories', 'textdomain'),
        'update_item' => __('Update Categories', 'textdomain'),
        'add_new_item' => __('Add New Categories', 'textdomain'),
        'new_item_name' => __('New Categories Name', 'textdomain'),
        'separate_items_with_commas' => __(
            'Separate Categories with commas',
            'textdomain'
        ),
        'add_or_remove_items' => __('Add or remove Categories', 'textdomain'),
        'choose_from_most_used' => __(
            'Choose from the most used Categories',
            'textdomain'
        ),
    ];

    $args = [
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => ['slug' => 'listingcategory'],
        
    ];

    register_taxonomy('listingcategory', null, $args);

    unset($args);
    unset($labels);

    $labels = [
        'name' => _x('Departements', 'taxonomy general name', 'textdomain'),
        'singular_name' => _x('Department','taxonomy singular name','textdomain'),
        'search_items' => __('Search Departments', 'textdomain'),
        'all_items' => __('All Departments', 'textdomain'),
        'parent_item' => __('Parent Department', 'textdomain'),
        'parent_item_colon' => __('Parent Department:', 'textdomain'),
        'edit_item' => __('Edit Department', 'textdomain'),
        'update_item' => __('Update Department', 'textdomain'),
        'add_new_item' => __('Add New Departments', 'textdomain'),
        'new_item_name' => __('New Department Name', 'textdomain'),
        'menu_name' => __('Departments', 'textdomain'),
    ];

    $args = [
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'departement'],
    ];

    register_taxonomy('departement', null, $args);

    unset($args);
    unset($labels);

    $labels = [
        'name' => _x('Regions', 'taxonomy general name', 'textdomain'),
        'singular_name' => _x('Region', 'taxonomy singular name', 'textdomain'),
        'search_items' => __('Search Regions', 'textdomain'),
        'popular_items' => __('Popular Regions', 'textdomain'),
        'all_items' => __('All Regions', 'textdomain'),
        'parent_item' => null,
        'parent_item_colon' => null,
        
        'edit_item' => __('Edit Region', 'textdomain'),
        'update_item' => __('Update Region', 'textdomain'),
        'add_new_item' => __('Add New Regions', 'textdomain'),
        'new_item_name' => __('New Writer Name', 'textdomain'),
        'menu_name' => __('Regions', 'textdomain'),

        'separate_items_with_commas' => __(
            'Separate writers with commas',
            'textdomain'
        ),
        'add_or_remove_items' => __('Add or remove writers', 'textdomain'),
        'choose_from_most_used' => __(
            'Choose from the most used writers',
            'textdomain'
        ),
        'not_found' => __('No writers found.', 'textdomain'),
    ];

    $args = [
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => ['slug' => 'region'],
    ];

    register_taxonomy('region', null, $args);
    unset($args);
    unset($labels);
    $labels = [
        'name' => _x('Villes', 'taxonomy general name', 'textdomain'),
        'singular_name' => _x('Ville', 'taxonomy singular name', 'textdomain'),
        'search_items' => __('Search Villes', 'textdomain'),
        'popular_items' => __('Popular Villes', 'textdomain'),
        'all_items' => __('All Villes', 'textdomain'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Ville', 'textdomain'),
        'update_item' => __('Update Ville', 'textdomain'),
        'add_new_item' => __('Add New Ville', 'textdomain'),
        'new_item_name' => __('New Writer Name', 'textdomain'),
        'separate_items_with_commas' => __(
            'Separate villes with commas',
            'textdomain'
        ),
        'add_or_remove_items' => __('Add or remove villes', 'textdomain'),
        'choose_from_most_used' => __(
            'Choose from the most used villes',
            'textdomain'
        ),
        'not_found' => __('No villes found.', 'textdomain'),
        'menu_name' => __('Villes', 'textdomain'),
    ];

    $args = [
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => ['slug' => 'villes'],
    ];

    register_taxonomy('villes', null, $args);

    
}
// hook into the init action and call create_listing_taxonomies when it fires
add_action('init', 'create_listing_taxonomies', 0);

/** End  register taxonomy */

/** End  register Terms regions */

function register_terms_tax(){

$regions_json = file_get_contents((plugin_dir_path(__FILE__).'include/departements-region.json'));
 
$decod_jsonR = json_decode($regions_json,true);

foreach ($decod_jsonR as $dec_json ) {
    //echo $dec_json['nom'];
    $nomRegion = $dec_json['region_name']; 
    $nomDep = $dec_json['dep_name']; 

    wp_insert_term($nomRegion, 'region');
    wp_insert_term($nomDep, 'departement');

}

}

add_action('init', 'register_terms_tax', 0);


/** End  register Terms villes */
/*function register_terms_villes(){

$villes_json = file_get_contents((plugin_dir_path(__FILE__).'include/cities.json'));
 
$decod_jsonV = json_decode($villes_json,true);

foreach ($decod_jsonV as $dec_json ) {

    $nomVille = $dec_json['name'];
    print_r($nomVille);
    
    //wp_insert_term($nomRegion, 'villes');

}

}

add_action('init', 'register_terms_villes', 0);*/

add_action('wp_ajax_getListe', 'getListe');
add_action('wp_ajax_nopriv_getListe', 'getListe');

add_action('wp_ajax_getListeHome', 'getListeHome');
add_action('wp_ajax_nopriv_getListeHome', 'getListeHome');

add_action('wp_ajax_getAllcategory', 'getAllcategory');
add_action('wp_ajax_nopriv_getAllcategory', 'getAllcategory');

/***  Get all Taxonomy and custom post saved in Table 'search listing' ***/

function getAllcategory()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'searchListing';
    $table_post = $wpdb->prefix . 'posts';

    $resu = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE status = 1 ORDER BY `$table_name`.`order` ASC"
    );
    $resuGs = $wpdb->get_results(
        "SELECT post_excerpt FROM $table_post WHERE post_type = 'acf-field' AND post_status = 'publish'"
    );
    $results = json_decode(json_encode($resu), true);
    $resultsGs = json_decode(json_encode($resuGs), true);

    $res = [];

    foreach ($results as $result) {

        $res['slug'][$result['id']] = $result['slug'];

    }

    foreach ($resultsGs as $key => $resultsG) {
        $res['post_excerpt'][] = $resultsG['post_excerpt'];
    }

    wp_send_json($res);
    die();
}

/**
 * Create custom post types.
 *
 * @see register_post_type() for registering custom post types.
 */


function create_posttype()
{
    $newpost = 'mylistingservice';
    $fslug = isset($_POST['fslug']) ? $_POST['fslug'] : '';
    $Pname = isset($_POST['Pname']) ? $_POST['Pname'] : '';

    $supports = [
        'title', // post title
        'editor', // post content
        'author', // post author
        'thumbnail', // featured images
        'excerpt', // post excerpt
        'custom-fields', // custom fields
        'comments', // post comments
        'revisions', // post revisions
        'post-formats', // post formats
    ];

    $labels = [
        'name' => _x('My listing', 'plural'),
        'singular_name' => _x('My listing', 'singular'),
        'menu_name' => _x('My listing', 'admin menu'),
        'name_admin_bar' => _x('My listing', 'admin bar'),
        'add_new' => _x('Add New', 'add new'),
        'add_new_item' => __('Add New listing'),
        'new_item' => __('New listing'),
        'edit_item' => __('Edit listing'),
        'view_item' => __('View listing'),
        'all_items' => __('All listing'),
        'search_items' => __('Search listing'),
        'not_found' => __('No news found.'),
    ];

    $args = [
        'supports' => $supports,
        'labels' => $labels,
        'public' => true,
        'query_var' => true,
        'rewrite'   => array( 'slug' => '/nos-avocats', 'with_front' => false ),
        'menu_icon' => 'dashicons-list-view',
        'has_archive' => false,
        'hierarchical' => false,
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
    ];

    register_post_type($newpost, $args);
}

add_action('init', 'create_posttype');

/**
 * Registre menu in admin panel si on activé le custom post
 */ 

add_action(
    'register_post_type_args',
    function ($args, $postType) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'searchListing';
        $rowc = $wpdb->get_results(
            "SELECT `ctp_name` FROM $table_name WHERE  `status_cpt` = 1"
        );
        $cpt = json_decode(json_encode($rowc), false);

        foreach ($cpt as $result) {
            $name_cpt = $result->ctp_name;
            if ($postType === $name_cpt) {
                $args['show_in_menu'] = true;
                $args['show_in_nav_menus'] = true;
                return $args;
            }

            //$args['show_in_nav_menus'] = true;
        }

        return $args;
    },
    99,
    2
);

/**
 * Initialisation Custom Field in Post
 */

function initialisation_metaboxes()
{
    add_meta_box(
        'id_meta_listing',
        'listing settings',
        'meta_listing',
        'mylistingservice',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'initialisation_metaboxes');

/**
 * Add meta box in post Listing
 */

function meta_listing($post)
{
    // on récupère la valeur actuelle pour la mettre dans le champ
    $tagline_text = get_post_meta($post->ID, '_tagline_text', true);
    $gAddress = get_post_meta($post->ID, '_gAddress', true);
    $latitude = get_post_meta($post->ID, '_latitude', true);
    $longitude = get_post_meta($post->ID, '_longitude', true);
    $phone = get_post_meta($post->ID, '_phone', true);
    $whatsapp = get_post_meta($post->ID, '_whatsapp', true);
    $email = get_post_meta($post->ID, '_email', true);
    $website = get_post_meta($post->ID, '_website', true);
    $twitter = get_post_meta($post->ID, '_twitter', true);
    $facebook = get_post_meta($post->ID, '_facebook', true);
    $linkedin = get_post_meta($post->ID, '_linkedin', true);
    $instagram = get_post_meta($post->ID, '_instagram', true);
    $business_logo = get_post_meta($post->ID, '_avatar', true);
    $IDs = get_post_meta($post->ID, 'post_banner_img', true);

    echo '<div class="setting_style">';
    echo '<div class="tagline_text">';
    echo '<label for="tagline_text">Business Tagline Text : </label>';
    echo '<input id="tagline_text" type="text" name="tagline_text" value="' .
        $tagline_text .
        '" />';
    echo '</div>';

    echo '<div class="gAddress">';
    echo '<label for="gAddress">Google Address : </label>';
    echo '<input id="gAddress" type="text" name="gAddress" value="' .
        $gAddress .
        '" />';
    echo '</div>';

    echo '<div class="latitude">';
    echo '<label for="latitude">Latitude: </label>';
    echo '<input id="latitude" type="text" name="latitude" value="' .
        $latitude .
        '" />';
    echo '</div>';
    echo '<div class="longitude">';
    echo '<label for="longitude">Longitude: </label>';
    echo '<input id="longitude" type="text" name="longitude" value="' .
        $longitude .
        '" />';
    echo '</div>';
    echo '<div class="phone">';
    echo '<label for="phone">Téléphone: </label>';
    echo '<input id="phone" type="text" name="phone" value="' . $phone . '" />';
    echo '</div>';

    echo '<div class="whatsapp">';
    echo '<label for="whatsapp">Whatsapp: </label>';
    echo '<input id="whatsapp" type="text" name="whatsapp" value="' .
        $whatsapp .
        '" />';
    echo '</div>';

    echo '<div class="email">';
    echo '<label for="email">E-mail: </label>';
    echo '<input id="email" type="text" name="email" value="' . $email . '" />';
    echo '</div>';

    echo '<div class="website">';
    echo '<label for="website">Website: </label>';
    echo '<input id="website" type="text" name="website" value="' .
        $website .
        '" />';
    echo '</div>';

    echo '<div class="twitter">';
    echo '<label for="twitter">Twitter: </label>';
    echo '<input id="twitter" type="text" name="twitter" value="' .
        $twitter .
        '" />';
    echo '</div>';

    echo '<div class="facebook">';
    echo '<label for="facebook">Facebook: </label>';
    echo '<input id="facebook" type="text" name="facebook" value="' .
        $facebook .
        '" />';
    echo '</div>';

    echo '<div class="linkedin">';
    echo '<label for="linkedin">Linkedin: </label>';
    echo '<input id="linkedin" type="text" name="linkedin" value="' .
        $linkedin .
        '" />';
    echo '</div>';

    echo '<div class="instagram">';
    echo '<label for="instagram">Instagram: </label>';
    echo '<input id="instagram" type="text" name="instagram" value="' .
        $instagram .
        '" />';
    echo '</div>';
}

add_action('add_meta_boxes', 'multi_media_uploader_meta_box');

/**
 * Add meta box Gallery media in post
 */

function multi_media_uploader_meta_box()
{
    add_meta_box(
        'my-post-box',
        'Carousel Images',
        'multi_media_uploader_meta_box_func',
        'mylistingservice',
        'normal',
        'high'
    );
}

function multi_media_uploader_meta_box_func($post)
{
    $banner_img = get_post_meta($post->ID, 'post_banner_img', true); ?>

	<table cellspacing="10" cellpadding="10">
		<tr>
			<td>
				<?php echo multi_media_uploader_field('post_banner_img', $banner_img); ?>
			</td>
		</tr>
	</table>

	<?php
}

function multi_media_uploader_field($name, $value = '')
{
    $image = '">Add Media';
    $image_str = '';
    $image_size = 'full';
    $display = 'none';
    $value = explode(',', $value);

    if (!empty($value)) {
        foreach ($value as $values) {
            if (
                $image_attributes = wp_get_attachment_image_src(
                    $values,
                    $image_size
                )
            ) {
                $image_str .=
                    '<li data-attechment-id=' .
                    $values .
                    '><a href="' .
                    $image_attributes[0] .
                    '" target="_blank"><img src="' .
                    $image_attributes[0] .
                    '" /></a><i class="dashicons dashicons-no delete-img"></i></li>';
            }
        }
    }

    if ($image_str) {
        $display = 'inline-block';
    }

    return '<div class="multi-upload-medias"><ul>' .
        $image_str .
        '</ul><a href="#" class="wc_multi_upload_image_button button' .
        $image .
        '</a><input type="hidden" class="attechments-ids ' .
        $name .
        '" name="' .
        $name .
        '" id="' .
        $name .
        '" value="' .
        esc_attr(implode(',', $value)) .
        '" /><a href="#" class="wc_multi_remove_image_button button" style="display:inline-block;display:' .
        $display .
        '">Remove media</a></div>';
}

/**
 * Save Meta gallery Box values.
 */ 
add_action('save_post', 'meta_box_save');

function meta_box_save($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post')) {
        return;
    }

    if (isset($_POST['post_banner_img'])) {
        update_post_meta(
            $post_id,
            'post_banner_img',
            $_POST['post_banner_img']
        );
    }
}
/**
 * Saved Metabox in post
 */
add_action('save_post', 'save_metaboxes');
function save_metaboxes($post_ID)
{
    // si la metabox est définie, on sauvegarde sa valeur
    if (isset($_POST['tagline_text'])) {
        update_post_meta(
            $post_ID,
            '_tagline_text',
            esc_html($_POST['tagline_text'])
        );
    }
    if (isset($_POST['gAddress'])) {
        update_post_meta($post_ID, '_gAddress', esc_html($_POST['gAddress']));
    }
    if (isset($_POST['latitude'])) {
        update_post_meta($post_ID, '_latitude', esc_html($_POST['latitude']));
    }

    if (isset($_POST['longitude'])) {
        update_post_meta($post_ID, '_longitude', esc_html($_POST['longitude']));
    }

    if (isset($_POST['phone'])) {
        update_post_meta($post_ID, '_phone', esc_html($_POST['phone']));
    }

    if (isset($_POST['whatsapp'])) {
        update_post_meta($post_ID, '_whatsapp', esc_html($_POST['whatsapp']));
    }
    if (isset($_POST['email'])) {
        update_post_meta($post_ID, '_email', esc_html($_POST['email']));
    }
    if (isset($_POST['website'])) {
        update_post_meta($post_ID, '_website', esc_html($_POST['website']));
    }
    if (isset($_POST['twitter'])) {
        update_post_meta($post_ID, '_twitter', esc_html($_POST['twitter']));
    }

    if (isset($_POST['facebook'])) {
        update_post_meta($post_ID, '_facebook', esc_html($_POST['facebook']));
    }

    if (isset($_POST['linkedin'])) {
        update_post_meta($post_ID, '_linkedin', esc_html($_POST['linkedin']));
    }

    if (isset($_POST['instagram'])) {
        update_post_meta($post_ID, '_instagram', esc_html($_POST['instagram']));
    }

}



/** Remove Term url Taxonomy */

add_filter('request', 'remove_term_request', 1, 1 );

function remove_term_request($query){

    $args = array(
        'public'   => true,
        '_builtin' => false
         
      ); 
      $output = 'names'; // or objects
      $operator = 'and'; // 'and' or 'or'
      $taxonomies = get_taxonomies( $args, $output, $operator );
  
      if ( $taxonomies ) {
          foreach ( $taxonomies  as $taxonomy ) {
            $tax_name = $taxonomy; // specify you taxonomy name here, it can be also 'category' or 'post_tag'
            // Request for child terms differs, we should make an additional check
	if( $query['taxonomy'] ) :
		$include_children = true;
		$name = $query['taxonomy'];
	else:
		$include_children = false;
		$name = $query['name'];
	endif;
	
	
	$term = get_term_by('slug', $name, $tax_name); // get the current term to make sure it exists
	
	if (isset($name) && $term && !is_wp_error($term)): // check it here
		
		if( $include_children ) {
			unset($query['taxonomy']);
			$parent = $term->parent;
			while( $parent ) {
				$parent_term = get_term( $parent, $tax_name);
				$name = $parent_term->slug . '/' . $name;
				$parent = $parent_term->parent;
			}
		} else {
			unset($query['name']);
		}
		
		switch( $tax_name ):
			case 'taxonomy':{
				$query['listingcategory'] = $name; // for categories
				break;
			}
			case 'taxonomy':{
				$query['departement'] = $name; // for post tags
				break;
			}
            case 'taxonomy':{
				$query['departement'] = $name; // for post tags
				break;
			}
			default:{
				$query[$tax_name] = $name; // for another taxonomies
				break;
			}
		endswitch;

	endif;
	

          }
          return $query;

      }
	
	
}


add_filter( 'term_link', 'search_term_permalink', 10, 3 );

function search_term_permalink( $url, $term, $taxonomy ){
    $args = array(
        'public'   => true,
        '_builtin' => false
         
      ); 
      $output = 'names'; // or objects
      $operator = 'and'; // 'and' or 'or'
      $taxonomies = get_taxonomies( $args, $output, $operator );
  
      if ( $taxonomies ) {
          foreach ( $taxonomies  as $taxonomy ) {
        $taxonomy_name = $taxonomy; // your taxonomy name here
        $taxonomy_slug = $taxonomy; // the taxonomy slug can be different with the taxonomy name (like 'post_tag' and 'tag' )

	// exit the function if taxonomy slug is not in URL
	if ( strpos($url, $taxonomy_slug) === FALSE || $taxonomy != $taxonomy_name ) return $url;
	
	$url = str_replace('/' . $taxonomy_slug, '', $url);
    }
	return $url;
}
}
/**
 * Remove the slug from published post permalinks.
 */
function remove_link_post( $query ) {

    $args = array(
        'public'   => true,
        '_builtin' => false,
     );
 
     $output = 'names'; // names or objects, note names is the default
     $operator = 'and'; // 'and' or 'or'
 
     $post_types = get_post_types( $args, $output, $operator ); 
 
     foreach ( $post_types  as $post_type ) {
 
    // Only loop the main query
    if ( ! $query->is_main_query() ) {
        return;
    }

    // Only loop our very specific rewrite rule match
    if ( 2 != count( $query->query )
        || ! isset( $query->query['page'] ) )
        return;

    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
    if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( $post_type ) );
    }     

}

}
//add_action( 'pre_get_posts', 'remove_link_post' );
/**
 * Get lat and long from address and set for listing
 */
if (!function_exists('lp_get_lat_long_from_address')) {
    function lp_get_lat_long_from_address($address, $listing_id)
    {
        global $post;
        $listingid = $post->ID;
        $exLat = get_post_meta($listingid, '_latitude', true);
        $exLong = get_post_meta($listingid, '_longitude', true);
        $mapkey = 'AIzaSyDQt64oivgdLy88A3j6-_Yl-4wP1Y1dz8s';

        if (empty($exLat) && empty($exLong)) {
            if (!empty($address) && !empty($listing_id)) {
                $address = urlencode($address);

                $url =
                    'https://maps.googleapis.com/maps/api/geocode/json?address=' .
                    $address .
                    '&key=' .
                    $mapkey;
                global $wp_filesystem;
                if (empty($wp_filesystem)) {
                    require_once ABSPATH . '/wp-admin/includes/file.php';
                    WP_Filesystem();
                }
                $resp = json_decode($wp_filesystem->get_contents($url), true);

                if ($resp['status'] === 'OK') {
                    $formatted_address = $resp['results'][0][
                        'formatted_address'
                    ]
                        ? $resp['results'][0]['formatted_address']
                        : '';
                    $lat = $resp['results'][0]['geometry']['location']['lat']
                        ? $resp['results'][0]['geometry']['location']['lat']
                        : '';
                    $long = $resp['results'][0]['geometry']['location']['lng']
                        ? $resp['results'][0]['geometry']['location']['lng']
                        : '';

                    if (!empty($lat) && !empty($long)) {
                        listing_set_metabox('_latitude', $lat, $listing_id);
                        listing_set_metabox('_longitude', $long, $listing_id);
                    }
                }
            }
        }
    }
}


/**
 * Set Template Taxonomy
 * Set Template Single detail post
 */


add_filter('template_include', 'set_template_category');

function set_template_category($template)
{
    if (is_tax('listingcategory')) {
        $template = plugin_dir_path(__FILE__) . 'templates/taxonomy-category.php';
    }

    return $template;
}

add_filter('template_include', 'set_template');

function set_template($template)
{
    if (is_tax('region')) {
        $template = plugin_dir_path(__FILE__) . 'templates/taxonomy-region.php';
    }

    return $template;
}

add_filter('template_include', 'set_template_departement');

function set_template_departement($template)
{
    if (is_tax('departement')) {
        $template =
            plugin_dir_path(__FILE__) . 'templates/taxonomy-departement.php';
    }

    return $template;
}

add_filter('template_include', 'set_template_ville');

function set_template_ville($template)
{
    if (is_tax('villes')) {
        $template = plugin_dir_path(__FILE__) . 'templates/taxonomy-ville.php';
    }

    return $template;
}

add_filter('template_include', 'template_chooser_single');

function template_chooser_single($template)
{
    $post_type = get_query_var('post_type');

    if ($post_type) {
        $template = plugin_dir_path(__FILE__) . 'templates/single-listing.php';
    }

    return $template;
}

/**
 * Get listed tax in change <select> form page home .
 */

function getListeHome()
{
    $idCat = $_POST['idCat'];
    $valueSelected = $_POST['valueSelected'];
    $term = get_term_by('slug', $valueSelected, $idCat);
    $term_id = $term->term_id;
    $data = [];

    if ($idCat == 'region') {
        $fields = get_option("tax_$term_id");
        foreach ($fields as $term) {
            $terms = get_term_by('name', $term, 'departement');

            $data[] = [
                'name' => $terms->name,
                'slug' => $terms->slug,
                'id' => $terms->term_id,
                'url' => esc_url_raw($loc),
            ];
        }
    }
    if ($idCat == 'departement') {
        $term_id = $term->term_id;
        $fields = get_option("tax_$term_id");
        foreach ($fields as $term) {
            $terms = get_term_by('name', $term, 'villes');

            $data[] = [
                'name' => $terms->name,
                'slug' => $terms->slug,
                'id' => $terms->term_id,
                'url' => esc_url_raw($loc),
            ];
        }
    }

    wp_send_json($data);
}


/**
 * add term nos-avocats in custom post
 */

add_action('init', function() {
    add_rewrite_rule('^nos-avocats/([^/]*)/?$', 'index.php?mylistingservice=$matches[1]', 'top');
});

/**
 * Get listed tax in change <select> form page Tax
*/


function getListe()
{
    $idCat = $_POST['idCat'];
    $home_racine = $_POST['id_home'];
    $valueSelected = $_POST['valueSelected'];
    $term = get_term_by('slug', $valueSelected, $idCat);
    $term_id = $term->term_id;
    $data = [];
    $url_params = [];
    $home_url = get_home_url();
    $url_params = $_POST['Selctedliste'];

    $parameters = [];
    foreach ($url_params as $value) {
        $parameters[] = $value['value'];
    }

    $url_slice = array_slice($parameters, 1);
    $url_cat = array_slice($parameters, 0);

    $category = current($url_cat);
    $regionv = next($url_cat);

    $dep = next($url_cat);

    if (!empty($category) && !empty($regionv)) {
        $url = $home_url . '/' . implode('/', $url_slice) . '/' . $category;
    } elseif (!empty($regionv)) {
        $url = $home_url . '/' . implode('/', $url_slice) . '/';
    } elseif (empty($dep)) {
        $url = $home_url . '/' . $regionv . '/' . $category;
    } else {
        $url = $home_url . '/' . $category;
    }

    if (isset($valueSelected)) {
        $fields = get_option("tax_$term_id");
        if ($fields) {
            foreach ($fields as $term) {
                $terms = get_term_by('name', $term, $idCat);

                $data[] = [
                    'name' => $terms->name,
                    'slug' => $terms->slug,
                    'id' => $terms->term_id,
                    'url' => esc_url_raw($url),
                ];
            }
        } else {
            $terms = get_terms(['taxonomy' => $idCat, 'orderby' => 'name']);

            foreach ($terms as $term) {
                $data[] = [
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'id' => $term->term_id,
                    'url' => esc_url_raw($url),
                ];
            }
        }
    }

    if (isset($home_racine)) {
        $data[] = ['url' => esc_url_raw($url)];
    }

    wp_send_json($data);
}


/**
 * Class Search listing
 */

if (!class_exists('SearchAndFilter')) {
    class SearchAndFilter
    {
        private $has_form_posted = false;
        private $hasqmark = false;
        private $hassearchquery = false;
        private $urlparams = '/';
        private $searchterm = '';
        private $tagid = 0;
        private $catid = 0;
        private $defaults = [];
        private $frmreserved = [];
        private $taxonomylist = [];

        public function __construct()
        {
            // Set up reserved fields
            $this->frmreserved = [
                'category',
                'search',
                'post_tag',
                'submitted',
                'post_date',
                'post_types',
            ];
            $this->frmqreserved = [
                'category_name',
                's',
                'tag',
                'submitted',
                'post_date',
                'post_types',
            ]; //same as reserved

            // Add shortcode support for widgets
            add_shortcode('searchandfilter', [$this, 'shortcode']);
            // Add styles
            add_action('wp_enqueue_scripts', [$this, 'of_enqueue_styles']);
            add_action('admin_enqueue_scripts', [$this, 'of_enqueue_admin_ss']);
            add_action('wp_ajax_my_region_se', 'my_region_se');
            add_action('init', [$this, 'listing_rewrite_tax_rule']);
            add_filter('query_vars', [$this, 'wp_query_vars']);
        }

        public function of_enqueue_styles()
        {
            wp_enqueue_style(
                'searchandfilter',
                plugin_dir_url(__FILE__) . 'style.css',
                false,
                1.0,
                'all'
            );
            if (is_tax() || is_singular()) {
                wp_enqueue_style(
                    'bootstrap',
                    'https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css'
                );
            }
            wp_enqueue_script(
                'filter_script',
                plugin_dir_url(__FILE__) . 'js/searchfilter.js'
            );
            wp_enqueue_script('main', plugin_dir_url(__FILE__) . 'js/main.js');

            wp_localize_script('filter_script', 'ajax_object', [
                'ajaxurl' => admin_url('admin-ajax.php'),
            ]);
        }

        public function of_enqueue_admin_ss($hook)
        {

            wp_enqueue_style(
                'of_style',
                SEARCHANDFILTER_PLUGIN_URL . '/admin/style.css',
                false,
                1.0,
                'all'
            );
            wp_enqueue_script(
                'of_syntax_script',
                SEARCHANDFILTER_PLUGIN_URL . '/admin/setting.js'
            );
            wp_enqueue_script(
                'ofsyntax_script',
                SEARCHANDFILTER_PLUGIN_URL .
                    '/admin/jquery.ui.touch-punch.min.js'
            );
            wp_localize_script('of_syntax_script', 'ajax_object', [
                'ajaxurl' => admin_url('admin-ajax.php'),
            ]);
        }

        public function shortcode($atts, $content = null)
        {
            // extract the attributes into variables
            //echo json_encode($data);
            extract(
                shortcode_atts(
                    [
                        'fields' => null,
                        'taxonomies' => null, //will be deprecated - use `fields` instead
                        'submit_label' => null,
                        'submitlabel' => null, //will be deprecated - use `submit_label` instead
                        'search_placeholder' => 'Search &hellip;',
                        'types' => '',
                        'type' => '', //will be deprecated - use `types` instead
                        'headings' => '',
                        'all_items_labels' => '',
                        'class' => '',
                        'post_types' => '',
                        'hierarchical' => '',
                        'hide_empty' => '',
                        'order_by' => '',
                        'show_count' => '',
                        'order_dir' => '',
                        'operators' => '',
                        'add_search_param' => '0',
                        'empty_search_url' => '',
                    ],
                    $atts
                )
            );
            global $wpdb;
            $table_name = $wpdb->prefix . 'searchListing';
            $results = $wpdb->get_results(
                "SELECT slug FROM $table_name WHERE status = 1 ORDER BY `$table_name`.`order` ASC"
            );
            $form_array = json_decode(json_encode($results), true);
            $nameData = [];

            foreach ($form_array as $result) {
                $nameData[] = $result['slug'];
                // each column n your row will be accessible like this
                //$names = implode(",",$nameData);

                //init `fields`
                if ($result != null) {
                    $fields = $nameData;
                } else {
                    $fields = explode(',', $taxonomies);
                }
            }

            $this->taxonomylist = $fields;
            $nofields = count([$fields]);

            $add_search_param = (int) $add_search_param;
            //init `types`
            if ($types != null) {
                $types = explode(',', $types);
            } else {
                $types = explode(',', $type);
            }

            if (!is_array($types)) {
                $types = [];
            }

            for ($i = 0; $i < $nofields; $i++) {
                //loop through all fields

                //set up types
                if (isset($types[$i])) {
                    if (
                        $types[$i] != 'select' &&
                        $types[$i] != 'checkbox' &&
                        $types[$i] != 'radio' &&
                        $types[$i] != 'list' &&
                        $types[$i] != 'multiselect'
                    ) {
                        //no accepted type matched - non compatible type defined by user
                        $types[$i] = 'select'; //use default
                    }
                }
            }

            //set all form defaults / dropdowns etc
            //$this->set_defaults();

            return $this->get_search_filter_form(
                $submit_label,
                $search_placeholder,
                $fields,
                $types,
                $hierarchical,
                $hide_empty,
                $show_count,
                $post_types,
                $order_by,
                $order_dir,
                $operators,
                $all_items_labels,
                $empty_search_url,
                $add_search_param,
                $class
            );
        }

        function wp_query_vars($query_vars)
        {
            $query_vars[] = 'region';
            $query_vars[] = 'departement';
            $query_vars[] = 'ville';
            $query_vars[] = 'listingcategory';
            return $query_vars;
        }




        /**
         * Rewrite URL Taxonomy
         */

        function listing_rewrite_tax_rule()
        {
            add_rewrite_tag('%region%', '([^&]+)');
            add_rewrite_tag('%departement%', '([^&]+)');
            add_rewrite_tag('%villes%', '([^&]+)');
            add_rewrite_tag('%listingcategory%', '([^&]+)');

            add_rewrite_rule(
                '^([^&]+)/([^&]+)/([^&]+)/([^&]+)/?$',
                'index.php?region=$matches[1]&departement=$matches[2]&villes=$matches[3]&listingcategory=$matches[4]',
                'top'
            );

            add_rewrite_rule(
                '^([^&]+)/([^&]+)/([^&]+)/?$',
                'index.php?region=$matches[1]&departement=$matches[2]&villes=$matches[3]',
                'top'
            );



            add_rewrite_rule(
                '^([^&]+)/([^&]+)/?$',
                'index.php?region=$matches[1]&departement=$matches[2]',
                'top'
            );
            
            add_rewrite_rule(
                '^([^&]+)/([^&]+)/?$',
                'index.php?region=$matches[1]&listingcategory=$matches[4]',
                'top'
            );

        }

        /**
         * Generate Form
         */

        public function get_search_filter_form(
            $submitlabel,
            $search_placeholder,
            $fields,
            $types,
            $hierarchical,
            $hide_empty,
            $show_count,
            $post_types,
            $order_by,
            $order_dir,
            $operators,
            $all_items_labels,
            $empty_search_url,
            $add_search_param,
            $class
        ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'searchListing';
            //var_dump($taxonomies['post_tag']['labels']['all_items']); - all items should be used in the drop downs
            $resu = $wpdb->get_results(
                "SELECT * FROM $table_name WHERE status = 1 ORDER BY `$table_name`.`order` ASC"
            );
            $results = json_decode(json_encode($resu), true);
            $returnvar = '';
            $addclass = '';
            $category = '';
            $region = '';
            $departement = '';
            $ville = '';
            if ($class != '') {
                $addclass = ' ' . $class;
            }
            $returnvar .=
                '<link rel="stylesheet" type="text/css" href="//use.fontawesome.com/releases/v5.7.2/css/all.css">';

            if (!is_archive()) {
                $taxonomies = get_terms( array(
                    'taxonomy' => 'region',
                    'hide_empty' => false
                ) );
                
                if ( !empty($taxonomies) ) :

                    $returnvar.=' <dl class="dropdown"> <dt>
    <a href="#">
      <span class="hida">Select</span>    
      <p class="multiSel"></p>  
    </a>
    </dt>
  
    <dd>
        <div class="mutliSelect"><ul class="region">';
            foreach( $taxonomies as $category ) {

                $returnvar.= '<li class="'. esc_attr( $category->term_id ) .'"><input type="radio" name="region" value="'. esc_attr( $category->slug ) .'">
                '. esc_html( $category->name ) .'</li>';
            }

            $returnvar.='</ul></div></dd></dl>';


            $returnvar.='<div>
    <b>Animal</b>
    <label><input type="radio" name="animal"/> Cat </label>
    <label><input type="radio" name="animal"/> Dog</label>
    <label><input type="radio" name="animal"/> Mouse </label>
</div>';
                    //echo $returnvar;
                endif;

            } else {
                $returnvar .=
                    '<form action="" method="POST" class="searchandfilter form-inline">';

                $returnvar .= '<div class="lp-search-bar clearfix">';

                if (isset($_COOKIE['listingcategory'])) {
                    $category = htmlspecialchars($_COOKIE['listingcategory']); // => 'villes'
                }

                if (isset($_COOKIE['region'])) {
                    $region = htmlspecialchars($_COOKIE['region']); // => 'villes'
                }
                if (isset($_COOKIE['departement'])) {
                    $departement = htmlspecialchars($_COOKIE['departement']); // => 'villes'
                }

                if (isset($_COOKIE['villes'])) {
                    $ville = htmlspecialchars($_COOKIE['villes']); // => 'villes'
                }
                foreach ($results as $result) {
                    $TermCat = $result['slug'];
                    $NameCat = $result['name'];

                    if (
                        $terms = get_terms([
                            'taxonomy' => $TermCat,
                            'orderby' => 'name',
                        ])
                    ) {
                        if ($TermCat == 'listingcategory') {
                            if (isset($category)) {
                                $returnvar .=
                                    '<select name="' .
                                    $TermCat .
                                    '" id="' .
                                    $TermCat .
                                    '" class="'.$TermCat.' lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields"><option value="" >Sélectionnez ' .
                                    $NameCat .
                                    '</option>';
                                foreach ($terms as $term) {
                                    $slug = $term->slug;
                                    $selected =
                                        $slug == $category
                                            ? 'selected="selected"'
                                            : '';
                                    $returnvar .=
                                        '<option id="' .
                                        $term->term_id .
                                        '" name="' .
                                        $term->slug .
                                        '"  value="' .
                                        $term->slug .
                                        '"' .
                                        $selected .
                                        '>' .
                                        $term->name .
                                        '</option>';
                                }
                                $returnvar .= '</select>';
                            } else {
                                $returnvar .=
                                    '<select name="' .
                                    $TermCat .
                                    '" id="' .
                                    $TermCat .
                                    '" class="lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields" ><option value="" >Sélectionnez une ' .
                                    $NameCat .
                                    '</option>';

                                foreach ($terms as $term) {
                                    $returnvar .=
                                        '<option id="' .
                                        $term->term_id .
                                        '" name="' .
                                        $term->slug .
                                        '"  value="' .
                                        $term->slug .
                                        '">' .
                                        $term->name .
                                        '</option>';
                                }
                                $returnvar .= '</select>';
                            }
                        }
                        if ($TermCat == 'region') {
                            if (isset($region)) {
                                $returnvar .=
                                    '<select name="' .
                                    $TermCat .
                                    '" id="' .
                                    $TermCat .
                                    '" class="lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields"><option value="" >Sélectionnez ' .
                                    $NameCat .
                                    '</option>';

                                foreach ($terms as $term) {
                                    $slug = $term->slug;
                                    $selected =
                                        $slug == $region
                                            ? 'selected="selected"'
                                            : '';
                                    $returnvar .=
                                        '<option id="' .
                                        $term->term_id .
                                        '" name="' .
                                        $term->slug .
                                        '"  value="' .
                                        $term->slug .
                                        '"' .
                                        $selected .
                                        '>' .
                                        $term->name .
                                        '</option>';
                                }
                                $returnvar .= '</select>';
                            } else {
                                $returnvar .=
                                    '<select name="' .
                                    $TermCat .
                                    '" id="' .
                                    $TermCat .
                                    '" class="lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields"><option value="" >Sélectionnez ' .
                                    $NameCat .
                                    '</option>';

                                foreach ($terms as $term) {
                                    $returnvar .=
                                        '<option id="' .
                                        $term->term_id .
                                        '" name="' .
                                        $term->slug .
                                        '"  value="' .
                                        $term->slug .
                                        '"' .
                                        $selected .
                                        '>' .
                                        $term->name .
                                        '</option>';
                                }
                                $returnvar .= '</select>';
                            }
                        }
                        if ($TermCat == 'departement') {
                            $term = get_term_by('slug', $region, 'region');
                            $term_id = $term->term_id;
                            $slug = $term->slug;
                            if (isset($term_id)) {
                                if (isset($valueSelected)) {
                                    $fields = get_option("tax_$term_id");
                                    foreach ($fields as $term) {
                                        $terms = get_term_by(
                                            'name',
                                            $term,
                                            'departement'
                                        );

                                        $data[] = [
                                            'name' => $terms->name,
                                            'slug' => $terms->slug,
                                            'id' => $terms->term_id,
                                            'url' => esc_url_raw($loc),
                                        ];
                                    }
                                }

                                $fields = get_option("tax_$term_id");

                                if ($fields):
                                    $returnvar .=
                                        '<select name="' .
                                        $TermCat .
                                        '" id="' .
                                        $TermCat .
                                        '" class="'.$TermCat.' lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields"><option value="" >Sélectionnez ' .
                                        $NameCat .
                                        '</option>';

                                    foreach ($fields as $term):
                                        $terms = get_term_by(
                                            'name',
                                            $term,
                                            'departement'
                                        );

                                        $slug = $terms->slug;
                                        $selected =
                                            $slug == $departement
                                                ? 'selected="selected"'
                                                : '';
                                        $returnvar .=
                                            '<option  value="' .
                                            $terms->slug .
                                            '"' .
                                            $selected .
                                            '>' .
                                            $terms->name .
                                            '</option>';
                                    endforeach;
                                    $returnvar .= '</select>';
                                endif;
                            } else {
                                $terms = get_terms([
                                    'taxonomy' => 'departement',
                                    'orderby' => 'name',
                                ]);
                                $returnvar .=
                                    '<select name="' .
                                    $TermCat .
                                    '" id="' .
                                    $TermCat .
                                    '" class="lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields" disabled><option value="" >Sélectionnez ' .
                                    $NameCat .
                                    '</option>';

                                foreach ($terms as $term):
                                    $returnvar .=
                                        '<option  value="' .
                                        $term->slug .
                                        '">' .
                                        $term->name .
                                        '</option>';
                                endforeach;
                                $returnvar .= '</select>';
                            }
                        }

                        if ($TermCat == 'villes') {
                            //$term = get_term_by('slug', $departement , 'departement');

                            $term = get_term_by(
                                'slug',
                                $departement,
                                'departement'
                            );
                            $term_id = $term->term_id;
                            $slug = $term->slug;

                            $term_id = $term->term_id;
                            $slug = '';
                            if (isset($term_id)) {
                                if (isset($valueSelected)) {
                                    $fields = get_option("tax_$term_id");
                                    foreach ($fields as $term) {
                                        $terms = get_term_by(
                                            'name',
                                            $term,
                                            'villes'
                                        );

                                        $data[] = [
                                            'name' => $terms->name,
                                            'slug' => $terms->slug,
                                            'id' => $terms->term_id,
                                            'url' => esc_url_raw($loc),
                                        ];
                                    }
                                }
                                if ($fields) {
                                    $returnvar .=
                                        '<select name="' .
                                        $TermCat .
                                        '" id="' .
                                        $TermCat .
                                        '" class="lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields"><option value="" >Sélectionnez ' .
                                        $NameCat .
                                        '</option>';
                                    $fields = get_option("tax_$term_id");
                                    foreach ($fields as $term):
                                        $terms = get_term_by(
                                            'name',
                                            $term,
                                            'villes'
                                        );

                                        $slug = $terms->slug;
                                        $selected =
                                            $slug == $ville
                                                ? 'selected="selected"'
                                                : '';
                                        $returnvar .=
                                            '<option  value="' .
                                            $terms->slug .
                                            '"' .
                                            $selected .
                                            '>' .
                                            $terms->name .
                                            '</option>';
                                    endforeach;

                                    $returnvar .= '</select>';
                                }
                            } else {
                                $returnvar .=
                                    '<select name="' .
                                    $TermCat .
                                    '" id="' .
                                    $TermCat .
                                    '" class="lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields" disabled><option value="" >Sélectionnez ' .
                                    $NameCat .
                                    '</option>';
                                $terms = get_terms([
                                    'taxonomy' => 'villes',
                                    'orderby' => 'name',
                                ]);
                                foreach ($terms as $term):
                                    $returnvar .=
                                        '<option  value="' .
                                        $term->slug .
                                        '" >' .
                                        $term->name .
                                        '</option>';
                                endforeach;
                                $returnvar .= '</select>';
                            }
                        }
                    }
                }
                $returnvar .= '</div>';
                $returnvar .= '</form>';
            }
            return $returnvar;
        }

        //gets all the data for the taxonomy then display as form element
        function build_taxonomy_element(
            $types,
            $taxonomy,
            $hierarchical,
            $hide_empty,
            $show_count,
            $order_by,
            $order_dir,
            $operators,
            $all_items_labels,
            $i
        ) {
            $returnvar = '';
            $taxonomydata = get_taxonomy($taxonomy);
            $taxName = $taxonomydata->labels->name;
            $class = $taxonomydata->name;
            if ($taxonomydata) {
                $returnvar .=
                    "<div class='form-group lp-suggested-search " .
                    $class .
                    "'>";

                $args = [
                    'sf_name' => $taxonomy,
                    'class' =>
                        'lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields',
                    'name' => $taxonomy,
                    'taxonomy' => $taxonomy,
                    'hierarchical' => false,
                    'child_of' => 0,
                    'echo' => false,
                    'hide_if_empty' => false,
                    'hide_empty' => true,
                    'show_option_none' => $taxName,
                    'show_count' => '0',
                    'show_option_all_sf' => '',
                    'value_field' => 'slug',
                    'option_none_value' => '',
                    'multiple' => true,
                ];
                $taxonomychildren = get_categories($args);
                if ($types[$i] == 'select') {
                    $returnvar .= $this->generate_wp_dropdown(
                        $args,
                        $taxonomy,
                        $this->tagid,
                        $taxonomydata->labels
                    );
                }

                $returnvar .= '</div>';
            }

            return $returnvar;
        }

        /*
         * generate forms
         */


        public function generate_wp_dropdown(

            
            $args,
            $name,
            $currentid = 0,
            $labels = null
        ) {
            
            $cat_slug = htmlspecialchars(get_query_var('listingcategory'));
            $region_slug = htmlspecialchars(get_query_var('region'));
            $term_dep = htmlspecialchars(get_query_var('departement'));
            $term_ville = htmlspecialchars(get_query_var('ville'));
            $SelctedData = [];
            $args['name'] = $args['sf_name'];
            $args['show_option_none']  = 'Sélectionnez ' . $args['show_option_none'];
            /*if(is_tax($queryname['name'] )){

                $args['show_option_none'] = 'Tous ' . $queryname['name'];

            }*/

            $returnvar = '';
            
            $returnvar .= wp_dropdown_categories($args);

            return $returnvar;
        }
    }
}

if (class_exists('SearchAndFilter')) {
    global $SearchAndFilter;
    $SearchAndFilter = new SearchAndFilter();
}

/*
 * Includes
 */

// classes
require_once SEARCHANDFILTER_PLUGIN_DIR . '/of-list-table.php';
//require_once(SEARCHANDFILTER_PLUGIN_DIR."/of-taxonomy-walker.php");

// admin screens & plugin mods
require_once SEARCHANDFILTER_PLUGIN_DIR . '/of-admin.php';?>

