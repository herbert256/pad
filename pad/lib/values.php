<?php


  /**
   * Returns constant value if defined, otherwise the input string.
   *
   * @param string $parm Constant name or literal value.
   *
   * @return mixed Constant value or original string.
   */
  function padConstant ( $parm ) {

    if ( defined ( $parm ) )
      return constant ( $parm );
    else
      return $parm;

  }


  /**
   * Checks if value is a simple scalar (not array/object/special).
   *
   * @param mixed $value Value to check.
   *
   * @return bool TRUE if simple scalar value.
   */
  function padSingleValue ( $value ) {

    if ( is_array        ( $value ) or
         is_object       ( $value ) or
         is_resource     ( $value ) or
         padSpecialValue ( $value ) )

      return FALSE;

    return TRUE;

  }


  /**
   * Checks if value is a special type (TRUE/FALSE/NULL/INF/NAN).
   *
   * @param mixed $value Value to check.
   *
   * @return bool TRUE if special value.
   */
  function padSpecialValue ( $value ) {

    if     ( $value === FALSE ) return TRUE;
    elseif ( $value === TRUE  ) return TRUE;
    elseif ( $value === NULL  ) return TRUE;
    elseif ( $value === INF   ) return TRUE;
    elseif ( is_float($value) && is_nan($value) ) return TRUE;
    else                        return FALSE;

  }


  /**
   * Converts value to boolean flag.
   *
   * @param mixed $input Value to convert.
   *
   * @return bool Boolean result.
   */
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


  /**
   * Converts value to content string.
   *
   * @param mixed $input Value to convert.
   *
   * @return string Content string.
   */
  function padMakeContent ( $input ) {

    if     ( $input === NULL        )  return '';
    elseif ( $input === FALSE       )  return '';
    elseif ( $input === TRUE        )  return '1';
    else                               return $input;

  }


  /**
   * Safely converts value to array.
   *
   * @param mixed $xxx Value to convert.
   *
   * @return array Converted array or empty array on failure.
   */
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


  /**
   * Encodes data as JSON with pretty printing.
   *
   * @param mixed $data Data to encode.
   *
   * @return string JSON string or '{}' on error.
   */
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


  /**
   * Strips leading $ from field name.
   *
   * @param string $parm Field name possibly with $.
   *
   * @return string Field name without $.
   */
  function padFieldName ($parm) {

    return (substr($parm, 0, 1) == '$') ? substr($parm, 1) : $parm;

  }


  /**
   * Converts associative array to PAD name/value format.
   *
   * @param array $data Input array.
   *
   * @return array Array with name/value structure.
   */
  function padDataForcePad ($data) {

    $result = [];

    foreach ( $data as $name => $value) {
      $result [$name] ['name'] = $name;
      $result [$name] ['value'] = $value;
    }

    return $result;

  }


  /**
   * Returns default empty data structure.
   *
   * @return array Default data array.
   */
  function padDefaultData () {

    return [ 999 => [] ];

  }


  /**
   * Checks if data matches default empty structure.
   *
   * @param mixed $data Data to check.
   *
   * @return bool TRUE if default data.
   */
  function padIsDefaultData ( $data ) {

    if ( ! is_array ( $data ) ) return FALSE;
    if ( count ( $data ) <> 1 ) return FALSE;

    $key = array_key_first ( $data );

    if ( ! is_array ( $data [$key] ) ) return FALSE;
    if ( count ( $data [$key] )      ) return FALSE;

    return TRUE;

  }


?>
