<?php

$rows = [ [ 'title' => 'red apple', 'kind' => 'fruit', 'tags' => [ 'red', 'red', 'fresh' ] ], [ 'title' => 'green apple', 'kind' => 'fruit', 'tags' => [ 'green', 'fresh' ] ], [ 'title' => 'apple tart', 'kind' => 'cake', 'tags' => [ 'baked' ] ], [ 'title' => 'pear', 'kind' => 'fruit', 'tags' => [ 'green' ] ] ];
$hits = padSearch ( $rows, 'apple', [ 'name' => 'facet-before-limit', 'fields' => 'title', 'facets' => 'kind,tags', 'first' => 1 ] );
$info = padSearchInfo ();
$ok = count ( $hits ) === 1 && $info ['total'] === 3
   && $info ['facets'] ['kind'] === [ [ 'value' => 'cake', 'count' => 1 ], [ 'value' => 'fruit', 'count' => 2 ] ]
   && $info ['facets'] ['tags'] === [ [ 'value' => 'baked', 'count' => 1 ], [ 'value' => 'fresh', 'count' => 2 ], [ 'value' => 'green', 'count' => 1 ], [ 'value' => 'red', 'count' => 1 ] ];
$answer = $ok ? 'facets count all matches and each document value once' : 'FAIL';

?>
