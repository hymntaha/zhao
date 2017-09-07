<?php

class featuredMicroguide extends kcol {

  public function getFeatures($time = null) {
    if (!$time) {
      $time = time();
    }

    $scrollerMicroguideWidth = 5;

    $cursor = featuredMicroguide::find(array('status'=>'active'))->sort(array("sequenceNumber" => 1));
    $_features = array();
    $features = array();
    $featuresById = array();

    $promotedMicroguides = array(
//    "50f5b81a6747df3578000000" => 0, // Field Design in first position
    );
    $promotedMicroguidesByPosition = array_flip($promotedMicroguides);

    // STEP 1: Get all features

    foreach ($cursor as $id => $feature) {

      if (!isset($promotedMicroguides[(string)$feature['microguideId']])) {
        $_features[] = $feature;
      }
      $featuresById[(string)$feature['microguideId']] = $feature;

    }

    // STEP 2: Filter for today's features

    $scrollerMicroguideWidthMinusPromotions = $scrollerMicroguideWidth - count($promotedMicroguides);
    $numberOfScrollGroups = ceil(count($_features) / $scrollerMicroguideWidthMinusPromotions);
    $scrollGroupOffset = date('z', $time) % $numberOfScrollGroups;

    $head = $scrollerMicroguideWidthMinusPromotions*$scrollGroupOffset;
    $features = array();
    for ($i = 0; $i < $scrollerMicroguideWidth; $i++) {
      if (isset($promotedMicroguidesByPosition[$i]) && isset($featuresById[$promotedMicroguidesByPosition[$i]])) {
        $features[] = $featuresById[$promotedMicroguidesByPosition[$i]];
      } else {
        if ($head >= count($_features)) {
          $head = 0;
        }
        $features[] = $_features[$head];
        $head++;
      }
    }

    return $features;
  }

}
