<?php

$rows = [ [ 'id' => 1, 'title' => 'apple', 'kind' => 'fruit' ], [ 'id' => 2, 'title' => 'apple', 'kind' => 'cake' ] ];
$options = [ 'name' => 'metadata-filter-cache', 'fields' => 'title', 'filters' => [ 'kind' => 'fruit' ], 'facets' => 'kind' ];
$before = padSearch ( $rows, 'apple', $options );
$rows [0] ['kind'] = 'cake'; $rows [1] ['kind'] = 'fruit';
$after = padSearch ( $rows, 'apple', $options );
$ok = array_column ( $before, 'id' ) === [1] && array_column ( $after, 'id' ) === [2];
padSearch ( $rows, 'missing', $options );
$ok = $ok && padSearchInfo () ['total'] === 0 && padSearchInfo () ['facets'] ['kind'] === [];
$answer = $ok ? 'current metadata controls filtering even when indexed text is unchanged' : 'FAIL';

?>
