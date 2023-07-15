<?php
/*
Plugin Name: Guaranteed Lowest Price
Description: Displays a list of products with the "Guaranteed Lowest Price" checkbox checked.
Version: 1.0
Author: kythonlk
*/

// Enqueue custom JavaScript and CSS files
function enqueue_custom_scripts()
{
    // Enqueue custom JavaScript
    wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . '/app.js', array('jquery'), '1.0', true);

    // Enqueue custom CSS
    wp_enqueue_style('custom-style', plugin_dir_url(__FILE__) . '/style.css', array(), '1.0');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

// Add a checkbox to the product edit page
function add_guaranteed_lowest_price_checkbox()
{
    global $post;
    $value = get_post_meta($post->ID, '_guaranteed_lowest_price', true);
    ?>
    <div class="options_group">
        <?php woocommerce_wp_checkbox( array(
            'id'          => 'guaranteed_lowest_price',
            'label'       => __( 'Guaranteed Lowest Price', 'guaranteed-lowest-price' ),
            'description' => __( 'Check this box to enable the guaranteed lowest price.', 'guaranteed-lowest-price' ),
            'value'       => $value,
        ) ); ?>
    </div>
    <?php
}
add_action('woocommerce_product_options_pricing', 'add_guaranteed_lowest_price_checkbox');

// Save the checkbox value when the product is updated
function save_guaranteed_lowest_price_checkbox($product)
{
    $value = isset($_POST['guaranteed_lowest_price']) ? 'yes' : 'no';
    $product->update_meta_data('_guaranteed_lowest_price', $value);
}
add_action('woocommerce_admin_process_product_object', 'save_guaranteed_lowest_price_checkbox');

// Add the "Guaranteed Lowest Price" tag on single product pages
function display_guaranteed_lowest_price_tag()
{
    global $product;

    $value = get_post_meta($product->get_id(), '_guaranteed_lowest_price', true);

    if ($value === 'yes') {
        echo '<div><a href="https://dhabione.com/guaranteed-lowest-price" target="_blank"><span class="glp-tag-spp">Guaranteed lowest price in UAE.</span></a></div>';
    }
}
add_action('woocommerce_single_product_summary', 'display_guaranteed_lowest_price_tag', 6);

/// Add the product name to the "guaranteed-lowest-price" page
function display_guaranteed_lowest_price_products()
{
    $args = array(
        'post_type' => 'product',
        'meta_key' => '_guaranteed_lowest_price',
        'meta_value' => 'yes',
        'posts_per_page' => -1,
    );

    $products = new WP_Query($args);

    if ($products->have_posts()) {
        $count = 0;

        while ($products->have_posts()) {
            $products->the_post();
            global $product;

            // Open a new row
            if ($count % 4 === 0) {
                echo '<div class="row">';
            }

            // Display the product card
            echo '<div class="col-md-3">';
            echo '<div class="card mb-4">';
            echo '<div class="card-body">';
            echo '<a href="' . get_permalink() . '">';
            echo '<div class="hot-div"><span class="hot-tag">Hot deals</span></div>';
            echo '<img class="card-img-top  mb-3" src="' . get_the_post_thumbnail_url() . '" alt="' . get_the_title() . '">';
            echo '<h3 class="card-title pr-title">' . get_the_title() . '</h3>';
            echo '<p class="card-text price">' . $product->get_price_html() . '</p>';
            echo '<a href="' . esc_url($product->add_to_cart_url()) . '" class="btn btn-primary">Add to Cart</a>';
            echo '</a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';

            $count++;

            // Close the row and start a new one
            if ($count % 4 === 0) {
                echo '</div>';
            }
        }

        // Close any remaining row
        if ($count % 4 !== 0) {
            echo '</div>';
        }
    } else {
        echo '<p>No products found.</p>';
    }

    wp_reset_postdata();
}
add_shortcode('guaranteed_lowest_price_products', 'display_guaranteed_lowest_price_products');
