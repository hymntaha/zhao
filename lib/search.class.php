<?php

class search {

  private static $queryTypes = array(
    'story'      => 'stories_stemmed',
    'microguide' => 'microguides_stemmed'
  );

  /***
   * Returns an array, keyed by object type, of search results.
   *
   * Examples:
   *
   *   $results = search::query('silicon');               //search all types for 'silicon'
   *   $storyCount = count($results['story']['matches']); //number of story results in this page
   *   $lastPage = ($storyCount < 20);                    //no more results if $storyCount is less than default page size
   *
   *   // Work with result objects
   *   $titles = array();
   *   foreach ($results['story']['matches'] as $match) {
   *     $story = $match['objectData'];
   *     $titles[] = $story['title'];
   *   }
   *
   * @param string $query search query
   * @param array $types an array of types to search.  Should be subkeys of $queryTypes.  Set to NULL to search all.
   * @param int $offset pagination offset.
   * @param int $limit pagination page size.
   *
   ***/
  public static function query ($query, $types=NULL, $offset=0, $limit=20) {

    $s = new SphinxClient;
    $s->setServer(SEARCH_HOST, SEARCH_PORT);
    $s->setMatchMode(SPH_MATCH_ANY);
    $s->setRankingMode(SPH_RANK_SPH04);
    $s->setSortMode(SPH_SORT_RELEVANCE);
    $s->setMaxQueryTime(3);
    $s->setLimits($offset, $limit);

    $results = array();

    foreach (array_keys(self::$queryTypes) as $type) {

      if ($types === NULL || in_array($type, $types)) {

        $queryResult = $s->query($query, self::$queryTypes[$type]);

        // Build up a list of IDs

        $mongoIds = array();
        $mongoIdSphinxIdMap = array();
        
        if (isset($queryResult['matches'])) {
          foreach ($queryResult['matches'] as $sphinxId => $match) {
            $mongoIds[] = new MongoId($match['attrs']['mongo_id']);
            $mongoIdSphinxIdMap[$match['attrs']['mongo_id']] = $sphinxId;
          }
        }

        // Get all matching objects and map into results

        $cursor = $type::find(
          array(
            '_id'    => array('$in' => $mongoIds),
            'status' => 'accepted'
          )
        );

        foreach ($cursor as $id => $objectData) {
          $queryResult['matches'][$mongoIdSphinxIdMap[$id]]['objectData'] = $objectData;
        }

        $results[$type] = $queryResult;

      }

    }

    return $results;
  }

  public function random_noresult_image() {
    $files = glob(dirname(__FILE__).'/../img/noresult/{*.jpg,*.png,*.gif}',GLOB_BRACE);
    return G_URL . 'img/noresult/' . basename($files[array_rand($files)]);
  }

}
