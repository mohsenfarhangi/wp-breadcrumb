<?php
function sardar_breadcrumbs() {

    $delimiter     = '';
    $name          = 'صفحه اصلی'; //text for the 'Home' link
    $currentBefore = '<li class="breadcrumb-item">';
    $currentAfter  = '</li>';
    global $post;
    global $wp_query;
    $home = get_bloginfo( 'url' );

    $post_type        = get_post_type();
    $post_type_object = get_post_type_object( $post_type );
    $post_type_lable  = $post_type_object->label;

    if ( ! is_front_page() ) {
        echo '<nav class="breadcrumb-wraper"><ul class="breadcrumb">';
        echo $currentBefore . '<a href="' . $home . '">' . $name . '</a> ' . $currentAfter . $delimiter . ' ';
    }
    if ( ! is_home() && ! is_front_page() || is_paged() ) {

        if ( is_category() ) {
            $cat_obj   = $wp_query->get_queried_object();
            $thisCat   = $cat_obj->term_id;
            $thisCat   = get_category( $thisCat );
            $parentCat = get_category( $thisCat->parent );
            if ( $thisCat->parent != 0 ) {
                echo( get_category_parents( $parentCat, true, ' ' . $delimiter . ' ' ) );
            }
            echo $currentBefore;
            single_cat_title();
            echo $currentAfter;

        } elseif ( is_post_type_archive() ) {
            $query_obj = $wp_query->get_queried_object();
            echo $currentBefore;
            echo $query_obj->label;
            echo $currentAfter;
        } elseif ( is_tax() ) {
            $tax_obj = $wp_query->get_queried_object();
            echo $currentBefore;
            echo '<a href="' . get_post_type_archive_link( $post_type ) . '">' . $post_type_lable . '</a>';
            echo $currentAfter;
            if ( $tax_obj->parent != 0 ) {
                echo $currentBefore;
                echo get_term_parents_list( $tax_obj->term_id, $tax_obj->taxonomy );
                echo $currentAfter;
            }
            echo $currentBefore;
            echo single_term_title();
            echo $currentAfter;
        } elseif ( is_day() ) {
            echo '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a> ' . $delimiter . ' ';
            echo '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '">' . get_the_time( 'F' ) . '</a> ' . $delimiter . ' ';
            echo $currentBefore . get_the_time( 'd' ) . $currentAfter;

        } elseif ( is_month() ) {
            echo '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a> ' . $delimiter . ' ';
            echo $currentBefore . get_the_time( 'F' ) . $currentAfter;

        } elseif ( is_year() ) {
            echo $currentBefore . get_the_time( 'Y' ) . $currentAfter;

        } elseif ( is_single() && ! is_attachment() ) {
            //post type
            echo $currentBefore;
            echo '<a href="' . get_post_type_archive_link( $post_type ) . '">' . $post_type_lable . '</a>';
            echo $currentAfter;
            
            $post_taxonomy = get_post_taxonomies($post->ID);
            if (is_product()) {
                $term = get_the_terms($post->ID, 'product_cat');
            } elseif (is_singular(['post'])) {
                $term = get_the_terms($post->ID, 'category');
            } else
                $term = get_the_terms($post->ID, $post_taxonomy);
            $term = $term[0];
            if ( $term != null ) {
                echo $currentBefore;
                echo '<a href="' . get_term_link( $term->term_id ) . '" class="item-link">' . $term->name . '</a>';
                echo $currentAfter;
            }
            echo $currentBefore;
            the_title();
            echo $currentAfter;

        } elseif ( is_attachment() ) {
            $parent = get_post( $post->post_parent );
            $cat    = get_the_category( $parent->ID );
            $cat    = $cat[0];
            echo get_category_parents( $cat, true, ' ' . $delimiter . ' ' );
            echo '<a href="' . get_permalink( $parent ) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
            echo $currentBefore;
            the_title();
            echo $currentAfter;

        } elseif ( is_page() && ! $post->post_parent ) {
            echo $currentBefore;
            the_title();
            echo $currentAfter;

        } elseif ( is_page() && $post->post_parent ) {
            $parent_id   = $post->post_parent;
            $breadcrumbs = array();
            while ( $parent_id ) {
                $page          = get_page( $parent_id );
                $breadcrumbs[] = '<a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a>';
                $parent_id     = $page->post_parent;
            }
            $breadcrumbs = array_reverse( $breadcrumbs );
            foreach ( $breadcrumbs as $crumb ) {
                echo $crumb . ' ' . $delimiter . ' ';
            }
            echo $currentBefore;
            the_title();
            echo $currentAfter;

        } elseif ( is_search() ) {
            echo $currentBefore . 'نتایج جست و جو برای ' . get_search_query() . $currentAfter;

        } elseif ( is_tag() ) {
            echo $currentBefore . 'Posts tagged &#39;';
            single_tag_title();
            echo '&#39;' . $currentAfter;

        } elseif ( is_author() ) {
            global $author;
            $userdata = get_userdata( $author );
            echo $currentBefore . 'Articles posted by ' . $userdata->display_name . $currentAfter;

        } elseif ( is_404() ) {
            echo $currentBefore . 'Error 404' . $currentAfter;
        }

        if ( get_query_var( 'paged' ) ) {
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
                echo ' (';
            }
            echo __( 'Page' ) . ' ' . get_query_var( 'paged' );
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
                echo ')';
            }
        }

    } elseif ( is_home() && ! is_front_page() ) {

        $query_obj = $wp_query->get_queried_object();
        echo $currentBefore;
        echo $query_obj->post_title;
        echo $currentAfter;
    }
    if ( ! is_front_page() ) {
        echo '</ul></nav>';
    }
}
