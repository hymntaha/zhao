<?php  $__script = array(

 'lib',
 'jquery.history',
 'jquery.nicescroll.min',
 'tooltip',
 'loader',
 'user',
 'search',
 'stories',
 'rich',
 'share',
 'story',
 'microguide',
 'microguides',
 'display',
 'bravo',
 'menu',
 'modal',
 'header',
 'br'
   );

if (isset($__more_script)) {
  $__script = array_merge($__script, $__more_script);
}

foreach($__script as $script): ?>
<script type="text/javascript" src="/jst/<?=$script?>.js?r=<?=CACHEBUST?>"></script>
<?php endforeach?>
