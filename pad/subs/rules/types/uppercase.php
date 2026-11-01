<?php

  // uppercase - nothing that has an upper case is written in lower case: a country code, a
  // licence plate. A value without letters passes.

  return [
    'check'   => fn ( $value ) => $value === mb_strtoupper ( $value, 'UTF-8' ),
    'message' => ':label must be in upper case'
  ];

?>
