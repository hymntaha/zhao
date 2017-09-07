<?php 

require_once '../config.php';

$mdata = microguide::findOne(array("slug"=>"beyond-banh-mi-san-jose"));
$m = microguide::i($mdata);

if (count($m->availableOn) == 0) {
    $availableOn = array(
                     'kindle' => 'http://www.amazon.com/Beer-Cheese-Perfect-Pairings-ebook/dp/B00D3UB2QK',
                     'itunes' => 'https://itunes.apple.com/us/book/beer-cheese-perfect-pairings/id654787185'
    );

    $m->availableOn = $availableOn;
    $m->save();

}


var_dump($mdata);

