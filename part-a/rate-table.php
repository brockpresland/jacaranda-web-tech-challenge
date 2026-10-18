<?php
/**
 * Plugin Name: Acme Rate Table
 * Renders a table of loan products via the [acme_products] shortcode.
 * NOTE: An AI assistant wrote this. Review it before we ship it.
 */

function acme_get_products($category) {
    global $wpdb;

    // The original function accepted any value; reject non-scalar input and return the same iterable type expected by the caller.
    if (!is_scalar($category)) {
        return [];
    }

    // The original category was not validated; normalize it to a safe WordPress key before using it as a category identifier.
    $category = sanitize_key((string) $category);

    // The original query hard-coded the default "wp_" prefix; use the configured WordPress table prefix instead.
    $products_table = $wpdb->prefix . 'acme_products';

    // The original SELECT * fetched unnecessary data; request only the columns used by the current renderer.
    // The original query concatenated the category directly into SQL; use a prepared %s placeholder to prevent SQL injection.
    $query = $wpdb->prepare(
        "SELECT name, lender_id, rate FROM {$products_table} WHERE category = %s AND active = 1",
        $category
    );

    // The original query result was returned without checking for failure; retain the result so database errors can be handled explicitly.
    $results = $wpdb->get_results(
        $query
    );

    // The original function treated a failed query like a valid result; log the database error and return an empty iterable safely.
    if ($results === null && $wpdb->last_error !== '') {
        error_log('Acme Rate Table database query failed: ' . $wpdb->last_error);
        return [];
    }

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
