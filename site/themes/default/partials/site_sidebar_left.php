<?php
/**
* @hook Fires at the left sidebar of the feeds page
*/
do_action('feed_left_sidebar_before');
?>

<nav class="navbar-light sidebar-categories-nav">

  <div class="sidebar-menu-wrapper">
    
    <ul class="nav navbar-categories flex-column">

        <!-- HOME -->
        <li class="nav-item">
          <a class="nav-link <?= e_attr($t["home_active"]); ?>" 
             href="<?= e_attr(url_for('site.home')); ?>">
             
            <img src="<?= e_attr(site_uri('assets/img/everything.png')); ?>" 
                 class="category-icon mr-1">
                 
            <?= __('category-label-everything', _T); ?>
          </a>
        </li>

        <!-- CATEGORIES -->
        <?php foreach ($t['categories'] as $category) : ?>
        <li class="nav-item">
          <a class="nav-link <?= e_attr($t["{$category['category_id']}_active"]); ?>" 
             href="<?= e_attr(url_for('site.category', ['slug' => $category['category_slug']])); ?>">
             
            <img src="<?= e_attr(category_icon_url($category['category_icon'])); ?>" 
                 class="category-icon mr-1">
                 
            <?= e(__(
              "category-label-{$category['category_slug']}",
              _T,
              ['defaultValue' => $category['category_name']]
            )); ?>
          </a>
        </li>
        <?php endforeach; ?>

    </ul>

  </div>
</nav>

<!-- Desktop Only Ad -->
<div class="ad-block d-none d-md-block">
  <?= get_option('ad_unit_2'); ?>
</div>

<?php
/**
* @hook Fires at the end of left sidebar of the feeds page
*/
do_action('feed_left_sidebar_after');
?>