<?php
/*
 * Set up Admin menus & pages
 */

add_action('wp_ajax_searchandfilter_settings', 'searchandfilter_settings');
add_action(
    'wp_ajax_nopriv_searchandfilter_settings',
    'searchandfilter_settings'
);

add_action('wp_ajax_wp_insert', 'wp_insert');
add_action('wp_ajax_nopriv_wp_insert', 'wp_insert');

add_action('wp_ajax_wp_update', 'wp_update');
add_action('wp_ajax_nopriv_wp_update', 'wp_update');

add_action('wp_ajax_auto_insert', 'auto_insert');
add_action('wp_ajax_nopriv_auto_insert', 'auto_insert');

add_action(
    'wp_ajax_wp_create_listing_taxonomies',
    'wp_create_listing_taxonomies'
);
add_action(
    'wp_ajax_nopriv_wp_create_listing_taxonomies',
    'wp_create_listing_taxonomies'
);

add_action('wp_ajax_cw_post_type_news', 'cw_post_type_news');
add_action('wp_ajax_nopriv_cw_post_type_news', 'cw_post_type_news');

add_action('admin_menu', 'searchandfilter_menu_pages');

// hook into the init action and call create_book_taxonomies when it fires
add_action('init', 'wp_create_listing_taxonomies');

function admin_enqueue_scripts_callback()
{
    //Add the Select2 CSS file
    wp_enqueue_style(
        'select2-css',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        [],
        '4.1.0-rc.0'
    );

    //Add the Select2 JavaScript file
    wp_enqueue_script(
        'select2-js',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        'jquery',
        '4.1.0-rc.0'
    );

    // Enqueue my scripts.

    wp_enqueue_script(
        'wpdocs-bootstrap-bundle-sc',
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js',
        [],
        null,
        true
    );

    //Add a JavaScript file to initialize the Select2 elements
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
        [],
        '4.1.0'
    );
}
add_action('admin_enqueue_scripts', 'admin_enqueue_scripts_callback');

/**
 * Update status Taxonomy
 */

function wp_update()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'searchListing';
    $datas = $_POST['data'];
    $nam2 = $_POST['data'];
    $sql2 = "UPDATE $table_name SET status = 0 WHERE name = '" . $nam2 . "'";
    $rezz = $wpdb->query($sql2);
    foreach ($datas as $key => $data) {
        $name = $data['name'];
        $value = $data['value'];

                if (isset($value)) {
                    $sql =
                        "UPDATE $table_name SET status = $value WHERE name = '" .
                        $name .
                        "'";
                    $rez = $wpdb->query($sql);

                } else {

                    $sql =
                        "UPDATE $table_name SET status = 0 WHERE name = '" .
                        $name .
                        "'";
                    $rez = $wpdb->query($sql);

                }

        date_default_timezone_set('Europe/Paris');
        $date = date('d-m-y h:i:s');
        $rowcount = $wpdb->get_var(
            "SELECT COUNT(*) FROM $table_name WHERE name = '$name' "
        );
        //If nothing found to update, it will try and create the record.
        if ($rowcount === false || $rowcount < 1) {
            //$wpdb->query("DELETE FROM $table_name WHERE 1 = 1");
            $wpdb->insert($table_name, [
                'name' => $name,
                'slug' => $name,
                'status' => $value,
                'created_at' => $date,
                'order' => intval($key),
            ]);
        }
    }
    $response = $_POST['reps'];
    $cpt_sql =
        "UPDATE $table_name SET status_cpt = 0 WHERE `ctp_name` = '" .
        $response .
        "'";
    $cpt_rez = $wpdb->query($cpt_sql);

    foreach ($response as $key => $data) {
        $name_cpt = $data['name'];
        $value_cpt = $data['value'];
        $status_cpt = $data['status'];

        if (isset($value_cpt)) {
            $sqll =
                "UPDATE $table_name SET status_cpt = 1 WHERE `ctp_name` = '" .
                $value_cpt .
                "'";
            $sql =
                "UPDATE $table_name SET status_cpt = 0 WHERE `ctp_name` <> '" .
                $value_cpt .
                "'";
            $rezz = $wpdb->query($sqll);
            $rez = $wpdb->query($sql);
        }
       
    }
    $new_order = $_POST['data'];

    $array = explode(',', $new_order);

    foreach ($array as $key => $v) {
        $update_sql =
            "UPDATE $table_name SET `order`=" .
            intval($key) .
            " WHERE name='" .
            $v .
            "'";
        $wpdb->query($update_sql);
    }
 die();
}

/**
 * Insert Taxonomy and Custom post in table 'search listing'
 * Show Listes Taxonomy and Custom Post in Admin panel
 * 
 */

function wp_insert()
{
    $ofVarListTable = new OF_Variable_List_Table();
    $ofVarListTable->prepare_items();
    $ofVarListTable->display();

    if (!empty($_POST['data'])) {
        $user_info = wp_get_current_user();
        $username = $user_info->user_login;
        // run validation if you are not doing it in JS
        global $wpdb;
        $datas = $_POST['data'];
        foreach ($datas as $key => $data) {
            // assigning textbox values to variables
            $table_name = $wpdb->prefix . 'searchListing';
            $name = $data['name'];
            $value = $data['value'];
            date_default_timezone_set('Europe/Paris');
            $date = date('d-m-y h:i:s');
            $rowcount = $wpdb->get_var(
                "SELECT COUNT(*) FROM $table_name WHERE name = '$name' "
            );
            //If nothing found to update, it will try and create the record.
            if ($rowcount === false || $rowcount < 1) {
                //$wpdb->query("DELETE FROM $table_name WHERE 1 = 1");
                $wpdb->insert($table_name, [
                    'name' => $name,
                    'slug' => $name,
                    'status' => $value,
                    'created_at' => $date,
                ]);
            }
        }
    }

    $args = [
        'public' => true,
        '_builtin' => false,
    ];
    $output = 'object'; // or objects
    $operator = 'and'; // 'and' or 'or'
    $taxonomies = array_values(get_taxonomies($args));
    global $wpdb;
    $table_name = $wpdb->prefix . 'searchListing';
    //var_dump($taxonomies['post_tag']['labels']['all_items']); - all items should be used in the drop downs
    $result = $wpdb->get_results(
        "SELECT * FROM  $table_name WHERE name !=''  ORDER BY `$table_name`.`order` ASC"
    );
    $resultcpt = $wpdb->get_results(
        "SELECT * FROM  $table_name WHERE ctp_name !=''  ORDER BY `$table_name`.`order` ASC"
    );

    $counter = 0;
    if ($result) { ?>

		  
		  <form method="post"  action="" name="Search_Listing_Setting_Form" id="Search_Listing_Setting_Form">
		  <div class="listes_tax">
		  <h3>Listes Taxonomies</h3>
		  <div id="checkbox" class="container">
				<div id="container-input-checkbox" class="container-input">
		  <ul id="mySortable">
		  <?php foreach ($result as $key => $taxonomy) { ?>
			  <div class="setting-tgmpa">
			  <li  class="item" name="<?php echo $taxonomy->name; ?>"  name="position" id="<?php echo $taxonomy->name; ?>" value="<?php echo $key; ?>">
			  
			  <!--input id="chek-<?php echo $key; ?>" type="checkbox" class="radis" id="<?php echo $taxonomy->name; ?>" name="<?php echo $taxonomy->name; ?>" value="<?php echo $taxonomy->status; ?>" <?php echo $taxonomy->status ==
1
    ? 'checked'
    : ''; ?>>
			  <label id="label-chek-<?php echo $key; ?>" class="label-radio"  for="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->name; ?></label-->



			  <input id="checkbox-<?php echo $key; ?>" class="checkboxs" type="checkbox" name="<?php echo $taxonomy->name; ?>" <?php echo $taxonomy->status ==
1
    ? 'checked'
    : ''; ?>>
					<label id="label-<?php echo $key; ?>" class="label-checkbox" for="checkbox-<?php echo $key; ?>"><?php echo $taxonomy->name; ?></label>
			  </li>
			</div>
			  <?php } ?>
			<!--  INPUT CHECKBOX  -->





			<?php }
    ?>



</div>
				</div></ul> 
		 <button type="submit" class="setting-save button button-primary">Enregistrer</button>		  </div>
</form>
		<?php
  $args = [
      'public' => true,
      '_builtin' => false,
  ];

  $output = 'objects'; // 'names' or 'objects' (default: 'names')
  $operator = 'and'; // 'and' or 'or' (default: 'and')
  $post_types = get_post_types($args, $output, $operator);
  foreach ( $post_types  as $post_type ) {
    $name_single = $post_type->labels->name;
 }

  echo '<form method="post" action="" name="Setting_Form_CPT" id="Setting_Form_CPT">';

  echo '<div class="listes_ctp">';
  echo '<h3>Listes Post Type</h3>';
  ?>
			<?php
   echo '<div id="radio" class="container"><div id="container-input-radio" class="container-input"><ul>';

   foreach ($resultcpt as $key => $post_type) {
       echo '<div class="setting-tgmpa">'; ?>
			  	<li  class="item" name="<?php echo $post_type->ctp_name; ?>"  name="position" id="<?php echo $post_type->ctp_name; ?>" value="<?php echo $key; ?>">
				  <input id="radio-<?php echo $key; ?>" class="radios" type="radio"  name="cpt" data-value="<?php echo $post_type->status_cpt; ?>" value="<?php echo $post_type->ctp_name; ?>" <?php echo $post_type->status_cpt ==
1
    ? 'checked'
    : ''; ?>>
				  <label id="label-radio-<?php echo $key; ?>" class="label-radio" for="radio-<?php echo $key; ?>"><?php echo $name_single; ?></label>
	
				</li>
						<?php echo '</div>';
   }

   echo '</div></div><ul>';
   echo '</div></form>';?>

<?php
}

/**
 * Add Meta box in Taxonomy
 */

add_action('departement_edit_form_fields', 'edit_fields_ville');
add_action('departement_add_form_fields', 'edit_fields_ville');

/* Fire our meta box setup function on the post editor screen. */

function edit_fields_ville($tag)
{
    $termid = $tag->term_id;
    $cat_met=[];
    $cat_met = get_option("tax_$termid");
    //die;
    $terms = get_terms([
        'taxonomy' => 'villes',
        'hide_empty' => false,
    ]);
    $term_list = wp_list_pluck($terms, 'name');
    array_merge(array($term_list), array($cat_met));
    ?>

	  <tr class="form-field">
	  <th valign="top" scope="row">
						<label for="catpic"><?php _e('listes des villes', ''); ?></label>
					</th>
				  <td>
				  <select name="ville_detail[]" id="example-select" multiple>
					  <?php foreach ($term_list as $term) { ?>

							  <option value="<?php echo $term; ?>" <?php if(is_array($cat_met)) { foreach ($cat_met as $cat_me) {
                        if ($term == $cat_me) {
                            echo 'selected="selected" style="color:#dedede"';
                        }
}} ?>><?php echo $term; ?></option>
						  <?php update_option('theme_faq', $faq);} ?>
				  </select>
				  </td>
			  </tr>
  <?php
}

add_action('region_edit_form_fields', 'edit_form_departement');
add_action('region_add_form_fields', 'edit_form_departement');

function edit_form_departement($tag)
{
    $termid = $tag->term_id;
    $cat_met = get_option("tax_$termid");

    $terms = get_terms([
        'taxonomy' => 'departement',
        'hide_empty' => false,
    ]);
    $term_list = wp_list_pluck($terms, 'name');
    array_merge(array($term_list), array($cat_met));

    ?>
		<tr class="form-field">
					<th valign="top" scope="row">
						<label for="catpic"><?php _e('listes des départements', ''); ?></label>
					</th>
					<td>
					<select name="departement_detail[]" id="example-select" multiple>
						<?php foreach ($term_list as $term) { ?>
								<option value="<?php echo $term; ?>" <?php if(is_array($cat_met)) { foreach ($cat_met as $cat_me) {
    if ($term == $cat_me) {
        echo 'selected="selected" style="color:#dedede"';
    }
}} ?>><?php echo $term; ?></option>
							<?php update_option('theme_faq', $faq);} ?>
					</select>
					</td>
				</tr>
	<?php
}

// when the form gets submitted, and the new field gets updated (in your case the option will get updated with the values of your custom fields above

add_action('edited_region', 'save_extra_fileds');
add_action('created_region', 'save_extra_fileds');

// save extra category extra fields callback function



function save_extra_fileds($term_id)
{
    $termid = $term_id;
    $cat_meta = get_option("tax_$termid");
    $value_departement = isset($_POST['departement_detail'])
        ? $_POST['departement_detail']
        : '';

    if ($cat_meta !== false) {

        update_option("tax_$termid", $value_departement); ?>
			<tr class="form-field">

			<th valign="top" scope="row">
						<label for="catpic"><?php _e('listes des villes', ''); ?></label>
					</th>
			<td>
			<select name="departement_detail[]" id="example-se" multiple>
				<?php foreach ($cat_meta as $term) {
        echo '<option selected="selected">' . $term->name . '</option>';
    } ?>
			</select>
			</td>
		</tr><?php
    } else {
        add_option("tax_$termid", $value_departement, '', 'yes');
    }
}

add_action('edited_departement', 'save_extra_fileds_departement');
add_action('created_departement', 'save_extra_fileds_departement');

function save_extra_fileds_departement($term_id)
{
    $termid = $term_id;
    $dep_meta = get_option("tax_$termid");


    if ($dep_meta !== false) {
        update_option("tax_$termid", $_POST['ville_detail']); ?>
			<tr class="form-field">

			<select name="ville_detail[]" id="example-select" multiple>
				<?php foreach ($dep_meta as $termm) {
        echo '<option selected="selected">' . $termm->name . '</option>';
    } ?>
			</select>
		</tr>

		<?php
    } else {
        add_option("tax_$termid", $_POST['ville_detail'], '', 'yes');
    }
}

/**

 *
 * @see register_post_type() for registering custom post types.
 */
function wp_create_listing_taxonomies()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'searchListing';
	$resu = $wpdb->get_results ("SELECT `ctp_name` FROM $table_name WHERE `status_cpt` = 1");
	$res = $wpdb->get_results ("SELECT `name` FROM $table_name WHERE `status` = 1");
	$results_cpt =json_decode(json_encode($resu), true);
	$results_tax =json_decode(json_encode($res), true);

	$args = array(
		'public'   => true,
		'_builtin' => false
			
		);


		$output = 'names';
		$operator = 'and';
		$date = date('d-m-y h:i:s');
		$post_types = array_values(get_post_types($args));
		$taxonomies = array_values(get_taxonomies( $args, $output, $operator ));
		$compteur = 0;
        $resultsTaxname = [];
		foreach($taxonomies as $key =>$taxon){
            $tax_sl = get_taxonomy($taxon);
            $tax_name = $tax_sl->labels->name;
	        $resultsTaxname[] =$tax_sl->rewrite['slug'];

            foreach($resultsTaxname as $key =>$taxSlug){

			$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE slug = '$taxSlug' ");
			if ($rowcount === FALSE || $rowcount < 1) {
                

					$wpdb->insert($table_name , array('name' => $tax_name,'slug' => $taxSlug,'status'=> 0,'created_at'=> $date,'order'=> $compteur)) ;

					$compteur++;

				}
            }

				$rowc = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `ctp_name` = '$post_types[$key]' ");
			if ($rowc === FALSE || $rowc < 1) {
					//$wpdb->query("DELETE FROM $table_name WHERE 1 = 1");
					$wpdb->insert($table_name , array('status'=>0,'ctp_name' => $post_types[$key],'status_cpt' => 0,'created_at'=> $date,'order'=> $compteur)) ;

				}

		}
    foreach ($results_cpt as $key => $cpt) {
        $custom_post = $cpt['ctp_name'];
    }
	$querytax = $wpdb->get_results ("SELECT * FROM $table_name WHERE `status` = 1");
	$querytaxo =json_decode(json_encode($querytax), true);
    foreach ($querytaxo as $taxonomy) {
        $labels = [
            'name' => _x(
                $taxonomy['name'],
                'taxonomy general name',
                'textdomain'
            ),
            'singular_name' => _x(
                $taxonomy['name'],
                'taxonomy singular name',
                'textdomain'
            ),
            'search_items' => __('Search ' . $taxonomy['name'], 'textdomain'),
            'all_items' => __('All ' . $taxonomy['name'], 'textdomain'),
            'parent_item' => __('Parent ' . $taxonomy['name'], 'textdomain'),
            'parent_item_colon' => __(
                'Parent ' . $taxonomy['name'],
                'textdomain'
            ),
            'edit_item' => __('Edit ' . $taxonomy['name'], 'textdomain'),
            'update_item' => __('Update ' . $taxonomy['name'], 'textdomain'),
            'add_new_item' => __('Add New ' . $taxonomy['name'], 'textdomain'),
            'new_item_name' => __(
                'New ' . $taxonomy['name'] . ' Name',
                'textdomain'
            ),
            'menu_name' => __($taxonomy['name'], 'textdomain'),
        ];

        $args = [
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => $taxonomy['slug']],
        ];
        register_taxonomy($taxonomy['slug'], $custom_post, $args);
    }


}

/**
 * Auto insert Term regions
 */

                function auto_insert(){

                      // Region Auvergne-Rhône-Alpes
                      $AuvergneTerm = get_term_by('slug', 'auvergne-rhone-alpes', 'region');
                      $AuvergneID = $AuvergneTerm->term_id;
                      $AuvergneDepartement = array('Ain','Allier','Ardèche','Cantal','Drôme','Isère','Loire','Haute-Loire','Puy-de-Dôme','Rhône','Savoie','Haute-Savoie');
      
                      // Region Bourgogne-Franche-Comté
      
                      $bourgogneFCTerm = get_term_by('slug', 'bourgogne-franche-comte', 'region');
                      $bourgogneFCID = $bourgogneFCTerm->term_id;
                      $bourgogneFCD = array('Côte-d\'Or','Doubs','Jura','Nièvre','Haute-Saône','Saône-et-Loire','Yonne','Territoire de Belfort');
      
                      
                      // Region Corse
                      $corseTerm = get_term_by('slug', 'corse', 'region');
                      $corseID = $corseTerm->term_id;
                      $CorseDepartement = array('Haute-Corse','Corse-du-Sud');
      
                      // Region Bretagne
                      $BretagneTerm = get_term_by('slug', 'Bretagne', 'region');
                      $BretagneID = $BretagneTerm->term_id;
                      $BretagneDepartement = array('Côtes-d\'Armor','Finistère','Ille-et-Vilaine','Morbihan');
      
                      // Region Centre-Val de Loire
      
                      $CentreValTerm = get_term_by('slug', 'centre-val-de-loire', 'region');
                      $CentreValID = $CentreValTerm->term_id;
                      $CentreValDepartement = array('Cher','Eure-et-Loir','Indre','Indre-et-Loire','Loir-et-Cher','Loiret');
      
                      // Region Grand Est
      
                      $GrandEstTerm = get_term_by('slug', 'grand-est', 'region');
                      $GrandEstID = $GrandEstTerm->term_id;
                      $GrandEstDepartement = array('Ardennes','Aube','Marne','Haute-Marne','Meurthe-et-Moselle','Meuse','Moselle','Bas-Rhin','Haut-Rhin','Vosges');
      
                      // Region Hauts-de-France
      
                      $HautsdeFranceTerm = get_term_by('slug', 'hauts-de-france', 'region');
                      $HautsdeFranceID = $HautsdeFranceTerm->term_id;
                      $HautsdeFranceDepartement = array('Aisne','Nord','Oise','Pas-de-Calais','Somme');
      
                      // Region Normandie
      
                      $NormandieTerm = get_term_by('slug', 'normandie', 'region');
                      $NormandieID = $NormandieTerm->term_id;
                      $NormandieDepartement = array('Calvados','Eure','Manche','Orne','Seine-Maritime');
      
                      // Region Nouvelle-Aquitaine
      
                      $NouvelleAquitaineTerm = get_term_by('slug', 'nouvelle-aquitaine', 'region');
                      $NouvelleAquitaineID = $NouvelleAquitaineTerm->term_id;
                      $NouvelleAquitaineDepartement = array('Charente','Charente-Maritime','Corrèze','Creuse','Dordogne','Gironde','Landes','Lot-et-Garonne','Pyrénées-Atlantiques','Deux-Sèvres','Vienne','Haute-Vienne');
                      
                      // Region Occitanie
                      $OccitanieTerm = get_term_by('slug', 'occitanie', 'region');
                      $OccitanieID = $OccitanieTerm->term_id;
                      $OccitanieDepartement = array('Ariège','Aude','Aveyron','Gard','Haute-Garonne','Gers','Hérault','Lot','Lozère','Hautes-Pyrénées','Pyrénées-Orientales','Tarn','Tarn-et-Garonne');
      
                      // Region Pays de la Loire
                      $paysloireTerm = get_term_by('slug', 'pays-de-la-loire', 'region');
                      $paysloireID = $paysloireTerm->term_id;
                      $paysloireDepartement = array('Loire-Atlantique','Maine-et-Loire','Manche','Mayenne','Sarthe','Vendée');
      
      
                      // Region Provence-Alpes-Côte d'Azur
                      $ProvenceAlpesTerm = get_term_by('slug', 'provence-alpes-cote-dazur', 'region');
                      $ProvenceAlpesID = $ProvenceAlpesTerm->term_id;
                      $ProvenceAlpesDepartement = array('Alpes-de-Haute-Provence','Hautes-Alpes','Alpes-Maritimes','Bouches-du-Rhône (13)','Var','Vaucluse');
      
                      // Region Île-de-France
                      $ÎleFranceTerm = get_term_by('slug', 'ile-de-france', 'region');
                      $ÎleFranceID = $ÎleFranceTerm->term_id;
                      $ÎleFranceDepartement = array('Paris','Seine-et-Marne','Yvelines','Essonne','Hautes se Seine','Seine-Saint-Denis','Val-de-Marne','Val-d\'oise');
      
                      // Region Guyane
                      $GuyaneTerm = get_term_by('slug', 'guyane', 'region');
                      $GuyaneID = $GuyaneTerm->term_id;
                      $GuyaneDepartement = array('Guyane');
      
                      // Region Guadeloupe
                      $GuadeloupeTerm = get_term_by('slug', 'guadeloupe', 'region');
                      $GuadeloupeID = $GuadeloupeTerm->term_id;
                      $GuadeloupeDepartement = array('Guadeloupe');
      
                      // Region La Réunion
                      $ReunionTerm = get_term_by('slug', 'la-reunion', 'region');
                      $ReunionID = $ReunionTerm->term_id;
                      $ReunionDepartement = array('La Réunion');
      
      
                      // Region Martinique
                      $MartiniqueTerm = get_term_by('slug', 'martinique', 'region');
                      $MartiniqueID = $MartiniqueTerm->term_id;
                      $MartiniqueDepartement = array('Martinique');
      
                      // Region Mayotte
                      $MayotteTerm = get_term_by('slug', 'mayotte', 'region');
                      $MayotteID = $MayotteTerm->term_id;
                      $MayotteDepartement = array('Mayotte');
  
  
                      // Region Provence-Alpes-Côte d'Azur
                  $ProvenceAlpesOption = get_option("tax_$ProvenceAlpesID");
  
                  if ($ProvenceAlpesOption !== false) {
                      update_option("tax_$ProvenceAlpesID", $ProvenceAlpesDepartement);
                  }else{
                      add_option("tax_$ProvenceAlpesID", $ProvenceAlpesDepartement, '', 'yes');
                  }
  
                  // Region Auvergne-Rhône-Alpes
                  $AuvergneOption = get_option("tax_$AuvergneID");
                  if ($AuvergneOption !== false) {
                      update_option("tax_$AuvergneID", $AuvergneDepartement);
                  }else{
                      add_option("tax_$AuvergneID", $AuvergneDepartement, '', 'yes');
                  }
  
                  // Region Bourgogne-Franche-Comté
                  $bourgogneFCOption = get_option("tax_$bourgogneFCID");
                  if ($bourgogneFCOption !== false) {
                      update_option("tax_$bourgogneFCID", $bourgogneFCD);
                  }else{
                      add_option("tax_$bourgogneFCID", $bourgogneFCD, '', 'yes');
                  }
  
                  // Region Corse
                  $CorseOption = get_option("tax_$corseID");
                  if ($CorseOption !== false) {
                      update_option("tax_$corseID", $CorseDepartement);
                  }else{
                      add_option("tax_$corseID", $CorseDepartement, '', 'yes');
                  }
  
                  // Region Bretagne
                  $BretagneOption = get_option("tax_$BretagneID");
                  if ($BretagneOption !== false) {
                      update_option("tax_$BretagneID", $BretagneDepartement);
                  }else{
                      add_option("tax_$BretagneID", $BretagneDepartement, '', 'yes');
                  }
  
                  // Region Centre-Val de Loire
                  $CentreValOption = get_option("tax_$CentreValID");
                  if ($CentreValOption !== false) {
                      update_option("tax_$CentreValID", $CentreValDepartement);
                  }else{
                      add_option("tax_$CentreValID", $CentreValDepartement, '', 'yes');
                  }
  
                  // Region Grand Est
                  $GrandEstOption = get_option("tax_$GrandEstID");
                  if ($GrandEstOption !== false) {
                      update_option("tax_$GrandEstID", $GrandEstDepartement);
                  }else{
                      add_option("tax_$GrandEstID", $GrandEstDepartement, '', 'yes');
                  }
  
  
                  // Region Hauts de France
                  $HautsdeFranceOption = get_option("tax_$HautsdeFranceID");
                  if ($HautsdeFranceOption !== false) {
                      update_option("tax_$HautsdeFranceID", $HautsdeFranceDepartement);
                  }else{
                      add_option("tax_$HautsdeFranceID", $HautsdeFranceDepartement, '', 'yes');
                  }
                  // Region Normandie
                  $NormandieOption = get_option("tax_$NormandieID");
                  if ($NormandieOption !== false) {
                      update_option("tax_$NormandieID", $NormandieDepartement);
                  }else{
                      add_option("tax_$NormandieID", $NormandieDepartement, '', 'yes');
                  }
  
                  // Region Nouvelle Aquitaine
                  $NouvelleAquitaineOption = get_option("tax_$NouvelleAquitaineID");
                  if ($NouvelleAquitaineOption !== false) {
                      update_option("tax_$NouvelleAquitaineID", $NouvelleAquitaineDepartement);
                  }else{
                      add_option("tax_$NouvelleAquitaineID", $NouvelleAquitaineDepartement, '', 'yes');
                  }
  
                  // Region Occitanie
                  $OccitanieOption = get_option("tax_$OccitanieID");
                  if ($OccitanieOption !== false) {
                      update_option("tax_$OccitanieID", $OccitanieDepartement);
                  }else{
                      add_option("tax_$OccitanieID", $OccitanieDepartement, '', 'yes');
                  }
  
                  // Region Pays loire
                  $paysloireOption = get_option("tax_$paysloireID");
                  if ($paysloireOption !== false) {
                      update_option("tax_$paysloireID", $paysloireDepartement);
                  }else{
                      add_option("tax_$paysloireID", $paysloireDepartement, '', 'yes');
                  }
  
                  // Region Île de France
                  $ÎleFranceOption = get_option("tax_$ÎleFranceID");
                  if ($ÎleFranceOption !== false) {
                      print_r($ÎleFranceOption);
                      update_option("tax_$ÎleFranceID", $ÎleFranceDepartement);
                  }else{
                      add_option("tax_$ÎleFranceID", $ÎleFranceDepartement, '', 'yes');
                  }
  
                  // Region Guyane
                  $GuyaneOption = get_option("tax_$GuyaneID");
                  if ($GuyaneOption !== false) {
                      update_option("tax_$GuyaneID", $GuyaneDepartement);
                  }else{
                      add_option("tax_$GuyaneID", $GuyaneDepartement, '', 'yes');
                  }
  
                  // Region Guadeloupe
                  $GuadeloupeOption = get_option("tax_$GuadeloupeID");
                  if ($GuadeloupeOption !== false) {
                      update_option("tax_$GuadeloupeID", $GuadeloupeDepartement);
                  }else{
                      add_option("tax_$GuadeloupeID", $GuadeloupeDepartement, '', 'yes');
                  }
  
  
                  // Region La Réunion
                  $ReunionOption = get_option("tax_$ReunionID");
                  if ($ReunionOption !== false) {
                      update_option("tax_$ReunionID", $ReunionDepartement);
                  }else{
                      add_option("tax_$ReunionID", $ReunionDepartement, '', 'yes');
                  }
  
                  // Region Martinique
                  $MartiniqueOption = get_option("tax_$MartiniqueID");
                  if ($MartiniqueOption !== false) {
                      update_option("tax_$MartiniqueID", $MartiniqueDepartement);
                  }else{
                      add_option("tax_$MartiniqueID", $MartiniqueDepartement, '', 'yes');
                  }
  
                  // Region Martinique
                  $MayotteOption = get_option("tax_$MayotteID");
                  if ($MayotteOption !== false) {
                      update_option("tax_$MayotteID", $MayotteDepartement);
                  }else{
                      add_option("tax_$MayotteID", $MayotteDepartement, '', 'yes');
                  }

            }

function add_custom_meta_box()
{
    add_meta_box(
        'demo-meta-box',
        'Lites departements',
        'edit_form_fields',
        'listing',
        'side',
        'high',
        null
    );
}

add_action('add_meta_boxes', 'add_custom_meta_box');

function searchandfilter_menu_pages()
{
    // Add the top-level admin menu
    $page_title = 'Search &amp; Filter Settings';
    $menu_title = 'Search &amp; Filter';
    $capability = 'manage_options';
    $menu_slug = 'searchandfilter-settings';
    $function = 'wp_insert';
    $icon_url = SEARCHANDFILTER_PLUGIN_URL . '/admin/icon.png';
    $icon =
        'data:image/svg+xml;base64,' .
        base64_encode('<svg
		xmlns="http://www.w3.org/2000/svg"
		viewBox="0 0 20 20"
	 >
	   <path
		  style="fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.31579"
		  d="M 9.9999995,0.9473685 C 2,5.4736845 2,5.4736845 2,5.4736845 2,14.526315 2,14.526315 2,14.526315 9.9999995,19.052631 9.9999995,19.052631 9.9999995,19.052631 18,14.526315 18,14.526315 18,14.526315 18,5.4736845 18,5.4736845 18,5.4736845 Z m 0,15.0526305 c -3.3684207,0 -5.9999989,-2.631578 -5.9999989,-5.9999995 0,-3.368421 2.6315782,-6 5.9999989,-6 3.3684205,0 6.0000005,2.631579 6.0000005,6 0,3.3684215 -2.63158,5.9999995 -6.0000005,5.9999995 z"
		  id="path17" />
	   <path
		  style="fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:0.350878"
		  d="m 13.62573,10.052631 c 0,1.988305 -1.637426,3.62573 -3.6257305,3.62573 -1.9883036,0 -3.6257309,-1.637425 -3.6257309,-3.62573 0,-1.988304 1.6374273,-3.625731 3.6257309,-3.625731 1.9883045,0 3.6257305,1.637427 3.6257305,3.625731 z"
		  id="path19" />
	 </svg>');

    add_menu_page(
        $page_title,
        $menu_title,
        $capability,
        $menu_slug,
        $function,
        $icon
    );

    // Add submenu page with same slug as parent to ensure no duplicates
    $sub_menu_title = 'Settings';
    add_submenu_page(
        $menu_slug,
        $page_title,
        $sub_menu_title,
        $capability,
        $menu_slug,
        $function
    );

    // Now add the submenu page for Help
    $submenu_page_title = 'Search &amp; Filter Help';
    $submenu_title = 'Help';
    $submenu_slug = 'searchandfilter-help';
    $submenu_function = 'searchandfilter_help';
    add_submenu_page(
        $menu_slug,
        $submenu_page_title,
        $submenu_title,
        $capability,
        $submenu_slug,
        $submenu_function
    );
}

function searchandfilter_help()
{
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    // Render the HTML for the Help page or include a file that does
}

/*
 * Add `settings` link on plugin page next to `activate`
 */

add_filter(
    'plugin_action_links_' . SEARCHANDFILTER_BASENAME,
    'searchandfilter_plugin_action_links',
    10,
    2
);

function searchandfilter_plugin_action_links($links, $file)
{
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = SEARCHANDFILTER_BASENAME;
    }
    if ($file == $this_plugin) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        $settings_link = esc_url(
            '<a href="' .
                get_admin_url() .
                'admin.php?page=searchandfilter-settings">Settings</a>'
        );
        array_unshift($links, $settings_link);
    }
    return $links;
}

?>
