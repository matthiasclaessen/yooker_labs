<?php

do_action('woocommerce_before_main_content');

// ── Get all child categories of PC Components ──────────────────────────────
$parent_cat = get_term_by('slug', 'pc-components', 'product_cat');
$child_cats = $parent_cat ? get_terms([ 'taxonomy' => 'product_cat', 'parent' => $parent_cat->term_id, 'hide_empty' => true ]) : [];

global $wpdb;
$price_row  = $wpdb->get_row("
    SELECT MIN(CAST(meta_value AS DECIMAL(10,2))) AS min_price,
           MAX(CAST(meta_value AS DECIMAL(10,2))) AS max_price
    FROM {$wpdb->postmeta}
    WHERE meta_key = '_price' AND meta_value != ''
");
$global_min = $price_row ? (int) floor($price_row->min_price) : 0;
$global_max = $price_row ? (int) ceil($price_row->max_price) : 2000;

$component_types = [
  'cpu'         => 'Processor (CPU)',
  'motherboard' => 'Motherboard',
  'ram'         => 'Memory (RAM)',
  'gpu'         => 'Graphics Card (GPU)',
  'storage'     => 'Storage',
  'psu'         => 'Power Supply (PSU)',
  'case'        => 'Case',
  'cooling'     => 'Cooling',
];
?>

  <div class="c-shop">


    <div class="c-shop__layout">

      <aside class="c-filter" id="js-filter-sidebar" aria-label="Product filters">

        <div class="c-filter__mobile-header">
                    <span class="c-filter__mobile-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <line x1="4" y1="6" x2="20" y2="6"/>
                            <line x1="8" y1="12" x2="20" y2="12"/>
                            <line x1="12" y1="18" x2="20" y2="18"/>
                        </svg>
                        <?php _e('Filters', 'brightbyte'); ?>
                    </span>
          <button class="c-filter__mobile-toggle" id="js-filter-toggle" aria-expanded="false" aria-controls="js-filter-body">
            <svg class="c-filter__toggle-icon c-filter__toggle-icon--open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
            <svg class="c-filter__toggle-icon c-filter__toggle-icon--close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            <span class="screen-reader-text"><?php _e('Toggle filters', 'brightbyte'); ?></span>
          </button>
        </div>

        <div class="c-filter__body" id="js-filter-body">

          <?php if ( ! empty($child_cats) && ! is_wp_error($child_cats) ) : ?>
            <div class="c-filter__group">
              <h3 class="c-filter__group-title"><?php _e('Category', 'brightbyte'); ?></h3>
              <ul class="c-filter__list">
                <?php foreach ( $child_cats as $cat ) : ?>
                  <li class="c-filter__item">
                    <label class="c-filter__label">
                      <input type="checkbox"
                             class="c-filter__checkbox js-filter-cat"
                             value="<?= esc_attr($cat->term_id); ?>"
                             data-filter="category">
                      <span class="c-filter__checkmark"></span>
                      <?= esc_html($cat->name); ?>
                      <span class="c-filter__count">(<?= $cat->count; ?>)</span>
                    </label>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <div class="c-filter__group">
            <h3 class="c-filter__group-title"><?php _e('Component Type', 'brightbyte'); ?></h3>
            <ul class="c-filter__list">
              <?php foreach ( $component_types as $slug => $label ) : ?>
                <li class="c-filter__item">
                  <label class="c-filter__label">
                    <input type="checkbox"
                           class="c-filter__checkbox js-filter-type"
                           value="<?= esc_attr($slug); ?>"
                           data-filter="component_type">
                    <span class="c-filter__checkmark"></span>
                    <?= esc_html($label); ?>
                  </label>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <div class="c-filter__group">
            <h3 class="c-filter__group-title"><?php _e('Price', 'brightbyte'); ?></h3>
            <div class="c-filter__price-range">
              <div class="c-filter__price-track">
                <div class="c-filter__price-fill" id="js-price-fill"></div>
                <input type="range"
                       class="c-filter__range c-filter__range--min"
                       id="js-price-min"
                       min="<?= $global_min; ?>"
                       max="<?= $global_max; ?>"
                       value="<?= $global_min; ?>"
                       step="10"
                       aria-label="Minimum price">
                <input type="range"
                       class="c-filter__range c-filter__range--max"
                       id="js-price-max"
                       min="<?= $global_min; ?>"
                       max="<?= $global_max; ?>"
                       value="<?= $global_max; ?>"
                       step="10"
                       aria-label="Maximum price">
              </div>
              <div class="c-filter__price-labels">
                                <span class="c-filter__price-label">
                                    €<span id="js-price-min-label"><?= $global_min; ?></span>
                                </span>
                <span class="c-filter__price-label">
                                    €<span id="js-price-max-label"><?= $global_max; ?></span>
                                </span>
              </div>
            </div>
          </div>

          <div class="c-filter__group">
            <h3 class="c-filter__group-title"><?php _e('Availability', 'brightbyte'); ?></h3>
            <label class="c-filter__label">
              <input type="checkbox"
                     class="c-filter__checkbox"
                     id="js-filter-stock"
                     data-filter="in_stock">
              <span class="c-filter__checkmark"></span>
              <?php _e('In stock only', 'brightbyte'); ?>
            </label>
          </div>

          <div class="c-filter__group">
            <h3 class="c-filter__group-title"><?php _e('Sort by', 'brightbyte'); ?></h3>
            <div class="c-filter__select-wrap">
              <select class="c-filter__select" id="js-filter-orderby">
                <option value="date"><?php _e('Latest', 'brightbyte'); ?></option>
                <option value="popularity"><?php _e('Most popular', 'brightbyte'); ?></option>
                <option value="price"><?php _e('Price: low to high', 'brightbyte'); ?></option>
                <option value="price-desc"><?php _e('Price: high to low', 'brightbyte'); ?></option>
                <option value="title"><?php _e('Name: A–Z', 'brightbyte'); ?></option>
              </select>
            </div>
          </div>

          <button class="c-filter__reset btn" id="js-filter-reset">
            <?php _e('Reset filters', 'brightbyte'); ?>
          </button>

        </div>
      </aside>

      <div class="c-shop__results">

        <div class="c-shop__toolbar">
          <p class="c-shop__count" id="js-result-count" aria-live="polite"></p>
          <div class="c-shop__active-filters" id="js-active-filters"></div>
        </div>

        <ul class="c-product-grid" id="js-product-grid">
          <?php
          $initial = new WP_Query([
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 12,
            'orderby'        => 'date',
            'order'          => 'DESC',
          ]);

          if ( $initial->have_posts() ) :
            while ( $initial->have_posts() ) :
              $initial->the_post();
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
                    <a href="<?= esc_url($product->add_to_cart_url()); ?>" data-product_id="<?= get_the_ID(); ?>" data-product_sku="<?= esc_attr($product->get_sku()); ?>"
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
            endwhile;
            wp_reset_postdata();
          else :
            ?>
            <li class="c-product-grid__empty">
              <p><?php _e('No products found.', 'brightbyte'); ?></p>
            </li>
          <?php endif; ?>
        </ul>

        <div class="c-shop__loading" id="js-grid-loading" aria-hidden="true">
          <span class="c-shop__spinner"></span>
        </div>

        <nav class="c-shop__pagination" id="js-pagination" aria-label="Product pages"></nav>

      </div>
    </div>
  </div>

<?php
// Pass data to JS
wp_localize_script('main-php-vars', 'filter_vars', [
  'ajax_url'  => admin_url('admin-ajax.php'),
  'nonce'     => wp_create_nonce('brightbyte_filter_nonce'),
  'price_min' => $global_min,
  'price_max' => $global_max,
  'currency'  => get_woocommerce_currency_symbol(),
]);

do_action('woocommerce_after_main_content');
?>