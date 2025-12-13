<?php


  /**
   * Normalizes input data into PAD's standard array format.
   *
   * Converts various input types (null, bool, array, object,
   * string) into the nested array structure expected by PAD
   * for data iteration.
   *
   * @param mixed  $input The input data to normalize.
   * @param string $type  Content type hint (auto-detected if empty).
   * @param string $name  Field name for wrapping simple arrays.
   *
   * @return array Normalized data array for PAD processing.
   */
  function padData ( $input, $type='', $name='' ) {

    if     ( $input === NULL           ) $data = [];
    elseif ( $input === FALSE          ) $data = [];
    elseif ( is_float($input) && is_nan($input) ) $data = [];
    elseif ( $input === INF            ) $data = [];
    elseif ( $input === TRUE           ) $data = [ 1 => [] ];
    elseif ( is_array ( $input)        ) $data = $input;
    elseif ( is_object ( $input)       ) $data = padToArray ( $input );
    elseif ( is_resource ( $input)     ) $data = padToArray ( $input );
    elseif ( ! $input                  ) $data = [];
    elseif ( strlen(trim($input)) == 0 ) $data = [];
    else                                 $data = trim ( $input );

    if ( ! is_array ( $data ) ) {    
      if ( ! $type )
        $type = padContentType ( $data );
      $data = include PAD . "data/$type.php";
    }
    
    if ( isset ( $GLOBALS ['padDataSetRecord'] ) and $GLOBALS ['padDataSetRecord'] ) {
      $data = padDataChkCheckRecord ($data,$name); 
      $GLOBALS ['padDataSetRecord'] = FALSE;
    }

    $data = padDataChkSimpleArray ($data,$name);
    $data = padDataChkChkOne      ($data,$name);   
    $data = padDataChkDataAttr    ($data,$name);
    $data = padDataChkCheckRecord ($data,$name); 
    $data = padDataChkCheckArray  ($data,$name);

    return $data;

  }
  

  /**
   * Wraps simple (non-nested) arrays into standard data format.
   *
   * If array contains no nested arrays, wraps each value in a
   * sub-array with the field name as key.
   *
   * @param array  $data The data array to check.
   * @param string $name The field name to use for wrapping.
   *
   * @return array The processed data array.
   */
  function padDataChkSimpleArray ($data,$name) {

    $result = $data;

    foreach ($result as $padK => $padV)
      if ( is_array($padV) )
        return $result;
  
    $name   = padDataName($name);
    $tmp    = $result;
    $result = [];
    
    foreach ($tmp as $k => $v)
      $result [$k] [$name] = $v;

    return $result;

  }
  

  /**
   * Converts sequential numeric arrays to nested data format.
   *
   * If inner arrays are sequential numeric (0,1,2...) with scalar
   * values, converts them to named sub-arrays.
   *
   * @param array  $data The data array to check.
   * @param string $name The field name to use for nesting.
   *
   * @return array The processed data array.
   */
  function padDataChkCheckArray ($data,$name) {

    $result = $data;

    foreach ($result as $k => $v) {
      $x = 0 ;
      foreach ($v as $k2 => $v2)
        if ( ctype_digit( (string) $k2) and ( $k2 == $x or ($k2-1==$x) ) and ! is_array($v2) )
          $x++;
        else
          return $result;
    }

    $name = padDataName($name);

    foreach ($result as $k => $v) {
      $tmp = $v;
      $result [$k] = [];
      foreach ($tmp as $k2 => $v2)
        $result [$k] ["$name"] [$k2] ["$name"] = $v2;
    }

    return $result;

  }


  /**
   * Wraps a single record into array format.
   *
   * If data contains scalar values at top level (single record),
   * wraps it in an outer array with index 0.
   *
   * @param array  $data The data array to check.
   * @param string $name The field name (unused).
   *
   * @return array The processed data array.
   */
  function padDataChkCheckRecord ($data,$name) {

    $result = $data;

    foreach ($result as $k => $v)
      if ( ! is_array($v) ) {
        $tmp = $result;
        $result = [];
        foreach ($tmp as $k => $v)
          $result [0] [$k] = $v;
        return $result;
      }

    return $result;

  }


  /**
   * Unwraps single-element arrays with sequential inner keys.
   *
   * If data has single element containing sequential numeric
   * array, promotes inner array to top level.
   *
   * @param array  $data The data array to check.
   * @param string $name The field name (unused).
   *
   * @return array The processed data array.
   */
  function padDataChkChkOne ($data,$name) {

    $result = $data;

    if ( count($result) == 1 and is_array($result[array_key_first($result)]) ) {

      $idx=0;
      foreach ($result[array_key_first($result)] as $key => $value) {
        if ( $key <> $idx ) {
          $idx = 0;
          break;
        }
        $idx++;
      }

      if ($idx) {
        $tmp = $result[array_key_first($result)];
        $result = $tmp;
      }

    }

    return $result;

  }


  /**
   * Promotes 'attr' sub-arrays to parent level.
   *
   * Recursively finds arrays with 'attr' key and merges their
   * contents up to parent level (XML attribute handling).
   *
   * @param array  $data The data array to process.
   * @param string $name The field name (unused).
   *
   * @return array The processed data array.
   */
  function padDataChkDataAttr ($data,$name) {

    $result = $data;

    foreach ($result as $k => $v)
      if ( is_array($v) )
        if (trim($k) == 'attr') {
          foreach ($v as $k2 => $v2)
            $result [$k2] = $v2;
          unset ($result [$k]);
        } else
          $result [$k] = padDataChkDataAttr ( $result [$k], $name );

    return $result;

  }


  /**
   * Resolves the field name to use for data operations.
   *
   * Checks explicit name, tag parameters, forced name, or
   * falls back to tag name. Strips leading $ if present.
   *
   * @param string $name Explicit name to use if provided.
   *
   * @return string The resolved field name.
   *
   * @global int   $pad    Current processing level.
   * @global array $padPrm Tag parameters per level.
   * @global array $padTag Tag names per level.
   */
  function padDataName ($name) {

    global $pad, $padPrm, $padTag, $padName, $padForceDataName;

    if     ( $name                              ) $return = $name;
    elseif ( isset ($padPrm [$pad] ['name'] )   ) $return = $padPrm [$pad] ['name'];
    elseif ( $padForceDataName                  ) $return = $padForceDataName;
    elseif ( isset ($padPrm [$pad] ['toData'] ) ) $return = $padPrm [$pad] ['toData'];
    elseif ( $padTag [$pad] == 'data '          ) $return = $padParm;
    elseif ( isset ($padTag [$pad] )            ) $return = $padTag [$pad];

    if ( substr($return, 0, 1) == '$' )
      $return = substr($return, 1);

    return $return;

  }
  

?>