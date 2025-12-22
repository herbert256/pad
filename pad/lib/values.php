<?php


  function padArrayNumericValues ( &$parm ) {

    if ( ! is_array ( $parm ) )
      return;

    array_walk_recursive ( $parm, 
      function ( &$value ) {
        if ( is_numeric ( $value ) ) {
          $value = $value + 0; // Converts to int or float
        }
      });

  }


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
    elseif ( $value === INF   ) return TRUE;
    elseif ( is_float($value) && is_nan($value) ) return TRUE;
    else                        return FALSE;

  }

  function padMakeFlag ( $input ) {

    if     ( $input === NULL  )          return FALSE;
    elseif ( $input === FALSE )          return FALSE;
    elseif ( $input === TRUE  )          return TRUE;
    elseif ( strlen(trim($input)) == 0 ) return FALSE;

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

      $return = json_encode ( $data, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK );

    } catch (Throwable $e) {

      $return = '{}';

    }

    restore_error_handler ();

    return $return;

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
    if ( count ( $data ) <> 1 ) return FALSE;

    $key = array_key_first ( $data );

    if ( ! is_array ( $data [$key] ) ) return FALSE;
    if ( count ( $data [$key] )      ) return FALSE;

    return TRUE;

  }

?>