<!-- Page Sidebar Start-->
<div class="sidebar-wrapper" data-layout="stroke-svg">
  <div class="logo-wrapper">
    <a href="index.html"><img class="img-fluid" src="<?= base_url(''); ?>riho/assets/images/logo/logo.png" alt="" /></a>
    <div class="back-btn"><i class="fa fa-angle-left"> </i></div>
    <!-- <div class="toggle-sidebar">
      <i
        class="status_toggle middle sidebar-toggle"
        data-feather="grid">
      </i>
    </div> -->
  </div>
  <div class="logo-icon-wrapper">
    <a href="index.html"><img
        class="img-fluid"
        src="<?= base_url(''); ?>riho/assets/images/logo/logo-icon.png"
        alt="" /></a>
  </div>
  <nav class="sidebar-main">
    <div class="left-arrow" id="left-arrow">
      <i data-feather="arrow-left"></i>
    </div>
    <div id="sidebar-menu">
      <ul class="sidebar-links" id="simple-bar">
        <li class="back-btn">
          <a href="index.html"><img
              class="img-fluid"
              src="<?= base_url(''); ?>riho/assets/images/logo/logo-icon.png"
              alt="" /></a>
          <div class="mobile-back text-end">
            <span>Back </span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i>
          </div>
        </li>
        <?php if ($menuTree) : ?>
          <?php function buildMenu($menus)
          {
            $currentURI = strtolower(str_replace(base_url(), '', current_url()));
          ?>
            <?php foreach ($menus as $menu) : ?>
              <li class="sidebar-list">
                <?php
                $menuLink = strtolower($menu['link_menu'] ?? ''); // Handle jika link_menu kosong
                $isActive = ($menu['is_last'] == 1) ? ($currentURI == $menuLink) : (strpos($currentURI, $menuLink) !== false);
                $hasActiveChild = false;

                // Logika untuk menandai parent aktif jika child aktif
                if (isset($menu['children'])) {
                  foreach ($menu['children'] as $child) {
                    $childLink = strtolower($child['link_menu'] ?? '');
                    if ($currentURI == $childLink || strpos($currentURI, $childLink) !== false) {
                      $hasActiveChild = true;
                      break;
                    }
                  }
                }
                ?>
                <?php if ($menu['is_last'] == 1) : // Kondisi untuk Link (Child) 
                ?>
                  <a class="sidebar-link sidebar-title link-nav <?= $isActive ? 'active' : ''; ?>" href="<?= base_url($menu['link_menu']); ?>">
                    <?php if (isset($menu['icon'])) : ?>
                      <svg class="stroke-icon">
                        <use href="<?= base_url('riho/assets/svg/icon-sprite.svg#stroke-' . $menu['icon']); ?>"></use>
                      </svg>
                    <?php endif; ?>
                    <span><?= $menu['name'];  ?></span>
                  </a>
                <?php else : // Kondisi untuk Parent dengan Submenu 
                ?>
                  <a class="sidebar-link sidebar-title <?= ($isActive || $hasActiveChild) ? 'active' : ''; ?>" href="#">
                    <?php if (isset($menu['icon'])) : ?>
                      <svg class="stroke-icon">
                        <use href="<?= base_url('riho/assets/svg/icon-sprite.svg#stroke-' . $menu['icon']); ?>"></use>
                      </svg>
                    <?php endif; ?>
                    <span><?= $menu['name']; ?></span>
                  </a>
                  <ul class="sidebar-submenu" style="display: block;">
                    <?php buildMenu($menu['children']); ?>
                  </ul>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          <?php } ?>
          <?php buildMenu($menuTree); ?>
        <?php else : ?>
          <li><a href="#">Tidak ada menu</a></li>
        <?php endif; ?>
      </ul>
      <div class="right-arrow" id="right-arrow">
        <i data-feather="arrow-right"></i>
      </div>
    </div>
  </nav>
</div>
<!-- Page Sidebar Ends-->