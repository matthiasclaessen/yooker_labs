<?php
// Options
$image_align = get_field("block_image_text_image_alignment");
$text_align = get_field("block_image_text_text_alignment");

// Content
$title = get_field("block_image_text_title");
$text = get_field("block_image_text_text");
$image = get_field("block_image_text_image");
$link = get_field("block_image_text_link");

?>

<section class="b-block b-image-text">
    <div class="container">
        <div class="row align-items-<?= $text_align ?>">
            <div class="col-lg-5 <?php if ($image_align === "right") : ?>order-lg-last<?php endif; ?>">
                <?php if ($link) : ?>
                    <a href="<?= $link["url"]; ?>" target="<?= $link["target"]; ?>">
                    <?php endif; ?>
                    <figure>
                        <?= wp_get_attachment_image($image["ID"], "large", false, array("title" => get_the_title($image["ID"]), "class" => "img-fluid")) ?>
                    </figure>
                    <?php if ($link) : ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-lg-7 <?php if ($image_align === "right") : ?>order-lg-first<?php endif; ?>">
                <?php if ($title) : ?>
                    <div class="row">
                        <div class="col">
                            <h2><?= $title; ?></h2>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($text) : ?>
                    <div class="row">
                        <div class="col">
                            <?= $text; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($link) : ?>
                    <div class="row">
                        <div class="col">
                            <a href="<?= $link["url"]; ?>" class="btn" target="<?php $link["target"]; ?>">
                                <?= $link["title"] ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>