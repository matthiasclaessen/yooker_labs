<!doctype html>
<html class="no-js" lang="<?php bloginfo('language'); ?>">

<head>
    <?php if (!defined('WPSEO_VERSION')) : ?>
        <title><?php bloginfo('name') ?></title>
        <meta name="description" content="<?php bloginfo('description'); ?>">
    <?php endif; ?>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="author" content="<?php bloginfo('name') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Custom Font(s) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;700;900&display=swap"
          rel="stylesheet">

    <?php wp_head(); ?>
</head>

<body <?php body_class('brightbyte-theme'); ?>>
<?php get_template_part('templates/core/header'); ?>
<main role="main">
    <?php include brightbyte_template_path(); ?>
</main>
<?php get_template_part('templates/core/footer'); ?>

<?php wp_footer(); ?>
<div class="hidden">

</div>
</body>

</html>