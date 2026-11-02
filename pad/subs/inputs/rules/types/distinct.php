<?php

  // distinct - no value is in a list twice (a field named guests[]): the addresses of the
  // people invited, each once. Items compare as written, case and all, each trimmed - as in:
  // reads them - and an item left empty is no value, so three fields of guests[] with two of
  // them blank pass; an item that is no text, a list in the list, fails it. A field of one
  // value holds one value, which is in it once.

  return [
    'check'   => fn ( $value ) => TRUE,
    'list'    => function ( $list ) {
      $seen = [];
      foreach ( $list as $one ) {
        if ( ! is_string ( $one ) and ! is_int ( $one ) and ! is_float ( $one ) )
          return FALSE;
        if ( ( $one = trim ( (string) $one ) ) === '' )
          continue;
        if ( isset ( $seen [$one] ) )
          return FALSE;
        $seen [$one] = TRUE;
      }
      return TRUE;
    },
    'message' => ':label must not hold the same value twice'
  ];

?>
