<?php

/**
 * Default AJAX filter function
 */

function brightbyte_default_ajax_filter(): void {
  $array_taxonomies = [];
  $category         = $_POST["category"];

  if ( ! empty($category) && $category !== "" ) {
    $array_taxonomies = [
      'taxonomy' => 'tax-products-kind',
      'field'    => 'term_id',
      'terms'    => $category,
    ];
  }

  $product_args = [
    'post_type'      => 'cpt-products',
    'post_status'    => 'publish',
    'posts_per_page' => - 1,
    'order'          => 'ASC',
  ];

  if ( ! empty($array_taxonomies) ) {
    $product_args = [
      'post_type'      => 'cpt-products',
      'post_status'    => 'publish',
      'posts_per_page' => - 1,
      'order'          => 'ASC',
      'tax_query'      => [
        'relation' => 'AND',
        $array_taxonomies,
      ],
    ];
  }

  $wp_query = new WP_Query($product_args);

  // Create ACF Query
  $products = get_posts([
    'post_type'      => 'cpt-products',
    'posts_per_page' => - 1,
    'meta_key'       => 'product_price',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'tax_query'      => [
      'relation' => 'AND',
      $array_taxonomies,
    ],
  ]);
  ?>

  <section class="c-products py-3">
    <div class="container">
      <?php if ( $products ) : ?>
        <div class="row" id="products">
          <?php foreach ( $products as $product ) : ?>
            <?php setup_postdata($product) ?>
            <div class="col-md-6 col-lg-4">
              <div class="product">
                <h2 class="product__name">
                  <?= $product->product_name ?>
                </h2>
                <p class="product__description">
                  <?= $product->product_description ?>
                </p>
                <small class="product__price">
                  €<?= $product->product_price ?>
                </small>
                <div class="product__cta">
                  <a href="#" class="btn"><?= __('Lees Meer', 'brightbyte'); ?></a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php wp_reset_postdata(); ?>
      <?php endif; ?>
    </div>
  </section>

  <!--//   --><?php //while ($wp_query->have_posts()) : $wp_query->the_post();
  ?>
  <!--        <div class="col-md-6 col-lg-4">-->
  <!--            <div class="product">-->
  <!--                <div class="product__image">-->
  <!--                    --><?php //the_post_thumbnail('medium', array('class' => 'img-fluid'));
  ?>
  <!--                </div>-->
  <!--                <div class="product__content">-->
  <!--                    <small class="content__id">--><?php //the_ID();
  ?><!--</small>-->
  <!--                    <h2>--><?php //the_title();
  ?><!--</h2>-->
  <!--                    <small class="content__excerpt">-->
  <!--                        --><?php //the_excerpt();
  ?>
  <!--                    </small>-->
  <!--                    <div class="product__cta">-->
  <!--                        <a href="--><?php //the_permalink();
  ?><!--"-->
  <!--                           class="btn">--><?php //= __('Lees Meer', 'brightbyte');
  ?><!--</a>-->
  <!--                    </div>-->
  <!--                </div>-->
  <!--            </div>-->
  <!--        </div>-->
  <!--    --><?php //endwhile;
  ?>
  <!---->
  <!--    --><?php //wp_reset_query();
  //
  //    exit();
}

add_action('wp_ajax_brightbyte_default_ajax_filter', 'brightbyte_default_ajax_filter');
add_action('wp_ajax_nopriv_brightbyte_default_ajax_filter', 'brightbyte_default_ajax_filter');


/**
 * AJAX functions for WooCommerce
 */
function brightbyte_ajax_get_components(): void {
  check_ajax_referer('pc_builder_nonce', 'nonce');

  $type    = sanitize_text_field($_POST['component_type'] ?? '');
  $context = json_decode(stripslashes($_POST['build_context'] ?? '[]'), true);

  if ( empty($type) ) {
    wp_send_json_error([ 'message' => 'No component type provided.' ]);
  }

  $args = [
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => - 1,
    'meta_query'     => [
      [
        'key'   => 'component_type',
        'value' => $type,
      ],
    ],
  ];

  $query    = new WP_Query($args);
  $products = [];

  foreach ( $query->posts as $post ) {
    $product = wc_get_product($post->ID);
    if ( ! $product ) {
      continue;
    }

    $compatibility = get_field('compatibility_tags', $post->ID) ?: [];
    $socket        = get_field('socket_type', $post->ID) ?: '';
    $tdp           = get_field('tdp_watts', $post->ID) ?: 0;
    $specs         = get_field('product_specifications', $post->ID) ?: [];

    // Determine if component is compatible with the current build context
    $is_compatible = brightbyte_check_compatibility($type, $post->ID, $context);

    $products[] = [
      'id'            => $product->get_id(),
      'name'          => $product->get_name(),
      'price'         => $product->get_price(),
      'price_html'    => $product->get_price_html(),
      'image'         => get_the_post_thumbnail_url($post->ID, 'medium') ?: wc_placeholder_img_src(),
      'permalink'     => get_permalink($post->ID),
      'in_stock'      => $product->is_in_stock(),
      'sku'           => $product->get_sku(),
      'compatibility' => $compatibility,
      'socket'        => $socket,
      'tdp'           => $tdp,
      'specs'         => $specs,
      'is_compatible' => $is_compatible['compatible'],
      'warnings'      => $is_compatible['warnings'],
    ];
  }

  // Sort: compatible first, then by price
  usort($products, function ($a, $b) {
    if ( $a['is_compatible'] !== $b['is_compatible'] ) {
      return $a['is_compatible'] ? - 1 : 1;
    }

    return $a['price'] <=> $b['price'];
  });

  wp_send_json_success($products);
}

add_action('wp_ajax_get_components', 'brightbyte_ajax_get_components');
add_action('wp_ajax_nopriv_get_components', 'brightbyte_ajax_get_components');

function brightbyte_check_compatibility(string $type, int $product_id, array $context): array {
  $warnings   = [];
  $compatible = true;

  switch ( $type ) {
    case 'motherboard':
      if ( ! empty($context['cpu']) ) {
        $cpu_socket  = get_field('socket_type', (int) $context['cpu']);
        $mobo_socket = get_field('socket_type', $product_id);

        if ( $cpu_socket && $mobo_socket && $cpu_socket !== $mobo_socket ) {
          $compatible = false;
          $warnings[] = sprintf(__('Socket mismatch: CPU uses %s, this motherboard uses %s.', 'brightbyte'), esc_html($cpu_socket), esc_html($mobo_socket));
        }
      }
      break;

    case 'cpu':
      if ( ! empty($context['motherboard']) ) {
        $mobo_socket = get_field('socket_type', (int) $context['motherboard']);
        $cpu_socket  = get_field('socket_type', $product_id);

        if ( $mobo_socket && $cpu_socket && $cpu_socket !== $mobo_socket ) {
          $compatible = false;
          $warnings[] = sprintf(__('Socket mismatch: motherboard uses %s, this CPU uses %s.', 'brightbyte'), esc_html($mobo_socket), esc_html($cpu_socket));
        }
      }
      break;

    case 'ram':
      if ( ! empty($context['motherboard']) ) {
        $mobo_ram = get_field('supported_ram', (int) $context['motherboard']); // e.g. 'DDR5'
        $ram_type = get_field('ram_type', $product_id);                        // e.g. 'DDR4'

        if ( $mobo_ram && $ram_type && $mobo_ram !== $ram_type ) {
          $compatible = false;
          $warnings[] = sprintf(__('RAM incompatibility: motherboard supports %s, this kit is %s.', 'brightbyte'), esc_html($mobo_ram), esc_html($ram_type));
        }
      }
      break;

    case 'psu':
      $total_tdp = 0;

      if ( ! empty($context['cpu']) ) {
        $total_tdp += (int) get_field('tdp_watts', (int) $context['cpu']);
      }
      if ( ! empty($context['gpu']) ) {
        $total_tdp += (int) get_field('tdp_watts', (int) $context['gpu']);
      }

      // Add a safety buffer of 20%
      $required_wattage = (int) ceil($total_tdp * 1.2);
      $psu_wattage      = (int) get_field('psu_wattage', $product_id);

      if ( $psu_wattage > 0 && $psu_wattage < $required_wattage ) {
        $compatible = false;
        $warnings[] = sprintf(__('Insufficient wattage: your components require at least %dW (with 20%% headroom), this PSU provides %dW.', 'brightbyte'), $required_wattage, $psu_wattage);
      }
      break;

    case 'cooling':
      if ( ! empty($context['cpu']) ) {
        $cpu_tdp    = (int) get_field('tdp_watts', (int) $context['cpu']);
        $cooler_tdp = (int) get_field('tdp_watts', $product_id);

        if ( $cooler_tdp > 0 && $cooler_tdp < $cpu_tdp ) {
          $compatible = false;
          $warnings[] = sprintf(__('Cooler TDP rating (%dW) is below your CPU\'s TDP (%dW).', 'brightbyte'), $cooler_tdp, $cpu_tdp);
        }
      }
      break;
  }

  return [ 'compatible' => $compatible, 'warnings' => $warnings ];
}

function brightbyte_ajax_add_build_to_cart(): void {
  check_ajax_referer('pc_builder_nonce', 'nonce');

  $products = json_decode(stripslashes($_POST['products'] ?? '[]'), true);

  if ( empty($products) || ! is_array($products) ) {
    wp_send_json_error([ 'message' => __('No products provided.', 'brightbyte') ]);
  }

  $added  = [];
  $errors = [];
  $cart   = WC()->cart;

  // Add a build group key so all parts can be identified together
  $build_key = 'build_' . time();

  foreach ( $products as $product_id ) {
    $product_id = absint($product_id);
    $product    = wc_get_product($product_id);

    if ( ! $product || ! $product->is_in_stock() ) {
      $errors[] = sprintf(__('"%s" is out of stock.', 'brightbyte'), get_the_title($product_id));
      continue;
    }

    $cart_item_data = [ '_build_group' => $build_key ];
    $cart_key       = $cart->add_to_cart($product_id, 1, 0, [], $cart_item_data);

    if ( $cart_key ) {
      $added[] = $product_id;
    } else {
      $errors[] = sprintf(__('Could not add "%s" to cart.', 'brightbyte'), $product->get_name());
    }
  }

  wp_send_json_success([
    'added'      => $added,
    'errors'     => $errors,
    'cart_count' => $cart->get_cart_contents_count(),
    'cart_url'   => wc_get_cart_url(),
  ]);
}

add_action('wp_ajax_add_build_to_cart', 'brightbyte_ajax_add_build_to_cart');
add_action('wp_ajax_nopriv_add_build_to_cart', 'brightbyte_ajax_add_build_to_cart');

function brightbyte_ajax_filter_products(): void {
  check_ajax_referer('brightbyte_filter_nonce', 'nonce');

  $categories      = array_map('absint', $_POST['categories'] ?? []);
  $component_types = array_map('sanitize_text_field', $_POST['component_types'] ?? []);
  $price_min       = isset($_POST['price_min']) ? (int) $_POST['price_min'] : 0;
  $price_max       = isset($_POST['price_max']) ? (int) $_POST['price_max'] : 0;
  $in_stock        = ! empty($_POST['in_stock']);
  $orderby         = sanitize_text_field($_POST['orderby'] ?? 'date');
  $paged           = max(1, (int) ( $_POST['paged'] ?? 1 ));
  $posts_per_page  = 12;

  // Base query args
  $args = [
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'meta_query'     => [ 'relation' => 'AND' ],
    'tax_query'      => [ 'relation' => 'AND' ],
  ];

  // Category filter
  if ( ! empty($categories) ) {
    $args['tax_query'][] = [
      'taxonomy'         => 'product_cat',
      'field'            => 'term_id',
      'terms'            => $categories,
      'operator'         => 'IN',
      'include_children' => false,
    ];
  }

  // Component type filter (ACF meta)
  if ( ! empty($component_types) ) {
    $args['meta_query'][] = [
      'key'     => 'component_type',
      'value'   => $component_types,
      'compare' => 'IN',
    ];
  }

  // Price range filter
  if ( $price_min > 0 || $price_max > 0 ) {
    $price_clause = [
      'key'     => '_price',
      'type'    => 'NUMERIC',
      'compare' => 'BETWEEN',
    ];
    if ( $price_min > 0 && $price_max > 0 ) {
      $price_clause['value'] = [ $price_min, $price_max ];
    } elseif ( $price_min > 0 ) {
      $price_clause['compare'] = '>=';
      $price_clause['value']   = $price_min;
    } else {
      $price_clause['compare'] = '<=';
      $price_clause['value']   = $price_max;
    }
    $args['meta_query'][] = $price_clause;
  }

  // Stock filter
  if ( $in_stock ) {
    $args['meta_query'][] = [
      'key'   => '_stock_status',
      'value' => 'instock',
    ];
  }

  // Ordering
  switch ( $orderby ) {
    case 'price':
      $args['orderby']  = 'meta_value_num';
      $args['meta_key'] = '_price';
      $args['order']    = 'ASC';
      break;
    case 'price-desc':
      $args['orderby']  = 'meta_value_num';
      $args['meta_key'] = '_price';
      $args['order']    = 'DESC';
      break;
    case 'popularity':
      $args['orderby']  = 'meta_value_num';
      $args['meta_key'] = 'total_sales';
      $args['order']    = 'DESC';
      break;
    case 'title':
      $args['orderby'] = 'title';
      $args['order']   = 'ASC';
      break;
    default:
      $args['orderby'] = 'date';
      $args['order']   = 'DESC';
  }

  $query = new WP_Query($args);

  // Render product cards
  ob_start();

  if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
      $query->the_post();
      $product    = wc_get_product(get_the_ID());
      $image      = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: wc_placeholder_img_src();
      $component  = get_post_meta(get_the_ID(), 'component_type', true);
      $in_stock_p = $product->is_in_stock();
      ?>
      <li class="c-product-card">
        <a href="<?= esc_url(get_permalink()); ?>" class="c-product-card__inner">
          <div class="c-product-card__image">
            <img src="<?= esc_url($image); ?>"
                 alt="<?= esc_attr(get_the_title()); ?>"
                 loading="lazy">
            <?php if ( ! $in_stock_p ) : ?>
              <span class="c-product-card__badge c-product-card__badge--out">
                                <?php _e('Out of stock', 'brightbyte'); ?>
                            </span>
            <?php endif; ?>
          </div>
          <div class="c-product-card__body">
            <?php if ( $component ) : ?>
              <span class="c-product-card__type">
                                <?= esc_html(ucfirst($component)); ?>
                            </span>
            <?php endif; ?>
            <h3 class="c-product-card__title"><?php the_title(); ?></h3>
            <span class="c-product-card__price">
                            <?= $product->get_price_html(); ?>
                        </span>
          </div>
        </a>
        <div class="c-product-card__actions">
          <?php if ( $in_stock_p ) : ?>
            <a href="<?= esc_url($product->add_to_cart_url()); ?>"
               data-product_id="<?= get_the_ID(); ?>"
               data-product_sku="<?= esc_attr($product->get_sku()); ?>"
               class="button add_to_cart_button ajax_add_to_cart btn btn-primary c-product-card__atc"
               aria-label="<?= esc_attr(sprintf(__('Add %s to cart', 'brightbyte'), get_the_title())); ?>">
              <?php _e('Add to cart', 'brightbyte'); ?>
            </a>
          <?php else : ?>
            <a href="<?= esc_url(get_permalink()); ?>" class="btn c-product-card__atc--out">
              <?php _e('Read more', 'brightbyte'); ?>
            </a>
          <?php endif; ?>
        </div>
      </li>
      <?php
    }
    wp_reset_postdata();
  } else {
    echo '<li class="c-product-grid__empty"><p>' . __('No products found matching your filters.', 'brightbyte') . '</p></li>';
  }

  $html = ob_get_clean();

  // Pagination data
  $total_pages = $query->max_num_pages;
  $total_found = $query->found_posts;

  wp_send_json_success([
    'html'        => $html,
    'total'       => $total_found,
    'total_pages' => $total_pages,
    'paged'       => $paged,
  ]);
}

add_action('wp_ajax_brightbyte_filter_products', 'brightbyte_ajax_filter_products');
add_action('wp_ajax_nopriv_brightbyte_filter_products', 'brightbyte_ajax_filter_products');

function brightbyte_ajax_get_price_range(): void {
  global $wpdb;

  // Get price for cheapest product and most expensive product
  $row = $wpdb->get_row("
        SELECT MIN(CAST(meta_value AS DECIMAL(10,2))) AS min_price,
               MAX(CAST(meta_value AS DECIMAL(10,2))) AS max_price
        FROM {$wpdb->postmeta}
        WHERE meta_key = '_price'
          AND meta_value != ''
    ");

  wp_send_json_success([
    'min' => $row ? (int) floor($row->min_price) : 0,
    'max' => $row ? (int) ceil($row->max_price) : 2000,
  ]);
}

add_action('wp_ajax_brightbyte_get_price_range', 'brightbyte_ajax_get_price_range');
add_action('wp_ajax_nopriv_brightbyte_get_price_range', 'brightbyte_ajax_get_price_range');

