<?php


  function pField_tag ($field) {

    if ( substr($field, 0, 1) == '#' ) {
      $temp  = pExplode ($field, '#', 2);
      $tag   = '';
      $field = $temp[0];
      $parm  = $temp[1]??'';
    } else {
      $temp  = pExplode ($field, '#', 3);
      $tag   = $temp[0];
      $field = $temp[1];
      $parm  = $temp[2]??'';
    }

    if ( ! $tag )
      $pIdx = pFieldFirstNonParm  ();
    else
      $pIdx = pFieldGetLevel ($tag);
    
    if ( file_exists ( PAD . "tag/".$field.".php" ) )
      return include PAD . "tag/$field.php";

    if ( in_array ( $parm, ['name','value'] ) and $pIdx and isset($GLOBALS['pCurrent'] ) ) {
      $pos = 1;
      foreach( $GLOBALS['pCurrent'] [$pIdx] as $key => $value )
        if ( $pos++ == $field )
          return ( $parm == 'name') ? $key : $value;
    }

    if ( $tag )
      return pFieldDoubleCheck ( $tag, ':', $field );

    return INF;

  }


  function pFieldFirstNonParm  () {

    global $p, $pType;

    for ($i=$p; $i; $i--)
      if ( $pType[$i] and $pType[$i] <> 'parm' )
        return $i;

    return $p - 1;

  }  


?>