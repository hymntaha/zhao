<?php 

require_once '../config.php';

$stats = array();
$authors = array();

foreach (story::find()->sort(array("created"=>1)) as $story) {
  if ( isset($story['status']) && $story['status'] == 'accepted' && isset($story['created'])) {
    $year = date('Y',$story['created']);
    $week = date('W',$story['created']);
    if (!isset($stats[$year])) $stats[$year] = array();
    if (!isset($stats[$year][$week])) $stats[$year][$week] = array('count'=>0,'authors'=>array());
    if (!isset($stats[$year][$week]['authors'][$story['authorSlug']]))
      $stats[$year][$week]['authors'][$story['authorSlug']] = 0;
    $stats[$year][$week]['count']++;
    $stats[$year][$week]['authors'][$story['authorSlug']]++;

    if (!isset($authors[$story['authorSlug']])) $authors[$story['authorSlug']] = 0;
    $authors[$story['authorSlug']]++;
  }
}

foreach ($stats as $year => $weeks) {
  printf("%6d\n", $year);
  printf("   %4s  %12s %12s\n", "week", "story count", "author count");
  foreach ($weeks as $week => $stories) {
    printf("   %4d  %12d %12d\n", $week, $stories['count'],count($stories['authors']));
  }
}

printf("Total author count: %5d\n", count($authors));
