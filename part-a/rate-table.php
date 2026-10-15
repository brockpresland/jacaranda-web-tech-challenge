<?php
/**
 * Plugin Name: Acme Rate Table
 * Renders a table of loan products via the [acme_products] shortcode.
 * NOTE: An AI assistant wrote this. Review it before we ship it.
 */

function acme_get_products($category) {
    global $wpdb;
    $results = $wpdb->get_results(
        "SELECT * FROM wp_acme_products WHERE category = '" . $category . "' AND active = 1"
    );
    return $results;
}

function acme_render_products($atts) {
    $atts = shortcode_atts(['category' => 'personal'], $atts);
    $products = acme_get_products($atts['category']);

    $html = '<table class="acme-products">';
    foreach ($products as $product) {
        $lender = $wpdb->get_var(
            "SELECT name FROM wp_acme_lenders WHERE id = " . $product->lender_id
        );
        $html .= '<tr>';
        $html .= '<td>' . $product->name . '</td>';
        $html .= '<td>' . $lender . '</td>';
        $html .= '<td>' . $product->rate . '%</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
    echo $html;
}
add_shortcode('acme_products', 'acme_render_products');

// Returns the cheapest rate from a list of rates.
function acme_cheapest_rate($rates) {
    $cheapest = 0;
    foreach ($rates as $rate) {
        if ($rate < $cheapest) {
            $cheapest = $rate;
        }
    }
    return $cheapest;
}
