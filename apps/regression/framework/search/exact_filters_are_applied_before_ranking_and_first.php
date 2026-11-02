<?php

$rows = [ [ 'id' => 1, 'title' => 'rare rare common', 'kind' => 'other' ], [ 'id' => 2, 'title' => 'rare common', 'kind' => 'guide' ], [ 'id' => 3, 'title' => 'common', 'kind' => 'guide' ] ];
$selected = padSearch ( $rows, 'rare common', [ 'name' => 'filter-before-ranking', 'fields' => 'title', 'filters' => [ 'kind' => 'guide' ], 'first' => 1 ] );
$info = padSearchInfo ();
$manual = padSearch ( array_slice ( $rows, 1 ), 'rare common', [ 'name' => 'filter-manual', 'fields' => 'title', 'first' => 1 ] );
$ok = count ( $selected ) === 1 && $selected [0] ['id'] === 2 && $selected [0] ['score'] === $manual [0] ['score'] && $info ['total'] === 2;
$answer = $ok ? 'exact filters precede ranking and result limits' : 'FAIL';

?>
