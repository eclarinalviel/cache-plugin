<?php
/**
* Plugin Name: Caching Plugin
* Plugin URI: http://mypluginuri.com/
* Description: Reduces processing load of web pages.
* Version: 1.0 
* Author: blogsample
* Author URI: Author's website
* License: A "Slug" license name e.g. GPL12
*/
function get_all_post_meta($post_id) {
    global $wpdb;

    if ( ! $data = wp_cache_get( $post_id, 'post_meta' ) ) {
        $data = array();
        $raw = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = $post_id", ARRAY_A );

        foreach ( $raw as $row ) {
            $data[$row['meta_key']][] = $row['meta_value'];
        }

        wp_cache_add( $post_id, $data, 'post_meta' );
    }

    return $data;
}
?>
	<h4>EVERYTHING'S FINE</h4>

