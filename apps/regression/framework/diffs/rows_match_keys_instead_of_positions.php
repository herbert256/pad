<?php
  include_once PAD . 'subs/diffs/_lib/structured.php';
  $before = padDiffValue ( '[{"id":1,"name":"Ada"},{"id":2,"name":"Bo"},{"id":4,"name":"Dee"}]' );
  $after = padDiffValue ( '[{"name":"Dee","id":4},{"id":2,"name":"Bob"},{"id":3,"name":"Cal"}]' );
  $changes = padDiffRowChanges ( $before, $after, 'id' );
  $answer = array_column ( $changes, 'path' ) === [ '/1', '/2/name', '/3' ]
    && array_column ( $changes, 'kind' ) === [ 'removed', 'changed', 'added' ] ? 'reordered records match keys and only changed fields are listed' : 'FAIL';
?>
