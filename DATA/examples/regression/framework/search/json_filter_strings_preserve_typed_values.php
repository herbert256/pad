<?php

  $rows = [ [ 'title' => 'item', 'id' => 1, 'kind' => 'a' ], [ 'title' => 'item', 'id' => '1', 'kind' => 'b' ], [ 'title' => 'item', 'id' => 2, 'kind' => 'b' ] ];
  $hits = padSearch ( $rows, 'item', [ 'fields' => 'title', 'filters' => '{"id":[1,2],"kind":"b"}' ] );
  $ok = array_column ( $hits, 'id' ) === [2];
  foreach ( [ '[]', 'null', '{bad}', '{"id":{"nested":1}}' ] as $filters )
    $ok = $ok && is_string ( padSearchRun ( [ 'bad-json-filters', NULL, $rows, FALSE ], 'item', [ 'fields' => 'title', 'filters' => $filters ] ) );
  $answer = $ok ? 'JSON filter strings keep exact typed matching' : 'FAIL';

?>
