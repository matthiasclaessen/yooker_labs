<footer class="c-footer">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <div class="col-auto">
                <span>&copy; <?= date('Y'); ?> <?php bloginfo('name'); ?></span>
            </div>
            <div class="col-auto">
                <nav role="navigation" class="c-navigation__legal">
                    <?php wp_nav_menu(array('container' => 'ul', 'menu_class' => false, 'theme_location' => 'legal_navigation')); ?>
                </nav>
            </div>
        </div>
    </div>
</footer>