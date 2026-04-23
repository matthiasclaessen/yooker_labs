<?php

/* Template Name: Template - Products */

// Get Filter Taxonomy
$terms_kind = get_terms(array(
    'taxonomy' => 'tax-products-kind',
    'hide_empty' => false,
    'parent' => 0
));

// Get Data From URL For Filtering
$kind_filter = false;

if (!empty($_GET['category'])) {
    $kind_filter = filter_var($_GET['category']);
}

// Query For Results
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$arguments = array(
    'post_type' => 'cpt-products',
    'posts_per_page' => -1,
    'paged' => $paged,
    'meta_key' => 'product_name',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'tax_query' => array(
        'relation' => 'AND'
    ),
);

// Filter Query
$tax_query = [];

if ($kind_filter) {
    $tax_query[] = array(
        'taxonomy' => 'tax-products-kind',
        'field' => 'term_id',
        'terms' => $kind_filter
    );
}

if (!empty($tax_query)) {
    $arguments['tax_query'] = $tax_query;
}

// Create Query
$wp_query = new WP_Query($arguments);

// Create ACF Query
$products = get_posts(array(
    'post_type' => 'cpt-products',
    'posts_per_page' => -1,
    'meta_key' => 'product_price',
    'orderby' => 'meta_value',
    'order' => 'DESC'
));

?>

<?php the_content(); ?>

<!-- WP Query -->
<section class="c-products__filter">
    <div class="container">
        <form action="<?php the_permalink(); ?>" method="GET" class="" id="filter">
            <?php if ($terms_kind): ?>
                <select name="category" id="category" title="<?= __("Hover Title", "brightbyte"); ?>">
                    <option value="" <?php if (empty($kind_filter)): ?> selected <?php endif; ?>><?= __("Alles", "brightbyte") ?>
                    </option>
                    <?php foreach ($terms_kind as $kind) { ?>
                        <option value="<?= $kind->term_id ?>" <?php if ($kind_filter == $kind->term_id): ?> selected <?php endif; ?>>
                            <?= $kind->name ?>
                        </option>
                    <?php } ?>
                </select>
            <?php endif; ?>
        </form>
    </div>
</section>

<section class="c-products py-3">
    <div class="container">
        <div class="row">
            <h2>WP Query (Filtered By: <?= $arguments['meta_key'] ?>)</h2>
        </div>
        <div class="row" id="products">
            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                <div class="col-md-6 col-lg-4">
                    <div class="product">
                        <div class="product__image">
                            <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                        </div>
                        <div class="product__content">
                            <small class="content__id"><?php the_ID(); ?></small>
                            <h2><?php the_title(); ?></h2>
                            <small class="content__excerpt">
                                <?php the_excerpt(); ?>
                            </small>
                            <div class="product__cta">
                                <a href="<?php the_permalink(); ?>"
                                   class="btn"><?= __('Lees Meer', 'brightbyte'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php wp_reset_query(); ?>
        </div>
    </div>
</section>

<!-- Get Posts-->
<section class="c-products py-3">
    <div class="container">
        <div class="row">
            <h2>Get Posts</h2>
        </div>
        <?php if ($products) : ?>
            <div class="row" id="products">
                <?php foreach ($products as $product) : ?>
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