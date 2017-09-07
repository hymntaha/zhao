<?php

require_once(dirname(__FILE__).'/../config.php');

$pageSize = 500;
$maxPages = 20;
$currentPage = 0;
$since = time() - (86400*2);
$usersSeen = array();
$udelay = 330000;

$cursor = mailsync::findUsersToSync($since, $usersSeen)->limit($pageSize);

while ($cursor->hasNext() && $currentPage < $maxPages) {

  $currentPage++;

  $syncBatch = array();

  foreach ($cursor as $key => $row) {

    if ( !isset($row['mailSync'])
         || ($row['mailSync'] <= $row['updated'] ) ) {

      $syncBatch[] = user::i($row);

    }

    $usersSeen[] = $row['slug'];

    // Pagination
    $since =  $row['updated'];

  }

  if (count($syncBatch)) {
    mailsync::syncToMailchimp($syncBatch);
  }

  usleep($udelay);

  $cursor = mailsync::findUsersToSync($since, $usersSeen)->limit($pageSize);

}
