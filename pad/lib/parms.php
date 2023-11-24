<?php


  function padGetParms ( $type, $parms ) {

    foreach ( $parms as $field => $value )
      if ( (!isset($GLOBALS[$field])) )
        if ( padValidVar ($field) )
          $GLOBALS [$field] = padGetParms2 ( $type, $value );

  }


  function padGetParms2 ( $type, $field ) {

    if ( is_array ( $field ) )
      foreach ( $field as $key => $value )
        $field [$key] = padGetParms2 ( $type, $value );
    else
      $field = trim ($field);

    return $field;

  }


?>