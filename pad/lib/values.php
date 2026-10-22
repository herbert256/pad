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

  // INF, -INF and NAN are written null, as padJsonFinite writes them for the page's JSON
  // answer: json_encode's partial output wrote them 0, so a file padFilePut wrote, a dump, a
  // trace line or an error message showed a number the value never was. The data is walked
  // only when json_encode met a value it could not write, so the common call costs no more.

  function padJson ( $data ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $flags  = JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
      $return = json_encode ( $data, $flags );

      if ( json_last_error () !== JSON_ERROR_NONE )
        $return = json_encode ( padJsonFinite ( $data ), $flags );

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

  // An object is walked as json_encode writes it, or one inside the data - a json_decode row,
  // a PDO FETCH_OBJ row, (object) [...], what an API client hands a page - still had its INF
  // written 0: one that serializes itself (JsonSerializable) by what jsonSerialize answers,
  // any other by its public properties, which get_object_vars answers out here as json_encode
  // sees them. Such an object is answered as a plain object of those properties only when a
  // value in it changed - JSON writes them the same - and is otherwise left as it is.
  //
  // An object met again inside itself is null, as json_encode writes a recursion: padJson
  // hands this any value it is given - an error message's, a dump's - and an object graph
  // whose children point back at their parent was walked down every path to the depth bound,
  // twice as many walks for every level of a tree with two children.

  function padJsonFinite ( $value, $depth = 0, $path = [] ) {

    if ( is_float ( $value ) and ! is_finite ( $value ) )
      return NULL;

    if ( $depth >= 512 )
      return $value;

    if ( is_object ( $value ) ) {

      if ( isset ( $path [ spl_object_id ( $value ) ] ) )
        return NULL;

      $path [ spl_object_id ( $value ) ] = TRUE;

      // A jsonSerialize that answers the object itself has it written by its properties, as
      // json_encode does.

      if ( $value instanceof JsonSerializable and ( $serial = $value->jsonSerialize () ) !== $value )
        return padJsonFinite ( $serial, $depth + 1, $path );

    }

    $object = is_object ( $value );

    if ( $object )
      $walk = get_object_vars ( $value );
    elseif ( is_array ( $value ) )
      $walk = $value;
    else
      return $value;

    $changed = FALSE;

    foreach ( $walk as $key => $one )
      if ( is_array ( $one ) or is_float ( $one ) or is_object ( $one ) ) {
        $new = padJsonFinite ( $one, $depth + 1, $path );
        if ( $new !== $one ) {
          $walk [$key] = $new;
          $changed     = TRUE;
        }
      }

    if ( ! $object )
      return $walk;

    return $changed ? (object) $walk : $value;

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
