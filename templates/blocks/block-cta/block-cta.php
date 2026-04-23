<?php
// Options

// Content
$title = get_field("block_cta_title");
$text = get_field("block_cta_text");
$link = get_field("block_cta_link");

?>

<section class="b-block b-cta">
    <div class="container">
        <?php if ($title) : ?>
            <div class="row">
                <div class="col">
                    <h2><?= $title ?></h2>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($text) : ?>
            <div class="row">
                <div class="col">
                    <p><?= $text ?></p>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($link) : ?>
            <div class="row">
                <div class="col">
                    <a href="<?= $link["url"]; ?>" class="btn" target="<?= $link["target"]; ?>">
                        <?= $link["title"] ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>