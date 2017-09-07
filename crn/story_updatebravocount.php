<?php 

require_once '../config.php';

$keys = array("story_id" => 1);
$initial = array("bravoCount" => 0);

$reduce = "function (obj, prev) { prev.bravoCount += obj.weight; }";

$g = bravo::col()->group($keys, $initial, $reduce);

$totals = array();
foreach ($g['retval'] as $story) {
    $totals[$story['story_id']->{'$id'}] = (int)$story['bravoCount'];
}

foreach (story::find() as $story) {
    $story = story::i($story);
    if (isset($totals[$story->_id->{'$id'}])) {
        print "Updating bravo count for ".$story->_id->{'$id'}." to ".$totals[$story->_id->{'$id'}].PHP_EOL;
        $story->bravoCount = $totals[$story->_id->{'$id'}];
    } else {
        print "Updating bravo count for ".$story->_id->{'$id'}." to 0".PHP_EOL;
        $story->bravoCount = 0;
    }
    $story->save();
}
