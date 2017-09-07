<?php

require_once(dirname(__FILE__).'/../config.php');

$cursor = microguide::find();

$c = 0;
foreach ($cursor as $id => $microguide) {

  $modified = false;
  $microguide = microguide::i($microguide);

  if ($microguide->description === NULL) {
    $modified = true;
    $microguide->description = '';
  }

  if ($microguide->tags === NULL) {
    $modified = true;
    $microguide->tags = array();
  }

  if ($modified) {
    $microguide->save();
    $c++;
  }

}

print "Updated $c microguides." . PHP_EOL;
