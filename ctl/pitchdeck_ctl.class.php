<?php 

class pitchdeck_ctl {

  public function __call($name, $args) {

    $config = dynamicConfig::findOne(array('name' => 'pitchdeckEmbedId'));
    $pitchdeckEmbedId = $config['value'];

    $pitchdeckSlideNumber = 1;
    if (isset($_GET['slide'])) {
      $pitchdeckSlideNumber = (int) $_GET['slide'];
    }
    if ($pitchdeckSlideNumber < 1) {
      $pitchdeckSlideNumber = 1;
    }
    require_once 'tpl/pitchdeck.php';

  }
}
