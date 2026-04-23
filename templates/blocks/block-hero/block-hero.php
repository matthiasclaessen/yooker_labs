<?php
// Options

// Content
$title = get_field("block_hero_title");
$text = get_field("block_hero_text");
$image = get_field("block_hero_image");
$link = get_field("block_hero_link");

?>

<section class="b-block b-hero">
    <div class="b-hero__image" style="background-image: url(<?= $image["sizes"]["large"]; ?>)"></div>
    <div class="b-hero__content">
        <div class="container">
            <?php if ($title) : ?>
                <div class="row">
                    <div class="col">
                        <h1><?= $title; ?></h1>
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
                        <a href="<?= $link["url"]; ?>" class="btn" target="<?= $link["target"] ?>">
                            <?= $link["title"]; ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>