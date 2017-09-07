<?php 

class spinner_ctl {

  public function __call($name, $args) {
    $story = story::findRandom();
    $url = G_URL;
    if (isset($story['slug'])) {
      $url .= 'story/' . $story['slug'];
    }

    header('Location: ' . $url );
    return true;

  }
}
