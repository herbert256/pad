<?php
  include_once PAD . 'subs/diffs/_lib/structured.php';
  $same = padDiffJsonChanges ( padDiffValue ( '{"a":1,"b":{"x":2,"y":3}}' ), padDiffValue ( '{"b":{"y":3,"x":2},"a":1}' ) );
  $different = padDiffJsonChanges ( padDiffValue ( '{}' ), padDiffValue ( '[]' ) );
  $leaves = padDiffJsonChanges ( padDiffValue ( '{"a":1,"b":{}}' ), padDiffValue ( '{"b":{},"a":1}' ), TRUE );
  $answer = $same === [] && count ( $different ) === 1 && $different [0] ['path'] === ''
    && array_column ( $leaves, 'kind' ) === [ 'same', 'same' ] ? 'member order ignored, empty types distinct and same leaves available' : 'FAIL';
?>
