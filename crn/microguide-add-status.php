<?php

require_once(dirname(__FILE__).'/../config.php');

$cursor = microguide::find();

$c = 0;
foreach ($cursor as $id => $microguide) {

  $microguide = microguide::i($microguide);
  if (!$microguide->status) {
    $microguide->status = 'accepted';
    $microguide->save();
    $c++;
  }

}

print "Updated $c microguides." . PHP_EOL;
