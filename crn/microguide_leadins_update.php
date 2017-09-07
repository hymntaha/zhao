<?php 

require_once(dirname(__FILE__).'/../config.php');
  
$fname = dirname(realpath(__FILE__)) . "/../dat/microguide_leadins.json";

$json = json_decode(file_get_contents($fname));

if (!$json) {
  echo "Json didn't parse.  Quitting." . PHP_EOL;
  die;
}

// Load existing features

$cursor = featuredMicroguide::find();
$featuredMicroguides = array();
foreach ($cursor as $id => $feature) {
  if (empty($feature['microguideId'])) {
    // Remove corrupt data
    featuredMicroguide::i($feature)->remove();
    continue;
  }
  $featuredMicroguides[(string)$feature['microguideId']] = featuredMicroguide::i($feature);
}


for ($i = 0; $i < count($json); $i++) {
  $leadin = $json[$i];
  $microguide = microguide::findOne(array('slug' => $leadin->slug));
  if (empty($microguide['_id'])) {
    echo "No microguide found for slug " . $leadin->slug . '. Skipping.' . PHP_EOL;
    continue;
  }
  if (isset($featuredMicroguides[$microguide['_id']->{'$id'}])) {
    $featuredMicroguides[$microguide['_id']->{'$id'}]->remove();
  }

  $feature = new featuredMicroguide();
  $feature->microguideId = $microguide['_id'];
  $feature->question = $leadin->question;
  $feature->questionVerb = $leadin->questionVerb;
  $feature->questionPlace = $leadin->questionPlace;
  $feature->status = 'active';
  $feature->sequenceNumber = $i;
  $feature->save();
}