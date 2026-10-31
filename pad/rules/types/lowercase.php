<?php

  // lowercase - nothing that has a lower case is written in upper case: a user name, a code.
  // A value without letters passes.

  return [
    'check'   => fn ( $value ) => $value === mb_strtolower ( $value, 'UTF-8' ),
    'message' => ':label must be in lower case'
  ];

?>
