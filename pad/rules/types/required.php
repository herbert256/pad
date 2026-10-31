<?php

  // required - the field holds something: a text that is not empty once trimmed, a list
  // (a field named tags[]) with at least one item. It is one of the two rules an empty field
  // is judged by, the other being accepted.

  return [
    'empty'   => TRUE,
    'check'   => fn ( $value ) => $value !== '',
    'list'    => fn ( $list ) => count ( $list ) > 0,
    'message' => ':label is required'
  ];

?>
