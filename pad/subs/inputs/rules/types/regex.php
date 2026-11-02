<?php

  // regex:/pattern/flags - the value matches a PCRE pattern. A pattern PCRE cannot read is
  // the author's error, named before any value is judged: it failed every value with "is not
  // valid" and the browser let it pass. In the rules text a regex: runs to its closing
  // delimiter and its flags, so its alternation is no rule boundary (padValidateSplit).

  return [
    'check'    => fn ( $value, $arg ) => (bool) @preg_match ( $arg, $value ),
    'argument' => function ( $arg ) {

      $why = '';

      set_error_handler ( function ( $type, $message ) use ( &$why ) {
        $why = preg_replace ( '/^preg_match\(\):\s*/', '', $message );
        return TRUE;
      } );

      try {
        $read = preg_match ( (string) $arg, '' ) !== FALSE;
      } finally {
        restore_error_handler ();
      }

      return $read ? '' : 'no pattern - ' . ( $why !== '' ? $why : 'PCRE cannot read it' );

    },
    'message'  => ':label is not valid'
  ];

?>
