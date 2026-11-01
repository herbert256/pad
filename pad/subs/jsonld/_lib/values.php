<?php

  // The ways of writing a value the kinds after the first eight take - a recipe's times and
  // ingredients - included by padJsonLdValue (jsonld.php) the first time a field asks for
  // one. A field's how is a word, or the word and what it needs beside it:
  //
  //   number        a number with a dot - 4.5, -73.98 - written as a JSON number
  //   count         a whole number, 0 or more - 1523 - written as a JSON number
  //   duration      a duration of ISO 8601 as it is - PT1H30M, P1D - or one written in
  //                 words, each a number and its unit: 1 h 30 min, 90 minutes, 1.5 hours,
  //                 2 days, 45 s - written as ISO 8601, PT1H30M; m is minutes, and a number
  //                 alone has no unit and is none
  //   energy        an amount of energy as Schema.org writes it, 120 calories - a number
  //                 alone is that many calories
  //   texts         a list of texts: a list, or a text with a line between them - the
  //                 ingredients of a recipe, which a comma cannot divide (1 cup flour,
  //                 sifted)
  //   steps         the steps of a recipe, each a HowToStep: a list of texts, a text with a
  //                 line between them, or rows of text (required), name, url and image -
  //                 nothing else of a row

  function padJsonLdMore ( $value, $how, $name, $what, $report ) {

    [ $how, $with ] = [ $how [0], $how [1] ?? NULL ];

    $no = function ( $what2, $hint = '' ) use ( $name, $what, $report, $value ) {
      $report ( "the $name of $what is no $what2: '" . padMakeSafe ( is_array ( $value ) ? implode ( ', ', array_filter ( $value, 'is_scalar' ) ) : padJsonLdScalar ( $value ), 40 ) . "'" . ( $hint !== '' ? " - $hint" : '' ) );
      return NULL;
    };

    if ( in_array ( $how, [ 'texts' ], TRUE ) ) {
      $list = padJsonLdList ( $value, "/\R/" );
      if ( array_filter ( $list, 'is_array' ) )
        $report ( "the $name of $what holds a row where a text is meant" );
      $list = array_values ( array_filter ( $list, 'is_string' ) );
    }

    switch ( $how ) {

      case 'texts':
        return $list ?: NULL;

      case 'steps':
        return padJsonLdSteps ( $value, $name, $what, $report );

    }

    if ( is_array ( $value ) ) {
      $report ( "the $name of $what is a list, not a value" );
      return NULL;
    }

    if ( is_float ( $value ) and is_finite ( $value ) )
      $value = rtrim ( rtrim ( number_format ( $value, 10, '.', '' ), '0' ), '.' );

    $text = trim ( (string) padJsonLdScalar ( $value ) );

    switch ( $how ) {

      case 'number':
        if ( ! preg_match ( '/^-?[0-9]+(\.[0-9]+)?$/D', $text ) )
          return $no ( 'number', 'write 4.5, with a dot' );
        return $text + 0;

      case 'count':
        return preg_match ( '/^[0-9]+$/D', $text ) ? $text + 0 : $no ( 'whole number', 'write 1523, 0 or more' );

      case 'duration':
        $duration = padJsonLdDuration ( $text );
        return ( $duration === FALSE ) ? $no ( 'duration', 'write PT1H30M, 90 min or 1 h 30 min' ) : $duration;

      case 'energy':
        return preg_match ( '/^[0-9]+(\.[0-9]+)?$/D', $text ) ? "$text calories" : $text;

    }

    padError ( "the JSON-LD field $name has no way of writing named '" . padMakeSafe ( $how, 20 ) . "'" );

    return NULL;

  }

  // The values of a list: a list as it is, a text divided where the pattern says, each one
  // trimmed and an empty one left out. A value in the list that is no text - a row - is
  // kept for the caller, which says what it makes of it.

  function padJsonLdList ( $value, $split ) {

    if ( ! is_array ( $value ) )
      $value = preg_split ( $split, (string) padJsonLdScalar ( $value ) );

    $list = [];

    foreach ( $value as $one ) {
      if ( is_array ( $one ) ) {
        if ( ! padJsonLdEmpty ( $one ) )
          $list [] = $one;
        continue;
      }
      $one = trim ( (string) padJsonLdScalar ( $one ) );
      if ( $one !== '' )
        $list [] = $one;
    }

    return $list;

  }

  // A duration as ISO 8601: one of ISO 8601 as it is, upper case - P1DT2H, PT1M33S - or one
  // in words made one: the seconds of every number and its unit added up and written in
  // days, hours, minutes and seconds. FALSE for one that is neither.

  function padJsonLdDuration ( $text ) {

    if ( preg_match ( '/^P(?=[0-9]|T[0-9])([0-9]+Y)?([0-9]+M)?([0-9]+W)?([0-9]+D)?(T(?=[0-9])([0-9]+H)?([0-9]+M)?([0-9]+(\.[0-9]+)?S)?)?$/iD', $text ) )
      return strtoupper ( $text );

    $units   = [ 'd' => 86400, 'h' => 3600, 'm' => 60, 's' => 1 ];
    $seconds = 0;
    $found   = 0;

    $rest = preg_replace_callback ( '/([0-9]+(?:\.[0-9]+)?)\s*(days?|d|hours?|hrs?|h|minutes?|mins?|m|seconds?|secs?|s)(?![a-z])/i',
      function ( $m ) use ( $units, &$seconds, &$found ) {
        $seconds += $m [1] * $units [ strtolower ( $m [2] [0] ) ];
        $found++;
        return ' ';
      }, $text );

    if ( ! $found or trim ( preg_replace ( '/[\s,]+|\band\b/i', '', $rest ) ) !== '' )
      return FALSE;

    $seconds = (int) round ( $seconds );

    if ( $seconds == 0 )
      return 'PT0S';

    $days    = intdiv ( $seconds, 86400 );
    $hours   = intdiv ( $seconds % 86400, 3600 );
    $minutes = intdiv ( $seconds % 3600, 60 );
    $seconds = $seconds % 60;

    $time = ( $hours ? "{$hours}H" : '' ) . ( $minutes ? "{$minutes}M" : '' ) . ( $seconds ? "{$seconds}S" : '' );

    return 'P' . ( $days ? "{$days}D" : '' ) . ( $time !== '' ? "T$time" : '' );

  }

  // The steps of a recipe: each a HowToStep of its text, or of the text, name, url and image
  // of a row - its other fields never written. A row without a text is an error naming the
  // step, and is left out.

  function padJsonLdSteps ( $value, $name, $what, $report ) {

    $steps = [];

    foreach ( padJsonLdList ( $value, "/\R/" ) as $number => $one ) {

      if ( ! is_array ( $one ) ) {
        $steps [] = [ '@type' => 'HowToStep', 'text' => (string) $one ];
        continue;
      }

      $step = [ '@type' => 'HowToStep' ];

      foreach ( [ 'name' => 'text', 'text' => 'text', 'url' => 'url', 'image' => 'url' ] as $field => $how )
        if ( isset ( $one [$field] ) and is_scalar ( $one [$field] ) and trim ( (string) $one [$field] ) !== '' ) {
          $text = trim ( (string) $one [$field] );
          $text = ( $how == 'url' ) ? padJsonLdUrl ( $text ) : $text;
          if ( $text !== '' )
            $step [$field] = $text;
        }

      if ( ! isset ( $step ['text'] ) ) {
        $report ( "step " . ( $number + 1 ) . " of the $name of $what has no text - a step is a text, or a row with a field text" );
        continue;
      }

      $steps [] = $step;

    }

    return $steps ?: NULL;

  }

?>
