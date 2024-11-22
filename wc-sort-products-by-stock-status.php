<?php
/**
 * Plugin Name: WooCommerce Sort Products by Stock Status
 * Description: Ordena os produtos do WooCommerce deixando os produtos sem estoque por último.
 * Version: 1.0
 * Author: Mateus Cardoso
 */

if (!defined('ABSPATH')) {
    exit;
}

class WC_Sort_Products_By_Stock_Status {
    public function __construct() {
        add_action('init', array($this, 'check_woocommerce_active'));
    }

    public function check_woocommerce_active() {
        if (class_exists('WooCommerce')) {
            add_filter('posts_clauses', array($this, 'custom_order_by_stock_status'), 2000);
        }
    }

    public function custom_order_by_stock_status($posts_clauses) {
        global $wpdb;

        if (is_woocommerce() && (is_shop() || is_product_category() || is_product_tag())) {
            $posts_clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} wc_stock_status ON {$wpdb->posts}.ID = wc_stock_status.post_id AND wc_stock_status.meta_key = '_stock_status' ";

            $posts_clauses['orderby'] = "
                CASE wc_stock_status.meta_value
                    WHEN 'instock' THEN 1
                    WHEN 'outofstock' THEN 2
                    ELSE 3
                END, " . $posts_clauses['orderby'];
        }

        return $posts_clauses;
    }
}

new WC_Sort_Products_By_Stock_Status();
