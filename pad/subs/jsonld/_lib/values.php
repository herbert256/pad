<?php

  // The ways of writing a value the kinds after the first eight take - a recipe's times and
  // ingredients, a business's opening hours, a job's salary - included by padJsonLdValue
  // (jsonld.php) the first time a field asks for one. A field's how is a word, or the word
  // and what it needs beside it - [ 'word', [ 'Bakery', 'Store' ] ]:
  //
  //   number        a number with a dot - 4.5, -73.98 - written as a JSON number; with
  //                 [ 'number', [ low, high ] ] it must lie between the two
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
  //   word          one word of the list beside it, in any case or spacing - bar or pub is
  //                 BarOrPub - written as the list writes it; a word that is none is
  //                 reported with the list, or with the word that comes nearest when the
  //                 list is long
  //   words         one or more of them: a list, or a text with a comma between them -
  //                 one written as a text, more as a list
  //   flag          yes or no - TRUE, 1, yes, true, on, or FALSE, 0, no, false, off -
  //                 and yes writes the word beside it: [ 'flag', 'TELECOMMUTE' ]
  //   names         one or more names, each an object of the type beside it, by its name:
  //                 [ 'names', 'Country' ] writes NL as { "@type": "Country", "name": "NL" }
  //   hours         opening hours as Schema.org's openingHours writes them - Mo-Fr
  //                 09:00-17:30; Sa 10:00-16:00 - written as OpeningHoursSpecification
  //                 objects, Google's form (padJsonLdHours)
  //   salary        an amount or a range of two - 4200, 40000-55000 - as a
  //                 QuantitativeValue of value, or of minValue and maxValue
  //
  // Beside them a kind's file may say, in 'objects', the whole object a path starts with
  // where its @type alone is not enough - a video's views are an InteractionCounter whose
  // interactionType is the WatchAction - and in 'needs' a list of the fields a field is no
  // use without: a job's salary needs its currency and its period. A field whose path ends
  // in @type is the type itself, one of its words: a Bakery for the business kind.

  // The days of the week by Schema.org's name, in order (padJsonLdHours).

  const padJsonLdDays = [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ];

  function padJsonLdMore ( $value, $how, $name, $what, $report ) {

    [ $how, $with ] = [ $how [0], $how [1] ?? NULL ];

    $no = function ( $what2, $hint = '' ) use ( $name, $what, $report, $value ) {
      $report ( "the $name of $what is no $what2: '" . padMakeSafe ( is_array ( $value ) ? implode ( ', ', array_filter ( $value, 'is_scalar' ) ) : padJsonLdScalar ( $value ), 40 ) . "'" . ( $hint !== '' ? " - $hint" : '' ) );
      return NULL;
    };

    if ( in_array ( $how, [ 'texts', 'names' ], TRUE ) ) {
      $list = padJsonLdList ( $value, ( $how == 'texts' ) ? "/\R/" : '/\s*,\s*/' );
      if ( array_filter ( $list, 'is_array' ) )
        $report ( "the $name of $what holds a row where a text is meant" );
      $list = array_values ( array_filter ( $list, 'is_string' ) );
    }

    switch ( $how ) {

      case 'downloads':
        include_once PAD . 'subs/jsonld/_lib/downloads.php';
        return padJsonLdDownloads ( $value, $name, $what, $report );

      case 'texts':
        return $list ?: NULL;

      case 'steps':
        return padJsonLdSteps ( $value, $name, $what, $report );

      case 'hours':
        return padJsonLdHours ( $value, $name, $what, $report );

      case 'words':
        $done = [];
        foreach ( padJsonLdList ( $value, '/\s*,\s*/' ) as $one ) {
          $word = padJsonLdMore ( $one, [ 'word', $with ], $name, $what, $report );
          if ( $word !== NULL and ! in_array ( $word, $done, TRUE ) )
            $done [] = $word;
        }
        return ( count ( $done ) > 1 ) ? $done : ( $done [0] ?? NULL );

      case 'names':
        $done = array_map ( fn ( $one ) => [ '@type' => $with, 'name' => $one ], $list );
        return ( count ( $done ) > 1 ) ? $done : ( $done [0] ?? NULL );

    }

    if ( is_array ( $value ) ) {
      $report ( "the $name of $what is a list, not a value" );
      return NULL;
    }

    if ( $how == 'flag' ) {
      if ( $value === TRUE )
        return $with;
      $flag = strtolower ( trim ( (string) padJsonLdScalar ( $value ) ) );
      if ( in_array ( $flag, [ '1', 'yes', 'true', 'on' ], TRUE ) )
        return $with;
      if ( in_array ( $flag, [ '0', 'no', 'false', 'off' ], TRUE ) )
        return NULL;
      return $no ( 'yes or no', 'yes writes ' . $with );
    }

    if ( is_float ( $value ) and is_finite ( $value ) )
      $value = rtrim ( rtrim ( number_format ( $value, 10, '.', '' ), '0' ), '.' );

    $text = trim ( (string) padJsonLdScalar ( $value ) );

    switch ( $how ) {

      case 'isbn':
        $isbn = str_replace ( [ ' ', '-' ], '', $text );
        $rule = include PAD . 'subs/inputs/rules/types/isbn.php';
        if ( $rule ['check'] ( $isbn ) ) return $isbn;
        return $no ( 'ISBN-10 or ISBN-13 with a valid checksum' );

      case 'number':
        if ( ! preg_match ( '/^-?[0-9]+(\.[0-9]+)?$/D', $text ) )
          return $no ( 'number', 'write 4.5, with a dot' );
        if ( ! is_finite ( $text + 0 ) )
          return $no ( 'number', 'it is too large to be written' );
        if ( $with and ( $text + 0 < $with [0] or $text + 0 > $with [1] ) ) {
          $report ( "the $name of $what is not between {$with [0]} and {$with [1]}: '" . padMakeSafe ( $text, 40 ) . "'" );
          return NULL;
        }
        return $text + 0;

      case 'count':
        if ( ! preg_match ( '/^[0-9]+$/D', $text ) )
          return $no ( 'whole number', 'write 1523, 0 or more' );
        return is_finite ( $text + 0 ) ? $text + 0 : $no ( 'whole number', 'it is too large to be written' );

      case 'duration':
        $duration = padJsonLdDuration ( $text );
        return ( $duration === FALSE ) ? $no ( 'duration', 'write PT1H30M, 90 min or 1 h 30 min' ) : $duration;

      case 'energy':
        return preg_match ( '/^[0-9]+(\.[0-9]+)?$/D', $text ) ? "$text calories" : $text;

      case 'word':
        $word = padJsonLdWord ( $text, $with );
        if ( $word !== NULL )
          return $word;
        if ( count ( $with ) <= 25 )
          $report ( "the $name of $what is none of " . implode ( ', ', $with ) . ": '" . padMakeSafe ( $text, 40 ) . "'" );
        else
          $report ( "the $name of $what is none of the " . count ( $with ) . " it takes: '" . padMakeSafe ( $text, 40 ) . "' - "
                    . padJsonLdNearest ( $text, $with ) . ' comes nearest' );
        return NULL;

      case 'salary':
        if ( ! preg_match ( '/^([0-9]+(?:\.[0-9]+)?)(?:\s*-\s*([0-9]+(?:\.[0-9]+)?))?$/D', $text, $m ) )
          return $no ( 'amount', 'write 4200 or 40000-55000, with a dot' );
        if ( ! is_finite ( $m [1] + 0 ) or ! is_finite ( ( $m [2] ?? 0 ) + 0 ) )
          return $no ( 'amount', 'it is too large to be written' );
        if ( ! isset ( $m [2] ) )
          return [ '@type' => 'QuantitativeValue', 'value' => $m [1] + 0 ];
        if ( $m [1] + 0 > $m [2] + 0 )
          return $no ( 'range from low to high', "write {$m [2]}-{$m [1]}" );
        return [ '@type' => 'QuantitativeValue', 'minValue' => $m [1] + 0, 'maxValue' => $m [2] + 0 ];

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

  // A word of a list, in any case and spacing - bar or pub, Bar-or-Pub and BarOrPub are the
  // same word - and with the address of Schema.org before it as well; NULL for none.

  function padJsonLdWord ( $text, $words ) {

    $key = fn ( $one ) => strtolower ( preg_replace ( '/[^A-Za-z0-9]/', '', $one ) );

    $find = $key ( preg_replace ( '#^https?://schema\.org/#i', '', $text ) );

    foreach ( $words as $word )
      if ( $find !== '' and $key ( $word ) === $find )
        return $word;

    return NULL;

  }

  // The word of a long list that comes nearest to one that is none of them.

  function padJsonLdNearest ( $text, $words ) {

    $text = strtolower ( substr ( $text, 0, 60 ) );
    $best = $words [0];
    $far  = PHP_INT_MAX;

    foreach ( $words as $word ) {
      $distance = levenshtein ( $text, strtolower ( $word ) );
      if ( $distance < $far ) {
        $far  = $distance;
        $best = $word;
      }
    }

    return $best;

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

    // More seconds than a whole number holds - 99999999999999999999 days - are no duration
    // either: the cast to one ended the page.

    if ( ! is_finite ( $seconds ) or $seconds >= PHP_INT_MAX )
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

  // Opening hours as Schema.org's openingHours text writes them, made the
  // OpeningHoursSpecification objects Google reads: a group is days and one or more times
  // - Mo-Fr 09:00-17:30, Mo,We 09:00-12:00, Sa 10:00-12:00,13:00-16:00 - and groups stand
  // after each other, with a ; or a , or a line between them, or as the values of a list. A
  // day is Mo, Mon or Monday, a range of days may pass Sunday (Fr-Mo), and a time is hh:mm
  // of a day - 00:00-23:59 for a whole day; closes before opens is open past midnight.
  // Each time of a group is an object of its own with the group's days - one day as a text,
  // more as a list, as Google's examples write them - and opens and closes as hh:mm:ss, the
  // form its list of the properties names.

  function padJsonLdHours ( $value, $name, $what, $report ) {

    $day   = '(?:mon(?:day)?|tue(?:sday)?|wed(?:nesday)?|thu(?:rsday)?|fri(?:day)?|sat(?:urday)?|sun(?:day)?|mo|tu|we|th|fr|sa|su)';
    $days  = "$day(?:\s*-\s*$day)?(?:\s*,\s*$day(?:\s*-\s*$day)?)*";
    $time  = '[0-9]{1,2}:[0-9]{2}\s*-\s*[0-9]{1,2}:[0-9]{2}';
    $group = "/\G[\s,;]*($days)\s+($time(?:\s*,\s*$time)*)/i";

    $specs = [];
    $bad   = FALSE;

    foreach ( padJsonLdList ( $value, '/\R/' ) as $text ) {

      if ( is_array ( $text ) ) {
        $bad = TRUE;
        continue;
      }

      $at = 0;

      while ( preg_match ( $group, $text, $m, 0, $at ) ) {

        $at = strlen ( $m [0] ) + $at;

        $list = [];

        foreach ( preg_split ( '/\s*,\s*/', $m [1] ) as $range ) {
          $ends  = array_map ( fn ( $one ) => padJsonLdDay ( $one ), preg_split ( '/\s*-\s*/', $range ) );
          $first = $ends [0];
          $last  = $ends [1] ?? $first;
          for ( $one = $first ; ; $one = ( $one + 1 ) % 7 ) {
            if ( ! in_array ( padJsonLdDays [$one], $list, TRUE ) )
              $list [] = padJsonLdDays [$one];
            if ( $one == $last )
              break;
          }
        }

        foreach ( preg_split ( '/\s*,\s*/', $m [2] ) as $times ) {
          [ $opens, $closes ] = array_map ( 'trim', explode ( '-', $times ) );
          foreach ( [ &$opens, &$closes ] as &$clock ) {
            [ $hour, $minute ] = explode ( ':', $clock );
            if ( $hour > 23 or $minute > 59 )
              $bad = TRUE;
            $clock = sprintf ( '%02d:%02d:00', $hour, $minute );
          }
          unset ( $clock );
          $specs [] = [ '@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ( count ( $list ) == 1 ) ? $list [0] : $list,
                        'opens' => $opens, 'closes' => $closes ];
        }

      }

      if ( trim ( substr ( $text, $at ), " \t\n\r,;" ) !== '' or $at == 0 )
        $bad = TRUE;

    }

    if ( $bad ) {
      $report ( "the $name of $what are no opening hours: '" . padMakeSafe ( is_array ( $value ) ? implode ( '; ', array_filter ( $value, 'is_scalar' ) ) : padJsonLdScalar ( $value ), 60 )
                . "' - write Mo-Fr 09:00-17:30; Sa 10:00-16:00, a day Mo, Mon or Monday, a time 00:00 to 23:59" );
      return NULL;
    }

    return $specs ?: NULL;

  }

  // The number of a day of the week, Monday 0, from the names an opening hours text uses.

  function padJsonLdDay ( $name ) {

    return array_search ( strtolower ( substr ( trim ( $name ), 0, 2 ) ), [ 'mo', 'tu', 'we', 'th', 'fr', 'sa', 'su' ], TRUE );

  }

?>
