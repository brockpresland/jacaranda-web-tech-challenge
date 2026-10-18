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
    // The original function used $wpdb without importing it into local scope; declare the WordPress database object as global.
    global $wpdb;

    // The original attribute parsing omitted the shortcode name; include it so WordPress attribute filters can target this shortcode.
    $atts = shortcode_atts(['category' => 'personal'], $atts, 'acme_products');
    $products = acme_get_products($atts['category']);

    // The original function rendered an empty table when no products were found; return a useful, safely translated empty-state message.
    if (empty($products)) {
        return '<p class="acme-products-empty">' . esc_html__('No products found.', 'acme-rate-table') . '</p>';
    }

    // The original code queried one lender per product; collect unique, valid lender IDs so they can be fetched in one query.
    $lender_ids = array_values(array_unique(array_filter(array_map('absint', wp_list_pluck($products, 'lender_id')))));

    // The original code had no reliable fallback for missing lenders or query failures; start with an empty lookup map.
    $lenders = [];

    if (!empty($lender_ids)) {
        // The original lender query hard-coded the "wp_" prefix; use the configured WordPress table prefix instead.
        $lenders_table = $wpdb->prefix . 'acme_lenders';

        // The original lender ID was concatenated into SQL; create integer placeholders for a prepared bulk query.
        $placeholders = implode(', ', array_fill(0, count($lender_ids), '%d'));

        // The original N+1 queries fetched one lender at a time; fetch all required lenders with one prepared query.
        $lender_query = $wpdb->prepare(
            "SELECT id, name FROM {$lenders_table} WHERE id IN ({$placeholders})",
            $lender_ids
        );

        // The original code did not retain enough information to distinguish lender-query failure from missing data; fetch complete lender rows.
        $lender_results = $wpdb->get_results($lender_query);

        // The original code ignored database failures; log an explicit error while allowing the table to render with fallback lender names.
        if ($lender_results === null && $wpdb->last_error !== '') {
            error_log('Acme Rate Table lender query failed: ' . $wpdb->last_error);
            $lender_results = [];
        }

        // The original code performed a query during every render-loop iteration; build an in-memory lender lookup instead.
        foreach ($lender_results as $lender) {
            $lenders[(int) $lender->id] = $lender->name;
        }
    }

    // The original table had no caption or column headings; add screen-reader context and semantic headers.
    $html = '<table class="acme-products">';
    $html .= '<caption class="screen-reader-text">' . esc_html__('Available loan products', 'acme-rate-table') . '</caption>';
    $html .= '<thead><tr>';
    $html .= '<th scope="col">' . esc_html__('Product', 'acme-rate-table') . '</th>';
    $html .= '<th scope="col">' . esc_html__('Lender', 'acme-rate-table') . '</th>';
    $html .= '<th scope="col">' . esc_html__('Rate', 'acme-rate-table') . '</th>';
    $html .= '</tr></thead><tbody>';

    foreach ($products as $product) {
        // The original code queried the database here and did not handle missing lenders; use the bulk lookup with a safe fallback label.
        $lender = $lenders[(int) $product->lender_id] ?? __('Unknown lender', 'acme-rate-table');

        $html .= '<tr>';

        // The original product name was inserted as raw HTML; escape database content to prevent stored cross-site scripting.
        $html .= '<td>' . esc_html($product->name) . '</td>';

        // The original lender name was inserted as raw HTML; escape it before adding it to the table.
        $html .= '<td>' . esc_html($lender) . '</td>';

        // The original rate was inserted as raw HTML; escape it before adding the percentage suffix.
        $html .= '<td>' . esc_html($product->rate) . '%</td>';
        $html .= '</tr>';
    }

    // The original table did not close its new semantic body section; close the body before closing the table.
    $html .= '</tbody></table>';

    // The original callback echoed output, which can render in the wrong location; return it as required by the shortcode API.
    return $html;
}
add_shortcode('acme_products', 'acme_render_products');

// Returns the cheapest rate from a list of rates.
function acme_cheapest_rate($rates) {
    // The original function assumed $rates was iterable; reject invalid input before foreach can emit a warning.
    if (!is_iterable($rates)) {
        return null;
    }

    // The original value of zero prevented positive rates from ever becoming the cheapest; use null to represent no valid rate yet.
    $cheapest = null;

    foreach ($rates as $rate) {
        // The original function compared arbitrary values using PHP coercion; ignore values that are not numeric.
        if (!is_numeric($rate)) {
            continue;
        }

        // The original function had no consistent return type; normalize valid rates to floats before comparing them.
        $rate = (float) $rate;

        // The original function accepted negative and non-finite rates; ignore values that cannot represent a valid loan rate.
        if ($rate < 0 || !is_finite($rate)) {
            continue;
        }

        // The original comparison could not select a positive first value; initialize from the first valid rate, then compare normally.
        if ($cheapest === null || $rate < $cheapest) {
            $cheapest = $rate;
        }
    }

    // The original empty-list result was a misleading zero; return null when the iterable contained no valid rates.
    return $cheapest;
}
