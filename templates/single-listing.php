<?php
/**
 * The template for displaying Listing single page.
 *
 */

global $listingpro_options;
$listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
$lp_detail_page_styles = $listingpro_options['lp_detail_page_styles'];

get_header();

global $post;
$listingid = $post->ID;
$gAddress = get_post_meta($listingid, '_gAddress', true);

get_template_part('listing_detail3');
get_template_part('listing-style3');

$url = home_url();

if (have_posts()):
    while (have_posts()):

        the_post();

        $user_id = get_the_ID();
        $listingAuthorId = get_post_field('post_author', get_the_ID());

        $rating = get_post_meta(get_the_ID(), 'listing_rate', true);

        $latitude = get_post_meta($user_id, '_latitude', true);
        $longitude = get_post_meta($user_id, '_longitude', true);
        $gAddress = get_post_meta($user_id, '_gAddress', true);
        $phone = get_post_meta($user_id, '_phone', true);
        $website = get_post_meta($user_id, '_website', true);
        $author_email = get_post_meta($user_id, '_user_email', true);
        $facebook = get_post_meta($user_id, '_facebook', true);
        $twitter = get_post_meta($user_id, '_twitter', true);
        $linkedin = get_post_meta($user_id, '_linkedin', true);
        $instagram = get_post_meta($user_id, '_instagram', true);
        $tagline_text = get_post_meta($user_id, '_tagline_text', true);


        $lp_detail_page_additional_detail_position =

        $showReport = true;

        if (isset($listingpro_options['lp_detail_page_report_button'])) {
            if ($listingpro_options['lp_detail_page_report_button'] == 'off') {
                $showReport = false;
            }
        }

        $resurva_url = get_post_meta($post->ID, 'resurva_url', true);

        $gIDs = get_post_meta($post->ID, 'gallery_image_ids', true);

        $sidebartop = 'sidebar-top444';

        if (empty($gIDs)) {
            $sidebartop = 'sidebar-top173';
        }

        $claimed_position = '';
        $title_len = strlen($title);
        if ($title_len > 34 && $title_len < 43) {
            $claimed_position = 'position-static';
        }

        ?>
            <section id="single-detail" class="lp-section lp-section-detail-page" data-mtype="openstreet" data-url="<?php echo $url; ?>" data-mtoken="0" data-mstyle="mapbox.streets-basic">
                <div class="lp-listing-top-title-header">
                    <div class="container pos-relative">
                        <div class="row">
                            <div class="col-md-8">
    
                                <div class="lp-style3-header-wrap">

                                
                              <?php
                              $IDs = get_post_meta(
                                  $post->ID,
                                  'post_banner_img',
                                  true
                              );

                              if (!empty($IDs)):

                                  $imgIDs = [];
                                  $numImages = 0;
                                  $ximgIDs = explode(',', $IDs);
                                  if (!empty($ximgIDs)) {
                                      foreach ($ximgIDs as $value) {
                                          if (
                                              !empty(get_post_type($value)) &&
                                              get_post_type($value) ==
                                                  'attachment'
                                          ) {
                                              $imgIDs[] = $value;
                                          }
                                      }

                                      if (!empty($imgIDs)) {
                                          $numImages = count($imgIDs);
                                      }
                                  }

                                  require_once plugin_dir_path(__DIR__) .
                                      '/include/aq_resizer.php';
                                  ?>



    <div class="lp-listing-slider" data-totalSlides="<?php echo esc_attr(
        $numImages
    ); ?>">

        <?php if ($numImages == 1) {

            $img_url = wp_get_attachment_image_src($imgIDs[0], 'full');

            $img_thumb = aq_resize($img_url[0], '780', '270', true, true, true);

            if (!filter_var($img_thumb, FILTER_VALIDATE_URL)) {
                $img_thumb = $img_url[0];
            }
            ?>

            <div class="col-md-12 lp-listing-slide-wrap">

                <div class="lp-listing-slide">

                    <a href="<?php echo esc_attr(
                        $img_url[0]
                    ); ?>" rel="prettyPhoto[gallery1]">

                        <img src="<?php echo esc_attr(
                            $img_thumb
                        ); ?>" alt="<?php the_title(); ?>">

                    </a>

                </div>

            </div>

            <?php
        } elseif ($numImages == 2) {
            foreach ($imgIDs as $imgID):

                $img_url = wp_get_attachment_image_src($imgID, 'full');

                $img_thumb = aq_resize(
                    $img_url[0],
                    '370',
                    '270',
                    true,
                    true,
                    true
                );
                if ($img_url): ?>

                    <div class="col-md-6 lp-listing-slide-wrap">

                        <div class="lp-listing-slide">

                            <a href="<?php echo esc_attr(
                                $img_url[0]
                            ); ?>" rel="prettyPhoto[gallery1]">

                                <img src="<?php echo esc_attr(
                                    $img_thumb
                                ); ?>" alt="<?php the_title(); ?>">

                            </a>

                        </div>

                    </div>

                <?php endif;
                ?>

            <?php
            endforeach;
        } else {
            foreach ($imgIDs as $imgID):

                $img_url = wp_get_attachment_image_src($imgID, 'full');

                $img_thumb = aq_resize(
                    $img_url[0],
                    '245',
                    '270',
                    true,
                    true,
                    true
                );

                if ($img_url): ?>

                    <div class="col-md-4 lp-listing-slide-wrap">

                        <div class="lp-listing-slide">

                            <a href="<?php echo esc_attr(
                                $img_url[0]
                            ); ?>" rel="prettyPhoto[gallery1]">

                                <img src="<?php echo esc_attr(
                                    $img_thumb
                                ); ?>" alt="<?php the_title(); ?>">

                            </a>

                        </div>

                    </div>

                <?php endif;
                ?>

            <?php
            endforeach;
        } ?>

    </div>

    
    <div class="lp-listing-title">

        <div class="lp-listing-logo">
       <?php the_post_thumbnail('thumbnail'); ?>
        </div>



    <?php
                              endif;
                              ?>



<div class="lp-listing-name">
    <h1><?php the_title(); ?></h1>
    <p class="lp-listing-name-tagline"><?php echo esc_attr(
        $tagline_text
    ); ?></p>
</div>

    
    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="container pos-relative">
                    <div class="row">
                        <div class="col-md-8 <?php if (!wp_is_mobile()) {
                            echo 'min-height-class';
                        } ?>">
                            <div><?php the_content(); ?></div>
                        </div>
                        <div class="col-md-4 sidebar-top0">
                            <div class="lp-sidebar listing-page-sidebar">
                                <?php

        //include_once (plugin_dir_url( __FILE__ ). 'single-list/listing-details-style3/sidebar/map-social.php' ) ;
        ?>
                                <div class="lp-widget lp-widget-top">
                                    <?php
                                    $user_id = get_current_user_id();
                                    global $post;
                                    $listingid = $post->ID;

                                    $latitude = get_post_meta(
                                        $listingid,
                                        '_latitude',
                                        true
                                    );
                                    $longitude = get_post_meta(
                                        $listingid,
                                        '_longitude',
                                        true
                                    );
                                    $gAddress = get_post_meta(
                                        $listingid,
                                        '_gAddress',
                                        true
                                    );
                                    $phone = get_post_meta(
                                        $listingid,
                                        '_phone',
                                        true
                                    );
                                    $website = get_post_meta(
                                        $listingid,
                                        '_website',
                                        true
                                    );
                                    $author_email = get_post_meta(
                                        $listingid,
                                        '_user_email',
                                        true
                                    );
                                    $facebook = get_post_meta(
                                        $listingid,
                                        '_facebook',
                                        true
                                    );
                                    $twitter = get_post_meta(
                                        $listingid,
                                        '_twitter',
                                        true
                                    );
                                    $linkedin = get_post_meta(
                                        $listingid,
                                        '_linkedin',
                                        true
                                    );
                                    $instagram = get_post_meta(
                                        $listingid,
                                        '_instagram',
                                        true
                                    );

                                    if (
                                        !empty($latitude) &&
                                        !empty($longitude)
                                    ):
 ?>

        <span class="singlebigmaptrigger" data-lat="<?php echo esc_attr(
            $latitude
        ); ?>" data-lan="<?php echo esc_attr($longitude); ?>"></span>
        <div id="singlepostmap" class="singlemap lp-widget-inner-wrap" data-lat="<?php echo esc_attr(
            $latitude
        ); ?>" data-lan="<?php echo esc_attr(
    $longitude
); ?>" data-pinicon="<?php echo esc_attr($lp_map_pin); ?>"></div>

        <?php
                                    endif;
                                    ?>
        <ul class="widget-social-icons">
            <?php
            if ($gAddress): ?>
                <li>
                    <p>
                        <span class="social-icon">
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAVTSURBVGhD7ZpbaB1lFIUTjVrvWlBj0b6oVRAFq0gLKjXgBdSi1YgIYvvg0Sp5iPGYe4L2wRvWFCxWQdGH4l1bKWjQFrERBEG8oNYLWhUaGlFb7cVgG/3WuEZSZ87knMx/TlLogs0/s/aetfefufyXk7oDSEF/f/8s7E7sBezTvr6+32j/ph3FhrGNnA/09vZeVSgUDvFl0wc9PT1nU+AabI8KL9NG6Fg37RGWmTp0dnaeQDFPY3td3J8cv4ndzvEFmO5QQ1tb25G0c7Am7EH8X9LGHdrC+RJL1h7chXMo4HsXs4vjhzo6Oo63e0LweM3nund9veyZlpaWw+yuDUh6JYX/4QI2dHd3n2pXxeD6Rdg2aw21t7cfa1d1QbJz6cROJ14V4qVF5yw0v5Em7WBzc/PBdlUHeidI9qMTrjQdBGg2ovmTtLEB09UBiVa7E+tpG0wHA+/N+WjHd/sS02GB8FxsDNvBnTnJdHCgfw+mjnzAaf2/bEAg/JYS8Be7z1RZKBaLR3PdoT6dEMTOIMcPyoUtMh0GfJVOQ3SMBPpSHWO6JIiZh71E/HZaFSTbhN2PHeewkiDmDl3D9YOmwgDRey38sqlU6GtD3HJMj2DcgV+xXfE5Glt5Fy72Jakg7kTiNMhqpjBhx8sGYutcxC2mUoF/heIwFd417l2q51x3KX48d+rFti8VxGheJq2FpvIDsc0WnWMqAXwLFOMi55v+P+rxr3TcZ1njBf5HHddpKj8QjB4VzZlMJYA/vmttplJBTAP2hWLp8DWmE0CnVTHYU6byQVMGC+4wlYBGd/yaMP5VzhSD2OgTS3zJQRX/TY5ZbSofEJshQaxkR/BplquY70xlgjtxmePXmUoA382KoSPPmcoPxHY7cepo7qmLkg6bygSxCx3/qqkE8N3lmBWm8gPBLRLFZplKgIRbHXO6qZIg9hHF0i4zlYB81uszlR+Iam4l0ctNJUDME4551lQq8DdimraPsaY5z3QC6K213g2m8gOxAYkiXjSVAP7Z+KMJH23qJ1PjCr4PHZM5uOKPFm109kxT+YHorU6+1lQqeImvJy5atxP7PraEYw2ETRwvw36RD9uU9XXDP9tx24KuTRA8RcIUsn2ihRSduYK4eF2RZq9hMx2eCq7Xml/5Sn4MJg1EP3chC0yVRGtr6+HELeYarV+GaAdpNQeb55BMEPcGplwFU+FAMQ9InPZJU1UBOWaSY5RWj2ij6XDQrok7oue87PVFpUC74Dxhp/DjQYKPlQS70VRwoP2RO5I5084FxJc6yUZTQaEPhfV/1ntmOjxIchRJov3crMFsskD3HXek5IgfDCR6WMmwV0wFgf4w7sTurq6uk01XDyTTFENbpHv1ATCdG+i96I4E3S/LBMkec9LXTeWCv4h70BvNs/1aMTxninZIeEEvNT1poBO9G9hyU7UDHSkqOe0neeZDaERrE2wEC7djUi70EwCJv3YRk5pKaN7GH+IradAuNV17UMC17sjIZH4OoPhog4E2c0elJqCIt92ZVabKAvGaqv/ua5tMTx344pxBIdqQG6vkxSc+2qzDMleUNQXF3O2ivs3a+4pB3GLFczeHaTPXJjWF93yHVBz2uOlUaJwgRvvB+nRfZ3r6YPwjhl1teh/AH4RtwHQ3njc9/UCB+mcB3ZWRtPkSfLyrr98/ps8jlQYK1JpcnXkP+28BxvFFdCBe+VXnZ7WQ0G/tFBxt5dBGu4Qca6IZb/KF23CrNij2Qv/11ZnbaHV3dLx+yge+SkHR2tPSHYhts/aI7d6/QPHxDqV2IOea3v/g8aUPK7lnfAD7oK7uH8W58IIxQYqrAAAAAElFTkSuQmCC">
                        </span>
                        <span id="lp-respo-direc"><?php echo esc_attr(
                            $gAddress
                        ); ?></span>
                    </p>
                    <a class="addr-margin" href="https://www.google.com/maps?daddr=<?php echo esc_attr(
                        $latitude
                    ); ?>,<?php echo esc_attr(
    $longitude
); ?>" target="_blank"><?php echo esc_html__(
    'Get Directions',
    'listingpro'
); ?></a>
                </li>
                <?php endif;
            if ($phone): ?>
                <li class="lp-listing-phone">
                    <span class="social-icon">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAQ7SURBVGhD7ZnJaxRBGMWTGOIKxi24ouICKir+BYroQQRBIYre9WSMMWgyyWRy8JKDQT2qqKDGXVAhUURB0KiQk+bghqKCCwoqiPv6+8avmm8qM0kMmekW5sGj0/W+7nrPmq6uLgvyyCOP/kV9ff3khoaGpY2NjVO06f8CxockEokWjr/gbzlyfqympma4lkQfEgJe1gApJEw7x2FaGl1gsgSzF6x5+No7vwqH6CXRA+YkxDljWHgEFtO+z2u/BAfppdFBeXn5AMyesmY5PyHtWlJI2wFPb+VYono04JuEp2GxyklIKNpkhGyYM35daMDMemsOnodp/6V15E549cfNyIWDioqKgRh7Y0y1wW5/LujyzMhI2DAJlcMBBhY5MxqoV7MRdSVQRs4F+QbHq5x70PkqZ4YgHdrcK3CNhHnsrmcFsFyl3AMDs50Rgnyvq6uboFKP4JqEu1Y5T6Xcg86LCPDImeHvRpW6BbV+iDaVwgPma4yhZ7Db6RQ9JQTX347FYmNUDg+YKcPMV2NunUpdICNm6qITwgFTh4y5hxy7jAoP80pXo3XRCiGIx+MzMPbdGN2gUgCZCLyaTSpFC5jcY0zKs9JlUUjbUVdD/fOqqqrBKkUHfA1OwtxnYzSmUgBGbhbtP03NVpWiBYw1G5MfCTdVpQBoe03NB47TVYoOMFUKXzij8CLNhX/Vv6itrR1BgJeuhr9vcozWcl6AsTXOpHKjSgFoW21ruKYl9BVwOmDurDH6Cc5WKQDm95sa4d7ehqFW1mkzYXa/MulgNAx+Ypi+7++giAl43dUoZUXc3eZEIfeKQTepyKq5KaujKftZdGhnqFa/QwlHe4er0bpOZre5WmIhIfxvf8cjWQ1DB3HbIUZ2qhSA9mFQPo2tsS8wbt4z6ULIT9aeZzWMbDqc9DqMq2YhdbXwh6mT4E/h2jQhbsAyKF+ktj17Ybi5bNjdMp2JwUqVU4C2GC34LMjAG+55k09tznMaZiS8YzqTMA0qp0B+TuhNUB5ka1AYhHAII8xEzMvK2Ha4K1OHsiqg/iBMLjQ5tmfaO855GFbA4zDU6XXYJm97LekCXVlvzxTCIUOYHSr3P7j5SIzJsiTokPOHGJ6vJX0G95IXpQ3zQ/5rQ+X+R3V19VDMHzMdCmXK3QaLtKxP4HrZg76r9xSuUClrkClXjPtT7jVGZ47W/DO4xxZ7P+61QKXsghXAQsw/tZ3D5NIDlmpZr0B9Sgjue4XmlNV3VqFLlcPWhPItlBdlj/85RE1KCHhPJheVcwtGZxmB0r0QX9FeKc+WlqYAPTohHPSFKPtG742xJAnzDjYzE03T8uRq2Ku7B8PbR/bBu2UUhpow+tGYTJK2n1DeR088LVohLDA2FtO7oXzfW9M+oxvCQiYEjG6GD9S44xdCyur4n2a40IHhIt4Ns3Tncsl/FyCPPCKPgoI/S9eiHsJFoB4AAAAASUVORK5CYII=">
                    </span>
                    <a data-lpid="<?php echo esc_attr(
                        $post->ID
                    ); ?>" class="phone-link" href="tel:<?php echo esc_attr(
    $phone
); ?>"><?php echo esc_attr($phone); ?></a>
                </li>
                <?php endif;

            $whatsappStatus =
            $whatsappMsg = esc_html__(
                'Hi, Contacting for you listing',
                'listingpro'
            );

            if ($website): ?>
                <li class="lp-user-web">
                    <span class="social-icon">
                        <img src="data:image/png;base64,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">
                    </span>
                    <a data-lpid="<?php echo esc_attr(
                        $post->ID
                    ); ?>" href="<?php echo esc_url(
    $website
); ?>" target="_blank"><?php echo esc_url($website); ?></a>
                </li>
            <?php endif;
            if (
                $social_show == 'true' &&
                (!empty($facebook) ||
                    !empty($twitter) ||
                    !empty($youtube) ||
                    !empty($instagram) ||
                    !empty($linkedin))
            ): ?>
                <li class="lp-widget-social-links">
                    <?php if (
                        !empty($facebook)
                    ): ?><a href="<?php echo esc_attr(
    $facebook
); ?>" target="_blank"><i class="fa fa-facebook-official" aria-hidden="true"></i></a><?php endif; ?>
                    <?php if (
                        !empty($twitter)
                    ): ?> <a href="<?php echo esc_attr(
     $twitter
 ); ?>" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a><?php endif; ?>
                    <?php if (!empty($youtube)): ?><a href="<?php echo esc_attr(
    $youtube
); ?>" target="_blank"><i class="fa fa-youtube-square" aria-hidden="true"></i></a><?php endif; ?>
                    <?php if (
                        !empty($instagram)
                    ): ?><a href="<?php echo esc_attr(
    $instagram
); ?>" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a><?php endif; ?>
                    <?php if (!empty($linkedin)) { ?>
    
                        <a href="<?php echo esc_attr(
                            $linkedin
                        ); ?>" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                        </a>
    
                    <?php } ?>
                    <div class="clearfix"></div>
                </li>
            <?php endif;
            ?>
        </ul>
    
                                </div>
    
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php
    endwhile;
    wp_reset_postdata();
endif;
global $post;

do_action('listing_single_page_content');
get_footer();
