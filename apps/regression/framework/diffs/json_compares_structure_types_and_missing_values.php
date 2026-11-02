<?php
  include_once PAD . 'subs/diffs/_lib/structured.php';
  $old = padDiffValue ( '{"b":true,"a":1,"nil":null,"items":[0,"old"],"a/b~c":3}' );
  $new = padDiffValue ( '{"a":"1","b":true,"items":[0,"new"],"a/b~c":4,"added":null}' );
  $changes = padDiffJsonChanges ( $old, $new );
  $answer = array_column ( $changes, 'path' ) === [ '/a', '/nil', '/items/1', '/a~1b~0c', '/added' ]
    && array_column ( $changes, 'kind' ) === [ 'changed', 'removed', 'changed', 'changed', 'added' ]
    && $changes [1] ['had'] && ! $changes [1] ['has'] && $changes [1] ['before'] === NULL
    && ! $changes [4] ['had'] && $changes [4] ['has'] && $changes [4] ['after'] === NULL
    ? 'typed values, array positions, pointer escapes and null presence' : 'FAIL';
?>
