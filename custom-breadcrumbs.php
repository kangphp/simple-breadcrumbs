<?php
/*
Plugin Name: Custom Breadcrumbs
Description: Plugin untuk menambahkan breadcrumbs ke situs WordPress.
Version: 1.2
Author: Theo Samuel
*/

function custom_breadcrumbs() {
    // Settings
    $separator          = '&gt;';
    $breadcrums_id      = 'breadcrumbs';
    $breadcrums_class   = 'breadcrumbs';
    $home_title         = 'Home';
    
    // If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
    $custom_taxonomy    = 'product_cat';
    
    // Get the query & post information
    global $post,$wp_query;
    
    // Do not display on the homepage
    if ( !is_front_page() ) {
    
        // Build the breadcrumbs
        echo '<ul id="' . $breadcrums_id . '" class="' . $breadcrums_class . '" itemscope itemtype="http://schema.org/BreadcrumbList">';
        
        // Home page
        echo '<li class="item-home" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
        echo '<a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '" itemprop="item"><span itemprop="name">' . $home_title . '</span></a>';
        echo '<meta itemprop="position" content="1" />';
        echo '</li>';
        echo '<li class="separator separator-home"> ' . $separator . ' </li>';
        
        $position = 2;
        
        if ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {
            
            echo '<li class="item-current item-archive" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<strong class="bread-current bread-archive" itemprop="name">' . post_type_archive_title($prefix, false) . '</strong>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            
        } else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
            
            $post_type = get_post_type();
            
            if($post_type != 'post') {
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
            
                echo '<li class="item-cat item-custom-post-type-' . $post_type . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '" itemprop="item"><span itemprop="name">' . $post_type_object->labels->name . '</span></a>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</li>';
                echo '<li class="separator"> ' . $separator . ' </li>';
                $position++;
            }
            
            $custom_tax_name = get_queried_object()->name;
            echo '<li class="item-current item-archive" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<strong class="bread-current bread-archive" itemprop="name">' . $custom_tax_name . '</strong>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            
        } else if ( is_single() ) {
            
            $post_type = get_post_type();
            
            if($post_type != 'post') {
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
            
                echo '<li class="item-cat item-custom-post-type-' . $post_type . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '" itemprop="item"><span itemprop="name">' . $post_type_object->labels->name . '</span></a>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</li>';
                echo '<li class="separator"> ' . $separator . ' </li>';
                $position++;
            }
            
            $category = get_the_category();
            
            if(!empty($category)) {
                $last_category = end(array_values($category));
                $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','),',');
                $cat_parents = explode(',',$get_cat_parents);
                
                $cat_display = '';
                foreach($cat_parents as $parents) {
                    $cat_display .= '<li class="item-cat" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                    $cat_display .= $parents;
                    $cat_display .= '<meta itemprop="position" content="' . $position . '" />';
                    $cat_display .= '</li>';
                    $cat_display .= '<li class="separator"> ' . $separator . ' </li>';
                    $position++;
                }
            }
            
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);
            if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
                $taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
                $cat_id         = $taxonomy_terms[0]->term_id;
                $cat_nicename   = $taxonomy_terms[0]->slug;
                $cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name       = $taxonomy_terms[0]->name;
            }
            
            if(!empty($last_category)) {
                echo $cat_display;
                echo '<li class="item-current item-' . $post->ID . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<strong class="bread-current bread-' . $post->ID . '" itemprop="name">' . get_the_title() . '</strong>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</li>';
            } else if(!empty($cat_id)) {
                echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<a class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '" itemprop="item"><span itemprop="name">' . $cat_name . '</span></a>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</li>';
                echo '<li class="separator"> ' . $separator . ' </li>';
                $position++;
                echo '<li class="item-current item-' . $post->ID . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<strong class="bread-current bread-' . $post->ID . '" itemprop="name">' . get_the_title() . '</strong>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</li>';
            } else {
                echo '<li class="item-current item-' . $post->ID . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<strong class="bread-current bread-' . $post->ID . '" itemprop="name">' . get_the_title() . '</strong>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</li>';
            }
            
        } else if ( is_category() ) {
            echo '<li class="item-current item-cat" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<strong class="bread-current bread-cat" itemprop="name">' . single_cat_title('', false) . '</strong>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
        
        } else if ( is_page() ) {
            
            if( $post->post_parent ){
                
                $anc = get_post_ancestors( $post->ID );
                
                $anc = array_reverse($anc);
                
                if ( !isset( $parents ) ) $parents = null;
                
                foreach ( $anc as $ancestor ) {
                    $parents .= '<li class="item-parent item-parent-' . $ancestor . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                    $parents .= '<a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '" itemprop="item"><span itemprop="name">' . get_the_title($ancestor) . '</span></a>';
                    $parents .= '<meta itemprop="position" content="' . $position . '" />';
                    $parents .= '</li>';
                    $parents .= '<li class="separator separator-' . $ancestor . '"> ' . $separator . ' </li>';
                    $position++;
                }
                
                echo $parents;
                
                echo '<li class="item-current item-' . $post->ID . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '" itemprop="name"> ' . get_the_title() . '</strong>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</li>';
                
            } else {
                
                echo '<li class="item-current item-' . $post->ID . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '" itemprop="name"> ' . get_the_title() . '</strong>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</li>';
                
            }
            
        } else if ( is_tag() ) {
            
            $term_id        = get_query_var('tag_id');
            $taxonomy       = 'post_tag';
            $args           = 'include=' . $term_id;
            $terms          = get_terms( $taxonomy, $args );
            $get_term_id    = $terms[0]->term_id;
            $get_term_slug  = $terms[0]->slug;
            $get_term_name  = $terms[0]->name;
            
            echo '<li class="item-current item-tag-' . $get_term_id . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<strong class="bread-current bread-tag-' . $get_term_id . '" itemprop="name">' . $get_term_name . '</strong>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
        
        } elseif ( is_day() ) {
            
            echo '<li class="item-year item-year-' . get_the_time('Y') . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '" itemprop="item"><span itemprop="name">' . get_the_time('Y') . ' Archives</span></a>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
            
            echo '<li class="item-month item-month-' . get_the_time('m') . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '" title="' . get_the_time('M') . '" itemprop="item"><span itemprop="name">' . get_the_time('M') . ' Archives</span></a>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            echo '<li class="separator separator-' . get_the_time('m') . '"> ' . $separator . ' </li>';
            
            echo '<li class="item-current item-' . get_the_time('j') . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<strong class="bread-current bread-' . get_the_time('j') . '" itemprop="name"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</strong>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            
        } else if ( is_month() ) {
            
            echo '<li class="item-year item-year-' . get_the_time('Y') . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '" itemprop="item"><span itemprop="name">' . get_the_time('Y') . ' Archives</span></a>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
            
            echo '<li class="item-month item-month-' . get_the_time('m') . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<strong class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '" itemprop="name">' . get_the_time('M') . ' Archives</strong>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            
        } else if ( is_year() ) {
            
            echo '<li class="item-current item-current-' . get_the_time('Y') . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<strong class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '" itemprop="name">' . get_the_time('Y') . ' Archives</strong>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            
        } else if ( is_author() ) {
            
            global $author;
            $userdata = get_userdata( $author );
            
            echo '<li class="item-current item-current-' . $userdata->user_nicename . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<strong class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '" itemprop="name">' . 'Author: ' . $userdata->display_name . '</strong>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            
        } else if ( get_query_var('paged') ) {
            
            echo '<li class="item-current item-current-' . get_query_var('paged') . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<strong class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '" itemprop="name">' . 'Page ' . get_query_var('paged') . '</strong>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            
        } else if ( is_search() ) {
        
            echo '<li class="item-current item-current-' . get_search_query() . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<strong class="bread-current bread-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '" itemprop="name">Search results for: ' . get_search_query() . '</strong>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
        
        } elseif ( is_404() ) {
            
            echo '<li>' . 'Error 404' . '</li>';
        }
    
        echo '</ul>';
        
    }
}

function custom_breadcrumbs_shortcode() {
    ob_start();
    custom_breadcrumbs();
    return ob_get_clean();
}
add_shortcode('custom_breadcrumbs', 'custom_breadcrumbs_shortcode');

function insert_breadcrumbs() {
    if (function_exists('custom_breadcrumbs')) {
        echo '<div class="breadcrumbs-container">';
        custom_breadcrumbs();  // Menampilkan breadcrumbs
        echo '</div>';
    }
}

function enqueue_breadcrumbs_styles() {
    wp_enqueue_style('breadcrumbs-style', plugins_url('style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'enqueue_breadcrumbs_styles');


add_action('ocean_tax_description', 'insert_breadcrumbs');
