<?php


  function pad_get_parms ( $type, $parms ) {

    foreach ( $parms as $field => $value )
      if ( (!isset($GLOBALS[$field])) )
        $GLOBALS [$field] = pad_get_parms_2 ( $type, $value );

  }


  function pad_get_parms_2 ( $type, $field ) {

    if ( is_array ( $field ) )
      foreach ( $field as $key => $value )
        $field [$key] = pad_get_parms_2 ( $type, $value );
    else
      $field = trim ($field);

    return $field;

  }

?>