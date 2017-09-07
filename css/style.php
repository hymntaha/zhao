<?php $__style = array(

 'fonts',
 'featured_microguides',
 'global',
 'header',
 'search',
 'modal_auth',
 'modal_microguide',
 'modal',
 'tooltip',
 'loader',
 'stories',
 'story',
 'share',
 'meter',
 'menu',
 'spinner',
 'bravo_button',
 'microguide'
   );

if (isset($__more_style)) {
  $__style = array_merge($__style, $__more_style);
}

foreach($__style as $style): ?>
<link rel="stylesheet" href="/css/<?=$style?>.css?r=<?=CACHEBUST?>" type="text/css" media="all" />
<?php endforeach?>
