<?php
/**
 * The template for displaying Listing category.
 */
get_header();
$type = 'mylistingservice';
$mtQuery = [];
$term_id = '';
$taxName = '';
$termID = '';
$term_ID = '';

$category = '';
$region = '';
$departement = '';
$ville = '';
$cat_slug = '';
$cat_name = '';
?>



<section class="page-container clearfix section-fixed listing-with-map pos-relative taxonomy" id="<?php echo esc_attr(
    $taxName
); ?>">
<?php $url = home_url(); ?>
<div data-layout-class="<?php echo esc_attr(
    $layout_class
); ?>" id="list-grid-view-v2" data-url="<?php echo $url; ?>" class=" <?php echo esc_attr(
    $header_style_v2
); ?> <?php echo esc_attr($v2_map_class); ?> <?php echo esc_attr(
     $listing_layout
 ); ?>"></div>

<div class="sidemap-container pull-right sidemap-fixed">
<div class="overlay_on_map_for_filter"></div>
<div class="map-pop map-container3" id="map-section">

 <div id='map' class="mapSidebar"></div>
</div>

<a href="#" class="open-img-view"><i class="fa fa-file-image-o"></i></a>
</div>
<div class="all-list-map"></div>
<div class=" pull-left post-with-map-container-right">
<div class="post-with-map-container pull-left">				

 <div class="margin-bottom-20 margin-top-30">
	 <?php
?>
 </div>

 <div class="margin-bottom-20 margin-top-30"> 
	<?php
echo do_shortcode(
     '[searchandfilter fields="listing-category,region,departement,location"]'
 ); ?>
</div>
<div class="result">
 <?php
 if (isset($queried_object)) {
     $category = $queried_object->slug;
 } else {
     $category = htmlspecialchars($_COOKIE['listingcategory']); // => 'departement'
 }

 if (isset($_COOKIE['departement'])) {
     $departement = htmlspecialchars($_COOKIE['departement']); // => 'listing category'
 }

 if (isset($_COOKIE['region'])) {
     $region = htmlspecialchars($_COOKIE['region']); // => 'region'
 }

 if (isset($_COOKIE['villes'])) {
     $ville = htmlspecialchars($_COOKIE['villes']); // => 'villes'
 }

 $args = [
     'post_type' => $type,
     'tax_query' => [
         'relation' => 'IN',

         [
             'relation' => 'AND',
             [
                 'taxonomy' => 'region',
                 'field' => 'slug',
                 'terms' => $region,
                 'operator' => 'AND',
             ],
             [
                 'taxonomy' => 'departement',
                 'field' => 'slug',
                 'terms' => $departement,
                 'operator' => 'AND',
             ],
             [
                 'taxonomy' => 'villes',
                 'field' => 'slug',
                 'terms' => $ville,
                 'operator' => 'AND',
             ],
             [
                 'taxonomy' => 'listingcategory',
                 'field' => 'slug',
                 'terms' => $category,
                 'operator' => 'AND',
             ],
         ],
     ],
 ];

 echo '<div class="row">';

 $query = new WP_Query($args);
 if ($query->have_posts()):
     $totalpost = $query->found_posts;
     /** Category name  */

     $term_category = get_term_by('slug', $category, 'listingcategory');
     $category_name = $term_category->name;

     /** region name  */

     $term_region = get_term_by('slug', $region, 'region');
     $region_name = $term_region->name;

     /** ville name  */

     $term_ville = get_term_by('slug', $ville, 'villes');
     $Ville_name = $term_ville->name;

     if (!empty($ville)) {
         echo '<div class="col-md-12 col-sm-12 margin-bottom-20 margin-top-10"><h1 class="G_titre">' .
             $totalpost .
             ' avocats  ' .
             $category_name .
             ' disponibles pour vous conseiller en ' .
             $Ville_name .
             ' </h1></div>';
     } elseif (!empty($region)) {
         echo '<div class="col-md-12 col-sm-12 margin-bottom-20 margin-top-10"><h1 class="G_titre">' .
             $totalpost .
             ' avocats  ' .
             $category_name .
             ' disponibles pour vous conseiller en ' .
             $region_name .
             ' </h1></div>';
     } else {
         echo '<div class="col-md-12 col-sm-12 margin-bottom-20 margin-top-10"><h1 class="G_titre">' .
             $totalpost .
             ' avocats  ' .
             $category_name .
             ' disponibles pour vous conseiller</h1></div>';
     }

     while ($query->have_posts()):

         $query->the_post();

         $listing_style = '';
         $listing_style = $listingpro_options['listing_style'];
         if (isset($_GET['list-style']) && !empty($_GET['list-style'])) {
             $listing_style = esc_html($_GET['list-style']);
         }
         if (is_front_page()) {
             $listing_style = 'col-md-4 col-sm-6';
             $postGridnumber = 3;
         } else {
             if ($listing_style == '1') {
                 $listing_style = 'col-md-4 col-sm-6';
                 $postGridnumber = 3;
             } elseif ($listing_style == '3' && !is_page()) {
                 $listing_style = 'col-md-6 col-sm-12';
                 $postGridnumber = 2;
             } elseif ($listing_style == '5') {
                 $listing_style = 'col-md-12 col-sm-12';
                 $postGridnumber = 2;
             } else {
                 $listing_style = 'col-md-6 col-sm-6';
                 $postGridnumber = 2;
             }
         }

         $meta = get_post_meta(get_the_ID());
         $latitude = get_post_meta(get_the_ID(), '_latitude', true);
         $longitude = get_post_meta(get_the_ID(), '_longitude', true);
         $gAddress = get_post_meta(get_the_ID(), '_gAddress', true);
         $listing_layout = $listingpro_options['listing_views'];

         if (
             isset($GLOBALS['my_listing_views']) &&
             $GLOBALS['my_listing_views'] != ''
         ) {
             $listing_layout = $GLOBALS['my_listing_views'];
         }
         ?>
<div class="<?php echo esc_attr($listing_style); ?> <?php echo esc_attr(
     $adClass
 ); ?> lp-grid-box-contianer grid_view2 card1 lp-grid-box-contianer1 listing-grid-view2-outer" data-title="<?php echo get_the_title(); ?>" data-postid="<?php echo get_the_ID(); ?>"   data-lattitue="<?php echo esc_attr(
    $latitude
); ?>" data-longitute="<?php echo esc_attr(
    $longitude
); ?>" data-posturl="<?php echo get_the_permalink(); ?>" data-lppinurl="<?php echo esc_url(
    $lp_default_map_pin
); ?>">
	 <?php if (is_page_template('template-favourites.php')) { ?>
		 <div class="remove-fav md-close" data-post-id="<?php echo get_the_ID(); ?>">
			 <i class="fa fa-close"></i>
		 </div>
	 <?php } ?>
	 <div class="lp-grid-box">
		 <div class="lp-grid-box-thumb-container" >
			 <div class="lp-grid-box-thumb">
				 <div class="show-img">
					 <?php if (has_post_thumbnail()) {
          $image = wp_get_attachment_image_src(
              get_post_thumbnail_id(get_the_ID()),
              'listingpro-blog-grid2'
          );
          if (!empty($image[0])) {
              echo "<a href='" .
                  get_the_permalink() .
                  "' >
											 <img src='" .
                  $image[0] .
                  "' />
										 </a>";
          } elseif (!empty($deafaultFeatImg)) {
              echo "<a href='" .
                  get_the_permalink() .
                  "' >
											<img src='" .
                  $deafaultFeatImg .
                  "' />
										 </a>";
          } else {
              echo '
									 <a href="' .
                  get_the_permalink() .
                  '" >
										 <img src="' .
                  esc_html__(
                      'https://via.placeholder.com/372x400',
                      'listingpro'
                  ) .
                  '" alt="image">
									 </a>';
          }
      } elseif (!empty($deafaultFeatImg)) {
          echo "<a href='" .
              get_the_permalink() .
              "' >
								 <img src='" .
              $deafaultFeatImg .
              "' />
							 </a>";
      } else {
          echo '
							 <a href="' .
              get_the_permalink() .
              '" >
								 <img src="' .
              esc_html__('https://via.placeholder.com/372x400', 'listingpro') .
              '" alt="image">
							 </a>';
      } ?>
				 </div>
				 <div class="hide-img listingpro-list-thumb">
					 <?php if (has_post_thumbnail()) {
          $image = wp_get_attachment_image_src(
              get_post_thumbnail_id(get_the_ID()),
              'listingpro-blog-grid'
          );
          if (!empty($image[0])) {
              echo "<a href='" .
                  get_the_permalink() .
                  "' >
											 <img src='" .
                  $image[0] .
                  "' />
										 </a>";
          } elseif (!empty($deafaultFeatImg)) {
              echo "<a href='" .
                  get_the_permalink() .
                  "' >
											<img src='" .
                  $deafaultFeatImg .
                  "' />
										 </a>";
          } else {
              echo '
									 <a href="' .
                  get_the_permalink() .
                  '" >
										 <img src="' .
                  esc_html__(
                      'https://via.placeholder.com/372x240',
                      'listingpro'
                  ) .
                  '" alt="image">
									 </a>';
          }
      } elseif (!empty($deafaultFeatImg)) {
          echo "<a href='" .
              get_the_permalink() .
              "' >
								 <img src='" .
              $deafaultFeatImg .
              "' />
							 </a>";
      } else {
          echo '
							 <a href="' .
              get_the_permalink() .
              '" >
								 <img src="' .
              esc_html__('https://via.placeholder.com/372x240', 'listingpro') .
              '" alt="image">
							 </a>';
      } ?>
				 </div>
				</div>
			 <div class="lp-grid-box-quick">
				 <ul class="lp-post-quick-links clearfix">
					 <li class="pull-left">
						 <a href="#" data-post-type="grids" data-post-id="<?php echo esc_attr(
           get_the_ID()
       ); ?>" data-success-text="<?php echo esc_html__(
    'Saved',
    'listingpro'
); ?>" class="status-btn <?php if ($favrt == 'yes') {
    echo 'remove-fav';
} else {
    echo 'add-to-fav';
} ?> lp-add-to-fav">
							 <i class="fa <?php echo esc_attr(
            $isfavouriteicon
        ); ?>"></i> <span><?php echo wp_kses_post($isfavouritetext); ?></span>
						 </a>
					 </li>
					 
				 </ul>
			 </div>
		 </div>
		 <div class="lp-grid-desc-container lp-border clearfix">
			 <div class="lp-grid-box-description ">
				 <div class="lp-grid-box-left pull-left">
					 <h4 class="lp-h4">
						 <a href="<?php echo get_the_permalink(); ?>">
							 <?php echo wp_kses_post($CHeckAd); ?>
							 <?php echo mb_substr(get_the_title(), 0, 40); ?>
							 <?php echo wp_kses_post($claim); ?>
						 </a>
					 </h4>
					 <ul>
						 <?php if ($lp_review_switch == 1) { ?>
						 <li>
							 <?php
        $NumberRating = listingpro_ratings_numbers($post->ID);
        if ($NumberRating != 0) {

            if ($NumberRating <= 1) {
                $review = esc_html__('Rating', 'listingpro');
            } else {
                $review = esc_html__('Ratings', 'listingpro');
            }
            echo lp_cal_listing_rate(get_the_ID());
            ?>
									 <span>
										 <?php echo esc_attr($NumberRating); ?>
										 <?php echo esc_attr($review); ?>
									 </span>
							 <?php
        } else {
            echo lp_cal_listing_rate(get_the_ID());
        }
        ?>
						 </li>
						 <?php } ?>
						 
						 <li>
							 <?php
        $cats = get_the_terms(get_the_ID(), 'listingcategory');
        if (!empty($cats)) {
            $catCount = 1;
            foreach ($cats as $cat) {
                if ($catCount == 1) {
                    //echo '<span class="cat-icon"><img class="icon icons8-Food" src="http://localhost/listing_site/wp-content/plugin/search-listing/img/icon-cat.png" alt="cat-icon"></span>';
                    $term_link = get_term_link($cat);
                    echo '
											 <a href="' .
                        $term_link .
                        '">
												 ' .
                        $cat->name .
                        '
											 </a>';
                    $catCount++;
                }
            }
        }
        ?>
						 </li>
					 </ul>
					 <div class="clearfix"></div>
					 <p class="description-container"><?php echo mb_substr(
          strip_tags(get_the_content()),
          0,
          100
      ); ?></p>
				 </div>
				 <div class="lp-grid-box-right pull-right">
				 </div>
			 </div>
			 <?php if (!empty($openStatus) || !empty($cats)) { ?>
				 <div class="lp-grid-box-bottom">
					 <div class="pull-left">
						 <div class="show">
							 <?php
        $countlocs = 1;
        $cats = get_the_terms(get_the_ID(), 'villes');
        if (!empty($cats)) {
            foreach ($cats as $cat) {
                if ($countlocs == 1) {
                    $term_link = get_term_link($cat);
                    echo '<span class="location">' . $cat->name . '</span>';
                }
                $countlocs++;
            }
        }
        ?>
						 </div>
							 <div class="hide">
								 <span class="cat-icon"></span>
								 <span class="text gaddress"><?php echo mb_substr(
             $gAddress,
             0,
             30
         ); ?>...</span>
							 </div>
					 </div>
					 <?php if (!empty($openStatus)) {
          echo '
							 <div class="pull-right">
								 <a class="status-btn">';
          echo wp_kses_post($openStatus);
          echo ' 
								 </a>
							 </div>';
      } ?>
					 <div class="clearfix"></div>
				 </div>
			 
			 <?php } ?>
		 </div>
	 </div>
</div>	
 

 <?php
         //get_template_part('templates/preview');
         ?>
			  
<?php if ($postGridCount % $postGridnumber == 0) {
    echo '<div class="clearfix lp-archive-clearfix"></div>';
} ?>

<?php
     endwhile;
     wp_reset_postdata();
 else:
     echo '<div class="col-md-12 margin-top-150"><span class="s_titre">Aucun avocat exist</span></div>';
 endif;
 echo '</div></div></div>';
 wp_reset_postdata();

 $response = ob_get_contents();
 ob_end_clean();

 echo $response;
 die(1);
 ?>
</div>
 <div class="content-grids-wraps">
	 <div class="clearfix lp-list-page-grid <?php echo esc_attr(
      $addClasscompact
  ); ?>" id="content-grids" >						
		 <?php
   if ($listing_layout == 'list_view_v2') {
       echo '<div class="lp-listings list-style active-view">
				 <div class="search-filter-response">
					 <div class="lp-listings-inner-wrap">';
   }
   if ($listing_layout == 'grid_view_v2') {
       echo '<div class="lp-listings grid-style active-view">
				 <div class="search-filter-response">
					 <div class="lp-listings-inner-wrap">';
   }
   ?>
		 <?php $array['features'] = ''; ?> 
			 <div class="promoted-listings">
				 <?php if (!empty($_GET['s']) && isset($_GET['s']) && $_GET['s'] == 'home') {
         echo listingpro_get_campaigns_listing(
             'lp_top_in_search_page_ads',
             false,
             $taxQuery,
             $TxQuery,
             $priceQuery,
             $sKeyword,
             null,
             $ad_campaignsIDS
         );
     } else {
         echo listingpro_get_campaigns_listing(
             'lp_top_in_search_page_ads',
             false,
             $TxQuery,
             $searchQuery,
             $priceQuery,
             $sKeyword,
             null,
             $ad_campaignsIDS
         );
     } ?> 
			 <div class="md-overlay"></div>
			 </div>
			 <?php if ($my_query->have_posts()) {
        while ($my_query->have_posts()):
            $my_query->the_post();
            get_template_part('listing-loop');
        endwhile;
        wp_reset_query();
    } elseif (empty($ad_campaignsIDS)) { ?>						
					 <div class="text-center margin-top-80 margin-bottom-80">
						 <h2><?php esc_html_e('No Results', 'listingpro'); ?></h2>
						 <p><?php esc_html_e(
           'Sorry! There are no listings matching your search.',
           'listingpro'
       ); ?></p>
						 <p><?php esc_html_e('Try changing your search filters or', 'listingpro'); ?>
						 <?php $currentURL = LP_current_URL(); ?>
							 <a href=""><?php esc_html_e('Reset Filter', 'listingpro'); ?></a>
						 </p>
					 </div>									
				 <?php } ?>
	 <div class="md-overlay"></div>
		 <?php if (
       $listing_layout == 'list_view_v2' ||
       $listing_layout == 'grid_view_v2'
   ) {
       echo '   <div class="clearfix"></div> <div>
			 <div>
		   <div><div class="clearfix"></div>';
   } ?>

	 </div>
 </div>

<?php
echo '<div id="lp-pages-in-cats">';
echo listingpro_load_more_filter($my_query, '1', $defSquery);
echo '</div>';
?>
<div class="lp-pagination pagination lp-filter-pagination-ajx"></div>
</div>
<input type="hidden" id="lp_current_query" value="<?php echo wp_kses_post(
    $defSquery
); ?>">
</div>
</section>
<!--==================================Section Open=================================-->

<?php get_footer(); ?>
