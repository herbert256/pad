<?php


  function padFieldTag ($field) {

    if ( substr($field, 0, 1) == '#' ) {
      $temp  = padExplode ($field, '#', 2);
      $tag   = '';
      $field = $temp[0];
      $parm  = $temp[1]??'';
    } else {
      $temp  = padExplode ($field, '#', 3);
      $tag   = $temp[0];
      $field = $temp[1]??'1';
      $parm  = $temp[2]??'value';
    }

    if ( ! $tag )
      $padIdx = padFieldFirstNonParm  ();
    else
      $padIdx = padFieldGetLevel ($tag);
    
    if ( file_exists ( PAD . "tag/".$field.".php" ) )
      return include PAD . "tag/$field.php";

    if ( in_array ( $parm, ['name','value'] ) and $padIdx and isset($GLOBALS ['padCurrent'] ) ) {
      $pados = 1;
      foreach( $GLOBALS ['padCurrent'] [$padIdx] as $key => $value )
        if ( $pados++ == $field )
          return ( $parm == 'name') ? $key : $value;
    }

    if ( $tag )
      return padFieldDoubleCheck ( $tag, ':', $field );

    return INF;

  }


  function padFieldFirstNonParm  () {

    global $pad, $padType;

    for ($i=$pad; $i; $i--)
      if ( $padType[$i] and $padType[$i] <> 'parm' )
        return $i;

    return $pad - 1;

  }  


?>