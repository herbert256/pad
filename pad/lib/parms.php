<?php


  /**
   * Sets global variables from parameter array.
   *
   * Only sets variables that don't already exist and pass
   * variable name validation.
   *
   * @param string $type  The parameter type (for processing).
   * @param array  $parms Key-value pairs to set as globals.
   *
   * @return void
   */
  function padGetParms ( $type, $parms ) {

    foreach ( $parms as $field => $value )
      if ( (!isset($GLOBALS[$field])) )
        if ( padValidVar ($field) )
          $GLOBALS [$field] = padGetParms2 ( $type, $value );

  }


  /**
   * Recursively trims parameter values.
   *
   * For arrays, recursively processes each element.
   * For scalars, trims whitespace.
   *
   * @param string $type  The parameter type (unused).
   * @param mixed  $field The value to process.
   *
   * @return mixed Trimmed value or array of trimmed values.
   */
  function padGetParms2 ( $type, $field ) {

    if ( is_array ( $field ) )
      foreach ( $field as $key => $value )
        $field [$key] = padGetParms2 ( $type, $value );
    else
      $field = trim ($field);

    return $field;

  }


?>