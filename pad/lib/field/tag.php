<?php


  function pField_tag ($field) {

    if ( substr($field, 0, 1) == '#' ) {
      $temp  = pExplode ($field, '#', 2);
      $tag   = '';
      $field = $temp[0];
      $padarm  = $temp[1]??'';
    } else {
      $temp  = pExplode ($field, '#', 3);
      $tag   = $temp[0];
      $field = $temp[1];
      $padarm  = $temp[2]??'';
    }

    if ( ! $tag )
      $padIdx = pFieldFirstNonParm  ();
    else
      $padIdx = pFieldGetLevel ($tag);
    
    if ( file_exists ( PAD . "tag/".$field.".php" ) )
      return include PAD . "tag/$field.php";

    if ( in_array ( $padarm, ['name','value'] ) and $padIdx and isset($GLOBALS ['padCurrent'] ) ) {
      $pados = 1;
      foreach( $GLOBALS ['padCurrent'] [$padIdx] as $key => $value )
        if ( $pados++ == $field )
          return ( $padarm == 'name') ? $key : $value;
    }

    if ( $tag )
      return pFieldDoubleCheck ( $tag, ':', $field );

    return INF;

  }


  function pFieldFirstNonParm  () {

    global $pad, $padType;

    for ($i=$pad; $i; $i--)
      if ( $padType[$i] and $padType[$i] <> 'parm' )
        return $i;

    return $pad - 1;

  }  


?>