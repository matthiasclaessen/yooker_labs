<?php
// Options

// Content
$title = get_field("block_editor_title");
$text = get_field("block_editor_text");

?>

<section class="b-block b-editor">
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
                    <?= $text ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>