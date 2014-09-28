<?php snippet('header') ?>

<div class="content">
  <div class="container" id="container-topics">
    <?php echo kirbytext($page->text()) ?>
    
    <?php snippet('login') ?>
  </div>  
</div>

<?php snippet('footer') ?>