<?php

  // Value coercion - the rules by which any PHP value becomes a template truth value, a
  // piece of output, or an array.
  //
  // padSingleValue / padSpecialValue  is this a plain scalar that can simply be printed,
  //                    or one of FALSE, TRUE, NULL, NAN, which the engine reads as
  //                    meaning rather than as content - INF is a value, as -INF is: a tag
  //                    answering it, {echo $big} or {big} over $big = INF, printed nothing
  //                    where {$big} and {echo $neg} over -INF print the value
  // padMakeFlag        the truth test behind {if} and a tag's hit/miss: empty is false, an
  //                    array is true when it holds more than the default occurrence, and a
  //                    string is handed to padEval
  // padMakeContent     the output form; NULL and FALSE print as nothing, TRUE as 1
  // padToArray         casts an object or resource to an array with errors silenced
  // padJson            json_encode with the engine's flags, never throwing
  // padConstant        a defined constant's value, else the name unchanged
  // padFieldName       drops a leading $ from a field name
  // padDataForcePad    rewrites a name => value map into occurrences with name and value
  //                    fields, the shape {fields@record} iterates
  // padDefaultData / padIsDefaultData  the single empty occurrence a tag stands in with
  //                    when it has no data of its own, and the test for it

  function padConstant ( $parm ) {

    if ( defined ( $parm ) )
      return constant ( $parm );
    else
      return $parm;

  }

  function padSingleValue ( $value ) {

    if ( is_array        ( $value ) or
         is_object       ( $value ) or
         is_resource     ( $value ) or
         padSpecialValue ( $value ) )

      return FALSE;

    return TRUE;

  }

  function padSpecialValue ( $value ) {

    if     ( $value === FALSE ) return TRUE;
    elseif ( $value === TRUE  ) return TRUE;
    elseif ( $value === NULL  ) return TRUE;
    elseif ( is_float($value) && is_nan($value) ) return TRUE;
    else                        return FALSE;

  }

  function padMakeFlag ( $input ) {

    if     ( $input === NULL  )          return FALSE;
    elseif ( $input === FALSE )          return FALSE;
    elseif ( $input === TRUE  )          return TRUE;
    elseif ( is_scalar ( $input ) and strlen(trim($input)) == 0 ) return FALSE;

    if ( is_array ($input) or is_object ($input) or is_resource ($input) )  {

      $array = padToArray( $input );

      if ( padIsDefaultData ( $array )  )
        return FALSE;

      if ( count ( $array ) )
        return TRUE;
      else
        return FALSE;

    }

    if ( padEval($input) )
      return TRUE;
    else
      return FALSE;

  }

  function padMakeContent ( $input ) {

    if     ( $input === NULL        )  return '';
    elseif ( $input === FALSE       )  return '';
    elseif ( $input === TRUE        )  return '1';
    else                               return $input;

  }

  function padToArray ($xxx) {

    if ( is_array($xxx) )
      return ($xxx);

    set_error_handler ( function ($s, $m, $f, $l) { return TRUE; } );
    $error_level = error_reporting(0);

    $array = [];

    try {
      $array = (array) $xxx;
    }
    catch (Throwable $e) {
      $array = [];
    }

    if     ( $array === NULL     )  $array = [];
    elseif ( ! is_array ($array) )  $array = [];

    error_reporting($error_level);
    restore_error_handler();

    return $array;

  }

  function padJson ( $data ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $return = json_encode ( $data, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

    } catch (Throwable $e) {

      $return = '{}';

    }

    restore_error_handler ();

    return $return;

  }

  // JSON has no INF or NAN, and json_encode's partial output writes them 0 - a number the page
  // never held, which a client cannot tell from a real 0. They are written null, at any depth
  // of an array, as a browser's JSON.stringify writes Infinity and NaN: a page's JSON answer
  // ($padExpose) and {^field} gave 0 where its HTML and its CSV gave INF.

  function padJsonFinite ( $value, $depth = 0 ) {

    if ( is_float ( $value ) and ! is_finite ( $value ) )
      return NULL;

    if ( is_array ( $value ) and $depth < 512 )
      foreach ( $value as $key => $one )
        if ( is_array ( $one ) or is_float ( $one ) ) {
          $new = padJsonFinite ( $one, $depth + 1 );
          if ( $new !== $one )
            $value [$key] = $new;
        }

    return $value;

  }

  function padFieldName ($parm) {

    return (substr($parm, 0, 1) == '$') ? substr($parm, 1) : $parm;

  }

  function padDataForcePad ($data) {

    $result = [];

    foreach ( $data as $name => $value) {
      $result [$name] ['name'] = $name;
      $result [$name] ['value'] = $value;
    }

    return $result;

  }

  function padDefaultData () {

    return [ 999 => [] ];

  }

  function padIsDefaultData ( $data ) {

    if ( ! is_array ( $data ) ) return FALSE;
    if ( count ( $data ) != 1 ) return FALSE;

    $key = array_key_first ( $data );

    if ( ! is_array ( $data [$key] ) ) return FALSE;
    if ( count ( $data [$key] )      ) return FALSE;

    return TRUE;

  }

?>
