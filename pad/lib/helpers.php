<?php

  // Value helpers: the small functions a page's PHP reaches for again and again while it
  // weighs, defaults and guards the values it works with - what Laravel ships as blank(),
  // filled(), value(), transform(), tap(), retry(), rescue() and once(), as plain functions.
  //
  // padBlank          TRUE for a value that holds nothing: NULL, '' or only whitespace, an
  //                   empty array, an empty Countable - never for 0, '0' or FALSE
  // padFilled         the opposite of padBlank
  // padValue          a Closure called with the arguments, any other value answered as it is
  // padTransform      the callback's answer for a filled value, the default for a blank one
  // padTap            hands the value to a callback, then answers the value itself
  // padRetry          calls a callback until it stops throwing, at most so many times
  // padRescue         calls a callback, and answers a fallback when it throws
  // padOnce           runs a callback once per request for each place that calls it
  // padValueCallable  private: whether a callback can be called, reported when it cannot
  // padValueShow      private: a value as an error message names it
  //
  // PHP's own empty() calls 0 and '0' empty, which is wrong for a form field that holds a
  // zero, and it calls '  ' filled, which is wrong for one that holds a few spaces; padBlank
  // is the test a page means. A wrong argument - a callback that is no function, a number of
  // tries below one - is reported with padError, naming the function, and the function then
  // answers NULL.

  // Blank is what holds nothing a page would show or store: NULL, a string of nothing but
  // whitespace (the unicode spaces too - a non-breaking space pasted into a form), an empty
  // array, and a Countable or Stringable object that is empty. A number, a boolean and every
  // other object are values, whatever they hold: 0 and FALSE are answers, not absences.

  function padBlank ( $value ) {

    if ( $value === NULL )
      return TRUE;

    if ( is_array ( $value ) )
      return $value === [];

    if ( $value instanceof Countable )
      return count ( $value ) === 0;

    if ( $value instanceof Stringable )
      $value = (string) $value;

    if ( ! is_string ( $value ) )
      return FALSE;

    // The unicode test fails - FALSE, not 0 - on text that is no valid UTF-8; the plain
    // trim judges that one.

    $blank = preg_match ( '/^[\s\p{Z}]*$/u', $value );

    return ( $blank === FALSE ) ? trim ( $value ) === '' : $blank === 1;

  }

  function padFilled ( $value ) {

    return ! padBlank ( $value );

  }

  // A setting that may be a value or the way to make one: a Closure is called with the
  // arguments and its answer is the value, anything else is the value already. Only a
  // Closure counts - a string such as 'date' is a value, not the name of a function to call.

  function padValue ( $value, ...$args ) {

    return ( $value instanceof Closure ) ? $value ( ...$args ) : $value;

  }

  // The callback's answer for a filled value - padTransform ( $name, fn ( $n ) => ucfirst ( $n ) )
  // - and the default for a blank one, so the callback never has to test for nothing. A
  // Closure default is called with the blank value and its answer used.

  function padTransform ( $value, $callback, $default = NULL ) {

    if ( ! padValueCallable ( 'padTransform', $callback ) )
      return NULL;

    if ( padFilled ( $value ) )
      return $callback ( $value );

    return padValue ( $default, $value );

  }

  // Hands the value to the callback and answers the value itself, whatever the callback
  // answers: a value can be logged, checked or changed in passing - an object the callback
  // changes is the changed object - in the middle of an expression.

  function padTap ( $value, $callback ) {

    if ( padValueCallable ( 'padTap', $callback ) )
      $callback ( $value );

    return $value;

  }

  // Calls the callback, with the number of the attempt (1, 2 ...), until it answers without
  // throwing - at most $times times - and answers what it answered: a remote service that
  // fails now and then gets a second chance. Between two attempts it waits
  // $sleepMilliseconds: one number for every wait, or a list with a wait per attempt, whose
  // last one repeats when there are more attempts than waits. $when, given the Throwable,
  // decides whether another attempt is worth it; when it says no, or after the last attempt,
  // the Throwable is thrown on to the caller.

  function padRetry ( $times, $callback, $sleepMilliseconds = 0, $when = NULL ) {

    // The attempts are counted as the number given, not cast to an int first: INF is no
    // limit, as Laravel's retry counts down from it for ever, where the cast raised PHP's
    // "not representable as an int" warning - for NAN too, which is no number of attempts.

    if ( ! is_numeric ( $times ) or is_nan ( (float) $times ) or $times < 1 ) {
      padError ( "padRetry: the number of attempts must be 1 or more, not " . padValueShow ( $times ) );
      return NULL;
    }

    if ( ! padValueCallable ( 'padRetry', $callback ) )
      return NULL;

    if ( $when !== NULL and ! padValueCallable ( 'padRetry', $when, 'the $when callback' ) )
      return NULL;

    $waits = is_array ( $sleepMilliseconds ) ? array_values ( $sleepMilliseconds ) : [ $sleepMilliseconds ];

    // A wait is a finite number: INF and NAN were cast to an int for usleep, which PHP 8.5
    // answers with "the float INF is not representable as an int".

    // And one usleep can make: a wait whose microseconds lie past every whole number PHP has
    // - 1e300 milliseconds - raised the same warning, being finite.

    foreach ( $waits as $wait )
      if ( ! is_numeric ( $wait ) or ! is_finite ( (float) $wait ) or $wait < 0 ) {
        padError ( "padRetry: a wait of " . padValueShow ( $wait ) . " milliseconds is not a finite number of 0 or more" );
        return NULL;
      } elseif ( round ( $wait * 1000 ) >= PHP_INT_MAX ) {
        padError ( "padRetry: a wait of " . padValueShow ( $wait ) . " milliseconds is too long to wait" );
        return NULL;
      }

    $times = floor ( (float) $times );

    for ( $attempt = 1 ; ; $attempt++ ) {

      try {

        return $callback ( $attempt );

      } catch ( Throwable $error ) {

        if ( $attempt >= $times or ( $when !== NULL and ! $when ( $error ) ) )
          throw $error;

      }

      $wait = $waits ? ( $waits [$attempt - 1] ?? $waits [count ( $waits ) - 1] ) : 0;

      if ( $wait > 0 )
        usleep ( (int) round ( $wait * 1000 ) );

    }

  }

  // Calls the callback and answers what it answers; when it throws - an Exception or an
  // Error - the answer is the rescue value, a Closure given the Throwable called for it. A
  // part of a page that may fail - a feed, a widget - fails alone, and the page goes on.
  // With $report the failure is written to PHP's error log, so a rescued fault is not lost.

  function padRescue ( $callback, $rescue = NULL, $report = TRUE ) {

    if ( ! padValueCallable ( 'padRescue', $callback ) )
      return NULL;

    try {

      return $callback ();

    } catch ( Throwable $error ) {

      if ( $report )
        error_log ( 'padRescue: ' . get_class ( $error ) . ': ' . $error->getMessage ()
                  . ' in ' . $error->getFile () . ':' . $error->getLine () );

      return padValue ( $rescue, $error );

    }

  }

  // Runs the callback the first time this spot - the file and line that call padOnce - is
  // reached in a request, and answers that first result every later time: work in a
  // function that a page calls in a loop, or that several parts of a page call, is done
  // once. Kept for the request only. A callback that throws has given no result, so the
  // next call runs it again. Two padOnce calls on one line share their result.

  function padOnce ( $callback ) {

    static $done = [];

    $site = '';

    foreach ( debug_backtrace ( DEBUG_BACKTRACE_IGNORE_ARGS, 2 ) as $frame )
      if ( isset ( $frame ['file'] ) ) {
        $site = $frame ['file'] . ':' . ( $frame ['line'] ?? 0 );
        break;
      }

    if ( array_key_exists ( $site, $done ) )
      return $done [$site];

    if ( ! padValueCallable ( 'padOnce', $callback ) )
      return NULL;

    return $done [$site] = $callback ();

  }

  // Whether a callback can be called; when it cannot, the author is told which function was
  // handed what - a misspelled function name shows itself.

  function padValueCallable ( $function, $callback, $what = 'the callback' ) {

    if ( is_callable ( $callback ) )
      return TRUE;

    padError ( "$function: $what " . padValueShow ( $callback ) . " is not a function or a Closure" );

    return FALSE;

  }

  // A value as an error message names it: a number as it is written - numeric text too, a
  // pipe's parameters are text - other text quoted and cut short, the rest by its kind.

  function padValueShow ( $value ) {

    if ( is_array  ( $value ) ) return 'an array';
    if ( is_object ( $value ) ) return 'an object of class ' . get_class ( $value );
    if ( is_bool   ( $value ) ) return $value ? 'TRUE' : 'FALSE';
    if ( $value === NULL      ) return 'NULL';
    if ( is_float  ( $value ) ) return var_export ( $value, TRUE );
    if ( is_int    ( $value ) ) return (string) $value;

    if ( is_string ( $value ) and is_numeric ( $value ) )
      return padMakeSafe ( $value, 60 );

    return "'" . padMakeSafe ( (string) $value, 60 ) . "'";

  }

?>
