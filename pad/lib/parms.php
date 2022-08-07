<?php


  function pGet_parms ( $type, $parms ) {

    foreach ( $parms as $field => $value )
      if ( (!isset($GLOBALS[$field])) )
        $GLOBALS [$field] = pGet_parms_2 ( $type, $value );

  }


  function pGet_parms_2 ( $type, $field ) {

    if ( is_array ( $field ) )
      foreach ( $field as $key => $value )
        $field [$key] = pGet_parms_2 ( $type, $value );
    else
      $field = trim ($field);

    return $field;

  }

?>