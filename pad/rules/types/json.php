<?php

  // json - a JSON text PHP reads: a settings field, a snippet of data. What json_validate
  // takes - its nesting at most 511 deep, no half of a surrogate pair - and on a PHP without
  // json_validate what json_decode reads without an error.

  return [
    'check'   => function ( $value ) {
      if ( function_exists ( 'json_validate' ) )
        return json_validate ( $value );
      json_decode ( $value, TRUE );
      return json_last_error () === JSON_ERROR_NONE;
    },
    'message' => ':label must be valid JSON'
  ];

?>
