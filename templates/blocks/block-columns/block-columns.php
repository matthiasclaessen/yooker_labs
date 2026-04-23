<?php
// Options
$columns = get_field("block_columns_number_of_columns");

// Content
$title = get_field("block_columns_title");
$repeater = get_field("block_columns_repeater");

?>

<section class="b-block b-columns">
    <div class="container">
        <?php if ($title) : ?>
            <div class="row">
                <div class="col">
                    <h2><?= $title; ?></h2>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($repeater) : ?>
            <div class="row">
                <?php foreach ($repeater as $column) : ?>
                    <?php $column_class = match ($columns) {
                        '3' => 'col-sm-12 col-lg-4',
                        '4' => 'col-sm-12 col-lg-6 col-xl-3',
                        default => 'col-sm-12 col-lg-6'
                    };
                    ?>
                    <?php
                    $title = $column['title'];
                    $text = $column['text'];
                    $link = $column['link'];
                    $button_type = $column['button_type'];
                    $image = wp_get_attachment_image($column['image']['ID'] ?? 1, 'full', false, array("title" => get_the_title($column['image']['ID']), 'class' => 'img-fluid'));
                    ?>
                    <div class="<?= $column_class ?>">
                        <div class="column">
                            <div class="column__image">
                                <?php if ($image) : ?>
                                    <?= $image ?>
                                <?php endif; ?>
                            </div>

                            <div class="column__content">
                                <?php if ($title) : ?>
                                    <h3><?= $title ?></h3>
                                <?php endif; ?>

                                <?php if ($text) : ?>
                                    <?= $text ?>
                                <?php endif; ?>

                                <?php if ($link) : ?>
                                    <a href="<?= $link['url']; ?>" class="btn btn-<?= $button_type; ?>"
                                       target="<?= $link['target']; ?>">
                                        <?= $link['title']; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>