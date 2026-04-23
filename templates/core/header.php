<header class="c-header">
  <div class="container">
    <div class="row align-items-center justify-content-between">

      <!-- Logo -->
      <div class="col-auto">
        <a href="<?= get_home_url(); ?>" class="c-logo" title="Home">
          <?php echo brightbyte_sprite('yooker_logo'); ?>
        </a>
      </div>

      <!-- Primary navigation -->
      <div class="col-auto">
        <nav role="navigation" class="c-navigation">
          <?php wp_nav_menu([
            'container'      => 'ul',
            'menu_class'     => false,
            'theme_location' => 'primary_navigation',
          ]); ?>
        </nav>
      </div>

      <!-- Utility icons -->
      <div class="col-auto">
        <div class="c-header__utils">

          <!-- Account -->
          <?php
          $account_url  = get_permalink(wc_get_page_id('myaccount'));
          $is_logged_in = is_user_logged_in();
          ?>
          <a href="<?= esc_url($account_url); ?>" class="c-header__icon-btn c-header__account <?= $is_logged_in ? 'c-header__account--in' : 'c-header__account--out'; ?>"
             aria-label="<?= $is_logged_in ? esc_attr__('My account', 'brightbyte') : esc_attr__('Login or register', 'brightbyte'); ?>">
            <?php if ( $is_logged_in ) : ?>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
                <polyline points="16.5 3.5 18.5 5.5 22 2"/>
              </svg>
            <?php else : ?>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            <?php endif; ?>
          </a>

          <!-- Cart -->
          <a href="<?= esc_url(wc_get_cart_url()); ?>" class="c-header__icon-btn c-header__cart" aria-label="<?= esc_attr__('Shopping cart', 'brightbyte'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
              <line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <span class="c-header__cart-count" data-count="<?= WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?>"><?= WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></span>
          </a>

        </div>
      </div>

    </div>
  </div>
</header>