<?php 

require_once '../config.php';

$count = 0;

foreach (bravo::find() as $bravo) {
  $bravo = bravo::i($bravo);

  if (!$bravo->weight) {
    $count++;
    $bravo->weight = 1;
    $bravo->save();
    print "Adding weight 1 to bravo " . $bravo->_id->{'$id'} . PHP_EOL;
  }
}

print "Added $count bravo weights." . PHP_EOL;

