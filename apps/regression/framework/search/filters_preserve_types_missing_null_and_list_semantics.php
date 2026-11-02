<?php

$rows = [ [ 'id' => 1, 'title' => 'item', 'meta' => [ 'state' => NULL ], 'code' => 1, 'tags' => [ 'a', 'b' ] ], [ 'id' => 2, 'title' => 'item', 'code' => '1', 'tags' => [ 'b' ] ], [ 'id' => 3, 'title' => 'item', 'meta' => [ 'state' => NULL ], 'code' => FALSE, 'tags' => [ 'c' ] ] ];
$filtered = padSearch ( $rows, 'item', [ 'name' => 'typed-filters', 'fields' => 'title', 'filters' => [ 'code' => [ 1, FALSE ], 'meta.state' => NULL, 'tags' => 'b' ], 'facets' => [ 'code' ] ] );
$ok = array_column ( $filtered, 'id' ) === [1] && padSearchInfo () ['facets'] ['code'] === [ [ 'value' => 1, 'count' => 1 ] ];
$none = padSearch ( $rows, 'item', [ 'fields' => 'title', 'filters' => [ 'code' => [] ], 'facets' => [ 'code' ] ] );
$ok = $ok && $none === [] && padSearchInfo () === [ 'total' => 0, 'facets' => [ 'code' => [] ] ];
$answer = $ok ? 'filters preserve types, null, absent fields and list membership' : 'FAIL';

?>
